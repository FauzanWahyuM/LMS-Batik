<?php

namespace App\Http\Controllers\Api;

use App\Models\Module;
use App\Services\ModuleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InstructorModuleApiController extends BaseApiController
{
    public function index(Request $request, ModuleService $service): JsonResponse
    {
        if ($response = $this->ensureInstructorRole($request)) {
            return $response;
        }

        $search = (string) $request->query('search', '');
        $modules = $service->list($search)->map(function (Module $module): array {
            return [
                'id' => $module->id,
                'slug' => $module->slug,
                'title' => $module->title,
                'description' => $module->description,
                'duration' => is_numeric($module->duration) ? (float) $module->duration : $module->duration,
                'status' => $module->status ?? 'Draft',
                'cover' => $module->cover,
                'created_at' => $module->created_at,
                'updated_at' => $module->updated_at,
            ];
        });

        return $this->successResponse('Daftar modul berhasil diambil.', $modules);
    }

    public function store(Request $request, ModuleService $service): JsonResponse
    {
        if ($response = $this->ensureInstructorRole($request)) {
            return $response;
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'duration' => ['required', 'numeric', 'min:0.25', 'max:10000'],
            'description' => ['nullable', 'string', 'max:1000'],
            'cover' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('cover')) {
            $validated['cover'] = $service->uploadCover($request->file('cover'));
        }

        $module = $service->create($validated);

        return $this->successResponse('Modul baru berhasil dibuat.', $module, 201);
    }

    public function show(Request $request, Module $module): JsonResponse
    {
        if ($response = $this->ensureInstructorRole($request)) {
            return $response;
        }

        return $this->successResponse('Detail modul berhasil diambil.', $module);
    }

    public function update(Request $request, Module $module, ModuleService $service): JsonResponse
    {
        if ($response = $this->ensureInstructorRole($request)) {
            return $response;
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'duration' => ['required', 'numeric', 'min:0.25', 'max:10000'],
            'description' => ['nullable', 'string', 'max:1000'],
            'cover' => ['nullable', 'image', 'max:2048'],
            'delete_cover' => ['nullable', 'boolean'],
            'chapters' => ['nullable', 'array'],
            'chapters.*.title' => ['nullable', 'string', 'max:255'],
            'chapters.*.description' => ['nullable', 'string', 'max:500'],
            'chapters.*.content' => ['nullable', 'string'],
            'chapters.*.video_source' => ['nullable', 'in:link,upload,none'],
            'chapters.*.video_link' => ['nullable', 'url', 'max:2048'],
            'chapters.*.video_upload' => ['nullable', 'file', 'mimes:mp4,mov,webm,ogg,mkv', 'max:51200'],
            'chapters.*.existing_video' => ['nullable', 'string', 'max:255'],
            'chapters.*.assignment' => ['nullable', 'string'],
            'chapters.*.assignment_deadline' => ['nullable', 'date_format:Y-m-d'],
            'chapters.*.images' => ['nullable', 'array'],
            'chapters.*.images.*.image_upload' => ['nullable', 'image', 'max:5120'],
            'chapters.*.images.*.existing_path' => ['nullable', 'string', 'max:255'],
            'chapters.*.images.*.delete_image' => ['nullable', 'boolean'],
            'chapters.*.images.*.title' => ['nullable', 'string', 'max:120'],
            'chapters.*.images.*.caption' => ['nullable', 'string', 'max:500'],
            'chapters.*.images.*.width' => ['nullable', 'integer', 'min:25', 'max:100'],
        ]);

        if ($request->hasFile('cover')) {
            $validated['cover'] = $service->uploadCover($request->file('cover'));
        }

        $updated = $service->update($module, $validated);

        return $this->successResponse('Modul berhasil diperbarui.', $updated);
    }

    public function destroy(Request $request, Module $module, ModuleService $service): JsonResponse
    {
        if ($response = $this->ensureInstructorRole($request)) {
            return $response;
        }

        $service->delete($module);

        return $this->successResponse('Modul berhasil dihapus.');
    }

    public function uploadChapterImage(Request $request, ModuleService $service): JsonResponse
    {
        if ($response = $this->ensureInstructorRole($request)) {
            return $response;
        }

        $validated = $request->validate([
            'image' => ['required', 'image', 'max:5120'],
        ]);

        $uploaded = $service->uploadChapterContentImage($validated['image']);

        return $this->successResponse('Gambar konten berhasil diunggah.', [
            'url' => $uploaded['url'],
            'path' => $uploaded['path'],
        ]);
    }

    private function ensureInstructorRole(Request $request): ?JsonResponse
    {
        $user = $request->session()->get('auth_user', []);

        if (($user['role'] ?? null) !== 'instructor') {
            return $this->errorResponse('Akses ditolak. Hanya pengajar yang diizinkan.', null, 403);
        }

        return null;
    }
}
