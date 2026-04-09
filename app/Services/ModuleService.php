<?php

namespace App\Services;

use App\Models\Module;
use App\Models\ModuleMaterial;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ModuleService
{
    public function list(string $search = '')
    {
        return Module::when($search, function ($query, $search) {
            $query->where('title', 'like', '%' . $search . '%');
        })->orderByDesc('updated_at')->get();
    }

    public function create(array $data): Module
    {
        $module = Module::create($this->normalize($data));
        $this->syncModuleMaterialsFromChapters($module);

        return $module;
    }

    public function update(Module $module, array $data): Module
    {
        $previousChapters = $module->chapters ?? [];

        if (isset($data['cover']) && $data['cover'] instanceof UploadedFile) {
            if ($module->cover) {
                Storage::disk('public')->delete($module->cover);
            }
            $data['cover'] = $this->uploadCover($data['cover']);
        } elseif (isset($data['cover']) && is_string($data['cover'])) {
            if ($module->cover) {
                Storage::disk('public')->delete($module->cover);
            }
            // cover already contains the stored path
        } elseif (isset($data['delete_cover']) && $data['delete_cover']) {
            if ($module->cover) {
                Storage::disk('public')->delete($module->cover);
            }
            $data['cover'] = null;
        } else {
            unset($data['cover']);
        }

        unset($data['delete_cover']);

        $normalizedData = $this->normalize($data, $module);
        $module->update($normalizedData);
        $this->syncModuleMaterialsFromChapters($module->fresh());
        $this->cleanupRemovedChapterImages($previousChapters, $normalizedData['chapters'] ?? []);

        return $module;
    }

    public function delete(Module $module): void
    {
        $module->delete();
    }

    public function uploadCover(UploadedFile $cover): string
    {
        return $cover->store('module-covers', 'public');
    }

    public function uploadChapterContentImage(UploadedFile $image): array
    {
        $path = $this->storeOptimizedImage($image->get(), $image->getClientOriginalExtension());

        return [
            'path' => $path,
            'url' => route('public-file', ['path' => ltrim($path, '/')]),
        ];
    }

    private function normalize(array $data, Module $module = null): array
    {
        if (isset($data['duration']) && is_numeric($data['duration'])) {
            $data['duration'] = round((float) $data['duration'], 2);
        }

        if (!isset($data['status'])) {
            $data['status'] = 'draft';
        } else {
            $data['status'] = strtolower(trim($data['status']));
        }

        if (!isset($data['chapters']) || !is_array($data['chapters'])) {
            $data['chapters'] = [];
        }

        // Process chapters to normalize video data and assignment deadline shape.
        foreach ($data['chapters'] as &$chapter) {
            $chapter['content'] = $this->stripImageTagsFromHtml((string) ($chapter['content'] ?? ''));

            $videoSource = $chapter['video_source'] ?? 'none';
            $existingVideo = $chapter['existing_video'] ?? null;
            $chapterImages = $this->normalizeChapterImages($chapter);

            if ($videoSource === 'link') {
                $chapter['video_type'] = 'link';
                $chapter['video'] = $this->normalizeVideoUrl((string) ($chapter['video_link'] ?? ''));
            } elseif ($videoSource === 'upload') {
                $chapter['video_type'] = 'upload';

                if (isset($chapter['video_upload']) && $chapter['video_upload'] instanceof UploadedFile) {
                    if (is_string($existingVideo) && $existingVideo !== '') {
                        Storage::disk('public')->delete($existingVideo);
                    }

                    $chapter['video'] = $chapter['video_upload']->store('module-videos', 'public');
                } else {
                    $chapter['video'] = $existingVideo;
                }
            } else {
                if (is_string($existingVideo) && $existingVideo !== '') {
                    Storage::disk('public')->delete($existingVideo);
                }

                $chapter['video_type'] = 'none';
                $chapter['video'] = null;
            }

            unset($chapter['video_source'], $chapter['video_link'], $chapter['video_upload'], $chapter['existing_video']);

            $chapter['images'] = $chapterImages;
            $chapter['image_path'] = $chapterImages[0]['path'] ?? null;
            $chapter['image_title'] = $chapterImages[0]['title'] ?? null;
            $chapter['image_caption'] = $chapterImages[0]['caption'] ?? null;
            $chapter['image_width'] = $chapterImages[0]['width'] ?? 75;

            if (empty($chapter['assignment_deadline'])) {
                $chapter['assignment_deadline'] = null;
            }
        }

        if (!isset($data['slug'])) {
            $data['slug'] = Str::slug($data['title'] ?? 'module') . '-' . Str::random(6);
        }

        if ($module) {
            unset($data['slug']);
        }

        return $data;
    }

    private function normalizeVideoUrl(string $url): string
    {
        if ($url === '') {
            return '';
        }

        // YouTube regular URL to embed
        if (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1];
        }

        // YouTube short URL
        if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1];
        }

        // Vimeo
        if (preg_match('/vimeo\.com\/(\d+)/', $url, $matches)) {
            return 'https://player.vimeo.com/video/' . $matches[1];
        }

        // If already embed URL or other, return as is
        return $url;
    }

    private function syncModuleMaterialsFromChapters(Module $module): void
    {
        $chapters = collect($module->chapters ?? []);

        if ($chapters->isEmpty()) {
            $module->materials()->delete();
            return;
        }

        $existingMaterials = $module->materials()->orderBy('order')->get()->keyBy('order');
        $usedIds = [];

        foreach ($chapters as $index => $chapter) {
            $order = $index + 1;
            $title = trim((string) ($chapter['title'] ?? 'Bab ' . $order));
            $content = (string) ($chapter['content'] ?? ($chapter['description'] ?? ''));
            $videoUrl = (string) ($chapter['video'] ?? '');
            $assignment = trim((string) ($chapter['assignment'] ?? ''));
            $images = $this->normalizeChapterImages($chapter);
            $imagePath = (string) ($images[0]['path'] ?? ($chapter['image_path'] ?? ''));

            $type = 'text';
            if ($assignment !== '') {
                $type = 'assignment';
            } elseif ($videoUrl !== '') {
                $type = 'video';
            }

            $metadata = [
                'description' => $chapter['description'] ?? null,
                'assignment' => $assignment !== '' ? $assignment : null,
                'deadline' => $chapter['assignment_deadline'] ?? null,
                'video_type' => $chapter['video_type'] ?? ($videoUrl !== '' ? (filter_var($videoUrl, FILTER_VALIDATE_URL) ? 'link' : 'upload') : 'none'),
                'images' => $images,
                'image_path' => $imagePath !== '' ? $imagePath : null,
                'image_title' => $images[0]['title'] ?? null,
                'image_caption' => $images[0]['caption'] ?? null,
                'image_width' => $images[0]['width'] ?? 75,
            ];

            $material = $existingMaterials->get($order);
            if ($material) {
                $material->update([
                    'title' => $title,
                    'content' => $content,
                    'video_url' => $videoUrl !== '' ? $videoUrl : null,
                    'thumbnail' => $imagePath !== '' ? $imagePath : null,
                    'type' => $type,
                    'metadata' => $metadata,
                    'order' => $order,
                ]);
            } else {
                $material = ModuleMaterial::create([
                    'module_id' => $module->id,
                    'title' => $title,
                    'slug' => Str::slug($title) . '-' . $module->id . '-' . $order,
                    'content' => $content,
                    'thumbnail' => $imagePath !== '' ? $imagePath : null,
                    'video_url' => $videoUrl !== '' ? $videoUrl : null,
                    'type' => $type,
                    'metadata' => $metadata,
                    'order' => $order,
                ]);
            }

            $usedIds[] = $material->id;
        }

        $module->materials()->whereNotIn('id', $usedIds)->delete();
    }

    private function normalizeChapterImages(array $chapter): array
    {
        $submittedImages = $chapter['images'] ?? [];

        if (!is_array($submittedImages)) {
            $submittedImages = [];
        }

        if (empty($submittedImages) && !empty($chapter['image_path'])) {
            $submittedImages = [[
                'existing_path' => $chapter['image_path'],
                'title' => $chapter['image_title'] ?? null,
                'caption' => $chapter['image_caption'] ?? null,
                'width' => $chapter['image_width'] ?? 75,
            ]];
        }

        $normalizedImages = [];

        foreach ($submittedImages as $imageData) {
            if (!is_array($imageData)) {
                continue;
            }

            $existingPath = (string) ($imageData['existing_path'] ?? $imageData['path'] ?? '');
            $deleteImage = !empty($imageData['delete_image']);

            if ($deleteImage) {
                if ($existingPath !== '') {
                    Storage::disk('public')->delete($existingPath);
                }

                continue;
            }

            $path = $existingPath;
            if (isset($imageData['image_upload']) && $imageData['image_upload'] instanceof UploadedFile) {
                if ($existingPath !== '') {
                    Storage::disk('public')->delete($existingPath);
                }

                $path = $this->storeOptimizedImage(
                    $imageData['image_upload']->get(),
                    $imageData['image_upload']->getClientOriginalExtension(),
                );
            }

            if ($path === '') {
                continue;
            }

            $title = trim((string) ($imageData['title'] ?? $imageData['image_title'] ?? ''));
            $caption = trim((string) ($imageData['caption'] ?? $imageData['image_caption'] ?? ''));
            $width = isset($imageData['width']) ? (int) $imageData['width'] : (int) ($imageData['image_width'] ?? 75);

            $normalizedImages[] = [
                'path' => $path,
                'title' => $title !== '' ? $title : null,
                'caption' => $caption !== '' ? $caption : null,
                'width' => max(25, min(100, $width ?: 75)),
            ];
        }

        return array_values($normalizedImages);
    }

    private function cleanupRemovedChapterImages(array $previousChapters, array $newChapters): void
    {
        $previousPaths = collect($previousChapters)
            ->flatMap(function ($chapter) {
                $images = $chapter['images'] ?? [];

                if (is_array($images) && !empty($images)) {
                    return collect($images)->pluck('path')->filter()->values();
                }

                if (!empty($chapter['image_path'])) {
                    return collect([$chapter['image_path']]);
                }

                return collect();
            })
            ->filter()
            ->unique()
            ->values();

        $newPaths = collect($newChapters)
            ->flatMap(function ($chapter) {
                $images = $chapter['images'] ?? [];

                if (is_array($images) && !empty($images)) {
                    return collect($images)->pluck('path')->filter()->values();
                }

                if (!empty($chapter['image_path'])) {
                    return collect([$chapter['image_path']]);
                }

                return collect();
            })
            ->filter()
            ->unique()
            ->values();

        $pathsToDelete = $previousPaths->diff($newPaths)->values();

        foreach ($pathsToDelete as $path) {
            Storage::disk('public')->delete($path);
        }
    }

    private function stripImageTagsFromHtml(string $content): string
    {
        if ($content === '') {
            return $content;
        }

        return (string) preg_replace('/<img\b[^>]*>/i', '', $content);
    }

    private function replaceEmbeddedBase64Images(string $content): string
    {
        if ($content === '' || stripos($content, 'data:image/') === false) {
            return $content;
        }

        return (string) preg_replace_callback(
            '/<img([^>]*?)src=["\'](data:image\/[a-zA-Z0-9.+-]+;base64,[^"\']+)["\']([^>]*?)>/i',
            function (array $matches): string {
                $dataUrl = $matches[2] ?? '';
                $stored = $this->storeImageFromDataUrl($dataUrl);

                if ($stored === null) {
                    return $matches[0];
                }

                    $src = e(route('public-file', ['path' => ltrim($stored, '/')]));

                return '<img' . $matches[1] . 'src="' . $src . '"' . $matches[3] . '>';
            },
            $content,
        );
    }

    private function storeImageFromDataUrl(string $dataUrl): ?string
    {
        if (!preg_match('/^data:image\/([a-zA-Z0-9.+-]+);base64,(.+)$/', $dataUrl, $matches)) {
            return null;
        }

        $binary = base64_decode($matches[2], true);
        if ($binary === false) {
            return null;
        }

        return $this->storeOptimizedImage($binary, $matches[1] ?? 'jpg');
    }

    private function storeOptimizedImage(string $binary, string $originalExtension = 'jpg'): string
    {
        if (!function_exists('imagecreatefromstring') || !function_exists('imagecreatetruecolor')) {
            $fallbackExtension = strtolower(trim($originalExtension, '.'));
            $fallbackExtension = $fallbackExtension !== '' ? $fallbackExtension : 'jpg';
            $fallbackPath = 'module-content-images/' . Str::uuid() . '.' . $fallbackExtension;
            Storage::disk('public')->put($fallbackPath, $binary);

            return $fallbackPath;
        }

        $image = @\imagecreatefromstring($binary);

        if ($image === false) {
            $fallbackExtension = strtolower(trim($originalExtension, '.'));
            $fallbackExtension = $fallbackExtension !== '' ? $fallbackExtension : 'jpg';
            $fallbackPath = 'module-content-images/' . Str::uuid() . '.' . $fallbackExtension;
            Storage::disk('public')->put($fallbackPath, $binary);

            return $fallbackPath;
        }

        $width = \imagesx($image);
        $height = \imagesy($image);
        $maxDimension = 1920;

        $targetWidth = $width;
        $targetHeight = $height;

        if ($width > $maxDimension || $height > $maxDimension) {
            $scale = min($maxDimension / $width, $maxDimension / $height);
            $targetWidth = max(1, (int) round($width * $scale));
            $targetHeight = max(1, (int) round($height * $scale));
        }

        $canvas = \imagecreatetruecolor($targetWidth, $targetHeight);
        \imagealphablending($canvas, false);
        \imagesavealpha($canvas, true);
        $transparent = \imagecolorallocatealpha($canvas, 0, 0, 0, 127);
        \imagefill($canvas, 0, 0, $transparent);
        \imagecopyresampled($canvas, $image, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

        $path = 'module-content-images/' . Str::uuid() . '.jpg';
        $encoded = false;

        ob_start();

        if (function_exists('imagewebp')) {
            $encoded = \imagewebp($canvas, null, 82);
            if ($encoded) {
                $path = 'module-content-images/' . Str::uuid() . '.webp';
            }
        } else {
            $encoded = \imagejpeg($canvas, null, 82);
        }

        $optimizedBinary = ob_get_clean();

        \imagedestroy($canvas);
        \imagedestroy($image);

        Storage::disk('public')->put($path, $encoded && $optimizedBinary !== false ? $optimizedBinary : $binary);

        return $path;
    }
}
