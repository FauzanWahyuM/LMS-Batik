<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Services\ModuleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InstructorModuleController extends Controller
{
    public function index(Request $request, ModuleService $service): View|RedirectResponse
    {
        $guard = $this->ensureInstructorRole($request);

        if ($guard) {
            return $guard;
        }

        $search = (string) $request->query('search', '');
        $modules = $service->list($search)->map(function (Module $module) {
            return $this->formatModule($module);
        });

        return view('dashboard.instructor.modules-list', [
            'user' => $request->session()->get('auth_user'),
            'dashboard' => $this->getInstructorDashboardConfig('modules'),
            'modules' => $modules,
        ]);
    }

    public function create(Request $request): View|RedirectResponse
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

    public function store(Request $request, ModuleService $service): RedirectResponse
    {
        $guard = $this->ensureInstructorRole($request);

        if ($guard) {
            return $guard;
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'duration' => ['required', 'string', 'min:3', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'cover' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('cover')) {
            $validated['cover'] = $service->uploadCover($request->file('cover'));
        }

        $service->create($validated);

        return redirect()
            ->route('dashboard.instructor.modules')
            ->with('status', 'Modul baru berhasil dibuat.');
    }

    public function show(Request $request, Module $module): View|RedirectResponse
    {
        $guard = $this->ensureInstructorRole($request);

        if ($guard) {
            return $guard;
        }

        return view('dashboard.instructor.modules-detail', [
            'user' => $request->session()->get('auth_user'),
            'dashboard' => $this->getInstructorDashboardConfig('modules'),
            'module' => $this->formatModule($module),
        ]);
    }

    public function edit(Request $request, Module $module): View|RedirectResponse
    {
        $guard = $this->ensureInstructorRole($request);

        if ($guard) {
            return $guard;
        }

        return view('dashboard.instructor.modules-edit', [
            'user' => $request->session()->get('auth_user'),
            'dashboard' => $this->getInstructorDashboardConfig('modules'),
            'module' => $this->formatModule($module),
        ]);
    }

    public function update(Request $request, Module $module, ModuleService $service): RedirectResponse
    {
        $guard = $this->ensureInstructorRole($request);

        if ($guard) {
            return $guard;
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'duration' => ['required', 'string', 'min:3', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'cover' => ['nullable', 'image', 'max:2048'],
            'delete_cover' => ['nullable', 'boolean'],
            'chapters' => ['nullable', 'array'],
            'chapters.*.title' => ['nullable', 'string', 'max:255'],
            'chapters.*.description' => ['nullable', 'string', 'max:500'],
            'chapters.*.content' => ['nullable', 'string'],
            'chapters.*.video' => ['nullable', 'url'],
            'chapters.*.assignment' => ['nullable', 'string'],
            'chapters.*.assignment_deadline' => ['nullable', 'string'],
        ]);

        if ($request->hasFile('cover')) {
            $validated['cover'] = $service->uploadCover($request->file('cover'));
        }

        $service->update($module, $validated);

        return redirect()
            ->route('dashboard.instructor.modules.detail', ['module' => $module->id])
            ->with('status', 'Modul berhasil diperbarui.');
    }

    public function destroy(Request $request, Module $module, ModuleService $service): RedirectResponse
    {
        $guard = $this->ensureInstructorRole($request);

        if ($guard) {
            return $guard;
        }

        $service->delete($module);

        return redirect()
            ->route('dashboard.instructor.modules')
            ->with('status', 'Modul berhasil dihapus.');
    }

    private function formatModule(Module $module): array
    {
        $chapters = $module->chapters ?? [];
        // Normalize video URLs in chapters
        foreach ($chapters as &$chapter) {
            if (isset($chapter['video']) && $chapter['video']) {
                $chapter['video'] = $this->normalizeVideoUrl($chapter['video']);
            }
        }

        return array_merge($module->toArray(), [
            'cover' => $module->cover ? asset('storage/' . ltrim($module->cover, '/')) : null,
            'chapters' => $chapters,
            'participants' => $module->participants_count ?? 0,
            'status' => $module->status ?? 'Draft',
        ]);
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

    private function ensureInstructorRole(Request $request): ?RedirectResponse
    {
        $user = $request->session()->get('auth_user');

        if (($user['role'] ?? null) !== 'instructor') {
            return redirect()->route('dashboard.index');
        }

        return null;
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
            'profile' => [
                'title' => 'Profil Pengguna - Pengajar',
                'subtitle' => 'Lihat dan perbarui data profil pengajar.',
            ],
        ];

        $selected = $config[$activePage] ?? $config['home'];

        return array_merge($selected, [
            'headerGradient' => 'from-[#0f4c81] to-[#1f6d8f]',
            'showNotification' => true,
            'roleBadgeClasses' => 'bg-sky-100 text-sky-700',
            'activeMenuClasses' => 'bg-sky-100 text-sky-900',
            'profileUrl' => route('dashboard.instructor.profile'),
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
        ]);
    }
}
