<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class AuthApiController extends BaseApiController
{
    private const WA_VALIDATION_SENDER_NUMBER = '081332650772';
    private const FORGOT_PASSWORD_CODE_TTL_MINUTES = 10;
    private const HARDCODED_ADMIN_USERNAME = 'admin';
    private const HARDCODED_ADMIN_PASSWORD = 'Admin@2026!';
    private const HARDCODED_ADMIN_NAME = 'Administrator';
    private const HARDCODED_ADMIN_EMAIL = 'admin@lmsbatik.local';

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $username = strtolower(trim((string) $credentials['username']));
        $password = (string) $credentials['password'];

        if (
            $username === self::HARDCODED_ADMIN_USERNAME
            && hash_equals(self::HARDCODED_ADMIN_PASSWORD, $password)
        ) {
            $request->session()->put('auth_user', [
                'id' => null,
                'name' => self::HARDCODED_ADMIN_NAME,
                'username' => self::HARDCODED_ADMIN_USERNAME,
                'email' => self::HARDCODED_ADMIN_EMAIL,
                'role' => 'manager',
                'is_hardcoded_admin' => true,
            ]);

            $request->session()->regenerate();

            return $this->successResponse('Login berhasil.', [
                'role' => 'manager',
                'redirect_url' => route('dashboard.index'),
                'force_password_change' => false,
            ]);
        }

        $dbUser = User::query()
            ->where('username', $username)
            ->orWhere('email', $username)
            ->first();

        if (! $dbUser || ! Hash::check($password, (string) $dbUser->password)) {
            return $this->errorResponse('Username atau password tidak valid.', null, 422);
        }

        if (Schema::hasColumn('users', 'current_password')) {
            $dbUser->current_password = $password;
            $dbUser->save();
        }

        Auth::login($dbUser);

        $sessionRole = $this->getSessionRoleFromDbRole((string) $dbUser->role);
        $normalizedStatus = $this->normalizeParticipantStatus((string) ($dbUser->status ?? 'active'));

        $request->session()->put('auth_user', [
            'id' => $dbUser->id,
            'name' => $dbUser->name,
            'username' => $dbUser->username ?: $dbUser->email,
            'email' => $dbUser->email,
            'role' => $sessionRole,
            'status' => $sessionRole === 'participant' ? $normalizedStatus : (string) ($dbUser->status ?? ''),
            'sidebar_role_label' => $sessionRole === 'participant' ? $this->buildParticipantRoleLabel($normalizedStatus) : null,
        ]);

        $request->session()->regenerate();

        $forcePasswordChange = (string) $dbUser->role === 'peserta' && ! (bool) $dbUser->password_changed;

        return $this->successResponse('Login berhasil.', [
            'role' => $sessionRole,
            'redirect_url' => $forcePasswordChange ? route('dashboard.participant.profile') : route('dashboard.index'),
            'force_password_change' => $forcePasswordChange,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::logout();

        $request->session()->forget('auth_user');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $this->successResponse('Logout berhasil.', [
            'redirect_url' => route('login'),
        ]);
    }

    public function requestForgotPasswordCode(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'forgot_username' => ['required', 'string', 'min:3', 'max:120'],
        ]);

        $username = strtolower(trim((string) $validated['forgot_username']));
        $user = User::query()
            ->where('role', 'peserta')
            ->where('username', $username)
            ->first();

        if (! $user) {
            return $this->errorResponse('Akun peserta tidak ditemukan.', null, 404);
        }

        $isGroupParticipant = ! empty($user->group_name);
        if ($isGroupParticipant && ! (bool) ($user->forgot_password_enabled ?? false)) {
            return $this->errorResponse('Fitur lupa password belum diaktifkan untuk akun kelompok ini. Hubungi admin.', null, 422);
        }

        $targetNumber = $this->resolveForgotPasswordWhatsappNumber($user);

        if ($targetNumber === '') {
            return $this->errorResponse('Nomor WhatsApp verifikasi belum tersedia. Untuk peserta kelompok, isi nomor pribadi di profil saat ganti password pertama.', null, 422);
        }

        $code = (string) random_int(100000, 999999);
        Cache::put(
            'forgot-password-code:' . $user->id,
            [
                'code' => $code,
                'expires_at' => now()->addMinutes(self::FORGOT_PASSWORD_CODE_TTL_MINUTES)->toDateTimeString(),
            ],
            now()->addMinutes(self::FORGOT_PASSWORD_CODE_TTL_MINUTES)
        );

        $message = "*Kode Verifikasi Reset Password LMS Batik*\n"
            . 'Username: ' . ($user->username ?? $user->email) . "\n"
            . 'Kode verifikasi: ' . $code . "\n"
            . 'Berlaku selama ' . self::FORGOT_PASSWORD_CODE_TTL_MINUTES . " menit.\n"
            . "Jangan bagikan kode ini kepada siapa pun.\n\n"
            . 'Pengirim (Admin): ' . self::WA_VALIDATION_SENDER_NUMBER;

        $waUrl = 'https://wa.me/' . $targetNumber . '?text=' . rawurlencode($message);

        return $this->successResponse('Kode verifikasi berhasil dibuat.', [
            'forgot_password_username' => $username,
            'forgot_password_wa_url' => $waUrl,
            'forgot_password_wa_target' => $this->maskPhoneNumber($targetNumber),
        ]);
    }

    public function resetForgotPassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'forgot_username' => ['required', 'string', 'min:3', 'max:120'],
            'verification_code' => ['required', 'digits:6'],
            'password' => ['required', 'string', 'min:6', 'max:40', 'confirmed'],
        ]);

        $username = strtolower(trim((string) $validated['forgot_username']));
        $user = User::query()
            ->where('role', 'peserta')
            ->where('username', $username)
            ->first();

        if (! $user) {
            return $this->errorResponse('Akun peserta tidak ditemukan.', null, 404);
        }

        $isGroupParticipant = ! empty($user->group_name);
        if ($isGroupParticipant && ! (bool) ($user->forgot_password_enabled ?? false)) {
            return $this->errorResponse('Reset password tidak diizinkan untuk akun kelompok ini.', null, 422);
        }

        $cachedCode = Cache::get('forgot-password-code:' . $user->id);
        $isValidCode = is_array($cachedCode)
            && isset($cachedCode['code'])
            && hash_equals((string) $cachedCode['code'], (string) $validated['verification_code']);

        if (! $isValidCode) {
            return $this->errorResponse('Kode verifikasi tidak valid atau sudah kedaluwarsa.', null, 422);
        }

        $user->password = Hash::make((string) $validated['password']);
        $user->password_changed = true;

        if (Schema::hasColumn('users', 'current_password')) {
            $user->current_password = (string) $validated['password'];
        }

        $user->save();

        Cache::forget('forgot-password-code:' . $user->id);

        return $this->successResponse('Password berhasil direset. Silakan login menggunakan password baru.');
    }

    private function getSessionRoleFromDbRole(string $role): string
    {
        return match ($role) {
            'pengajar' => 'instructor',
            'pengelola' => 'manager',
            default => 'participant',
        };
    }

    private function normalizeParticipantStatus(string $status): string
    {
        $normalized = strtolower(trim($status));

        return match ($normalized) {
            'aktif', 'active' => 'active',
            'lulus', 'graduated' => 'graduated',
            'nonaktif', 'non-active', 'non active' => 'non-active',
            default => 'active',
        };
    }

    private function buildParticipantRoleLabel(string $status): string
    {
        return match ($this->normalizeParticipantStatus($status)) {
            'active' => 'Participant - Active',
            'graduated' => 'Participant - Graduated (Alumni)',
            'non-active' => 'Participant - Non-active',
            default => 'Participant - Active',
        };
    }

    private function resolveForgotPasswordWhatsappNumber(User $user): string
    {
        $isGroupParticipant = ! empty($user->group_name);
        $candidatePhone = $isGroupParticipant
            ? (string) ($user->personal_phone ?? '')
            : (string) ($user->phone ?? '');

        return $this->normalizeWhatsappNumber($candidatePhone);
    }

    private function normalizeWhatsappNumber(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if ($digits === '') {
            return '';
        }

        if (str_starts_with($digits, '0')) {
            return '62' . substr($digits, 1);
        }

        if (str_starts_with($digits, '62')) {
            return $digits;
        }

        return $digits;
    }

    private function maskPhoneNumber(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if (strlen($digits) <= 6) {
            return $digits;
        }

        return substr($digits, 0, 3) . str_repeat('*', max(0, strlen($digits) - 6)) . substr($digits, -3);
    }
}
