<?php

namespace App\Http\Controllers;

use App\Models\DiscussionReply;
use App\Models\ForumDiscussion;
use App\Models\Discussion;
use App\Models\ModuleDiscussionReply;
use App\Models\Module;
use App\Services\ForumDiscussionService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Schema;

class ForumController extends Controller
{
    private function normalizeRole(?string $role): string
    {
        $value = strtolower((string) $role);

        return in_array($value, ['instructor', 'pengajar', 'teacher'], true) ? 'pengajar' : 'peserta';
    }

    public function index(Request $request, ?string $moduleSlug = null): View|RedirectResponse
    {
        $user = $request->session()->get('auth_user', []);
        $service = app(ForumDiscussionService::class);
        $selectedModuleSlug = (string) $request->query('module', $moduleSlug ?? '');

        if (empty($user['email'])) {
            return redirect()->route('login')
                ->withErrors(['forum' => 'Anda harus login untuk mengakses forum.']);
        }

        $discussions = Schema::hasTable('forum_discussions')
            ? $service->getForumDiscussions($selectedModuleSlug !== '' ? $selectedModuleSlug : null)
            : collect();

        return view('forum.index', [
            'discussions' => $discussions,
            'user' => $user,
            'modules' => $service->getForumModules(),
            'forumThemes' => $service->getForumThemes(),
            'selectedModuleSlug' => $selectedModuleSlug,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->session()->get('auth_user', []);

        if (empty($user['email'])) {
            return redirect()->route('login')
                ->withErrors(['forum' => 'Anda harus login untuk membuat diskusi.']);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'min:5', 'max:255'],
            'content' => ['required', 'string', 'min:10', 'max:5000'],
            'theme_option' => ['nullable', 'string', 'max:255'],
            'new_theme' => ['nullable', 'string', 'min:3', 'max:255'],
            'module_slug' => ['nullable', 'string', 'exists:modules,slug'],
        ]);

        $themeOption = trim((string) ($validated['theme_option'] ?? ''));
        $newTheme = trim((string) ($validated['new_theme'] ?? ''));
        $theme = $themeOption === 'new' ? $newTheme : $themeOption;

        if ($theme === '') {
            return back()
                ->withErrors(['theme_option' => 'Silakan pilih tema yang ada atau buat tema baru.'])
                ->withInput();
        }

        $moduleId = null;
        $moduleSlug = trim((string) ($validated['module_slug'] ?? ''));
        if ($moduleSlug !== '') {
            $module = Module::query()->where('slug', $moduleSlug)->first();
            if ($module) {
                $moduleId = (int) $module->id;
                if ($themeOption !== 'new' && $newTheme === '') {
                    $theme = (string) $module->title;
                }
            }
        }

        if (!Schema::hasTable('forum_discussions')) {
            return back()
                ->withErrors(['forum' => 'Tabel forum belum tersedia. Jalankan migrasi terlebih dahulu.']);
        }

        ForumDiscussion::create([
            'module_id' => $moduleId,
            'theme' => $theme,
            'user_id' => $user['id'] ?? null,
            'user_name' => $user['name'] ?? 'Peserta',
            'user_email' => $user['email'],
            'user_role' => $this->normalizeRole($user['role'] ?? null),
            'title' => $validated['title'],
            'content' => $validated['content'],
        ]);

        return back()->with('modal_success', 'Diskusi berhasil dibuat!');
    }

    public function update(Request $request, string $discussion): RedirectResponse
    {
        $user = $request->session()->get('auth_user', []);

        if (empty($user['email'])) {
            return redirect()->route('login')
                ->withErrors(['forum' => 'Anda harus login untuk mengedit diskusi.']);
        }

        if (!Schema::hasTable('forum_discussions')) {
            return back()
                ->withErrors(['forum' => 'Tabel forum belum tersedia.']);
        }

        $target = ForumDiscussion::find($discussion);

        if (!$target) {
            return back()
                ->withErrors(['forum' => 'Diskusi tidak ditemukan.']);
        }

        if (($target->user_email ?? null) !== ($user['email'] ?? null)) {
            return back()
                ->withErrors(['forum' => 'Anda tidak diizinkan mengedit diskusi ini.']);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'min:5', 'max:255'],
            'theme' => ['required', 'string', 'min:3', 'max:255'],
            'content' => ['required', 'string', 'min:10', 'max:5000'],
        ]);

        $target->update([
            'theme' => trim((string) $validated['theme']),
            'title' => $validated['title'],
            'content' => $validated['content'],
        ]);

        return back()->with('modal_success', 'Diskusi berhasil diperbarui!');
    }

    public function delete(Request $request, string $discussion): RedirectResponse
    {
        $user = $request->session()->get('auth_user', []);

        if (empty($user['email'])) {
            return redirect()->route('login');
        }

        if (Schema::hasTable('forum_discussions')) {
            $target = ForumDiscussion::find($discussion);

            if ($target && ($target->user_email ?? null) === ($user['email'] ?? null)) {
                $target->delete();
                return back()->with('modal_success', 'Diskusi berhasil dihapus!');
            }
        }

        return back()->withErrors(['forum' => 'Tidak dapat menghapus diskusi.']);
    }

    public function togglePin(Request $request, string $discussion): RedirectResponse
    {
        $user = $request->session()->get('auth_user', []);

        if ($this->normalizeRole($user['role'] ?? null) !== 'pengajar') {
            return back()->withErrors(['forum' => 'Hanya pengajar yang dapat menandai diskusi.']);
        }

        if (!Schema::hasTable('forum_discussions')) {
            return back()->withErrors(['forum' => 'Tabel forum belum tersedia.']);
        }

        $target = ForumDiscussion::find($discussion);

        if ($target) {
            $target->update(['is_pinned' => !$target->is_pinned]);
            $message = $target->is_pinned ? 'Diskusi ditandai penting!' : 'Diskusi dihapus dari penting.';
            return back()->with('modal_success', $message);
        }

        return back()->withErrors(['forum' => 'Diskusi tidak ditemukan.']);
    }

    public function toggleClose(Request $request, string $discussion): RedirectResponse
    {
        $user = $request->session()->get('auth_user', []);

        if ($this->normalizeRole($user['role'] ?? null) !== 'pengajar') {
            return back()->withErrors(['forum' => 'Hanya pengajar yang dapat menutup diskusi.']);
        }

        if (!Schema::hasTable('forum_discussions')) {
            return back()->withErrors(['forum' => 'Tabel forum belum tersedia.']);
        }

        $target = ForumDiscussion::find($discussion);

        if ($target) {
            $target->update(['is_closed' => !$target->is_closed]);
            $message = $target->is_closed ? 'Diskusi ditutup!' : 'Diskusi dibuka kembali.';
            return back()->with('modal_success', $message);
        }

        return back()->withErrors(['forum' => 'Diskusi tidak ditemukan.']);
    }

    public function storeReply(Request $request, string $discussion): RedirectResponse
    {
        $user = $request->session()->get('auth_user', []);

        if (empty($user['email'])) {
            return redirect()->route('login');
        }

        if (!Schema::hasTable('forum_discussion_replies')) {
            return back()->withErrors(['forum' => 'Tabel balasan forum belum tersedia.']);
        }

        $validated = $request->validate([
            'content' => ['required', 'string', 'min:3', 'max:5000'],
            'parent_id' => ['nullable', 'integer', 'exists:forum_discussion_replies,id'],
        ]);

        if (!Schema::hasTable('forum_discussions')) {
            return back()->withErrors(['forum' => 'Tabel forum belum tersedia.']);
        }

        $target = ForumDiscussion::find($discussion);

        if (!$target) {
            return back()->withErrors(['forum' => 'Diskusi tidak ditemukan.']);
        }

        if ($target->is_closed) {
            return back()->withErrors(['forum' => 'Diskusi ini sudah ditutup.']);
        }

        if (!empty($target->module_id) && $this->normalizeRole($user['role'] ?? null) !== 'pengajar') {
            return back()->withErrors(['forum' => 'Hanya pengajar yang dapat membalas diskusi modul.']);
        }

        $parentId = $validated['parent_id'] ?? null;

        if ($parentId !== null) {
            $parentReply = DiscussionReply::where('id', $parentId)
                ->where('discussion_id', $target->id)
                ->first();

            if (!$parentReply) {
                return back()->withErrors(['forum' => 'Balasan induk tidak ditemukan.']);
            }
        }

        DiscussionReply::create([
            'discussion_id' => $target->id,
            'parent_id' => $parentId,
            'user_id' => $user['id'] ?? null,
            'user_name' => $user['name'] ?? 'Peserta',
            'user_email' => $user['email'] ?? null,
            'user_role' => $this->normalizeRole($user['role'] ?? null),
            'content' => $validated['content'],
        ]);

        return back()->with('modal_success', 'Balasan berhasil ditambahkan!');
    }

    public function updateReply(Request $request, string $reply): RedirectResponse
    {
        $user = $request->session()->get('auth_user', []);

        if (empty($user['email'])) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'content' => ['required', 'string', 'min:3', 'max:5000'],
        ]);

        if (!Schema::hasTable('forum_discussion_replies')) {
            return back()->withErrors(['forum' => 'Tabel balasan forum belum tersedia.']);
        }

        $target = DiscussionReply::find($reply);

        if (!$target) {
            return back()->withErrors(['forum' => 'Balasan tidak ditemukan.']);
        }

        if (($target->user_email ?? null) !== ($user['email'] ?? null)) {
            return back()->withErrors(['forum' => 'Anda tidak diizinkan mengubah balasan ini.']);
        }

        $target->update(['content' => $validated['content']]);

        return back()->with('modal_success', 'Balasan berhasil diperbarui!');
    }

    public function deleteReply(Request $request, string $reply): RedirectResponse
    {
        $user = $request->session()->get('auth_user', []);

        if (empty($user['email'])) {
            return redirect()->route('login');
        }

        if (!Schema::hasTable('forum_discussion_replies')) {
            return back()->withErrors(['forum' => 'Tabel balasan forum belum tersedia.']);
        }

        $target = DiscussionReply::find($reply);

        if (!$target) {
            return back()->withErrors(['forum' => 'Balasan tidak ditemukan.']);
        }

        if (($target->user_email ?? null) !== ($user['email'] ?? null)) {
            return back()->withErrors(['forum' => 'Anda tidak diizinkan menghapus balasan ini.']);
        }

        $target->delete();

        return back()->with('modal_success', 'Balasan berhasil dihapus!');
    }

    public function storeModuleDiscussion(Request $request): RedirectResponse
    {
        $user = $request->session()->get('auth_user', []);

        if (empty($user['email'])) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'module_slug' => ['required', 'string', 'exists:modules,slug'],
            'title' => ['required', 'string', 'min:5', 'max:255'],
            'content' => ['required', 'string', 'min:10', 'max:5000'],
        ]);

        $module = Module::query()->where('slug', $validated['module_slug'])->first();

        if (!$module) {
            return back()->withErrors(['forum' => 'Modul tidak ditemukan.']);
        }

        Discussion::create([
            'module_id' => $module->id,
            'user_id' => $user['id'] ?? null,
            'user_name' => $user['name'] ?? 'Peserta',
            'user_email' => $user['email'],
            'user_role' => $this->normalizeRole($user['role'] ?? null),
            'title' => $validated['title'],
            'content' => $validated['content'],
        ]);

        return back()->with('modal_success', 'Diskusi modul berhasil dibuat!');
    }

    public function storeModuleReply(Request $request, string $discussion): RedirectResponse
    {
        $user = $request->session()->get('auth_user', []);

        if (empty($user['email'])) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'content' => ['required', 'string', 'min:3', 'max:5000'],
        ]);

        $target = Discussion::find($discussion);

        if (!$target) {
            return back()->withErrors(['forum' => 'Diskusi modul tidak ditemukan.']);
        }

        $isInstructor = $this->normalizeRole($user['role'] ?? null) === 'pengajar';
        $isOwner = ($target->user_email ?? null) === ($user['email'] ?? null);

        if (!$isInstructor && !$isOwner) {
            return back()->withErrors(['forum' => 'Anda tidak diizinkan membalas diskusi ini.']);
        }

        ModuleDiscussionReply::create([
            'discussion_id' => $target->id,
            'user_id' => $user['id'] ?? null,
            'user_name' => $user['name'] ?? 'Peserta',
            'user_email' => $user['email'],
            'user_role' => $this->normalizeRole($user['role'] ?? null),
            'content' => $validated['content'],
        ]);

        return back()->with('modal_success', 'Balasan diskusi modul berhasil ditambahkan!');
    }

    public function updateModuleDiscussion(Request $request, string $discussion): RedirectResponse
    {
        $user = $request->session()->get('auth_user', []);

        if (empty($user['email'])) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'min:5', 'max:255'],
            'content' => ['required', 'string', 'min:10', 'max:5000'],
        ]);

        $target = Discussion::find($discussion);

        if (!$target) {
            return back()->withErrors(['forum' => 'Diskusi modul tidak ditemukan.']);
        }

        $isOwner = ($target->user_email ?? null) === ($user['email'] ?? null);

        if (!$isOwner) {
            return back()->withErrors(['forum' => 'Anda tidak diizinkan mengedit diskusi ini.']);
        }

        $target->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
        ]);

        return back()->with('modal_success', 'Diskusi modul berhasil diperbarui!');
    }

    public function deleteModuleDiscussion(Request $request, string $discussion): RedirectResponse
    {
        $user = $request->session()->get('auth_user', []);

        if (empty($user['email'])) {
            return redirect()->route('login');
        }

        $target = Discussion::find($discussion);

        if (!$target) {
            return back()->withErrors(['forum' => 'Diskusi modul tidak ditemukan.']);
        }

        $isOwner = ($target->user_email ?? null) === ($user['email'] ?? null);

        if (!$isOwner) {
            return back()->withErrors(['forum' => 'Anda tidak diizinkan menghapus diskusi ini.']);
        }

        $target->delete();

        return back()->with('modal_success', 'Diskusi modul berhasil dihapus!');
    }

    public function updateModuleReply(Request $request, string $reply): RedirectResponse
    {
        $user = $request->session()->get('auth_user', []);

        if (empty($user['email'])) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'content' => ['required', 'string', 'min:3', 'max:5000'],
        ]);

        $target = ModuleDiscussionReply::find($reply);

        if (!$target) {
            return back()->withErrors(['forum' => 'Balasan diskusi modul tidak ditemukan.']);
        }

        $isOwner = ($target->user_email ?? null) === ($user['email'] ?? null);

        if (!$isOwner) {
            return back()->withErrors(['forum' => 'Anda tidak diizinkan mengedit balasan ini.']);
        }

        $target->update([
            'content' => $validated['content'],
        ]);

        return back()->with('modal_success', 'Balasan diskusi modul berhasil diperbarui!');
    }

    public function deleteModuleReply(Request $request, string $reply): RedirectResponse
    {
        $user = $request->session()->get('auth_user', []);

        if (empty($user['email'])) {
            return redirect()->route('login');
        }

        $target = ModuleDiscussionReply::find($reply);

        if (!$target) {
            return back()->withErrors(['forum' => 'Balasan diskusi modul tidak ditemukan.']);
        }

        $isOwner = ($target->user_email ?? null) === ($user['email'] ?? null);

        if (!$isOwner) {
            return back()->withErrors(['forum' => 'Anda tidak diizinkan menghapus balasan ini.']);
        }

        $target->delete();

        return back()->with('modal_success', 'Balasan diskusi modul berhasil dihapus!');
    }
}
