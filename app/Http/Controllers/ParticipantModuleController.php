<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\ModuleMaterial;
use App\Services\ParticipantModuleService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class ParticipantModuleController extends Controller
{
    private ParticipantModuleService $moduleService;

    public function __construct(ParticipantModuleService $moduleService)
    {
        $this->moduleService = $moduleService;
    }

    /**
     * Display all available modules for the participant
     */
    public function index(Request $request): View
    {
        $modules = $this->moduleService->getAvailableModules();
        $user = auth()->user() ?? $this->getDefaultParticipantUser() ?? $this->getDefaultParticipantUser();

        // Calculate progress for each module
        $modulesWithProgress = $modules->map(function ($module) use ($user) {
            $progress = $this->moduleService->calculateOverallProgress($module, $user);
            return [
                'module' => $module,
                'progress' => $progress,
            ];
        });

        $dashboard = $this->getParticipantDashboardConfig('modules');
        $viewUser = [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role ?? 'participant',
        ];

        return view('dashboard.participant.modules', [
            'modulesWithProgress' => $modulesWithProgress,
            'dashboard' => $dashboard,
            'user' => $viewUser,
        ]);
    }

    /**
     * Display module details with materials
     */
    public function show(Request $request, string $moduleSlug): View
    {
        $user = auth()->user() ?? $this->getDefaultParticipantUser() ?? $this->getDefaultParticipantUser();
        $moduleData = $this->moduleService->getModuleForParticipantBySlug($moduleSlug, $user);

        if (! $moduleData) {
            abort(404);
        }

        $module = $moduleData['module'];

        // Handle tab parameter
        $activeTab = $request->query('tab', 'materi');
        $selectedMaterialSlug = $request->query('material');

        // Get selected material if specified
        $selectedMaterial = null;
        if ($selectedMaterialSlug) {
            $selectedMaterial = $moduleData['materials']->firstWhere('slug', $selectedMaterialSlug);
        } elseif ($moduleData['materials']->isNotEmpty()) {
            $selectedMaterial = $moduleData['materials']->first();
        }

        $dashboard = $this->getParticipantDashboardConfig('modules');
        $viewUser = [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role ?? 'participant',
        ];

        return view('dashboard.participant.module-detail', [
            'module' => $module,
            'moduleData' => $moduleData,
            'activeTab' => $activeTab,
            'selectedMaterial' => $selectedMaterial,
            'moduleSlug' => $moduleSlug,
            'dashboard' => $dashboard,
            'user' => $viewUser,
        ]);
    }

    /**
     * Mark material as started (API endpoint)
     */
    public function markMaterialStarted(Request $request, string $moduleSlug, string $materialSlug): JsonResponse
    {
        $user = auth()->user() ?? $this->getDefaultParticipantUser();
        $module = $this->moduleService->findModuleBySlug($moduleSlug);

        if (! $module) {
            abort(404);
        }

        $material = $module->materials->firstWhere('slug', $materialSlug);

        if (! $material) {
            abort(404);
        }

        $progress = $this->moduleService->markMaterialAsStarted($module, $material, $user);

        return response()->json([
            'success' => true,
            'progress' => $progress,
        ]);
    }

    /**
     * Mark material as completed (API endpoint)
     */
    public function markMaterialCompleted(Request $request, string $moduleSlug, string $materialSlug): JsonResponse
    {
        $user = auth()->user() ?? $this->getDefaultParticipantUser();
        $module = $this->moduleService->findModuleBySlug($moduleSlug);

        if (! $module) {
            abort(404);
        }

        $material = $module->materials->firstWhere('slug', $materialSlug);

        if (! $material) {
            abort(404);
        }

        $progress = $this->moduleService->markMaterialAsCompleted($module, $material, $user);

        return response()->json([
            'success' => true,
            'progress' => $progress,
        ]);
    }

    /**
     * Submit assignment for a module
     */
    public function submitAssignment(Request $request, string $moduleSlug): RedirectResponse
    {
        $request->validate([
            'assignment_file' => 'required|file|max:10240|mimes:pdf,doc,docx,ppt,pptx,jpg,jpeg,png,zip,rar',
        ]);

        $user = auth()->user() ?? $this->getDefaultParticipantUser();
        $module = $this->moduleService->findModuleBySlug($moduleSlug);

        if (! $module) {
            abort(404);
        }

        // Find the assignment material (if any)
        $material = null;
        if ($request->has('material_slug')) {
            $material = $module->materials->firstWhere('slug', $request->material_slug);
        }

        try {
            $assignment = $this->moduleService->submitAssignment(
                $module,
                $material,
                $user,
                $request->file('assignment_file')
            );

            return redirect()->back()->with('status', 'Tugas berhasil dikirim!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['assignment_file' => 'Gagal mengupload tugas. Silakan coba lagi.']);
        }
    }

    /**
     * Get module progress (API endpoint)
     */
    public function getProgress(Request $request, string $moduleSlug): JsonResponse
    {
        $user = auth()->user() ?? $this->getDefaultParticipantUser();
        $module = $this->moduleService->findModuleBySlug($moduleSlug);

        if (! $module) {
            abort(404);
        }

        $progress = $this->moduleService->calculateOverallProgress($module, $user);
        $statistics = $this->moduleService->getModuleStatistics($module, $user);

        return response()->json([
            'progress' => $progress,
            'statistics' => $statistics,
        ]);
    }

    private function getDefaultParticipantUser(): \App\Models\User
    {
        $user = new \App\Models\User();
        $user->id = 0;
        $user->name = 'Peserta Batik';
        $user->email = 'peserta@lms-batik.local';
        $user->role = 'participant';

        return $user;
    }

    private function getParticipantDashboardConfig(string $activePage): array
    {
        $pages = [
            'home' => [
                'title' => 'Dashboard Peserta',
                'subtitle' => 'Pantau progres belajar dan akses fitur utama dengan cepat.',
            ],
            'modules' => [
                'title' => 'Modul Pembelajaran',
                'subtitle' => 'Lihat progres modul dan lanjutkan pembelajaran Anda.',
            ],
        ];

        $selected = $pages[$activePage] ?? $pages['home'];

        return [
            ...$selected,
            'view' => 'dashboard.participant.modules',
            'headerGradient' => 'from-slate-900 to-blue-900',
            'roleBadgeClasses' => 'bg-blue-100 text-blue-700',
            'activeMenuClasses' => 'bg-blue-100 text-blue-800',
            'profileUrl' => route('dashboard.participant.profile'),
            'showNotification' => true,
            'menuItems' => [
                [
                    'label' => 'Dashboard',
                    'icon' => 'home',
                    'url' => route('dashboard.participant.home'),
                    'active' => $activePage === 'home',
                ],
                [
                    'label' => 'Modul Pembelajaran',
                    'icon' => 'book',
                    'url' => route('dashboard.participant.modules'),
                    'active' => $activePage === 'modules',
                ],
                [
                    'label' => 'Forum Diskusi',
                    'icon' => 'chat',
                    'url' => route('dashboard.participant.forum'),
                    'active' => $activePage === 'forum',
                ],
                [
                    'label' => 'Galeri Karya',
                    'icon' => 'gallery',
                    'url' => route('dashboard.participant.gallery'),
                    'active' => $activePage === 'gallery',
                ],
            ],
        ];
    }
}
