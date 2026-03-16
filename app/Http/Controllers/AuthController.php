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

        $dashboardConfig = [
            'instructor' => [
                'view' => 'dashboard.instructor.index',
                'title' => 'Dashboard Pengajar',
                'subtitle' => 'Kelola kelas, evaluasi tugas, dan aktivitas pembelajaran.',
                'headerGradient' => 'from-emerald-700 to-teal-600',
                'roleBadgeClasses' => 'bg-emerald-100 text-emerald-700',
                'activeMenuClasses' => 'bg-emerald-100 text-emerald-800',
                'menuItems' => [
                    ['label' => 'Beranda', 'icon' => '[H]', 'url' => route('dashboard.index'), 'active' => true],
                    ['label' => 'Daftar Kelas', 'icon' => '[K]', 'url' => '#', 'active' => false],
                    ['label' => 'Penilaian', 'icon' => '[P]', 'url' => '#', 'active' => false],
                    ['label' => 'Materi', 'icon' => '[M]', 'url' => '#', 'active' => false],
                ],
            ],
            'manager' => [
                'view' => 'dashboard.manager.index',
                'title' => 'Dashboard Pengelola',
                'subtitle' => 'Lihat ringkasan operasional dan performa keseluruhan program.',
                'headerGradient' => 'from-slate-800 to-slate-600',
                'roleBadgeClasses' => 'bg-slate-200 text-slate-700',
                'activeMenuClasses' => 'bg-slate-200 text-slate-800',
                'menuItems' => [
                    ['label' => 'Beranda', 'icon' => '[H]', 'url' => route('dashboard.index'), 'active' => true],
                    ['label' => 'Manajemen User', 'icon' => '[U]', 'url' => '#', 'active' => false],
                    ['label' => 'Laporan', 'icon' => '[L]', 'url' => '#', 'active' => false],
                    ['label' => 'Pengaturan', 'icon' => '[P]', 'url' => '#', 'active' => false],
                ],
            ],
        ];

        $dashboard = $dashboardConfig[$role] ?? $dashboardConfig['participant'];

        return view($dashboard['view'], [
            'user' => $user,
            'dashboard' => $dashboard,
        ]);
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

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('auth_user');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Anda berhasil logout.');
    }
}
