<?php

namespace App\Http\Controllers\Api;

use App\Models\DiscussionReply;
use App\Models\ForumDiscussion;
use App\Models\Module;
use App\Services\ForumDiscussionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ForumDiscussionApiController extends BaseApiController
{
    public function __construct(private readonly ForumDiscussionService $service)
    {
    }

    public function modules(): JsonResponse
    {
        return $this->successResponse('Daftar modul forum berhasil diambil.', $this->service->getForumModules());
    }

    public function index(Request $request): JsonResponse
    {
        $moduleSlug = (string) $request->query('module', '');
        $discussions = Schema::hasTable('forum_discussions')
            ? $this->service->getForumDiscussions($moduleSlug !== '' ? $moduleSlug : null)
            : collect();

        return $this->successResponse('Daftar diskusi berhasil diambil.', $discussions);
    }

    public function store(Request $request): JsonResponse
    {
        if (!Schema::hasTable('forum_discussions')) {
            return $this->errorResponse('Tabel forum belum tersedia.', null, 422);
        }

        $user = $this->authUser($request);

        $validated = $request->validate([
            'title' => ['required', 'string', 'min:5', 'max:255'],
            'theme' => ['required', 'string', 'min:3', 'max:255'],
            'content' => ['required', 'string', 'min:10', 'max:5000'],
            'module_id' => ['nullable', 'string', 'exists:modules,slug'],
        ]);

        $moduleId = null;
        if (!empty($validated['module_id'])) {
            $moduleId = Module::query()
                ->where('slug', (string) $validated['module_id'])
                ->value('id');
        }

        $discussion = ForumDiscussion::create([
            'module_id' => $moduleId,
            'theme' => $validated['theme'],
            'user_id' => $user['id'] ?? null,
            'user_name' => $user['name'] ?? 'Peserta',
            'user_email' => $user['email'] ?? null,
            'user_role' => $this->normalizeRole($user['role'] ?? null),
            'title' => $validated['title'],
            'content' => $validated['content'],
        ]);

        return $this->successResponse('Diskusi berhasil dibuat.', $discussion, 201);
    }

    public function update(Request $request, string $discussion): JsonResponse
    {
        $target = ForumDiscussion::find($discussion);

        if (! $target) {
            return $this->errorResponse('Diskusi tidak ditemukan.', null, 404);
        }

        $user = $this->authUser($request);
        $isInstructor = $this->normalizeRole($user['role'] ?? null) === 'pengajar';
        if (($target->user_email ?? null) !== ($user['email'] ?? null) && ! $isInstructor) {
            return $this->errorResponse('Anda tidak diizinkan mengubah diskusi ini.', null, 403);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'min:5', 'max:255'],
            'theme' => ['required', 'string', 'min:3', 'max:255'],
            'content' => ['required', 'string', 'min:10', 'max:5000'],
        ]);

        $target->update([
            'title' => $validated['title'],
            'theme' => $validated['theme'],
            'content' => $validated['content'],
        ]);

        return $this->successResponse('Diskusi berhasil diperbarui.', $target->fresh());
    }

    public function destroy(Request $request, string $discussion): JsonResponse
    {
        $target = ForumDiscussion::find($discussion);

        if (! $target) {
            return $this->errorResponse('Diskusi tidak ditemukan.', null, 404);
        }

        $user = $this->authUser($request);
        $isInstructor = $this->normalizeRole($user['role'] ?? null) === 'pengajar';

        if (($target->user_email ?? null) !== ($user['email'] ?? null) && ! $isInstructor) {
            return $this->errorResponse('Anda tidak diizinkan menghapus diskusi ini.', null, 403);
        }

        $target->delete();

        return $this->successResponse('Diskusi berhasil dihapus.');
    }

    public function togglePin(Request $request, string $discussion): JsonResponse
    {
        $user = $this->authUser($request);

        if ($this->normalizeRole($user['role'] ?? null) !== 'pengajar') {
            return $this->errorResponse('Hanya pengajar yang dapat menandai diskusi.', null, 403);
        }

        $target = ForumDiscussion::find($discussion);
        if (! $target) {
            return $this->errorResponse('Diskusi tidak ditemukan.', null, 404);
        }

        $target->update(['is_pinned' => ! $target->is_pinned]);

        return $this->successResponse('Status pin diskusi berhasil diperbarui.', $target->fresh());
    }

    public function toggleClose(Request $request, string $discussion): JsonResponse
    {
        $user = $this->authUser($request);

        if ($this->normalizeRole($user['role'] ?? null) !== 'pengajar') {
            return $this->errorResponse('Hanya pengajar yang dapat menutup diskusi.', null, 403);
        }

        $target = ForumDiscussion::find($discussion);
        if (! $target) {
            return $this->errorResponse('Diskusi tidak ditemukan.', null, 404);
        }

        $target->update(['is_closed' => ! $target->is_closed]);

        return $this->successResponse('Status diskusi berhasil diperbarui.', $target->fresh());
    }

    public function storeReply(Request $request, string $discussion): JsonResponse
    {
        $target = ForumDiscussion::find($discussion);
        if (! $target) {
            return $this->errorResponse('Diskusi tidak ditemukan.', null, 404);
        }

        if ($target->is_closed) {
            return $this->errorResponse('Diskusi sudah ditutup.', null, 422);
        }

        $user = $this->authUser($request);
        if (!empty($target->module_id) && $this->normalizeRole($user['role'] ?? null) !== 'pengajar') {
            return $this->errorResponse('Hanya pengajar yang dapat membalas diskusi modul.', null, 403);
        }

        $validated = $request->validate([
            'content' => ['required', 'string', 'min:3', 'max:5000'],
            'parent_id' => ['nullable', 'integer', 'exists:forum_discussion_replies,id'],
        ]);

        $parentId = $validated['parent_id'] ?? null;
        if ($parentId !== null) {
            $parent = DiscussionReply::where('id', $parentId)
                ->where('discussion_id', $target->id)
                ->first();

            if (! $parent) {
                return $this->errorResponse('Balasan induk tidak ditemukan.', null, 404);
            }
        }

        $reply = DiscussionReply::create([
            'discussion_id' => $target->id,
            'parent_id' => $parentId,
            'user_id' => $user['id'] ?? null,
            'user_name' => $user['name'] ?? 'Peserta',
            'user_email' => $user['email'] ?? null,
            'user_role' => $this->normalizeRole($user['role'] ?? null),
            'content' => $validated['content'],
        ]);

        return $this->successResponse('Balasan berhasil ditambahkan.', $reply, 201);
    }

    public function updateReply(Request $request, string $reply): JsonResponse
    {
        $target = DiscussionReply::find($reply);
        if (! $target) {
            return $this->errorResponse('Balasan tidak ditemukan.', null, 404);
        }

        $user = $this->authUser($request);
        $isInstructor = $this->normalizeRole($user['role'] ?? null) === 'pengajar';
        if (($target->user_email ?? null) !== ($user['email'] ?? null) && ! $isInstructor) {
            return $this->errorResponse('Anda tidak diizinkan mengubah balasan ini.', null, 403);
        }

        $validated = $request->validate([
            'content' => ['required', 'string', 'min:3', 'max:5000'],
        ]);

        $target->update(['content' => $validated['content']]);

        return $this->successResponse('Balasan berhasil diperbarui.', $target->fresh());
    }

    public function destroyReply(Request $request, string $reply): JsonResponse
    {
        $target = DiscussionReply::find($reply);
        if (! $target) {
            return $this->errorResponse('Balasan tidak ditemukan.', null, 404);
        }

        $user = $this->authUser($request);
        $isInstructor = $this->normalizeRole($user['role'] ?? null) === 'pengajar';
        if (($target->user_email ?? null) !== ($user['email'] ?? null) && ! $isInstructor) {
            return $this->errorResponse('Anda tidak diizinkan menghapus balasan ini.', null, 403);
        }

        $target->delete();

        return $this->successResponse('Balasan berhasil dihapus.');
    }

    private function authUser(Request $request): array
    {
        $user = $request->session()->get('auth_user', []);

        return is_array($user) ? $user : [];
    }

    private function normalizeRole(?string $role): string
    {
        $value = strtolower((string) $role);

        return in_array($value, ['instructor', 'pengajar', 'teacher'], true) ? 'pengajar' : 'peserta';
    }
}
