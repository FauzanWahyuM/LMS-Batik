<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Services\ParticipantModuleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class ParticipantLearningApiController extends BaseApiController
{
    public function __construct(private readonly ParticipantModuleService $moduleService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $user = $this->resolveParticipantUser($request);

        $modules = $this->moduleService->getAvailableModules()->map(function ($module) use ($user) {
            $moduleData = $this->moduleService->getModuleForParticipant($module, $user);

            return [
                'module' => $module,
                'progress' => [
                    'overall' => $moduleData['overall_progress'],
                    'completed' => $moduleData['completed_count'],
                    'total' => $moduleData['total_count'],
                ],
            ];
        });

        return $this->successResponse('Daftar modul berhasil diambil.', $modules);
    }

    public function show(Request $request, string $moduleSlug): JsonResponse
    {
        $user = $this->resolveParticipantUser($request);
        $moduleData = $this->moduleService->getModuleForParticipantBySlug($moduleSlug, $user);

        if (! $moduleData) {
            return $this->errorResponse('Modul tidak ditemukan.', null, 404);
        }

        return $this->successResponse('Detail modul berhasil diambil.', $moduleData);
    }

    public function progress(Request $request, string $moduleSlug): JsonResponse
    {
        $user = $this->resolveParticipantUser($request);
        $module = $this->moduleService->findModuleBySlug($moduleSlug);

        if (! $module) {
            return $this->errorResponse('Modul tidak ditemukan.', null, 404);
        }

        return $this->successResponse('Progres modul berhasil diambil.', [
            'overall_progress' => $this->moduleService->calculateOverallProgress($module, $user),
            'statistics' => $this->moduleService->getModuleStatistics($module, $user),
        ]);
    }

    public function startMaterial(Request $request, string $moduleSlug, string $materialSlug): JsonResponse
    {
        $user = $this->resolveParticipantUser($request);
        $module = $this->moduleService->findModuleBySlug($moduleSlug);

        if (! $module) {
            return $this->errorResponse('Modul tidak ditemukan.', null, 404);
        }

        $material = $module->materials->firstWhere('slug', $materialSlug);
        if (! $material) {
            return $this->errorResponse('Materi tidak ditemukan.', null, 404);
        }

        $progress = $this->moduleService->markMaterialAsStarted($module, $material, $user);

        return $this->successResponse('Materi berhasil ditandai mulai dipelajari.', $progress);
    }

    public function markMaterialRead(Request $request, string $moduleSlug, string $materialSlug): JsonResponse
    {
        $user = $this->resolveParticipantUser($request);
        $module = $this->moduleService->findModuleBySlug($moduleSlug);

        if (! $module) {
            return $this->errorResponse('Modul tidak ditemukan.', null, 404);
        }

        $material = $this->resolveMaterial($module, $materialSlug);
        if (! $material) {
            return $this->errorResponse('Materi tidak ditemukan.', null, 404);
        }

        $this->moduleService->markMaterialAsRead($module, $material, $user);

        return $this->successResponse('Status baca materi berhasil diperbarui.', $this->buildMaterialResponse($module, $material, $user));
    }

    public function markMaterialWatched(Request $request, string $moduleSlug, string $materialSlug): JsonResponse
    {
        $user = $this->resolveParticipantUser($request);
        $module = $this->moduleService->findModuleBySlug($moduleSlug);

        if (! $module) {
            return $this->errorResponse('Modul tidak ditemukan.', null, 404);
        }

        $material = $this->resolveMaterial($module, $materialSlug);
        if (! $material) {
            return $this->errorResponse('Materi tidak ditemukan.', null, 404);
        }

        $this->moduleService->markMaterialVideoAsWatched($module, $material, $user);

        return $this->successResponse('Status tonton video berhasil diperbarui.', $this->buildMaterialResponse($module, $material, $user));
    }

    public function uploadAssignment(Request $request, string $moduleSlug): JsonResponse
    {
        $validated = $request->validate([
            'assignment_file' => ['required', 'file', 'max:10240', 'mimes:pdf,doc,docx,ppt,pptx,jpg,jpeg,png,zip,rar'],
            'material_slug' => ['nullable', 'string'],
        ]);

        $user = $this->resolveParticipantUser($request);
        $module = $this->moduleService->findModuleBySlug($moduleSlug);

        if (! $module) {
            return $this->errorResponse('Modul tidak ditemukan.', null, 404);
        }

        $material = null;
        $materialSlug = (string) ($validated['material_slug'] ?? '');
        if ($materialSlug !== '') {
            $material = $module->materials->firstWhere('slug', $materialSlug);
        }

        try {
            $assignment = $this->moduleService->submitAssignment(
                $module,
                $material,
                $user,
                $request->file('assignment_file'),
                $materialSlug !== '' ? $materialSlug : null
            );

            return $this->successResponse('Tugas berhasil diunggah.', $assignment, 201);
        } catch (\Throwable $e) {
            return $this->errorResponse('Gagal mengunggah tugas.', [
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    private function buildMaterialResponse($module, $material, User $user): array
    {
        return [
            'material' => $this->moduleService->getMaterialStateForUser($module, $material, $user),
            'progress' => [
                'overall' => $this->moduleService->calculateOverallProgress($module, $user),
            ],
            'statistics' => $this->moduleService->getModuleStatistics($module, $user),
        ];
    }

    private function resolveMaterial($module, string $materialSlug)
    {
        $material = $module->materials->firstWhere('slug', $materialSlug);

        if ($material) {
            return $material;
        }

        return $this->moduleService->getMaterialsForModule($module)->firstWhere('slug', $materialSlug);
    }

    private function resolveParticipantUser(Request $request): User
    {
        if (Auth::check()) {
            return Auth::user();
        }

        $authUser = $request->session()->get('auth_user', []);

        if (!empty($authUser['email'])) {
            $name = $authUser['name'] ?? 'Peserta Batik';

            return User::firstOrCreate(
                ['email' => $authUser['email']],
                [
                    'name' => $name,
                    'password' => Str::random(32),
                    'role' => 'peserta',
                ]
            );
        }

        $user = new User();
        $user->id = 0;
        $user->name = 'Peserta Batik';
        $user->email = 'peserta@lms-batik.local';
        $user->role = 'peserta';

        return $user;
    }
}
