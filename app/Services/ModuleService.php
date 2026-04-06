<?php

namespace App\Services;

use App\Models\Module;
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
        return Module::create($this->normalize($data));
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
        if (!isset($data['status'])) {
            $data['status'] = 'Draft';
        }

        if (!isset($data['chapters']) || !is_array($data['chapters'])) {
            $data['chapters'] = [];
        }

        // Process chapters to normalize video URLs
        foreach ($data['chapters'] as &$chapter) {
            if (isset($chapter['video']) && $chapter['video']) {
                $chapter['video'] = $this->normalizeVideoUrl($chapter['video']);
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
}
