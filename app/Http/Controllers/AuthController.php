<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Hardcoded users for temporary authentication simulation.
     *
     * @var array<string, array{name: string, password: string, role: string}>
     */
    private array $users = [
        'participant@lmsbatik.test' => [
            'name' => 'Demo Participant',
            'password' => 'participant123',
            'role' => 'participant',
        ],
        'instructor@lmsbatik.test' => [
            'name' => 'Demo Instructor',
            'password' => 'instructor123',
            'role' => 'instructor',
        ],
        'manager@lmsbatik.test' => [
            'name' => 'Demo Manager',
            'password' => 'manager123',
            'role' => 'manager',
        ],
    ];

    public function showLogin(Request $request): View|RedirectResponse
    {
        if ($request->session()->has('auth_user')) {
            return redirect()->route('dashboard.index');
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $email = strtolower(trim($credentials['email']));
        $user = $this->users[$email] ?? null;

        if ((! $user) || ($user['password'] !== $credentials['password'])) {
            return back()
                ->withErrors(['email' => 'Email atau password tidak valid.'])
                ->withInput($request->except('password'));
        }

        $request->session()->put('auth_user', [
            'name' => $user['name'],
            'email' => $email,
            'role' => $user['role'],
        ]);

        $request->session()->regenerate();

        return redirect()->route('dashboard.index');
    }

    public function dashboard(Request $request): View|RedirectResponse
    {
        $user = $request->session()->get('auth_user');
        $role = $user['role'] ?? 'participant';

        if ($role === 'participant') {
            return redirect()->route('dashboard.participant.home');
        }

        if ($role === 'instructor') {
            return redirect()->route('dashboard.instructor.home');
        }

        if ($role === 'manager') {
            return redirect()->route('dashboard.manager.home');
        }

        return redirect()->route('dashboard.participant.home');
    }

    public function managerHome(Request $request): View|RedirectResponse
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        return view('dashboard.manager.index', [
            'user' => $request->session()->get('auth_user'),
            'dashboard' => $this->getManagerDashboardConfig('home'),
            'stats' => $this->getManagerStats(),
            'activities' => $this->getManagerActivities(),
        ]);
    }

    public function managerIndividualParticipants(Request $request): View|RedirectResponse
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        return view('dashboard.manager.participants-individual', [
            'user' => $request->session()->get('auth_user'),
            'dashboard' => $this->getManagerDashboardConfig('participants-individual'),
            'participants' => $this->getManagerIndividualParticipants(),
        ]);
    }

    public function managerIndividualParticipantsUpdate(Request $request, string $participant): RedirectResponse
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        $request->validate([
            'status' => ['required', 'string', 'in:Aktif,Perlu Verifikasi,Nonaktif'],
        ]);

        return redirect()
            ->route('dashboard.manager.participants.individual')
            ->with('status', 'Status peserta individu ' . strtoupper($participant) . ' diperbarui (simulasi).');
    }

    public function managerGroupParticipants(Request $request): View|RedirectResponse
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        return view('dashboard.manager.participants-group', [
            'user' => $request->session()->get('auth_user'),
            'dashboard' => $this->getManagerDashboardConfig('participants-group'),
            'groups' => $this->getManagerGroupParticipants(),
        ]);
    }

    public function managerGroupParticipantsUpdate(Request $request, string $group): RedirectResponse
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        $request->validate([
            'status' => ['required', 'string', 'in:Aktif,Perlu Verifikasi,Selesai'],
        ]);

        return redirect()
            ->route('dashboard.manager.participants.group')
            ->with('status', 'Status kelompok ' . strtoupper($group) . ' diperbarui (simulasi).');
    }

    public function managerInstructors(Request $request): View|RedirectResponse
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        return view('dashboard.manager.instructors', [
            'user' => $request->session()->get('auth_user'),
            'dashboard' => $this->getManagerDashboardConfig('instructors'),
            'instructors' => $this->getManagerInstructors(),
        ]);
    }

    public function managerInstructorsUpdate(Request $request, string $instructor): RedirectResponse
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        $request->validate([
            'status' => ['required', 'string', 'in:Aktif,Cuti,Nonaktif'],
        ]);

        return redirect()
            ->route('dashboard.manager.instructors')
            ->with('status', 'Status pengajar ' . strtoupper($instructor) . ' diperbarui (simulasi).');
    }

    public function managerPrograms(Request $request): View|RedirectResponse
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        return view('dashboard.manager.programs', [
            'user' => $request->session()->get('auth_user'),
            'dashboard' => $this->getManagerDashboardConfig('programs'),
            'programs' => $this->getManagerPrograms(),
        ]);
    }

    public function managerProgramsUpdate(Request $request, string $program): RedirectResponse
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        $request->validate([
            'status' => ['required', 'string', 'in:Aktif,Draf,Ditutup'],
        ]);

        return redirect()
            ->route('dashboard.manager.programs')
            ->with('status', 'Status program ' . strtoupper($program) . ' diperbarui (simulasi).');
    }

    public function managerReports(Request $request): View|RedirectResponse
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        return view('dashboard.manager.reports', [
            'user' => $request->session()->get('auth_user'),
            'dashboard' => $this->getManagerDashboardConfig('reports'),
            'reports' => $this->getManagerReportSummary(),
            'monthlyParticipation' => $this->getManagerMonthlyParticipation(),
        ]);
    }

    public function managerReportsExport(Request $request): RedirectResponse
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        $request->validate([
            'report_type' => ['required', 'string', 'in:partisipasi,kinerja,pengajar'],
        ]);

        return redirect()
            ->route('dashboard.manager.reports')
            ->with('status', 'Export laporan ' . strtoupper((string) $request->input('report_type')) . ' berhasil diproses (simulasi).');
    }

    public function managerSettings(Request $request): View|RedirectResponse
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        return view('dashboard.manager.settings', [
            'user' => $request->session()->get('auth_user'),
            'dashboard' => $this->getManagerDashboardConfig('settings'),
            'settingsData' => $this->getManagerSettingsData(),
        ]);
    }

    public function managerSettingsUpdate(Request $request): RedirectResponse
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        $request->validate([
            'organization_name' => ['required', 'string', 'min:3', 'max:120'],
            'support_email' => ['required', 'email'],
            'timezone' => ['required', 'string'],
        ]);

        return redirect()
            ->route('dashboard.manager.settings')
            ->with('status', 'Pengaturan sistem berhasil diperbarui (simulasi).');
    }

    public function instructorHome(Request $request): View|RedirectResponse
    {
        $guard = $this->ensureInstructorRole($request);

        if ($guard) {
            return $guard;
        }

        $dashboard = $this->getInstructorDashboardConfig('home');

        return view('dashboard.instructor.index', [
            'user' => $request->session()->get('auth_user'),
            'dashboard' => $dashboard,
            'summary' => $this->getInstructorDashboardSummary(),
            'activeParticipants' => $this->getInstructorHomeParticipants(),
            'availableModules' => $this->getInstructorHomeModules(),
            'pendingWorks' => $this->getInstructorPendingWorks(),
        ]);
    }

    public function instructorModules(Request $request): View|RedirectResponse
    {
        $guard = $this->ensureInstructorRole($request);

        if ($guard) {
            return $guard;
        }

        $search = $request->query('search', '');
        $modules = $this->getInstructorModulesList();

        if ($search) {
            $modules = array_filter($modules, function ($module) use ($search) {
                return stripos($module['title'], $search) !== false;
            });
        }

        return view('dashboard.instructor.modules-list', [
            'user' => $request->session()->get('auth_user'),
            'dashboard' => $this->getInstructorDashboardConfig('modules'),
            'modules' => $modules,
        ]);
    }

    public function instructorModulesCreate(Request $request): View|RedirectResponse
    {
        $guard = $this->ensureInstructorRole($request);

        if ($guard) {
            return $guard;
        }

        return view('dashboard.instructor.modules-create', [
            'user' => $request->session()->get('auth_user'),
            'dashboard' => $this->getInstructorDashboardConfig('modules'),
        ]);
    }

    public function instructorModulesStore(Request $request): RedirectResponse
    {
        $guard = $this->ensureInstructorRole($request);

        if ($guard) {
            return $guard;
        }

        $request->validate([
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'duration' => ['required', 'string', 'min:3', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'cover' => ['nullable', 'image', 'max:2048'],
        ]);

        return redirect()
            ->route('dashboard.instructor.modules')
            ->with('status', 'Modul baru berhasil dibuat (simulasi).');
    }

    public function instructorModulesDetail(Request $request, string $module): View|RedirectResponse
    {
        $guard = $this->ensureInstructorRole($request);

        if ($guard) {
            return $guard;
        }

        $moduleData = $this->getInstructorModuleDetail($module);

        if (!$moduleData) {
            return redirect()->route('dashboard.instructor.modules');
        }

        return view('dashboard.instructor.modules-detail', [
            'user' => $request->session()->get('auth_user'),
            'dashboard' => $this->getInstructorDashboardConfig('modules'),
            'module' => $moduleData,
        ]);
    }

    public function instructorModulesEdit(Request $request, string $module): View|RedirectResponse
    {
        $guard = $this->ensureInstructorRole($request);

        if ($guard) {
            return $guard;
        }

        $moduleData = $this->getInstructorModuleDetail($module);

        if (!$moduleData) {
            return redirect()->route('dashboard.instructor.modules');
        }

        return view('dashboard.instructor.modules-edit', [
            'user' => $request->session()->get('auth_user'),
            'dashboard' => $this->getInstructorDashboardConfig('modules'),
            'module' => $moduleData,
        ]);
    }

    public function instructorModulesEditStore(Request $request, string $module): RedirectResponse
    {
        $guard = $this->ensureInstructorRole($request);

        if ($guard) {
            return $guard;
        }

        $request->validate([
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'duration' => ['required', 'string', 'min:3', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'cover' => ['nullable', 'image', 'max:2048'],
            'chapters' => ['nullable', 'array'],
            'chapters.*.title' => ['nullable', 'string', 'max:255'],
            'chapters.*.description' => ['nullable', 'string', 'max:500'],
            'chapters.*.content' => ['nullable', 'string'],
            'chapters.*.video' => ['nullable', 'url'],
            'chapters.*.assignment' => ['nullable', 'string'],
            'chapters.*.assignment_deadline' => ['nullable', 'string'],
        ]);

        return redirect()
            ->route('dashboard.instructor.modules.detail', ['module' => $module])
            ->with('status', 'Modul berhasil diperbarui (simulasi).');
    }

    public function instructorModulesDelete(Request $request, string $module): RedirectResponse
    {
        $guard = $this->ensureInstructorRole($request);

        if ($guard) {
            return $guard;
        }

        return redirect()
            ->route('dashboard.instructor.modules')
            ->with('status', 'Modul berhasil dihapus (simulasi).');
    }

    public function instructorParticipants(Request $request): View|RedirectResponse
    {
        $guard = $this->ensureInstructorRole($request);

        if ($guard) {
            return $guard;
        }

        $selected = (string) $request->query('participant', '');

        return view('dashboard.instructor.participants', [
            'user' => $request->session()->get('auth_user'),
            'dashboard' => $this->getInstructorDashboardConfig('participants'),
            'participants' => $this->getInstructorParticipants(),
            'selectedParticipant' => $selected,
        ]);
    }

    public function instructorForum(Request $request): View|RedirectResponse
    {
        $guard = $this->ensureInstructorRole($request);

        if ($guard) {
            return $guard;
        }

        $selected = (string) $request->query('thread', 't-01');

        return view('dashboard.instructor.forum', [
            'user' => $request->session()->get('auth_user'),
            'dashboard' => $this->getInstructorDashboardConfig('forum'),
            'threads' => $this->getInstructorThreads(),
            'selectedThread' => $selected,
        ]);
    }

    public function instructorForumReply(Request $request, string $thread): RedirectResponse
    {
        $guard = $this->ensureInstructorRole($request);

        if ($guard) {
            return $guard;
        }

        $request->validate([
            'reply' => ['required', 'string', 'min:3', 'max:500'],
        ]);

        return redirect()
            ->route('dashboard.instructor.forum', ['thread' => $thread])
            ->with('status', 'Balasan thread berhasil dikirim (simulasi).');
    }

    public function instructorAssessments(Request $request): View|RedirectResponse
    {
        $guard = $this->ensureInstructorRole($request);

        if ($guard) {
            return $guard;
        }

        return view('dashboard.instructor.assessments', [
            'user' => $request->session()->get('auth_user'),
            'dashboard' => $this->getInstructorDashboardConfig('assessments'),
            'submissions' => $this->getInstructorSubmissions(),
        ]);
    }

    public function instructorAssessmentScore(Request $request, string $submission): RedirectResponse
    {
        $guard = $this->ensureInstructorRole($request);

        if ($guard) {
            return $guard;
        }

        $request->validate([
            'score' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        return redirect()
            ->route('dashboard.instructor.assessments')
            ->with('status', 'Nilai untuk tugas ' . strtoupper($submission) . ' berhasil disimpan (simulasi).');
    }

    public function participantHome(Request $request): View|RedirectResponse
    {
        $guard = $this->ensureParticipantRole($request);

        if ($guard) {
            return $guard;
        }

        return $this->renderParticipantPage($request, 'home');
    }

    public function participantModules(Request $request): View|RedirectResponse
    {
        $guard = $this->ensureParticipantRole($request);

        if ($guard) {
            return $guard;
        }

        return $this->renderParticipantPage($request, 'modules');
    }

    public function participantModuleDetail(Request $request, string $module): View|RedirectResponse
    {
        $guard = $this->ensureParticipantRole($request);

        if ($guard) {
            return $guard;
        }

        $modules = $this->getParticipantModules();
        $moduleData = $modules[$module] ?? null;

        if (! $moduleData) {
            return redirect()->route('dashboard.participant.modules');
        }

        $activeTab = $request->query('tab', 'materi');

        if (! in_array($activeTab, ['materi', 'video', 'tugas', 'diskusi'], true)) {
            $activeTab = 'materi';
        }

        $selectedMaterialSlug = $request->query('material');
        $selectedMaterial = null;

        if (is_string($selectedMaterialSlug) && isset($moduleData['materials'])) {
            foreach ($moduleData['materials'] as $materialItem) {
                if (($materialItem['slug'] ?? null) === $selectedMaterialSlug) {
                    $selectedMaterial = $materialItem;
                    break;
                }
            }
        }

        if (! $selectedMaterial && isset($moduleData['materials'][0])) {
            $selectedMaterial = $moduleData['materials'][0];
        }

        if (is_string($selectedMaterialSlug) && $selectedMaterial !== null) {
            $activeTab = 'materi';
        }

        $dashboard = $this->getParticipantDashboardConfig('modules');
        $dashboard['view'] = 'dashboard.participant.module-detail';
        $dashboard['title'] = 'Modul (Detail) - Peserta';
        $dashboard['subtitle'] = 'Pelajari detail modul secara terstruktur berdasarkan materi, video, tugas, dan diskusi.';

        return view($dashboard['view'], [
            'user' => $request->session()->get('auth_user'),
            'dashboard' => $dashboard,
            'moduleData' => $moduleData,
            'moduleSlug' => $module,
            'activeTab' => $activeTab,
            'selectedMaterial' => $selectedMaterial,
        ]);
    }

    public function uploadParticipantTask(Request $request, string $module): RedirectResponse
    {
        $guard = $this->ensureParticipantRole($request);

        if ($guard) {
            return $guard;
        }

        $modules = $this->getParticipantModules();

        if (! isset($modules[$module])) {
            return redirect()->route('dashboard.participant.modules');
        }

        $validated = $request->validate([
            'assignment_file' => ['required', 'file', 'max:10240', 'mimes:pdf,doc,docx,ppt,pptx,jpg,jpeg,png,zip,rar'],
        ]);

        $user = $request->session()->get('auth_user', []);
        $safeUserKey = preg_replace('/[^a-z0-9]+/i', '-', (string) ($user['email'] ?? 'peserta'));
        $safeUserKey = trim((string) $safeUserKey, '-');
        $safeUserKey = $safeUserKey !== '' ? $safeUserKey : 'peserta';

        $uploadedFile = $validated['assignment_file'];
        $fileName = now()->format('YmdHis') . '-' . $safeUserKey . '.' . $uploadedFile->getClientOriginalExtension();
        $uploadedFile->storeAs('tugas/' . $module, $fileName, 'local');

        return redirect()
            ->route('dashboard.participant.modules.detail', ['module' => $module, 'tab' => 'tugas'])
            ->with('status', 'Tugas berhasil diunggah: ' . $uploadedFile->getClientOriginalName());
    }

    public function participantForum(Request $request): View|RedirectResponse
    {
        $guard = $this->ensureParticipantRole($request);

        if ($guard) {
            return $guard;
        }

        return $this->renderParticipantPage($request, 'forum');
    }

    public function participantGallery(Request $request): View|RedirectResponse
    {
        $guard = $this->ensureParticipantRole($request);

        if ($guard) {
            return $guard;
        }

        return $this->renderParticipantPage($request, 'gallery');
    }

    private function renderParticipantPage(Request $request, string $page): View
    {
        $user = $request->session()->get('auth_user');
        $dashboard = $this->getParticipantDashboardConfig($page);

        return view($dashboard['view'], [
            'user' => $user,
            'dashboard' => $dashboard,
        ]);
    }

    private function getParticipantDashboardConfig(string $activePage): array
    {
        $config = [
            'home' => [
                'view' => 'dashboard.participant.home',
                'title' => 'Dashboard Peserta',
                'subtitle' => 'Pantau progres belajar dan akses fitur utama dengan cepat.',
                'headerGradient' => 'from-slate-900 to-blue-900',
            ],
            'modules' => [
                'view' => 'dashboard.participant.modules',
                'title' => 'Modul Pembelajaran',
                'subtitle' => 'Lihat progres modul dan lanjutkan pembelajaran Anda.',
                'headerGradient' => 'from-slate-900 to-blue-900',
            ],
            'forum' => [
                'view' => 'dashboard.participant.forum',
                'title' => 'Forum Diskusi',
                'subtitle' => 'Bertanya, berdiskusi, dan berbagi pengalaman belajar.',
                'headerGradient' => 'from-slate-900 to-blue-900',
            ],
            'gallery' => [
                'view' => 'dashboard.participant.gallery',
                'title' => 'Galeri Karya',
                'subtitle' => 'Kelola dan tampilkan hasil karya terbaik Anda.',
                'headerGradient' => 'from-slate-900 to-blue-900',
            ],
        ];

        $selected = $config[$activePage] ?? $config['home'];

        return [
            ...$selected,
            'roleBadgeClasses' => 'bg-blue-100 text-blue-700',
            'activeMenuClasses' => 'bg-blue-100 text-blue-800',
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

    private function ensureParticipantRole(Request $request): ?RedirectResponse
    {
        $user = $request->session()->get('auth_user');

        if (($user['role'] ?? null) !== 'participant') {
            return redirect()->route('dashboard.index');
        }

        return null;
    }

    private function ensureInstructorRole(Request $request): ?RedirectResponse
    {
        $user = $request->session()->get('auth_user');

        if (($user['role'] ?? null) !== 'instructor') {
            return redirect()->route('dashboard.index');
        }

        return null;
    }

    private function ensureManagerRole(Request $request): ?RedirectResponse
    {
        $user = $request->session()->get('auth_user');

        if (($user['role'] ?? null) !== 'manager') {
            return redirect()->route('dashboard.index');
        }

        return null;
    }

    private function getManagerDashboardConfig(string $activePage): array
    {
        $config = [
            'home' => [
                'title' => 'Dashboard - Pengelola',
                'subtitle' => 'Ringkasan operasional peserta, pengajar, dan program berjalan.',
            ],
            'participants-individual' => [
                'title' => 'Kelola Peserta Individu',
                'subtitle' => 'Lihat data peserta individu dan kelola status administratifnya.',
            ],
            'participants-group' => [
                'title' => 'Kelola Peserta Kelompok',
                'subtitle' => 'Pantau performa pendaftaran kelompok dan validasi kelengkapan data.',
            ],
            'instructors' => [
                'title' => 'Kelola Pengajar',
                'subtitle' => 'Atur status pengajar aktif serta distribusi beban program.',
            ],
            'programs' => [
                'title' => 'Kelola Program',
                'subtitle' => 'Kelola katalog program, kuota peserta, dan status publikasi.',
            ],
            'reports' => [
                'title' => 'Laporan',
                'subtitle' => 'Tinjau ringkasan partisipasi dan ekspor laporan operasional.',
            ],
            'settings' => [
                'title' => 'Pengaturan',
                'subtitle' => 'Konfigurasi preferensi dasar sistem dan kontak operasional.',
            ],
        ];

        $selected = $config[$activePage] ?? $config['home'];

        return [
            ...$selected,
            'headerGradient' => 'from-[#1f2937] to-[#374151]',
            'showNotification' => true,
            'roleBadgeClasses' => 'bg-slate-200 text-slate-700',
            'activeMenuClasses' => 'bg-slate-200 text-slate-900',
            'menuItems' => [
                [
                    'label' => 'Dashboard',
                    'icon' => 'dashboard',
                    'url' => route('dashboard.manager.home'),
                    'active' => $activePage === 'home',
                ],
                [
                    'label' => 'Kelola Peserta Individu',
                    'icon' => 'participant-individual',
                    'url' => route('dashboard.manager.participants.individual'),
                    'active' => $activePage === 'participants-individual',
                ],
                [
                    'label' => 'Kelola Peserta Kelompok',
                    'icon' => 'participant-group',
                    'url' => route('dashboard.manager.participants.group'),
                    'active' => $activePage === 'participants-group',
                ],
                [
                    'label' => 'Kelola Pengajar',
                    'icon' => 'instructor-manage',
                    'url' => route('dashboard.manager.instructors'),
                    'active' => $activePage === 'instructors',
                ],
                [
                    'label' => 'Kelola Program',
                    'icon' => 'program-manage',
                    'url' => route('dashboard.manager.programs'),
                    'active' => $activePage === 'programs',
                ],
                [
                    'label' => 'Laporan',
                    'icon' => 'reports',
                    'url' => route('dashboard.manager.reports'),
                    'active' => $activePage === 'reports',
                ],
                [
                    'label' => 'Pengaturan',
                    'icon' => 'settings',
                    'url' => route('dashboard.manager.settings'),
                    'active' => $activePage === 'settings',
                ],
            ],
        ];
    }

    /**
     * @return array<string, int>
     */
    private function getManagerStats(): array
    {
        return [
            'individualParticipants' => 120,
            'groupParticipants' => 34,
            'activeInstructors' => 9,
            'activePrograms' => 6,
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function getManagerActivities(): array
    {
        return [
            ['time' => '08:10', 'title' => 'Pendaftaran kelompok baru', 'description' => 'Kelompok Batik Lestari menunggu verifikasi berkas.'],
            ['time' => '09:35', 'title' => 'Perubahan status program', 'description' => 'Program Teknik Warna Dasar ditandai aktif.'],
            ['time' => '11:00', 'title' => 'Jadwal pengajar diperbarui', 'description' => 'Redistribusi jadwal pengajar untuk batch 4.'],
            ['time' => '13:20', 'title' => 'Laporan partisipasi bulanan', 'description' => 'Ringkasan Maret siap diekspor.'],
        ];
    }

    /**
     * @return array<int, array<string, string|int>>
     */
    private function getManagerIndividualParticipants(): array
    {
        return [
            ['id' => 'pi-01', 'name' => 'Nadia Putri', 'program' => 'Teknik Canting Dasar', 'progress' => 88, 'status' => 'Aktif'],
            ['id' => 'pi-02', 'name' => 'Rafi Akbar', 'program' => 'Teknik Warna Dasar', 'progress' => 61, 'status' => 'Perlu Verifikasi'],
            ['id' => 'pi-03', 'name' => 'Salsa Wicaksono', 'program' => 'Komposisi Motif', 'progress' => 75, 'status' => 'Aktif'],
            ['id' => 'pi-04', 'name' => 'Tio Ramadhan', 'program' => 'Teknik Canting Dasar', 'progress' => 42, 'status' => 'Nonaktif'],
        ];
    }

    /**
     * @return array<int, array<string, string|int>>
     */
    private function getManagerGroupParticipants(): array
    {
        return [
            ['id' => 'pg-01', 'group_name' => 'Batik Lestari', 'members' => 5, 'program' => 'Teknik Canting Dasar', 'status' => 'Aktif'],
            ['id' => 'pg-02', 'group_name' => 'Motif Muda', 'members' => 4, 'program' => 'Teknik Warna Dasar', 'status' => 'Perlu Verifikasi'],
            ['id' => 'pg-03', 'group_name' => 'Sanggar Nawasena', 'members' => 6, 'program' => 'Komposisi Motif', 'status' => 'Selesai'],
        ];
    }

    /**
     * @return array<int, array<string, string|int>>
     */
    private function getManagerInstructors(): array
    {
        return [
            ['id' => 'ig-01', 'name' => 'Dewi Handayani', 'specialty' => 'Canting', 'active_classes' => 3, 'status' => 'Aktif'],
            ['id' => 'ig-02', 'name' => 'Agus Pramono', 'specialty' => 'Pewarnaan', 'active_classes' => 2, 'status' => 'Aktif'],
            ['id' => 'ig-03', 'name' => 'Lina Saputri', 'specialty' => 'Motif', 'active_classes' => 0, 'status' => 'Cuti'],
        ];
    }

    /**
     * @return array<int, array<string, string|int>>
     */
    private function getManagerPrograms(): array
    {
        return [
            ['id' => 'pr-01', 'title' => 'Teknik Canting Dasar', 'quota' => 60, 'enrolled' => 48, 'status' => 'Aktif'],
            ['id' => 'pr-02', 'title' => 'Teknik Warna Dasar', 'quota' => 50, 'enrolled' => 37, 'status' => 'Aktif'],
            ['id' => 'pr-03', 'title' => 'Komposisi Motif Modern', 'quota' => 40, 'enrolled' => 22, 'status' => 'Draf'],
        ];
    }

    /**
     * @return array<string, int|string>
     */
    private function getManagerReportSummary(): array
    {
        return [
            'total_registration' => 154,
            'completion_rate' => '84%',
            'avg_attendance' => '89%',
            'instructor_utilization' => '78%',
        ];
    }

    /**
     * @return array<int, array<string, string|int>>
     */
    private function getManagerMonthlyParticipation(): array
    {
        return [
            ['month' => 'Jan', 'value' => 92],
            ['month' => 'Feb', 'value' => 105],
            ['month' => 'Mar', 'value' => 114],
            ['month' => 'Apr', 'value' => 128],
            ['month' => 'Mei', 'value' => 134],
            ['month' => 'Jun', 'value' => 121],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function getManagerSettingsData(): array
    {
        return [
            'organization_name' => 'LPK Kama Praja Madiun',
            'support_email' => 'support@lmsbatik.test',
            'timezone' => 'Asia/Jakarta',
        ];
    }

    private function getInstructorDashboardConfig(string $activePage): array
    {
        $config = [
            'home' => [
                'title' => 'Dashboard - Penguji',
                'subtitle' => 'Ringkasan performa kelas dan aktivitas penilaian terbaru.',
            ],
            'modules' => [
                'title' => 'Kelola Modul',
                'subtitle' => 'Atur status, konten, dan kualitas modul pembelajaran.',
            ],
            'participants' => [
                'title' => 'Daftar Peserta',
                'subtitle' => 'Pantau progres peserta dan lihat aktivitas pembelajaran.',
            ],
            'forum' => [
                'title' => 'Forum Diskusi',
                'subtitle' => 'Kelola thread, jawab pertanyaan, dan jaga kualitas diskusi.',
            ],
            'assessments' => [
                'title' => 'Penilaian Tugas',
                'subtitle' => 'Review dan beri nilai pada tugas yang telah dikumpulkan.',
            ],
        ];

        $selected = $config[$activePage] ?? $config['home'];

        return [
            ...$selected,
            'headerGradient' => 'from-[#0f4c81] to-[#1f6d8f]',
            'showNotification' => true,
            'roleBadgeClasses' => 'bg-sky-100 text-sky-700',
            'activeMenuClasses' => 'bg-sky-100 text-sky-900',
            'menuItems' => [
                [
                    'label' => 'Dashboard',
                    'icon' => 'dashboard',
                    'url' => route('dashboard.instructor.home'),
                    'active' => $activePage === 'home',
                ],
                [
                    'label' => 'Kelola Modul',
                    'icon' => 'module',
                    'url' => route('dashboard.instructor.modules'),
                    'active' => $activePage === 'modules',
                ],
                [
                    'label' => 'Daftar Peserta',
                    'icon' => 'participants',
                    'url' => route('dashboard.instructor.participants'),
                    'active' => $activePage === 'participants',
                ],
                [
                    'label' => 'Forum Diskusi',
                    'icon' => 'forum',
                    'url' => route('dashboard.instructor.forum'),
                    'active' => $activePage === 'forum',
                ],
                [
                    'label' => 'Penilaian Tugas',
                    'icon' => 'assessment',
                    'url' => route('dashboard.instructor.assessments'),
                    'active' => $activePage === 'assessments',
                ],
            ],
        ];
    }

    /**
     * @return array<string, int>
     */
    private function getInstructorDashboardSummary(): array
    {
        $activeParticipants = $this->getInstructorHomeParticipants();
        $availableModules = $this->getInstructorHomeModules();
        $pendingWorks = $this->getInstructorPendingWorks();

        return [
            'activeParticipants' => count($activeParticipants),
            'pendingReviews' => count($pendingWorks),
            'totalModules' => count($availableModules),
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function getInstructorHomeParticipants(): array
    {
        return [
            ['id' => 'p-01', 'name' => 'Anita Wijaya', 'program_type' => 'Teknik Canting Dasar', 'status' => 'Aktif'],
            ['id' => 'p-02', 'name' => 'Bima Pradana', 'program_type' => 'Teknik Warna Dasar', 'status' => 'Aktif'],
            ['id' => 'p-03', 'name' => 'Citra Kurnia', 'program_type' => 'Komposisi Motif Modern', 'status' => 'Aktif'],
            ['id' => 'p-04', 'name' => 'Deni Santoso', 'program_type' => 'Teknik Canting Dasar', 'status' => 'Butuh Pendampingan'],
            ['id' => 'p-05', 'name' => 'Eka Lestari', 'program_type' => 'Teknik Warna Dasar', 'status' => 'Aktif'],
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function getInstructorHomeModules(): array
    {
        return [
            ['id' => 'm-01', 'title' => 'Teknik Canting Dasar', 'summary' => 'Dasar penguasaan alat, malam, dan kontrol garis canting.', 'status' => 'Aktif'],
            ['id' => 'm-02', 'title' => 'Teknik Warna Dasar', 'summary' => 'Pencampuran warna, proses fiksasi, dan hasil warna konsisten.', 'status' => 'Aktif'],
            ['id' => 'm-03', 'title' => 'Komposisi Motif Modern', 'summary' => 'Perancangan motif kontemporer dengan akar visual tradisional.', 'status' => 'Perlu Revisi'],
            ['id' => 'm-04', 'title' => 'Finishing dan Quality Control', 'summary' => 'Tahap akhir pengerjaan batik dan standar kualitas hasil karya.', 'status' => 'Aktif'],
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function getInstructorPendingWorks(): array
    {
        return [
            ['id' => 's-01', 'participant' => 'Anita Wijaya', 'module' => 'Teknik Canting Dasar', 'submitted_at' => '16 Mar 2026, 14:20', 'status' => 'Menunggu Penilaian'],
            ['id' => 's-03', 'participant' => 'Citra Kurnia', 'module' => 'Komposisi Motif Modern', 'submitted_at' => '15 Mar 2026, 16:40', 'status' => 'Menunggu Penilaian'],
            ['id' => 's-04', 'participant' => 'Eka Lestari', 'module' => 'Teknik Warna Dasar', 'submitted_at' => '15 Mar 2026, 09:30', 'status' => 'Menunggu Penilaian'],
            ['id' => 's-05', 'participant' => 'Deni Santoso', 'module' => 'Finishing dan Quality Control', 'submitted_at' => '14 Mar 2026, 18:05', 'status' => 'Menunggu Penilaian'],
        ];
    }

    /**
     * @return array<int, array<string, string|int>>
     */
    private function getInstructorModules(): array
    {
        return [
            ['id' => 'm-01', 'title' => 'Teknik Canting Dasar', 'category' => 'Dasar', 'lessons' => 8, 'participants' => 48, 'status' => 'Aktif', 'updated_at' => '17 Mar 2026'],
            ['id' => 'm-02', 'title' => 'Teknik Warna Dasar', 'category' => 'Praktik', 'lessons' => 10, 'participants' => 44, 'status' => 'Aktif', 'updated_at' => '15 Mar 2026'],
            ['id' => 'm-03', 'title' => 'Komposisi Motif Modern', 'category' => 'Lanjutan', 'lessons' => 6, 'participants' => 29, 'status' => 'Revisi', 'updated_at' => '12 Mar 2026'],
        ];
    }

    /**
     * @return array<int, array<string, string|int>>
     */
    private function getInstructorParticipants(): array
    {
        return [
            ['id' => 'p-01', 'name' => 'Anita Wijaya', 'batch' => 'Batch 3', 'progress' => 82, 'last_activity' => '2 jam lalu'],
            ['id' => 'p-02', 'name' => 'Bima Pradana', 'batch' => 'Batch 3', 'progress' => 71, 'last_activity' => '1 jam lalu'],
            ['id' => 'p-03', 'name' => 'Citra Kurnia', 'batch' => 'Batch 2', 'progress' => 91, 'last_activity' => '30 menit lalu'],
            ['id' => 'p-04', 'name' => 'Deni Santoso', 'batch' => 'Batch 2', 'progress' => 66, 'last_activity' => '3 jam lalu'],
        ];
    }

    /**
     * @return array<int, array<string, string|int>>
     */
    private function getInstructorThreads(): array
    {
        return [
            ['id' => 't-01', 'title' => 'Tips menjaga konsistensi malam', 'author' => 'Anita Wijaya', 'replies' => 12, 'last_message' => '20 menit lalu', 'excerpt' => 'Bagaimana cara menjaga aliran malam tetap stabil saat membuat garis panjang?'],
            ['id' => 't-02', 'title' => 'Rasio campuran warna untuk gradasi', 'author' => 'Bima Pradana', 'replies' => 7, 'last_message' => '1 jam lalu', 'excerpt' => 'Adakah rasio standar untuk membuat gradasi warna biru ke hijau?'],
            ['id' => 't-03', 'title' => 'Referensi motif kontemporer', 'author' => 'Citra Kurnia', 'replies' => 5, 'last_message' => 'Kemarin', 'excerpt' => 'Mohon rekomendasi referensi motif modern yang tetap mempertahankan unsur tradisional.'],
        ];
    }

    /**
     * @return array<int, array<string, string|int|null>>
     */
    private function getInstructorSubmissions(): array
    {
        return [
            ['id' => 's-01', 'participant' => 'Anita Wijaya', 'module' => 'Teknik Canting Dasar', 'submitted_at' => '16 Mar 2026, 14:20', 'status' => 'Menunggu', 'score' => null],
            ['id' => 's-02', 'participant' => 'Bima Pradana', 'module' => 'Teknik Warna Dasar', 'submitted_at' => '16 Mar 2026, 11:05', 'status' => 'Revisi', 'score' => 74],
            ['id' => 's-03', 'participant' => 'Citra Kurnia', 'module' => 'Komposisi Motif Modern', 'submitted_at' => '15 Mar 2026, 16:40', 'status' => 'Menunggu', 'score' => null],
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function getParticipantModules(): array
    {
        return [
            'modul-1' => [
                'title' => 'Modul 1 - Teknik Canting Dasar',
                'duration' => '72 Jam',
                'progress' => 79,
                'videoTitle' => 'Judul Video Materi',
                'description' => 'Materi ini membahas tahapan dasar menyiapkan alat dan bahan batik, mulai dari pemilihan kain, malam, canting, hingga setup area kerja yang aman dan efektif.',
                'taskTitle' => 'Teknik Tugas',
                'taskItems' => [
                    'Buatlah motif batik sederhana dengan acuan yang telah Anda pelajari pada materi atau video.',
                    'Kumpulkan sebelum deadline.',
                ],
                'deadline' => '24/10/2026',
                'materials' => [
                    [
                        'slug' => 'bab-1-persiapan-alat-bahan',
                        'title' => 'Bab 1 - Persiapan Alat dan Bahan Batik',
                        'summary' => 'Pengenalan alat utama batik, fungsi, dan cara menyiapkan area kerja yang aman.',
                        'thumbnailLabel' => 'Thumbnail Bab 1',
                    ],
                    [
                        'slug' => 'bab-2-pembuatan-pola',
                        'title' => 'Bab 2 - Pembuatan Pola Batik',
                        'summary' => 'Dasar membuat pola batik yang proporsional sebelum proses pencantingan.',
                        'thumbnailLabel' => 'Thumbnail Bab 2',
                    ],
                ],
                'discussionItems' => [
                    [
                        'name' => 'Adi - Peserta',
                        'message' => 'Apakah ada tips untuk menjaga ketebalan malam tetap konsisten?',
                    ],
                    [
                        'name' => 'Susanti - Pengajar',
                        'message' => 'Gunakan tekanan canting yang stabil dan lakukan latihan garis berulang.',
                    ],
                    [
                        'name' => 'Rina - Peserta',
                        'message' => 'Terima kasih, tipsnya sangat membantu.',
                    ],
                ],
            ],
            'modul-2' => [
                'title' => 'Modul 2 - Teknik Warna Dasar',
                'duration' => '120 Jam',
                'progress' => 79,
                'videoTitle' => 'Judul Video Materi',
                'description' => 'Materi ini berfokus pada teknik pewarnaan dasar, pemahaman komposisi warna, proses fiksasi, serta praktik menghasilkan warna yang konsisten pada kain batik.',
                'taskTitle' => 'Teknik Tugas',
                'taskItems' => [
                    'Lakukan percobaan 3 variasi warna pada motif berbeda.',
                    'Kumpulkan hasil dokumentasi dan catatan proses sebelum deadline.',
                ],
                'deadline' => '31/10/2026',
                'materials' => [
                    [
                        'slug' => 'bab-1-pengenalan-warna',
                        'title' => 'Bab 1 - Pengenalan Warna Dasar',
                        'summary' => 'Memahami teori warna primer, sekunder, dan pencampuran dasar pada kain batik.',
                        'thumbnailLabel' => 'Thumbnail Bab 1',
                    ],
                    [
                        'slug' => 'bab-2-teknik-fiksasi',
                        'title' => 'Bab 2 - Teknik Fiksasi Warna',
                        'summary' => 'Teknik mengunci warna agar hasil pewarnaan lebih tahan lama dan konsisten.',
                        'thumbnailLabel' => 'Thumbnail Bab 2',
                    ],
                ],
                'discussionItems' => [
                    [
                        'name' => 'Budi - Peserta',
                        'message' => 'Perbandingan campuran warna untuk gradasi halus berapa ya?',
                    ],
                    [
                        'name' => 'Mira - Pengajar',
                        'message' => 'Mulai dari rasio 1:3, lalu uji bertahap hingga mencapai gradasi yang diinginkan.',
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function getInstructorModulesList(): array
    {
        return [
            [
                'id' => 'm-01',
                'title' => 'Teknik Canting Dasar',
                'duration' => '72 Jam',
                'chapters' => 8,
                'participants' => 48,
                'status' => 'Aktif',
                'updated_at' => '17 Mar 2026',
                'cover' => null,
                'description' => 'Pelajari dasar-dasar teknik canting, termasuk penyiapan alat, bahan, dan penguasaan kontrol garis.',
            ],
            [
                'id' => 'm-02',
                'title' => 'Teknik Warna Dasar',
                'duration' => '120 Jam',
                'chapters' => 10,
                'participants' => 44,
                'status' => 'Aktif',
                'updated_at' => '15 Mar 2026',
                'cover' => null,
                'description' => 'Mendalami teknik pewarnaan batik, pencampuran warna, dan proses fiksasi untuk hasil yang konsisten.',
            ],
            [
                'id' => 'm-03',
                'title' => 'Komposisi Motif Modern',
                'duration' => '96 Jam',
                'chapters' => 6,
                'participants' => 29,
                'status' => 'Perlu Revisi',
                'updated_at' => '12 Mar 2026',
                'cover' => null,
                'description' => 'Eksplorasi desain motif kontemporer sambil mempertahankan nilai tradisional dan nuansa budaya.',
            ],
            [
                'id' => 'm-04',
                'title' => 'Finishing dan Quality Control',
                'duration' => '48 Jam',
                'chapters' => 5,
                'participants' => 22,
                'status' => 'Aktif',
                'updated_at' => '10 Mar 2026',
                'cover' => null,
                'description' => 'Tahap akhir produksi batik: finishing, packing, dan standar kontrol kualitas internasional.',
            ],
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function getInstructorModuleDetail(string $moduleId): ?array
    {
        $modules = $this->getInstructorModulesList();

        foreach ($modules as $module) {
            if ($module['id'] === $moduleId) {
                return [
                    ...$module,
                    'chapters' => $this->getModuleChapters($moduleId),
                ];
            }
        }

        return null;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function getModuleChapters(string $moduleId): array
    {
        $allChapters = [
            'm-01' => [
                [
                    'id' => 'ch-01',
                    'title' => 'Bab 1 - Persiapan Alat dan Bahan',
                    'description' => 'Pengenalan alat utama dan bahan batik serta cara penyiapannya.',
                    'content' => 'Pada bab ini, Anda akan mempelajari berbagai jenis alat canting (canting kompleks dan canting sederhana), bahan malam, kain, dan pewarna yang digunakan dalam batik modern.',
                    'images' => [],
                    'video' => null,
                    'assignment' => 'Bersiaplah dan dokumentasikan semua alat dan bahan yang ada di studio Anda.',
                    'assignment_deadline' => '30 Mar 2026',
                ],
                [
                    'id' => 'ch-02',
                    'title' => 'Bab 2 - Dasar Teknik Garis Canting',
                    'description' => 'Penguasaan dasar dalam memegang dan menggerakkan canting.',
                    'content' => 'Pelajari teknik memegang canting dengan benar, cara mengontrol aliran malam, dan latihan membuat berbagai jenis garis untuk meningkatkan presisi.',
                    'images' => [],
                    'video' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                    'assignment' => 'Praktik membuat 10 variasi garis canting pada kain uji coba.',
                    'assignment_deadline' => '5 Apr 2026',
                ],
                [
                    'id' => 'ch-03',
                    'title' => 'Bab 3 - Pembuatan Pola dan Design',
                    'description' => 'Merancang pola batik sebelum proses canting.',
                    'content' => 'Teknik membuat pola dengan pensil, cetak, atau metode transfer lainnya. Pahami prinsip komposisi dan keseimbangan dalam desain batik.',
                    'images' => [],
                    'video' => null,
                    'assignment' => 'Buatlah 3 pola desain batik tradisional di atas kain.',
                    'assignment_deadline' => '10 Apr 2026',
                ],
            ],
            'm-02' => [
                [
                    'id' => 'ch-01',
                    'title' => 'Bab 1 - Pengenalan Warna Dasar',
                    'description' => 'Teori warna dan pemahaman dasar kombinasi warna dalam batik.',
                    'content' => 'Pelajari teori warna primer, sekunder, tersier, serta psikologi warna dalam penerapannya pada batik tradisional dan modern.',
                    'images' => [],
                    'video' => null,
                    'assignment' => 'Membuat palet warna dengan 5 kombinasi warna berbeda.',
                    'assignment_deadline' => '12 Apr 2026',
                ],
                [
                    'id' => 'ch-02',
                    'title' => 'Bab 2 - Teknik Pencampuran Warna',
                    'description' => 'Cara mencampur pewarna untuk hasil yang optimal.',
                    'content' => 'Teknik pencampuran pewarna alami dan sintetis, perbandingan proporsi, dan faktor-faktor yang mempengaruhi hasil warna akhir.',
                    'images' => [],
                    'video' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                    'assignment' => 'Lakukan percobaan 5 gradasi warna dari gelap ke terang.',
                    'assignment_deadline' => '18 Apr 2026',
                ],
            ],
            'm-03' => [
                [
                    'id' => 'ch-01',
                    'title' => 'Bab 1 - Filosofi Motif Tradisional',
                    'description' => 'Memahami makna dan filosofi di balik motif batik tradisional.',
                    'content' => 'Eksplorasi motif klasik seperti Parang, Kawung, Semen, dan filosofi serta sejarah di baliknya untuk inspirasi desain modern.',
                    'images' => [],
                    'video' => null,
                    'assignment' => 'Riset dan catat 5 motif tradisional beserta filosofinya.',
                    'assignment_deadline' => '20 Apr 2026',
                ],
            ],
        ];

        return $allChapters[$moduleId] ?? [];
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('auth_user');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Anda berhasil logout.');
    }
}
