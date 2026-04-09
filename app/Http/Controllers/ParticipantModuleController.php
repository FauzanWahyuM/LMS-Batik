<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ForumDiscussionService;
use App\Services\ParticipantModuleService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class ParticipantModuleController extends Controller
{
    private ParticipantModuleService $moduleService;
    private ForumDiscussionService $discussionService;

    public function __construct(ParticipantModuleService $moduleService, ForumDiscussionService $discussionService)
    {
        $this->moduleService = $moduleService;
        $this->discussionService = $discussionService;
    }

    /**
     * Display all available modules for the participant
     */
    public function index(Request $request): View
    {
        $modules = $this->moduleService->getAvailableModules();
        $user = $this->resolveParticipantUser($request);

        $modulesWithProgress = $modules->map(function ($module) use ($user) {
            $moduleData = $this->moduleService->getModuleForParticipant($module, $user);
            return [
                'module' => $module,
                'progress' => $moduleData['overall_progress'],
                'moduleData' => $moduleData,
            ];
        });

        $dashboard = $this->getParticipantDashboardConfig('modules');
        $viewUser = [
            'id' => $user->id,
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
        $user = $this->resolveParticipantUser($request);
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
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role ?? 'participant',
        ];
        $moduleDiscussions = $this->discussionService->getModuleDiscussions($moduleSlug);

        return view('dashboard.participant.module-detail', [
            'module' => $module,
            'moduleData' => $moduleData,
            'activeTab' => $activeTab,
            'selectedMaterial' => $selectedMaterial,
            'moduleSlug' => $moduleSlug,
            'moduleDiscussions' => $moduleDiscussions,
            'dashboard' => $dashboard,
            'user' => $viewUser,
        ]);
    }

    /**
     * Mark material as started (API endpoint)
     */
    public function markMaterialStarted(Request $request, string $moduleSlug, string $materialSlug): JsonResponse|RedirectResponse
    {
        $user = $this->resolveParticipantUser($request);
        $module = $this->moduleService->findModuleBySlug($moduleSlug);

        if (! $module) {
            abort(404);
        }

        $material = $module->materials->firstWhere('slug', $materialSlug);

        if (! $material) {
            abort(404);
        }

        $progress = $this->moduleService->markMaterialAsStarted($module, $material, $user);

        if (! $request->expectsJson()) {
            return redirect()->route('dashboard.participant.modules.detail', [
                    'module' => $moduleSlug,
                    'tab' => 'materi',
                    'material' => $materialSlug,
                ]);
        }

        return response()->json([
            'success' => true,
            'progress' => $progress,
        ]);
    }

    /**
     * Mark material as completed (API endpoint)
     */
    public function markMaterialCompleted(Request $request, string $moduleSlug, string $materialSlug): JsonResponse|RedirectResponse
    {
        $user = $this->resolveParticipantUser($request);
        $module = $this->moduleService->findModuleBySlug($moduleSlug);

        if (! $module) {
            abort(404);
        }

        $material = $module->materials->firstWhere('slug', $materialSlug);

        if (! $material) {
            abort(404);
        }

        $progress = $this->moduleService->markMaterialAsCompleted($module, $material, $user);

        if (! $request->expectsJson()) {
            return redirect()->route('dashboard.participant.modules.detail', [
                    'module' => $moduleSlug,
                    'tab' => 'materi',
                    'material' => $materialSlug,
                ])->with('status', 'Bab berhasil ditandai selesai.');
        }

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
            'material_slug' => 'nullable|string',
        ]);

        $user = $this->resolveParticipantUser($request);
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
            $this->moduleService->submitAssignment(
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
        $user = $this->resolveParticipantUser($request);
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

    private function resolveParticipantUser(Request $request): User
    {
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

        return $this->getDefaultParticipantUser();
    }

    private function getDefaultParticipantUser(): User
    {
        $user = new User();
        $user->id = 0;
        $user->name = 'Peserta Batik';
        $user->email = 'peserta@lms-batik.local';
        $user->role = 'peserta';

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
