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

        $module->update($this->normalize($data, $module));
        $this->syncModuleMaterialsFromChapters($module->fresh());

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
            $videoSource = $chapter['video_source'] ?? 'none';
            $existingVideo = $chapter['existing_video'] ?? null;

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
            ];

            $material = $existingMaterials->get($order);
            if ($material) {
                $material->update([
                    'title' => $title,
                    'content' => $content,
                    'video_url' => $videoUrl !== '' ? $videoUrl : null,
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
}
