<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class ManagerUserApiController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        if ($response = $this->ensureManagerRole($request)) {
            return $response;
        }

        $role = strtolower((string) $request->query('role', ''));
        $dbRole = $this->toDbRole($role);

        $query = User::query();
        if ($dbRole !== null) {
            $query->where('role', $dbRole);
        }

        $users = $query->orderBy('name')->get()->map(function (User $user): array {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $this->toApiRole((string) $user->role),
                'status' => $user->status,
                'phone' => $user->phone,
                'address' => $user->address,
                'education' => $user->education,
                'group_name' => $user->group_name,
                'forgot_password_enabled' => (bool) ($user->forgot_password_enabled ?? false),
                'password_changed' => (bool) ($user->password_changed ?? false),
            ];
        });

        return $this->successResponse('Daftar user berhasil diambil.', $users);
    }

    public function store(Request $request): JsonResponse
    {
        if ($response = $this->ensureManagerRole($request)) {
            return $response;
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:120'],
            'username' => ['required', 'string', 'min:3', 'max:120', 'unique:users,username'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'max:60'],
            'role' => ['required', 'string', 'in:participant,instructor,manager'],
            'status' => ['nullable', 'string', 'max:40'],
            'phone' => ['nullable', 'string', 'max:40'],
            'address' => ['nullable', 'string', 'max:255'],
            'education' => ['nullable', 'string', 'max:120'],
            'group_name' => ['nullable', 'string', 'max:150'],
            'forgot_password_enabled' => ['nullable', 'boolean'],
        ]);

        $createPayload = [
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => (string) $this->toDbRole((string) $validated['role']),
            'status' => $validated['status'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'education' => $validated['education'] ?? null,
            'group_name' => $validated['group_name'] ?? null,
        ];

        if (Schema::hasColumn('users', 'forgot_password_enabled')) {
            $createPayload['forgot_password_enabled'] = (bool) ($validated['forgot_password_enabled'] ?? false);
        }

        if (Schema::hasColumn('users', 'password_changed')) {
            $createPayload['password_changed'] = false;
        }

        if (Schema::hasColumn('users', 'current_password')) {
            $createPayload['current_password'] = $validated['password'];
        }

        $user = User::create($createPayload);

        return $this->successResponse('User berhasil ditambahkan.', $user, 201);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        if ($response = $this->ensureManagerRole($request)) {
            return $response;
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:120'],
            'username' => ['required', 'string', 'min:3', 'max:120', 'unique:users,username,' . $user->id],
            'email' => ['required', 'email', 'max:150', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:6', 'max:60'],
            'role' => ['required', 'string', 'in:participant,instructor,manager'],
            'status' => ['nullable', 'string', 'max:40'],
            'phone' => ['nullable', 'string', 'max:40'],
            'address' => ['nullable', 'string', 'max:255'],
            'education' => ['nullable', 'string', 'max:120'],
            'group_name' => ['nullable', 'string', 'max:150'],
            'forgot_password_enabled' => ['nullable', 'boolean'],
        ]);

        $updatePayload = [
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'role' => (string) $this->toDbRole((string) $validated['role']),
            'status' => $validated['status'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'education' => $validated['education'] ?? null,
            'group_name' => $validated['group_name'] ?? null,
        ];

        if (Schema::hasColumn('users', 'forgot_password_enabled')) {
            $updatePayload['forgot_password_enabled'] = (bool) ($validated['forgot_password_enabled'] ?? false);
        }

        if (! empty($validated['password'])) {
            $updatePayload['password'] = Hash::make((string) $validated['password']);

            if (Schema::hasColumn('users', 'current_password')) {
                $updatePayload['current_password'] = (string) $validated['password'];
            }

            if (Schema::hasColumn('users', 'password_changed')) {
                $updatePayload['password_changed'] = false;
            }
        }

        $user->update($updatePayload);

        return $this->successResponse('User berhasil diperbarui.', $user->fresh());
    }

    public function destroy(Request $request, User $user): JsonResponse
    {
        if ($response = $this->ensureManagerRole($request)) {
            return $response;
        }

        $user->delete();

        return $this->successResponse('User berhasil dihapus.');
    }

    public function updateStatus(Request $request, User $user): JsonResponse
    {
        if ($response = $this->ensureManagerRole($request)) {
            return $response;
        }

        $validated = $request->validate([
            'status' => ['required', 'string', 'max:40'],
            'forgot_password_enabled' => ['nullable', 'boolean'],
        ]);

        $user->status = $validated['status'];
        if (Schema::hasColumn('users', 'forgot_password_enabled') && array_key_exists('forgot_password_enabled', $validated)) {
            $user->forgot_password_enabled = (bool) $validated['forgot_password_enabled'];
        }
        $user->save();

        return $this->successResponse('Status user berhasil diperbarui.', $user->fresh());
    }

    private function ensureManagerRole(Request $request): ?JsonResponse
    {
        $authUser = $request->session()->get('auth_user', []);

        if (($authUser['role'] ?? null) !== 'manager') {
            return $this->errorResponse('Akses ditolak. Hanya pengelola yang diizinkan.', null, 403);
        }

        return null;
    }

    private function toDbRole(string $apiRole): ?string
    {
        return match ($apiRole) {
            'participant' => 'peserta',
            'instructor' => 'pengajar',
            'manager' => 'pengelola',
            default => null,
        };
    }

    private function toApiRole(string $dbRole): string
    {
        return match ($dbRole) {
            'peserta' => 'participant',
            'pengajar' => 'instructor',
            'pengelola' => 'manager',
            default => 'participant',
        };
    }
}
