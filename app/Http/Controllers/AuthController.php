<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Achievement;
use App\Models\Artwork;
use App\Models\Facility;
use App\Models\Module;
use App\Models\Partner;
use App\Models\ParticipantAssignment;
use App\Models\ParticipantProgress;
use App\Models\Program;
use App\Models\RegistrationGroup;
use App\Models\RegistrationIndividual;
use App\Models\Testimonial;
use App\Models\User;
use App\Services\ForumDiscussionService;
use App\Services\ParticipantModuleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AuthController extends Controller
{
    private const WA_VALIDATION_SENDER_NUMBER = '081332650772';
    private const FORGOT_PASSWORD_CODE_TTL_MINUTES = 10;
    private const HARDCODED_ADMIN_USERNAME = 'admin';
    private const HARDCODED_ADMIN_PASSWORD = 'Admin@2026!';
    private const HARDCODED_ADMIN_NAME = 'Administrator';
    private const HARDCODED_ADMIN_EMAIL = 'admin@lmsbatik.local';

    /**
     * @return array<string, string>
     */
    private function getManagerProfileData(Request $request): array
    {
        $authUser = $request->session()->get('auth_user', []);
        $isHardcodedAdmin = (bool) ($authUser['is_hardcoded_admin'] ?? false);
        $defaults = [
            'photo' => '',
            'full_name' => (string) ($authUser['name'] ?? ($isHardcodedAdmin ? self::HARDCODED_ADMIN_NAME : '')),
            'username' => (string) ($authUser['username'] ?? ($isHardcodedAdmin ? self::HARDCODED_ADMIN_USERNAME : '')),
            'password' => $isHardcodedAdmin ? self::HARDCODED_ADMIN_PASSWORD : '',
            'email' => (string) ($authUser['email'] ?? ($isHardcodedAdmin ? self::HARDCODED_ADMIN_EMAIL : '')),
            'phone' => '081234567890',
            'address' => 'Kantor LPK Kama Praja Madiun',
            'role_label' => 'Admin',
        ];

        $sessionData = $request->session()->get('profile_manager_data', []);

        if (!is_array($sessionData)) {
            return $defaults;
        }

        return array_merge($defaults, $sessionData);
    }

    /**
     * @return array<string, string>
     */
    private function getInstructorProfileData(Request $request): array
    {
        $authUser = $request->session()->get('auth_user', []);
        $authenticatedUserId = (int) ($authUser['id'] ?? Auth::id() ?? 0);
        $defaults = [
            'photo' => '',
            'full_name' => (string) ($authUser['name'] ?? ''),
            'username' => (string) ($authUser['username'] ?? ''),
            'password' => '',
            'email' => (string) ($authUser['email'] ?? ''),
            'phone' => '081298765432',
            'address' => 'Jl. Batik Madiun No. 21',
            'role_label' => 'Pengajar',
        ];

        $sessionData = $request->session()->get('profile_instructor_data', []);
        $profile = is_array($sessionData) ? array_merge($defaults, $sessionData) : $defaults;

        $dbUser = null;
        if ($authenticatedUserId > 0) {
            $dbUser = User::query()
                ->where('id', $authenticatedUserId)
                ->whereIn('role', ['pengajar', 'instructor'])
                ->first();
        } elseif (!empty($authUser['username'])) {
            $dbUser = User::query()
                ->where('username', (string) $authUser['username'])
                ->whereIn('role', ['pengajar', 'instructor'])
                ->first();
        } elseif (!empty($authUser['email'])) {
            $emailMatches = User::query()
                ->where('email', (string) $authUser['email'])
                ->whereIn('role', ['pengajar', 'instructor'])
                ->get();

            if ($emailMatches->count() === 1) {
                $dbUser = $emailMatches->first();
            }
        }

        if ($dbUser) {
            $storedPassword = '';
            if (Schema::hasColumn('users', 'current_password')) {
                $storedPassword = (string) ($dbUser->current_password ?? '');
            } elseif (!empty($profile['password'])) {
                $storedPassword = (string) $profile['password'];
            }

            return array_merge($profile, [
                'photo' => (string) ($profile['photo'] ?? ''),
                'full_name' => $dbUser->name,
                'username' => (string) $dbUser->username,
                'password' => $storedPassword,
                'email' => $dbUser->email,
                'phone' => (string) ($dbUser->phone ?? ''),
                'address' => (string) ($dbUser->address ?? ''),
                'role_label' => 'Pengajar',
            ]);
        }

        return $profile;
    }

    /**
     * @return array<string, string>
     */
    private function getParticipantProfileData(Request $request): array
    {
        $authUser = $request->session()->get('auth_user', []);
        $authenticatedUserId = (int) ($authUser['id'] ?? Auth::id() ?? 0);
        $defaults = [
            'photo' => '',
            'participant_type' => 'individual',
            'full_name' => (string) ($authUser['name'] ?? 'Peserta'),
            'username' => (string) ($authUser['username'] ?? ''),
            'password' => '',
            'email' => (string) ($authUser['email'] ?? ''),
            'phone' => '081300112233',
            'personal_phone' => '',
            'address' => 'Jl. Karya Batik No. 8',
            'motivation' => 'Ingin memperdalam teknik membatik untuk usaha mandiri.',
            'group_name' => '',
            'pic_name' => '',
            'role_label' => 'Peserta Individu',
        ];

        $sessionData = $request->session()->get('profile_participant_data', []);
        $profile = is_array($sessionData) && count($sessionData) > 0
            ? array_merge($defaults, $sessionData)
            : $defaults;

        $dbUser = null;

        if ($authenticatedUserId > 0) {
            $dbUser = User::query()
                ->where('id', $authenticatedUserId)
                ->whereIn('role', ['peserta', 'participant'])
                ->first();
        } elseif (!empty($authUser['username'])) {
            $dbUser = User::query()
                ->where('username', (string) $authUser['username'])
                ->whereIn('role', ['peserta', 'participant'])
                ->first();
        } elseif (!empty($authUser['email'])) {
            $emailMatches = User::query()
                ->where('email', (string) $authUser['email'])
                ->whereIn('role', ['peserta', 'participant'])
                ->get();

            if ($emailMatches->count() === 1) {
                $dbUser = $emailMatches->first();
            }
        }

        if ($dbUser) {
            $participantType = !empty($dbUser->group_name) ? 'group' : 'individual';
            $storedPassword = '';

            if (Schema::hasColumn('users', 'current_password')) {
                $storedPassword = (string) ($dbUser->current_password ?? '');
            } elseif (!empty($profile['password'])) {
                $storedPassword = (string) $profile['password'];
            }

            $groupName = $participantType === 'group' ? (string) $dbUser->group_name : '';
            $picName = '';
            $picEmail = (string) $dbUser->email;

            if ($participantType === 'group' && Schema::hasTable('registration_groups')) {
                $registrationGroup = RegistrationGroup::query()
                    ->where('nama_lembaga', $groupName)
                    ->orderByDesc('created_at')
                    ->first();

                if ($registrationGroup) {
                    $picName = (string) $registrationGroup->nama_pic;
                    $picEmail = (string) ($registrationGroup->email_pic ?? $dbUser->email);
                }
            }

            return array_merge($profile, [
                'participant_type' => $participantType,
                'full_name' => $dbUser->name,
                'username' => $dbUser->username,
                'password' => $storedPassword,
                'email' => $participantType === 'group' ? $picEmail : $dbUser->email,
                'phone' => $dbUser->phone ?? '',
                'personal_phone' => $participantType === 'group' ? ((string) ($dbUser->personal_phone ?? '')) : '',
                'address' => $dbUser->address ?? '',
                'motivation' => $participantType === 'individual'
                    ? ($dbUser->motivation ?? $defaults['motivation'])
                    : '',
                'group_name' => $groupName,
                'institution_name' => $groupName,
                'pic_name' => $picName,
                'pic_email' => $picEmail,
                'role_label' => $participantType === 'group' ? 'Peserta Kelompok' : 'Peserta Individu',
            ]);
        }

        return $profile;
    }

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
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $username = strtolower(trim($credentials['username']));

        if (
            $username === self::HARDCODED_ADMIN_USERNAME
            && hash_equals(self::HARDCODED_ADMIN_PASSWORD, (string) $credentials['password'])
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

            return redirect()->route('dashboard.index');
        }

        $dbUser = User::where('username', $username)
            ->orWhere('email', $username)
            ->first();

        if ($dbUser && Hash::check($credentials['password'], $dbUser->password)) {
            if (Schema::hasColumn('users', 'current_password')) {
                $dbUser->current_password = (string) $credentials['password'];
                $dbUser->save();
            }

            // Use Laravel Auth for database users
            Auth::login($dbUser);

            $sessionRole = $this->getSessionRoleFromDbRole($dbUser->role);
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

            // Check if participant needs to change password
            if ($dbUser->role === 'peserta' && !$dbUser->password_changed) {
                return redirect()->route('dashboard.participant.profile')
                    ->with('force_password_change', true);
            }

            return redirect()->route('dashboard.index');
        }

        return back()
            ->withErrors(['username' => 'Username atau password tidak valid.'])
            ->withInput($request->except('password'));
    }

    public function requestForgotPasswordCode(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'forgot_username' => ['required', 'string', 'min:3', 'max:120'],
        ]);

        $username = strtolower(trim((string) $validated['forgot_username']));
        $user = User::query()
            ->where('role', 'peserta')
            ->where('username', $username)
            ->first();

        if (!$user) {
            return redirect()
                ->route('login')
                ->withErrors([
                    'forgot_password' => 'Akun peserta tidak ditemukan.',
                ])
                ->withInput(['forgot_username' => $username]);
        }

        $isGroupParticipant = !empty($user->group_name);
        if ($isGroupParticipant && !(bool) ($user->forgot_password_enabled ?? false)) {
            return redirect()
                ->route('login')
                ->withErrors([
                    'forgot_password' => 'Fitur lupa password belum diaktifkan untuk akun kelompok ini. Hubungi admin.',
                ])
                ->withInput(['forgot_username' => $username]);
        }

        $targetNumber = $this->resolveForgotPasswordWhatsappNumber($user);

        if ($targetNumber === '') {
            return redirect()
                ->route('login')
                ->withErrors([
                    'forgot_password' => 'Nomor WhatsApp verifikasi belum tersedia. Untuk peserta kelompok, isi nomor pribadi di profil saat ganti password pertama.',
                ])
                ->withInput(['forgot_username' => $username]);
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
            . 'Jangan bagikan kode ini kepada siapa pun.\n\n'
            . 'Pengirim (Admin): ' . self::WA_VALIDATION_SENDER_NUMBER;

        $waUrl = 'https://wa.me/' . $targetNumber . '?text=' . rawurlencode($message);

        return redirect()
            ->route('login')
            ->with('status', 'Kode verifikasi berhasil dibuat. Klik tombol WhatsApp untuk mengirim kode.')
            ->with('forgot_password_username', $username)
            ->with('forgot_password_wa_url', $waUrl)
            ->with('forgot_password_wa_target', $this->maskPhoneNumber($targetNumber));
    }

    public function resetForgotPassword(Request $request): RedirectResponse
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

        if (!$user) {
            return redirect()
                ->route('login')
            ->withErrors(['forgot_password' => 'Akun peserta tidak ditemukan.'])
                ->withInput(['forgot_username' => $username]);
        }

        $isGroupParticipant = !empty($user->group_name);
        if ($isGroupParticipant && !(bool) ($user->forgot_password_enabled ?? false)) {
            return redirect()
            ->route('login')
            ->withErrors(['forgot_password' => 'Reset password tidak diizinkan untuk akun kelompok ini.'])
            ->withInput(['forgot_username' => $username]);
        }

        $cachedCode = Cache::get('forgot-password-code:' . $user->id);
        $isValidCode = is_array($cachedCode)
            && isset($cachedCode['code'])
            && hash_equals((string) $cachedCode['code'], (string) $validated['verification_code']);

        if (!$isValidCode) {
            return redirect()
                ->route('login')
                ->withErrors(['forgot_password' => 'Kode verifikasi tidak valid atau sudah kedaluwarsa.'])
                ->withInput(['forgot_username' => $username]);
        }

        $user->password = Hash::make((string) $validated['password']);
        $user->password_changed = true;
        if (Schema::hasColumn('users', 'current_password')) {
            $user->current_password = (string) $validated['password'];
        }
        $user->save();

        Cache::forget('forgot-password-code:' . $user->id);

        return redirect()
            ->route('login')
            ->with('status', 'Password berhasil direset. Silakan login menggunakan password baru.');
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

        $registrationStats = $this->getManagerRegistrationStatistics();

        return view('dashboard.manager.index', [
            'user' => $request->session()->get('auth_user'),
            'dashboard' => $this->getManagerDashboardConfig('home'),
            'stats' => $this->getManagerStats(),
            'activities' => $this->getManagerActivities(),
            'alumniAchievements' => $this->getManagerAlumniAchievements(),
            'registrationStats' => $registrationStats,
        ]);
    }

    public function managerIndividualParticipants(Request $request): View|RedirectResponse
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        $pendingParticipants = $this->getManagerPendingIndividualValidations();
        $pendingPerPage = 5;
        $pendingCurrentPage = max(1, (int) $request->query('pending_page', 1));
        $pendingPageItems = array_slice($pendingParticipants, ($pendingCurrentPage - 1) * $pendingPerPage, $pendingPerPage);
        $pendingParticipantsPaginator = new LengthAwarePaginator(
            $pendingPageItems,
            count($pendingParticipants),
            $pendingPerPage,
            $pendingCurrentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
                'pageName' => 'pending_page',
            ]
        );
        $managedParticipants = $this->getManagerIndividualParticipants();

        return view('dashboard.manager.participants-individual', [
            'user' => $request->session()->get('auth_user'),
            'dashboard' => $this->getManagerDashboardConfig('participants-individual'),
            'participants' => $managedParticipants,
            'pendingParticipantsPaginator' => $pendingParticipantsPaginator,
            'generatedCredential' => $request->session()->get('manager_generated_credential'),
        ]);
    }

    public function managerIndividualParticipantsGenerateCredential(Request $request, string $participant): RedirectResponse
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        $registrationId = (int) str_replace('individual-', '', $participant);
        $registration = RegistrationIndividual::where('id', $registrationId)
            ->where('status', 'pending')
            ->first();

        if (!$registration) {
            return redirect()
                ->route('dashboard.manager.participants.individual')
                ->withErrors(['credential' => 'Data peserta untuk validasi tidak ditemukan atau sudah divalidasi.']);
        }

        $baseUsername = strtolower(Str::slug($registration->nama_lengkap, '')) ?: 'peserta';
        $baseUsername = substr($baseUsername, 0, 10);
        $username = $baseUsername;
        $attempt = 0;

        while (User::where('username', $username)->exists()) {
            $attempt++;
            $username = substr($baseUsername, 0, max(3, 10 - strlen((string) $attempt))) . rand(10, 99) . $attempt;
            if ($attempt > 50) {
                $username = $baseUsername . Str::random(4);
                break;
            }
        }

        $password = Str::upper(Str::random(2)) . rand(10, 99) . Str::lower(Str::random(3)) . '!';

        $userData = [
            'name' => $registration->nama_lengkap,
            'username' => $username,
            'email' => $registration->email,
            'password' => Hash::make($password),
            'role' => 'peserta',
            'phone' => $registration->no_handphone,
            'address' => $registration->alamat,
            'education' => $registration->pendidikan_terakhir,
            'motivation' => $registration->motivasi,
            'status' => 'active',
            'password_changed' => false,
        ];

        if (Schema::hasColumn('users', 'personal_phone')) {
            $userData['personal_phone'] = null;
        }

        if (Schema::hasColumn('users', 'forgot_password_enabled')) {
            // Individual participants can use forgot-password with their own registered phone number.
            $userData['forgot_password_enabled'] = true;
        }

        if (Schema::hasColumn('users', 'current_password')) {
            $userData['current_password'] = $password;
        }

        User::create($userData);

        $registration->update(['status' => 'approved']);

        $request->session()->put('manager_generated_credential', [
            'participant_id' => 'individual-' . $registration->id,
            'participant_name' => $registration->nama_lengkap,
            'participant_whatsapp' => $registration->no_handphone,
            'username' => $username,
            'password' => $password,
        ]);

        return redirect()
            ->route('dashboard.manager.participants.individual')
            ->with('status', 'Peserta berhasil divalidasi dan akun dibuat. Silakan kirim kredensial via WhatsApp.');
    }

    public function managerIndividualParticipantsSendCredential(Request $request, string $participant): RedirectResponse
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        $request->validate([
            'username' => ['required', 'string', 'min:4', 'max:30'],
            'password' => ['required', 'string', 'min:6', 'max:30'],
        ]);

        $generatedCredential = $request->session()->get('manager_generated_credential');

        if (!$generatedCredential || (($generatedCredential['participant_id'] ?? '') !== $participant)) {
            return redirect()
                ->route('dashboard.manager.participants.individual')
                ->withErrors(['credential' => 'Silakan generate kredensial terlebih dahulu sebelum mengirim via WhatsApp.']);
        }

        $targetNumber = $this->normalizeWhatsappNumber((string) ($generatedCredential['participant_whatsapp'] ?? ''));
        if ($targetNumber === '') {
            return redirect()
                ->route('dashboard.manager.participants.individual')
                ->withErrors(['credential' => 'Nomor WhatsApp peserta tidak valid atau tidak tersedia.']);
        }

        $sentParticipantIds = $request->session()->get('manager_sent_individual_ids', []);
        if (!in_array($participant, $sentParticipantIds, true)) {
            $sentParticipantIds[] = $participant;
        }

        $request->session()->put('manager_sent_individual_ids', $sentParticipantIds);
        $request->session()->forget('manager_generated_credential');

        $message = "*Hasil Validasi Pendaftaran LMS Batik*\n"
            . "Status validasi: Disetujui\n"
            . "Nama peserta: {$generatedCredential['participant_name']}\n"
            . "Username: {$generatedCredential['username']}\n"
            . "Password awal: {$generatedCredential['password']}\n"
            . "Silakan login dan ganti password pada login pertama.\n\n"
            . "Pengirim (Admin): " . self::WA_VALIDATION_SENDER_NUMBER;

        $waUrl = 'https://wa.me/' . $targetNumber . '?text=' . rawurlencode($message);

        return redirect()->away($waUrl);
    }

    public function managerIndividualParticipantsUpdate(Request $request, string $participant): RedirectResponse
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(['active', 'graduated', 'non-active', 'Aktif', 'Lulus', 'Nonaktif'])],
        ]);

        $status = $this->normalizeParticipantStatus($validated['status']);

        $query = User::query();
        if (is_numeric($participant)) {
            $query->where('id', (int) $participant);
        } else {
            $query->where('username', $participant)
                ->orWhere('email', $participant);
        }

        $user = $query->where('role', 'peserta')->first();

        if (!$user) {
            return redirect()
                ->route('dashboard.manager.participants.individual')
                ->withErrors(['status' => 'Peserta tidak ditemukan untuk diperbarui.']);
        }

        $user->status = $status;
        $user->forgot_password_enabled = $request->boolean('forgot_password_enabled');
        $user->save();

        return redirect()
            ->route('dashboard.manager.participants.individual')
            ->with('status', 'Status peserta individu ' . $user->name . ' berhasil diperbarui menjadi ' . $this->buildParticipantRoleLabel($status) . '.');
    }

    public function managerGroupParticipants(Request $request): View|RedirectResponse
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        $sentGroupIds = $request->session()->get('manager_sent_group_ids', []);
        $pendingGroups = $this->getManagerPendingGroupValidations();
        $pendingGroups = array_values(array_filter($pendingGroups, function (array $group) use ($sentGroupIds): bool {
            return !in_array($group['id'], $sentGroupIds, true);
        }));
        $pendingPerPage = 5;
        $pendingCurrentPage = max(1, (int) $request->query('pending_page', 1));
        $pendingPageItems = array_slice($pendingGroups, ($pendingCurrentPage - 1) * $pendingPerPage, $pendingPerPage);
        $pendingGroupsPaginator = new LengthAwarePaginator(
            $pendingPageItems,
            count($pendingGroups),
            $pendingPerPage,
            $pendingCurrentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
                'pageName' => 'pending_page',
            ]
        );

        $managedGroups = $this->getManagerGroupParticipants();
        $perPage = 6;
        $currentPage = max(1, (int) $request->query('page', 1));
        $groupPageItems = array_slice($managedGroups, ($currentPage - 1) * $perPage, $perPage);
        $groupPaginator = new LengthAwarePaginator(
            $groupPageItems,
            count($managedGroups),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return view('dashboard.manager.participants-group', [
            'user' => $request->session()->get('auth_user'),
            'dashboard' => $this->getManagerDashboardConfig('participants-group'),
            'groups' => $managedGroups,
            'groupPaginator' => $groupPaginator,
            'pendingGroupsPaginator' => $pendingGroupsPaginator,
            'generatedGroupCredential' => $request->session()->get('manager_generated_group_credential'),
            'groupExportMeta' => $request->session()->get('manager_group_last_export'),
        ]);
    }

    public function managerGroupParticipantsGenerateCredential(Request $request, string $group): RedirectResponse
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        $selectedGroup = collect($this->getManagerPendingGroupValidations())->firstWhere('id', $group);

        if (!$selectedGroup) {
            return redirect()
                ->route('dashboard.manager.participants.group')
                ->withErrors(['group_credential' => 'Data kelompok untuk validasi tidak ditemukan.']);
        }

        $credentials = [];
        $registrationId = (int) str_replace('group-', '', $group);
        $prefix = 'pesertalmb';

        for ($index = 1; $index <= (int) $selectedGroup['members']; $index++) {
            $username = $prefix . $registrationId . str_pad((string) $index, 2, '0', STR_PAD_LEFT) . rand(1, 9);
            while (User::where('username', $username)->exists()) {
                $username = $prefix . $registrationId . str_pad((string) $index, 2, '0', STR_PAD_LEFT) . rand(1, 9);
            }
            $password = Str::upper(Str::random(2)) . rand(10, 99) . Str::lower(Str::random(2)) . '!';

            // Create user account
            $groupUserData = [
                'name' => $selectedGroup['group_name'] . ' - Anggota ' . $index,
                'username' => $username,
                'email' => 'group' . $selectedGroup['id'] . 'member' . $index . '@lmsbatik.test',
                'password' => Hash::make($password),
                'role' => 'peserta',
                'phone' => $selectedGroup['pic_phone'],
                'address' => $selectedGroup['pic_address'],
                'education' => '',
                'motivation' => '',
                'group_name' => $selectedGroup['group_name'],
                'status' => 'active',
                'password_changed' => false,
            ];

            if (Schema::hasColumn('users', 'personal_phone')) {
                $groupUserData['personal_phone'] = null;
            }

            if (Schema::hasColumn('users', 'forgot_password_enabled')) {
                $groupUserData['forgot_password_enabled'] = false;
            }

            if (Schema::hasColumn('users', 'current_password')) {
                $groupUserData['current_password'] = $password;
            }

            User::create($groupUserData);

            $credentials[] = [
                'member_no' => $index,
                'username' => $username,
                'password' => $password,
            ];
        }

        // Update registration status to approved
        $registration = RegistrationGroup::find($registrationId);
        if ($registration) {
            $registration->update(['status' => 'approved']);
        }

        $sentGroupIds = $request->session()->get('manager_sent_group_ids', []);
        if (!in_array($selectedGroup['id'], $sentGroupIds, true)) {
            $sentGroupIds[] = $selectedGroup['id'];
        }
        $request->session()->put('manager_sent_group_ids', $sentGroupIds);

        $request->session()->put('manager_generated_group_credential', [
            'group_id' => $selectedGroup['id'],
            'group_name' => $selectedGroup['group_name'],
            'pic_name' => $selectedGroup['pic_name'],
            'pic_whatsapp' => $selectedGroup['pic_phone'],
            'pic_email' => $selectedGroup['pic_email'],
            'credentials' => $credentials,
        ]);

        $csvContent = $this->buildGroupCredentialExportCsv($selectedGroup['pic_name'], $credentials);
        Storage::disk('public')->put('exports/group-credentials-' . $selectedGroup['id'] . '.csv', $csvContent);

        $credentialHistory = $request->session()->get('manager_group_credentials_by_group', []);
        $credentialHistory[$selectedGroup['id']] = $credentials;
        $request->session()->put('manager_group_credentials_by_group', $credentialHistory);

        $message = $selectedGroup['members'] > 20
            ? 'If you register more than 20 participants, credentials will be sent manually for efficiency. Please wait 1–5 minutes.'
            : 'Kredensial anggota kelompok berhasil digenerate. Lanjutkan download Excel dari halaman ini dan kirim ke PIC via WhatsApp.';

        return redirect()
            ->route('dashboard.manager.participants.group')
            ->with('status', $message);
    }

    public function managerGroupParticipantsSendCredential(Request $request, string $group): RedirectResponse
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        $generated = $request->session()->get('manager_generated_group_credential');

        if (!$generated || (($generated['group_id'] ?? '') !== $group)) {
            return redirect()
                ->route('dashboard.manager.participants.group')
                ->withErrors(['group_credential' => 'Silakan generate kredensial kelompok terlebih dahulu.']);
        }

        $registrationId = (int) str_replace('group-', '', $group);
        $registration = RegistrationGroup::query()->find($registrationId);
        $targetNumber = $this->normalizeWhatsappNumber((string) ($registration?->no_handphone_pic ?? ''));

        if ($targetNumber === '') {
            return redirect()
                ->route('dashboard.manager.participants.group')
                ->withErrors(['group_credential' => 'Nomor WhatsApp PIC tidak tersedia di database.']);
        }

        $credentialLines = array_map(function (array $credential): string {
            return '- ' . $credential['username'] . ' / ' . $credential['password'];
        }, $generated['credentials']);

        $message = "*Hasil Validasi Pendaftaran Kelompok LMS Batik*\n"
            . "Status validasi: Disetujui\n"
            . "Lembaga: {$generated['group_name']}\n"
            . "PIC: {$generated['pic_name']}\n"
            . "Kredensial peserta:\n" . implode("\n", $credentialLines) . "\n\n"
            . "Pengirim (Admin): " . self::WA_VALIDATION_SENDER_NUMBER;

        $waUrl = 'https://wa.me/' . $targetNumber . '?text=' . rawurlencode($message);

        $request->session()->forget('manager_generated_group_credential');

        return redirect()->away($waUrl);
    }

    public function managerGroupParticipantsDownloadCredentialExport(Request $request, string $group)
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        $registrationId = (int) str_replace('group-', '', $group);
        $registration = RegistrationGroup::query()->find($registrationId);

        if (!$registration) {
            return redirect()
                ->route('dashboard.manager.participants.group')
                ->withErrors(['group_credential' => 'Data kelompok tidak ditemukan.']);
        }

        $groupUsers = User::query()
            ->where('role', 'peserta')
            ->where('group_name', $registration->nama_lembaga)
            ->orderBy('username')
            ->get()
            ->all();

        $filename = 'group-data-' . $group . '.csv';
        $csvContent = $this->buildGroupDataExportCsv($registration, $groupUsers);

        return response()->streamDownload(function () use ($csvContent): void {
            echo $csvContent;
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function managerGroupParticipantsUpdate(Request $request, string $group): RedirectResponse
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(['active', 'graduated', 'non-active', 'Aktif', 'Lulus', 'Nonaktif'])],
            'forgot_password_enabled' => ['nullable', 'boolean'],
        ]);

        $status = $this->normalizeParticipantStatus($validated['status']);
        $registrationId = str_starts_with($group, 'group-') ? (int) str_replace('group-', '', $group) : (int) $group;

        $registration = RegistrationGroup::query()->find($registrationId);
        if (!$registration) {
            return redirect()
                ->route('dashboard.manager.participants.group')
                ->withErrors(['status' => 'Data lembaga tidak ditemukan untuk diperbarui.']);
        }

        $affectedUsers = User::query()
            ->where('role', 'peserta')
            ->where('group_name', $registration->nama_lembaga)
            ->get();

        if ($affectedUsers->isEmpty()) {
            return redirect()
                ->route('dashboard.manager.participants.group')
                ->withErrors(['status' => 'Belum ada user yang berafiliasi dengan lembaga ini untuk diperbarui.']);
        }

        foreach ($affectedUsers as $user) {
            $user->status = $status;
            $user->save();
        }

        return redirect()
            ->route('dashboard.manager.participants.group')
            ->with('status', 'Status peserta kelompok untuk lembaga ' . $registration->nama_lembaga . ' berhasil diperbarui menjadi ' . $this->buildParticipantRoleLabel($status) . '.');
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
            'instructors' => $this->getManagerManagedInstructors($request),
        ]);
    }

    public function managerInstructorsStore(Request $request): RedirectResponse
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        $payload = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:120'],
            'username' => ['nullable', 'string', 'min:4', 'max:30', Rule::unique('users', 'username')],
            'password' => ['nullable', 'string', 'min:6', 'max:50'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'string', 'min:8', 'max:20'],
            'address' => ['required', 'string', 'min:5', 'max:255'],
            'education' => ['required', 'string', 'min:2', 'max:100'],
            'salary' => ['required', 'integer', 'min:0'],
            'certificate' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ]);

        $username = trim((string) ($payload['username'] ?? ''));
        $password = trim((string) ($payload['password'] ?? ''));

        if ($username === '') {
            do {
                $username = substr(strtolower(Str::slug($payload['name'], '')), 0, 10) . rand(10, 99);
            } while (User::where('username', $username)->exists());
        }

        if ($password === '') {
            $password = Str::upper(Str::random(2)) . rand(10, 99) . Str::lower(Str::random(3)) . '!';
        }

        $certificateName = null;
        if ($request->hasFile('certificate')) {
            $certificateName = (string) $request->file('certificate')->getClientOriginalName();
        }

        User::create([
            'name' => $payload['name'],
            'username' => $username,
            'email' => $payload['email'],
            'password' => $password,
            'role' => 'pengajar',
            'phone' => $payload['phone'],
            'address' => $payload['address'],
            'education' => $payload['education'],
            'salary' => (int) $payload['salary'],
            'certificate' => $certificateName,
            'status' => 'Aktif',
        ]);

        $passwordCache = $request->session()->get('manager_instructor_passwords', []);
        $passwordCache[$username] = $password;
        $request->session()->put('manager_instructor_passwords', $passwordCache);

        return redirect()
            ->route('dashboard.manager.instructors')
            ->with('status', 'Pengajar baru berhasil ditambahkan dan tampil di daftar.');
    }

    public function managerInstructorsUpdate(Request $request, string $instructor): RedirectResponse
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        $request->validate([
            'status' => ['required', 'string', 'in:Aktif,Nonaktif'],
        ]);

        $dbUser = User::where('username', $instructor)->first();
        if ($dbUser) {
            $dbUser->status = (string) $request->input('status');
            $dbUser->save();

            return redirect()
                ->route('dashboard.manager.instructors')
                ->with('status', 'Status pengajar ' . strtoupper($instructor) . ' berhasil diperbarui.');
        }

        return redirect()
            ->route('dashboard.manager.instructors')
            ->withErrors(['instructors' => 'Data pengajar tidak ditemukan untuk diperbarui.']);
    }

    public function managerInstructorsEdit(Request $request, string $instructor): RedirectResponse
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        $dbUser = User::where('username', $instructor)->first();
        $usernameRules = ['required', 'string', 'min:4', 'max:30'];

        if ($dbUser) {
            $usernameRules[] = Rule::unique('users', 'username')->ignore($dbUser->id);
        }

        $payload = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:120'],
            'username' => $usernameRules,
            'password' => ['nullable', 'string', 'min:6', 'max:50'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'string', 'min:8', 'max:20'],
            'address' => ['required', 'string', 'min:5', 'max:255'],
            'education' => ['required', 'string', 'min:2', 'max:100'],
            'salary' => ['required', 'integer', 'min:0'],
            'certificate' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'status' => ['required', 'string', 'in:Aktif,Nonaktif'],
        ]);

        $certificateName = null;
        if ($request->hasFile('certificate')) {
            $certificateName = (string) $request->file('certificate')->getClientOriginalName();
        }

        if ($dbUser) {
            $originalUsername = $dbUser->username;
            $dbUser->name = $payload['name'];
            $dbUser->username = $payload['username'];
            $dbUser->email = $payload['email'];
            $dbUser->phone = $payload['phone'];
            $dbUser->address = $payload['address'];
            $dbUser->education = $payload['education'];
            $dbUser->salary = (int) $payload['salary'];
            if ($certificateName !== null) {
                $dbUser->certificate = $certificateName;
            }
            if ($payload['password'] !== '') {
                $dbUser->password = $payload['password'];
            }
            $dbUser->status = $payload['status'];
            $dbUser->save();

            $passwordCache = $request->session()->get('manager_instructor_passwords', []);
            if ($payload['password'] !== '') {
                $passwordCache[$dbUser->username] = $payload['password'];
            } elseif (isset($passwordCache[$originalUsername]) && $originalUsername !== $dbUser->username) {
                $passwordCache[$dbUser->username] = $passwordCache[$originalUsername];
                unset($passwordCache[$originalUsername]);
            }
            $request->session()->put('manager_instructor_passwords', $passwordCache);

            return redirect()
                ->route('dashboard.manager.instructors')
                ->with('status', 'Data pengajar berhasil diperbarui.');
        }

        $instructors = $this->getManagerManagedInstructors($request);
        $found = false;
        $updated = array_map(function (array $item) use ($instructor, $payload, $certificateName, &$found): array {
            if ($item['id'] === $instructor) {
                $found = true;
                $item['name'] = $payload['name'];
                $item['username'] = $payload['username'];
                if ($payload['password'] !== '') {
                    $item['password'] = $payload['password'];
                }
                $item['email'] = $payload['email'];
                $item['phone'] = $payload['phone'];
                $item['address'] = $payload['address'];
                $item['education'] = $payload['education'];
                $item['salary'] = (int) $payload['salary'];
                if ($certificateName !== null) {
                    $item['certificate'] = $certificateName;
                }
                $item['status'] = $payload['status'];
            }

            return $item;
        }, $instructors);

        if (!$found) {
            return redirect()
                ->route('dashboard.manager.instructors')
                ->withErrors(['instructors' => 'Data pengajar tidak ditemukan untuk diedit.']);
        }

        return redirect()
            ->route('dashboard.manager.instructors')
            ->with('status', 'Data pengajar berhasil diperbarui.');
    }

    public function managerInstructorsDelete(Request $request, string $instructor): RedirectResponse
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        $dbUser = User::where('username', $instructor)->first();
        if ($dbUser) {
            $dbUser->delete();

            $passwordCache = $request->session()->get('manager_instructor_passwords', []);
            if (isset($passwordCache[$instructor])) {
                unset($passwordCache[$instructor]);
                $request->session()->put('manager_instructor_passwords', $passwordCache);
            }

            return redirect()
                ->route('dashboard.manager.instructors')
                ->with('status', 'Data pengajar berhasil dihapus.');
        }

        return redirect()
            ->route('dashboard.manager.instructors')
            ->withErrors(['instructors' => 'Data pengajar tidak ditemukan untuk dihapus.']);
    }

    public function managerPrograms(Request $request): View|RedirectResponse
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        $search = trim((string) $request->query('search', ''));

        $programs = Schema::hasTable('programs')
            ? Program::query()
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($builder) use ($search) {
                        $builder->where('name', 'like', '%' . $search . '%')
                            ->orWhere('description', 'like', '%' . $search . '%')
                            ->orWhere('benefits', 'like', '%' . $search . '%');
                    });
                })
                ->latest()
                ->get()
            : collect();

        return view('dashboard.manager.programs', [
            'user' => $request->session()->get('auth_user'),
            'dashboard' => $this->getManagerDashboardConfig('programs'),
            'programs' => $programs,
            'search' => (string) $request->query('search', ''),
        ]);
    }

    public function managerProgramsStore(Request $request): RedirectResponse
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        if (!Schema::hasTable('programs')) {
            return redirect()->route('dashboard.manager.programs')
                ->withErrors(['programs' => 'Tabel program belum tersedia. Jalankan migrasi terlebih dahulu.']);
        }

        $payload = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:120'],
            'duration_hours' => ['required', 'numeric', 'min:0.5', 'max:10000'],
            'fee_amount' => ['required', 'numeric', 'min:0'],
            'fee_unit' => ['required', 'string', 'min:2', 'max:50'],
            'description' => ['required', 'string', 'min:10', 'max:2000'],
            'benefits' => ['required', 'array', 'min:1'],
            'benefits.*' => ['required', 'string', 'min:3', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        Program::create([
            'name' => $payload['name'],
            'duration_hours' => $payload['duration_hours'],
            'fee_amount' => $payload['fee_amount'],
            'fee_unit' => $payload['fee_unit'],
            'description' => $payload['description'],
            'benefits' => array_filter($payload['benefits'], fn ($b) => !empty(trim($b))),
            'is_active' => (bool) ($payload['is_active'] ?? false),
        ]);

        return redirect()
            ->route('dashboard.manager.programs')
            ->with('status', 'Program baru berhasil ditambahkan.');
    }

    public function managerProgramsEdit(Request $request, string $program): RedirectResponse
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        if (!Schema::hasTable('programs')) {
            return redirect()->route('dashboard.manager.programs')
                ->withErrors(['programs' => 'Tabel program belum tersedia. Jalankan migrasi terlebih dahulu.']);
        }

        $payload = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:120'],
            'duration_hours' => ['required', 'numeric', 'min:0.5', 'max:10000'],
            'fee_amount' => ['required', 'numeric', 'min:0'],
            'fee_unit' => ['required', 'string', 'min:2', 'max:50'],
            'description' => ['required', 'string', 'min:10', 'max:2000'],
            'benefits' => ['required', 'array', 'min:1'],
            'benefits.*' => ['required', 'string', 'min:3', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $target = Program::find($program);

        if (!$target) {
            return redirect()
                ->route('dashboard.manager.programs')
                ->withErrors(['programs' => 'Program tidak ditemukan untuk diedit.']);
        }

        $target->update([
            'name' => $payload['name'],
            'duration_hours' => $payload['duration_hours'],
            'fee_amount' => $payload['fee_amount'],
            'fee_unit' => $payload['fee_unit'],
            'description' => $payload['description'],
            'benefits' => array_filter($payload['benefits'], fn ($b) => !empty(trim($b))),
            'is_active' => (bool) ($payload['is_active'] ?? false),
        ]);

        return redirect()
            ->route('dashboard.manager.programs')
            ->with('status', 'Program berhasil diperbarui.');
    }

    public function managerProgramsDelete(Request $request, string $program): RedirectResponse
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        if (Schema::hasTable('programs')) {
            Program::where('id', $program)->delete();
        }

        return redirect()
            ->route('dashboard.manager.programs')
            ->with('status', 'Program berhasil dihapus.');
    }

    public function managerProgramsUpdate(Request $request, string $program): RedirectResponse
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        if (!Schema::hasTable('programs')) {
            return redirect()->route('dashboard.manager.programs')
                ->withErrors(['programs' => 'Tabel program belum tersedia. Jalankan migrasi terlebih dahulu.']);
        }

        $payload = $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        $target = Program::find($program);

        if (!$target) {
            return redirect()->route('dashboard.manager.programs')
                ->withErrors(['programs' => 'Program tidak ditemukan untuk diperbarui.']);
        }

        $target->update([
            'is_active' => (bool) $payload['is_active'],
        ]);

        return redirect()
            ->route('dashboard.manager.programs')
            ->with('status', 'Status program berhasil diperbarui.');
    }

    public function managerReports(Request $request): View|RedirectResponse
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        $month = (int) $request->query('month', now()->month);
        $year = (int) $request->query('year', now()->year);

        // Validate month and year
        if ($month < 1 || $month > 12) {
            $month = now()->month;
        }

        $monthlyData = $this->getMonthlyReportData($month, $year);
        $yearMonths = $this->getAvailableYears();

        $monthNames = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        $allMonthsData = $this->getAllMonthsData($year);

        return view('dashboard.manager.reports', [
            'user' => $request->session()->get('auth_user'),
            'dashboard' => $this->getManagerDashboardConfig('reports'),
            'monthlyData' => $monthlyData,
            'selectedMonth' => $month,
            'selectedYear' => $year,
            'availableYears' => $yearMonths,
            'allMonthsData' => $allMonthsData,
            'monthNames' => $monthNames,
        ]);
    }

    public function managerReportsExport(Request $request): BinaryFileResponse|RedirectResponse
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        $monthInput = (string) $request->input('month', (string) now()->month);
        $year = (int) $request->input('year', now()->year);
        $exportType = (string) $request->input('export_type', 'pdf');
        $isAnnual = $monthInput === 'all';
        $month = $isAnnual ? now()->month : max(1, min(12, (int) $monthInput));

        $reportData = $isAnnual
            ? $this->buildAnnualReportExportData($year)
            : $this->buildMonthlyReportExportData($month, $year);

        $reportData = array_replace_recursive([
            'mode' => $isAnnual ? 'annual' : 'monthly',
            'title' => $isAnnual ? 'Laporan Tahunan ' . $year : 'Laporan ' . $this->getMonthName($month) . ' ' . $year,
            'summary' => [],
            'rows' => [],
            'branding' => [],
        ], is_array($reportData) ? $reportData : []);

        $settings = $request->session()->get('manager_settings', $this->getManagerSettingsData());
        $logoPublicPath = '';
        $sessionLogo = (string) ($settings['logo_filename'] ?? '');
        if ($sessionLogo !== '') {
            $storageLogoPath = storage_path('app/public/logos/' . $sessionLogo);
            if (file_exists($storageLogoPath)) {
                $logoPublicPath = $storageLogoPath;
            }
        }

        if ($logoPublicPath === '') {
            $logoPublicPath = public_path('img/Logo.png');
            if (!file_exists($logoPublicPath)) {
                $logoPublicPath = public_path('img/komunitasbatik.png');
            }
        }

        $logoDataUri = null;
        if (file_exists($logoPublicPath)) {
            $logoMime = mime_content_type($logoPublicPath) ?: 'image/png';
            $logoDataUri = 'data:' . $logoMime . ';base64,' . base64_encode((string) file_get_contents($logoPublicPath));
        }

        $reportData['branding'] = [
            'organization_name' => (string) ($settings['organization_name'] ?? 'LPK Kama Praja Madiun'),
            'address' => (string) ($settings['organization_address'] ?? 'Kantor LPK Kama Praja Madiun'),
            'phone' => (string) (($settings['contacts']['whatsapp'] ?? '') ?: '081234567890'),
            'logo_data_uri' => $logoDataUri,
        ];

        $baseFilename = $isAnnual
            ? 'Laporan-Tahunan-' . $year
            : 'Laporan-' . $this->getMonthName($month) . '-' . $year;

        if ($exportType === 'csv') {
            $filename = $baseFilename . '.csv';
            $content = $this->buildReportCsv($reportData);
            $mimeType = 'text/csv; charset=UTF-8';
        } else {
            $filename = $baseFilename . '.pdf';
                $content = Pdf::loadView('dashboard.manager.report-export-template', compact('reportData'))
                    ->setPaper('a4', 'portrait')
                    ->output();
            $mimeType = 'application/pdf';
        }

        $relativePath = 'reports/' . now()->format('YmdHis') . '-' . $filename;
        Storage::disk('local')->put($relativePath, $content);

        return response()->download(Storage::disk('local')->path($relativePath), $filename, [
            'Content-Type' => $mimeType,
        ])->deleteFileAfterSend(true);
    }

    public function managerAchievements(Request $request): View|RedirectResponse
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        $search = trim((string) $request->query('search', ''));
        $achievementsQuery = Schema::hasTable('achievements') ? Achievement::query()->latest() : null;

        if ($achievementsQuery && $search !== '') {
            $achievementsQuery->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('event_name', 'like', '%' . $search . '%')
                    ->orWhere('winner_name', 'like', '%' . $search . '%');
            });
        }

        return view('dashboard.manager.achievements', [
            'user' => $request->session()->get('auth_user'),
            'dashboard' => $this->getManagerDashboardConfig('achievements'),
            'achievements' => $achievementsQuery ? $achievementsQuery->get() : collect(),
            'search' => $search,
        ]);
    }

    public function managerAchievementsStore(Request $request): RedirectResponse
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        if (!Schema::hasTable('achievements')) {
            return redirect()->route('dashboard.manager.achievements')
                ->withErrors(['achievements' => 'Tabel prestasi belum tersedia. Jalankan migrasi terlebih dahulu.']);
        }

        $payload = $request->validate([
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'event_name' => ['required', 'string', 'min:3', 'max:255'],
            'winner_name' => ['required', 'string', 'min:3', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'rank' => ['nullable', 'integer', 'min:1', 'max:3'],
            'year' => ['nullable', 'integer', 'min:2000', 'max:2100'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        Achievement::create([
            'title' => $payload['title'],
            'event_name' => $payload['event_name'],
            'winner_name' => $payload['winner_name'],
            'description' => $payload['description'] ?? null,
            'rank' => $payload['rank'] ?? null,
            'year' => $payload['year'] ?? null,
            'is_active' => (bool) ($payload['is_active'] ?? false),
        ]);

        return redirect()->route('dashboard.manager.achievements')
            ->with('status', 'Prestasi berhasil ditambahkan.');
    }

    public function managerAchievementsEdit(Request $request, string $achievement): RedirectResponse
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        if (!Schema::hasTable('achievements')) {
            return redirect()->route('dashboard.manager.achievements')
                ->withErrors(['achievements' => 'Tabel prestasi belum tersedia. Jalankan migrasi terlebih dahulu.']);
        }

        $payload = $request->validate([
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'event_name' => ['required', 'string', 'min:3', 'max:255'],
            'winner_name' => ['required', 'string', 'min:3', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'rank' => ['nullable', 'integer', 'min:1', 'max:3'],
            'year' => ['nullable', 'integer', 'min:2000', 'max:2100'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $target = Achievement::find($achievement);

        if (!$target) {
            return redirect()->route('dashboard.manager.achievements')
                ->withErrors(['achievements' => 'Data prestasi tidak ditemukan.']);
        }

        $target->update([
            'title' => $payload['title'],
            'event_name' => $payload['event_name'],
            'winner_name' => $payload['winner_name'],
            'description' => $payload['description'] ?? null,
            'rank' => $payload['rank'] ?? null,
            'year' => $payload['year'] ?? null,
            'is_active' => (bool) ($payload['is_active'] ?? false),
        ]);

        return redirect()->route('dashboard.manager.achievements')
            ->with('status', 'Prestasi berhasil diperbarui.');
    }

    public function managerAchievementsDelete(Request $request, string $achievement): RedirectResponse
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        if (Schema::hasTable('achievements')) {
            Achievement::where('id', $achievement)->delete();
        }

        return redirect()->route('dashboard.manager.achievements')
            ->with('status', 'Prestasi berhasil dihapus.');
    }

    public function managerTestimonials(Request $request): View|RedirectResponse
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        $search = trim((string) $request->query('search', ''));
        $query = Schema::hasTable('testimonials') ? Testimonial::query()->orderBy('sort_order')->latest() : null;

        if ($query && $search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', '%' . $search . '%')
                    ->orWhere('role_label', 'like', '%' . $search . '%')
                    ->orWhere('quote', 'like', '%' . $search . '%');
            });
        }

        return view('dashboard.manager.testimonials', [
            'user' => $request->session()->get('auth_user'),
            'dashboard' => $this->getManagerDashboardConfig('testimonials'),
            'testimonials' => $query ? $query->get() : collect(),
            'search' => $search,
        ]);
    }

    public function managerTestimonialsStore(Request $request): RedirectResponse
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        if (!Schema::hasTable('testimonials')) {
            return redirect()->route('dashboard.manager.testimonials')
                ->withErrors(['testimonials' => 'Tabel testimoni belum tersedia. Jalankan migrasi terlebih dahulu.']);
        }

        $payload = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:120'],
            'role_label' => ['nullable', 'string', 'max:150'],
            'quote' => ['required', 'string', 'min:10', 'max:1500'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        Testimonial::create([
            'name' => $payload['name'],
            'role_label' => $payload['role_label'] ?? null,
            'quote' => $payload['quote'],
            'sort_order' => (int) ($payload['sort_order'] ?? 0),
            'is_active' => (bool) ($payload['is_active'] ?? false),
        ]);

        return redirect()->route('dashboard.manager.testimonials')
            ->with('status', 'Testimoni berhasil ditambahkan.');
    }

    public function managerTestimonialsEdit(Request $request, string $testimonial): RedirectResponse
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        if (!Schema::hasTable('testimonials')) {
            return redirect()->route('dashboard.manager.testimonials')
                ->withErrors(['testimonials' => 'Tabel testimoni belum tersedia. Jalankan migrasi terlebih dahulu.']);
        }

        $payload = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:120'],
            'role_label' => ['nullable', 'string', 'max:150'],
            'quote' => ['required', 'string', 'min:10', 'max:1500'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $target = Testimonial::find($testimonial);
        if (!$target) {
            return redirect()->route('dashboard.manager.testimonials')
                ->withErrors(['testimonials' => 'Data testimoni tidak ditemukan.']);
        }

        $target->update([
            'name' => $payload['name'],
            'role_label' => $payload['role_label'] ?? null,
            'quote' => $payload['quote'],
            'sort_order' => (int) ($payload['sort_order'] ?? 0),
            'is_active' => (bool) ($payload['is_active'] ?? false),
        ]);

        return redirect()->route('dashboard.manager.testimonials')
            ->with('status', 'Testimoni berhasil diperbarui.');
    }

    public function managerTestimonialsDelete(Request $request, string $testimonial): RedirectResponse
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        if (Schema::hasTable('testimonials')) {
            Testimonial::where('id', $testimonial)->delete();
        }

        return redirect()->route('dashboard.manager.testimonials')
            ->with('status', 'Testimoni berhasil dihapus.');
    }

    public function managerFacilities(Request $request): View|RedirectResponse
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        $search = trim((string) $request->query('search', ''));
        $query = Schema::hasTable('facilities') ? Facility::query()->orderBy('sort_order')->latest() : null;

        if ($query && $search !== '') {
            $query->where('name', 'like', '%' . $search . '%');
        }

        return view('dashboard.manager.facilities', [
            'user' => $request->session()->get('auth_user'),
            'dashboard' => $this->getManagerDashboardConfig('facilities'),
            'facilities' => $query ? $query->get() : collect(),
            'search' => $search,
        ]);
    }

    public function managerFacilitiesStore(Request $request): RedirectResponse
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        if (!Schema::hasTable('facilities')) {
            return redirect()->route('dashboard.manager.facilities')
                ->withErrors(['facilities' => 'Tabel fasilitas belum tersedia. Jalankan migrasi terlebih dahulu.']);
        }

        $payload = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:120'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('landing/facilities', 'public');
        }

        Facility::create([
            'name' => $payload['name'],
            'image_path' => $imagePath,
            'sort_order' => (int) ($payload['sort_order'] ?? 0),
            'is_active' => (bool) ($payload['is_active'] ?? false),
        ]);

        return redirect()->route('dashboard.manager.facilities')
            ->with('status', 'Fasilitas berhasil ditambahkan.');
    }

    public function managerFacilitiesEdit(Request $request, string $facility): RedirectResponse
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        if (!Schema::hasTable('facilities')) {
            return redirect()->route('dashboard.manager.facilities')
                ->withErrors(['facilities' => 'Tabel fasilitas belum tersedia. Jalankan migrasi terlebih dahulu.']);
        }

        $payload = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:120'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $target = Facility::find($facility);
        if (!$target) {
            return redirect()->route('dashboard.manager.facilities')
                ->withErrors(['facilities' => 'Data fasilitas tidak ditemukan.']);
        }

        if ($request->hasFile('image')) {
            if (!empty($target->image_path) && Storage::disk('public')->exists($target->image_path)) {
                Storage::disk('public')->delete($target->image_path);
            }

            $target->image_path = $request->file('image')->store('landing/facilities', 'public');
        }

        $target->name = $payload['name'];
        $target->sort_order = (int) ($payload['sort_order'] ?? 0);
        $target->is_active = (bool) ($payload['is_active'] ?? false);
        $target->save();

        return redirect()->route('dashboard.manager.facilities')
            ->with('status', 'Fasilitas berhasil diperbarui.');
    }

    public function managerFacilitiesDelete(Request $request, string $facility): RedirectResponse
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        $target = Schema::hasTable('facilities') ? Facility::find($facility) : null;

        if ($target) {
            if (!empty($target->image_path) && Storage::disk('public')->exists($target->image_path)) {
                Storage::disk('public')->delete($target->image_path);
            }
            $target->delete();
        }

        return redirect()->route('dashboard.manager.facilities')
            ->with('status', 'Fasilitas berhasil dihapus.');
    }

    public function managerPartners(Request $request): View|RedirectResponse
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        $search = trim((string) $request->query('search', ''));
        $query = Schema::hasTable('partners') ? Partner::query()->orderBy('sort_order')->latest() : null;

        if ($query && $search !== '') {
            $query->where('name', 'like', '%' . $search . '%');
        }

        return view('dashboard.manager.partners', [
            'user' => $request->session()->get('auth_user'),
            'dashboard' => $this->getManagerDashboardConfig('partners'),
            'partners' => $query ? $query->get() : collect(),
            'search' => $search,
        ]);
    }

    public function managerPartnersStore(Request $request): RedirectResponse
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        if (!Schema::hasTable('partners')) {
            return redirect()->route('dashboard.manager.partners')
                ->withErrors(['partners' => 'Tabel mitra belum tersedia. Jalankan migrasi terlebih dahulu.']);
        }

        $payload = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:150'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('landing/partners', 'public');
        }

        Partner::create([
            'name' => $payload['name'],
            'logo_path' => $logoPath,
            'sort_order' => (int) ($payload['sort_order'] ?? 0),
            'is_active' => (bool) ($payload['is_active'] ?? false),
        ]);

        return redirect()->route('dashboard.manager.partners')
            ->with('status', 'Mitra berhasil ditambahkan.');
    }

    public function managerPartnersEdit(Request $request, string $partner): RedirectResponse
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        if (!Schema::hasTable('partners')) {
            return redirect()->route('dashboard.manager.partners')
                ->withErrors(['partners' => 'Tabel mitra belum tersedia. Jalankan migrasi terlebih dahulu.']);
        }

        $payload = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:150'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $target = Partner::find($partner);
        if (!$target) {
            return redirect()->route('dashboard.manager.partners')
                ->withErrors(['partners' => 'Data mitra tidak ditemukan.']);
        }

        if ($request->hasFile('logo')) {
            if (!empty($target->logo_path) && Storage::disk('public')->exists($target->logo_path)) {
                Storage::disk('public')->delete($target->logo_path);
            }

            $target->logo_path = $request->file('logo')->store('landing/partners', 'public');
        }

        $target->name = $payload['name'];
        $target->sort_order = (int) ($payload['sort_order'] ?? 0);
        $target->is_active = (bool) ($payload['is_active'] ?? false);
        $target->save();

        return redirect()->route('dashboard.manager.partners')
            ->with('status', 'Mitra berhasil diperbarui.');
    }

    public function managerPartnersDelete(Request $request, string $partner): RedirectResponse
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        $target = Schema::hasTable('partners') ? Partner::find($partner) : null;

        if ($target) {
            if (!empty($target->logo_path) && Storage::disk('public')->exists($target->logo_path)) {
                Storage::disk('public')->delete($target->logo_path);
            }
            $target->delete();
        }

        return redirect()->route('dashboard.manager.partners')
            ->with('status', 'Mitra berhasil dihapus.');
    }

    public function managerSettings(Request $request): View|RedirectResponse
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        $defaults = $this->getManagerSettingsData();
        $sessionSettings = $request->session()->get('manager_settings', []);

        return view('dashboard.manager.settings', [
            'user' => $request->session()->get('auth_user'),
            'dashboard' => $this->getManagerDashboardConfig('settings'),
            'settingsData' => is_array($sessionSettings) ? array_replace_recursive($defaults, $sessionSettings) : $defaults,
        ]);
    }

    public function managerSettingsUpdate(Request $request): RedirectResponse
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        $validated = $request->validate([
            'organization_name' => ['required', 'string', 'min:3', 'max:120'],
            'organization_address' => ['required', 'string', 'min:5', 'max:255'],
            'support_email' => ['required', 'email'],
            'timezone' => ['required', 'string'],
            'logo' => ['nullable', 'image', 'max:2048', 'mimes:jpeg,png,jpg,webp'],
            'logo_fit' => ['nullable', 'string', 'in:contain,cover,fill'],
            'contact_googlemaps' => ['nullable', 'url'],
            'contact_whatsapp' => ['nullable', 'string', 'regex:/^\+?[0-9]{7,15}$/'],
            'contact_facebook' => ['nullable', 'url'],
            'contact_instagram' => ['nullable', 'url'],
            'contact_youtube' => ['nullable', 'url'],
        ]);

        // Store in session settings state
        $currentSettings = $request->session()->get('manager_settings', []);

        // Handle logo upload if provided
        if ($request->hasFile('logo')) {
            $logoFile = $request->file('logo');
            $logoName = 'logo-' . now()->timestamp . '.' . $logoFile->getClientOriginalExtension();
            $logoFile->storeAs('logos', $logoName, 'public');
            $currentSettings['logo_filename'] = $logoName;
        }

        // Update basic settings
        $currentSettings['organization_name'] = $validated['organization_name'];
        $currentSettings['organization_address'] = $validated['organization_address'];
        $currentSettings['support_email'] = $validated['support_email'];
        $currentSettings['timezone'] = $validated['timezone'];
        $currentSettings['logo_fit'] = $validated['logo_fit'] ?? 'contain';

        // Update contact information
        $currentSettings['contacts'] = [
            'googlemaps' => $validated['contact_googlemaps'] ?? '',
            'whatsapp' => $validated['contact_whatsapp'] ?? '',
            'facebook' => $validated['contact_facebook'] ?? '',
            'instagram' => $validated['contact_instagram'] ?? '',
            'youtube' => $validated['contact_youtube'] ?? '',
        ];

        $request->session()->put('manager_settings', $currentSettings);

        return redirect()
            ->route('dashboard.manager.settings')
            ->with('status', 'Pengaturan dan kontak berhasil diperbarui (simulasi).');
    }

    public function managerProfile(Request $request): View|RedirectResponse
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        return view('dashboard.manager.profile', [
            'user' => $request->session()->get('auth_user'),
            'dashboard' => $this->getManagerDashboardConfig('profile'),
            'profile' => $this->getManagerProfileData($request),
        ]);
    }

    public function managerProfileUpdate(Request $request): RedirectResponse
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'min:3', 'max:120'],
            'username' => ['required', 'string', 'min:4', 'max:40'],
            'password' => ['required', 'string', 'min:6', 'max:40'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'string', 'min:7', 'max:20'],
            'address' => ['required', 'string', 'min:5', 'max:255'],
            'role_label' => ['required', 'string', 'min:3', 'max:40'],
        ]);

        $profile = $this->getManagerProfileData($request);

        $profile['full_name'] = $validated['full_name'];
        $profile['username'] = $validated['username'];
        $profile['password'] = $validated['password'];
        $profile['email'] = $validated['email'];
        $profile['phone'] = $validated['phone'];
        $profile['address'] = $validated['address'];
        $profile['role_label'] = $validated['role_label'];

        $request->session()->put('profile_manager_data', $profile);

        $authUser = $request->session()->get('auth_user', []);
        $authUser['name'] = $validated['full_name'];
        $authUser['username'] = $validated['username'];
        $authUser['email'] = $validated['email'];
        $request->session()->put('auth_user', $authUser);

        return redirect()
            ->route('dashboard.manager.profile')
            ->with('status', 'Profil admin berhasil diperbarui.');
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

    public function instructorProfile(Request $request): View|RedirectResponse
    {
        $guard = $this->ensureInstructorRole($request);

        if ($guard) {
            return $guard;
        }

        return view('dashboard.instructor.profile', [
            'user' => $request->session()->get('auth_user'),
            'dashboard' => $this->getInstructorDashboardConfig('profile'),
            'profile' => $this->getInstructorProfileData($request),
        ]);
    }

    public function instructorProfileUpdate(Request $request): RedirectResponse
    {
        $guard = $this->ensureInstructorRole($request);

        if ($guard) {
            return $guard;
        }

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'min:3', 'max:120'],
            'username' => ['required', 'string', 'min:4', 'max:40'],
            'password' => ['required', 'string', 'min:6', 'max:40'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'string', 'min:7', 'max:20'],
            'address' => ['required', 'string', 'min:5', 'max:255'],
        ]);

        $profile = $this->getInstructorProfileData($request);

        $profile['full_name'] = $validated['full_name'];
        $profile['username'] = $validated['username'];
        $profile['password'] = $validated['password'];
        $profile['email'] = $validated['email'];
        $profile['phone'] = $validated['phone'];
        $profile['address'] = $validated['address'];
        $profile['role_label'] = 'Pengajar';

        $request->session()->put('profile_instructor_data', $profile);

        $authUser = $request->session()->get('auth_user', []);
        $authenticatedUserId = (int) ($authUser['id'] ?? Auth::id() ?? 0);
        $dbUserQuery = User::query()->whereIn('role', ['pengajar', 'instructor']);

        if ($authenticatedUserId > 0) {
            $dbUserQuery->where('id', $authenticatedUserId);
        } elseif (!empty($authUser['username'])) {
            $dbUserQuery->where('username', (string) $authUser['username']);
        }

        $dbUser = $dbUserQuery->first();

        if ($dbUser) {
            $dbUser->name = $validated['full_name'];
            $dbUser->username = $validated['username'];
            $dbUser->email = $validated['email'];
            $dbUser->password = Hash::make($validated['password']);
            $dbUser->phone = $validated['phone'];
            $dbUser->address = $validated['address'];

            if (Schema::hasColumn('users', 'current_password')) {
                $dbUser->current_password = (string) $validated['password'];
            }

            $dbUser->save();
        }

        $authUser['name'] = $validated['full_name'];
        $authUser['username'] = $validated['username'];
        $authUser['email'] = $validated['email'];
        if ($dbUser) {
            $authUser['id'] = $dbUser->id;
        }
        $request->session()->put('auth_user', $authUser);

        return redirect()
            ->route('dashboard.instructor.profile')
            ->with('status', 'Profil pengajar berhasil diperbarui.');
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
        $individualPage = (int) $request->query('individual_page', 1);
        $groupPage = (int) $request->query('group_page', 1);

        $allIndividual = $this->getInstructorIndividualParticipants();
        $allGroup = $this->getInstructorGroupParticipants();

        $paginatedIndividual = $this->paginateArray($allIndividual, $individualPage, 5);
        $paginatedGroup = $this->paginateArray($allGroup, $groupPage, 5);

        return view('dashboard.instructor.participants', [
            'user' => $request->session()->get('auth_user'),
            'dashboard' => $this->getInstructorDashboardConfig('participants'),
            'individualParticipants' => $paginatedIndividual['items'],
            'individualPage' => $individualPage,
            'individualTotalPages' => $paginatedIndividual['total_pages'],
            'individualTotal' => $paginatedIndividual['total'],
            'groupParticipants' => $paginatedGroup['items'],
            'groupPage' => $groupPage,
            'groupTotalPages' => $paginatedGroup['total_pages'],
            'groupTotal' => $paginatedGroup['total'],
            'selectedParticipant' => $selected,
        ]);
    }

    public function instructorParticipantsIndividualDetail(Request $request): View|RedirectResponse
    {
        $guard = $this->ensureInstructorRole($request);

        if ($guard) {
            return $guard;
        }

        return view('dashboard.instructor.participants-individual-detail', [
            'user' => $request->session()->get('auth_user'),
            'dashboard' => $this->getInstructorDashboardConfig('participants'),
            'individualParticipants' => $this->getInstructorIndividualParticipants(),
        ]);
    }

    public function instructorParticipantsGroupDetail(Request $request): View|RedirectResponse
    {
        $guard = $this->ensureInstructorRole($request);

        if ($guard) {
            return $guard;
        }

        return view('dashboard.instructor.participants-group-detail', [
            'user' => $request->session()->get('auth_user'),
            'dashboard' => $this->getInstructorDashboardConfig('participants'),
            'groupParticipants' => $this->getInstructorGroupParticipants(),
        ]);
    }

    public function instructorForum(Request $request): View|RedirectResponse
    {
        $guard = $this->ensureInstructorRole($request);

        if ($guard) {
            return $guard;
        }

        $service = app(ForumDiscussionService::class);
        $selectedModuleSlug = (string) $request->query('module', '');
        $discussionsQuery = Schema::hasTable('forum_discussions')
            ? $service->getForumDiscussions($selectedModuleSlug !== '' ? $selectedModuleSlug : null)
            : collect();

        return view('dashboard.instructor.forum', [
            'user' => $request->session()->get('auth_user'),
            'discussions' => $discussionsQuery,
            'modules' => $service->getForumModules(),
            'forumThemes' => $service->getForumThemes(),
            'selectedModuleSlug' => $selectedModuleSlug,
            'dashboard' => $this->getInstructorDashboardConfig('forum'),
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

    public function instructorAssessmentsDetail(Request $request, string $submission): View|RedirectResponse
    {
        $guard = $this->ensureInstructorRole($request);

        if ($guard) {
            return $guard;
        }

        $assignment = $this->getInstructorSubmissionById($submission);

        if (! $assignment) {
            return redirect()->route('dashboard.instructor.assessments');
        }

        return view('dashboard.instructor.assessments-detail', [
            'user' => $request->session()->get('auth_user'),
            'dashboard' => $this->getInstructorDashboardConfig('assessments'),
            'submission' => $assignment,
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
            'feedback' => ['nullable', 'string', 'max:1000'],
        ]);

        $assignment = $this->getInstructorSubmissionById($submission);

        if (! $assignment) {
            return redirect()->route('dashboard.instructor.assessments')
                ->withErrors(['submission' => 'Tugas tidak ditemukan.']);
        }

        $service = new ParticipantModuleService();
        $service->gradeAssignment($assignment, (int) $request->score, $request->feedback);

        return redirect()
            ->route('dashboard.instructor.assessments')
            ->with('status', 'Nilai untuk tugas peserta berhasil disimpan.');
    }

    private function getInstructorSubmissions()
    {
        return ParticipantAssignment::with(['user', 'module', 'material'])
            ->orderByDesc('submitted_at')
            ->get();
    }

    private function getInstructorSubmissionById(string $submissionId): ?ParticipantAssignment
    {
        if (!is_numeric($submissionId)) {
            return null;
        }

        return ParticipantAssignment::with(['user', 'module', 'material'])
            ->find((int) $submissionId);
    }

    public function participantHome(Request $request): View|RedirectResponse
    {
        $guard = $this->ensureParticipantRole($request);

        if ($guard) {
            return $guard;
        }

        $participantUser = $this->resolveParticipantAccountUser($request);
        $moduleService = app(ParticipantModuleService::class);
        $modules = $moduleService->getAvailableModules();

        $moduleProgressItems = $modules->map(function ($module) use ($moduleService, $participantUser): array {
            $moduleData = $moduleService->getModuleForParticipant($module, $participantUser);

            return [
                'title' => $module->title,
                'slug' => $module->slug,
                'url' => route('dashboard.participant.modules.detail', ['module' => $module->slug]),
                'progress' => (int) ($moduleData['overall_progress'] ?? 0),
                'completed_count' => (int) ($moduleData['completed_count'] ?? 0),
                'total_count' => (int) ($moduleData['total_count'] ?? 0),
                'status_label' => (int) ($moduleData['overall_progress'] ?? 0) >= 100 ? 'Selesai' : 'Berjalan',
            ];
        })->values();

        $nextModule = $moduleProgressItems->first(function (array $module): bool {
            return $module['progress'] < 100;
        }) ?? $moduleProgressItems->first();

        $latestArtwork = null;
        $artworkCount = 0;
        if (Schema::hasTable('artworks') && !empty($participantUser->email)) {
            $artworkQuery = Artwork::query()->where('creator_email', $participantUser->email)->latest();
            $artworkCount = (clone $artworkQuery)->count();
            $latestArtwork = $artworkQuery->first();
        }

        return $this->renderParticipantPage($request, 'home', [
            'moduleProgressItems' => $moduleProgressItems,
            'homeStats' => [
                'module_total' => $moduleProgressItems->count(),
                'module_completed' => $moduleProgressItems->where('progress', 100)->count(),
                'artwork_total' => $artworkCount,
            ],
            'continueModuleUrl' => $nextModule['url'] ?? route('dashboard.participant.modules'),
            'latestArtwork' => $latestArtwork,
        ]);
    }

    public function participantProfile(Request $request): View|RedirectResponse
    {
        $guard = $this->ensureParticipantRole($request);

        if ($guard) {
            return $guard;
        }

        return view('dashboard.participant.profile', [
            'user' => $this->resolveSidebarUser($request),
            'dashboard' => $this->getParticipantDashboardConfig('profile'),
            'profile' => $this->getParticipantProfileData($request),
        ]);
    }

    public function participantProfileUpdate(Request $request): RedirectResponse
    {
        $guard = $this->ensureParticipantRole($request);

        if ($guard) {
            return $guard;
        }

        // Base validation rules
        $rules = [
            'participant_type' => ['required', 'in:individual,group'],
            'full_name' => ['required', 'string', 'min:3', 'max:120'],
            'username' => ['required', 'string', 'min:4', 'max:40'],
            'password' => ['required', 'string', 'min:6', 'max:40'],
            'password_confirmation' => ['required', 'string', 'same:password'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'string', 'min:7', 'max:20'],
            'address' => ['required', 'string', 'min:5', 'max:255'],
            'motivation' => ['nullable', 'string', 'max:255'],
            'group_name' => ['nullable', 'string', 'max:120'],
            'pic_name' => ['nullable', 'string', 'max:120'],
            'role_label' => ['required', 'string', 'min:3', 'max:40'],
        ];

        $authUser = $request->session()->get('auth_user', []);
        $authenticatedUserId = (int) ($authUser['id'] ?? Auth::id() ?? 0);
        $participantQuery = User::query()->whereIn('role', ['peserta', 'participant']);

        if ($authenticatedUserId > 0) {
            $participantQuery->where('id', $authenticatedUserId);
        } else {
            $participantQuery->where('username', (string) ($authUser['username'] ?? ''));
        }

        $dbUser = $participantQuery->first();

        if (!$dbUser) {
            return back()->withErrors(['general' => 'User tidak ditemukan.'])->withInput();
        }

        $participantType = !empty($dbUser->group_name) ? 'group' : 'individual';
        // Add personal_phone validation only for group participants
        if ($participantType === 'group') {
            $rules['personal_phone'] = ['required', 'string', 'min:7', 'max:20'];
        }

        $validated = $request->validate($rules);

        if ($participantType === 'individual' && empty($validated['motivation'])) {
            return back()->withErrors(['motivation' => 'Motivasi singkat wajib diisi untuk peserta individu.'])->withInput();
        }

        if ($participantType === 'group') {
            $personalPhone = trim((string) ($validated['personal_phone'] ?? ''));

            if ($personalPhone === '') {
                return back()->withErrors(['personal_phone' => 'No. handphone pribadi wajib diisi untuk peserta kelompok saat mengganti password.'])->withInput();
            }

            if ($this->normalizeWhatsappNumber($personalPhone) === $this->normalizeWhatsappNumber((string) $validated['phone'])) {
                return back()->withErrors(['personal_phone' => 'No. handphone pribadi harus berbeda dari no. handphone PIC.'])->withInput();
            }
        }

        // Update user data
        $dbUser->name = $validated['full_name'];
        $dbUser->username = $validated['username'];
        $dbUser->email = $validated['email'];
        $dbUser->password = Hash::make($validated['password']);
        $dbUser->phone = $validated['phone'];

        // Only update personal_phone for group participants
        if ($participantType === 'group') {
            $dbUser->personal_phone = trim((string) ($validated['personal_phone'] ?? ''));
        }

        $dbUser->address = $validated['address'];
        $dbUser->motivation = $participantType === 'individual' ? ($validated['motivation'] ?? null) : null;
        $dbUser->password_changed = true; // Mark as changed
        if (Schema::hasColumn('users', 'forgot_password_enabled')) {
            $dbUser->forgot_password_enabled = true;
        }
        if (Schema::hasColumn('users', 'current_password')) {
            $dbUser->current_password = (string) $validated['password'];
        }

        $dbUser->save();

        // Update session
        $authUser['name'] = $validated['full_name'];
        $authUser['username'] = $validated['username'];
        $authUser['email'] = $validated['email'];
        $authUser['id'] = $dbUser->id;
        $request->session()->put('auth_user', $authUser);

        return redirect()
            ->route('dashboard.participant.home')
            ->with('status', 'Profil peserta berhasil diperbarui.');
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

        $service = app(ForumDiscussionService::class);
        $selectedModuleSlug = (string) $request->query('module', '');
        $discussionsQuery = Schema::hasTable('forum_discussions')
            ? $service->getForumDiscussions($selectedModuleSlug !== '' ? $selectedModuleSlug : null)
            : collect();

        return $this->renderParticipantPage($request, 'forum', [
            'discussions' => $discussionsQuery,
            'modules' => $service->getForumModules(),
            'forumThemes' => $service->getForumThemes(),
            'selectedModuleSlug' => $selectedModuleSlug,
        ]);
    }

    public function participantGallery(Request $request): View|RedirectResponse
    {
        $guard = $this->ensureParticipantRole($request);

        if ($guard) {
            return $guard;
        }

        $search = trim((string) $request->query('search', ''));
        $currentUserEmail = (string) data_get($request->session()->get('auth_user', []), 'email', '');

        $artworksQuery = null;

        if (Schema::hasTable('artworks') && $currentUserEmail !== '') {
            $artworksQuery = Artwork::query()
                ->where('creator_email', $currentUserEmail)
                ->latest();
        }

        if ($artworksQuery && $search !== '') {
            $artworksQuery->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhere('creator_name', 'like', '%' . $search . '%');
            });
        }

        return $this->renderParticipantPage($request, 'gallery', [
            'artworks' => $artworksQuery ? $artworksQuery->get() : collect(),
            'search' => $search,
        ]);
    }

    public function participantGalleryUpload(Request $request): View|RedirectResponse
    {
        $guard = $this->ensureParticipantRole($request);

        if ($guard) {
            return $guard;
        }

        return $this->renderParticipantPage($request, 'gallery-upload');
    }

    public function participantGalleryStore(Request $request): RedirectResponse
    {
        $guard = $this->ensureParticipantRole($request);

        if ($guard) {
            return $guard;
        }

        $validated = $request->validate([
            'nama_pembuat' => ['required', 'string', 'max:255'],
            'judul_karya' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string', 'max:2000'],
            'gambar_karya' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ]);

        $imagePath = $request->file('gambar_karya')->store('artworks', 'public');
        $authUser = $request->session()->get('auth_user', []);

        if (!Schema::hasTable('artworks')) {
            return redirect()
                ->route('dashboard.participant.gallery')
                ->withErrors(['gambar_karya' => 'Penyimpanan karya belum siap. Jalankan migrasi database terlebih dahulu.']);
        }

        Artwork::create([
            'title' => $validated['judul_karya'],
            'description' => $validated['deskripsi'],
            'image_path' => $imagePath,
            'creator_name' => $validated['nama_pembuat'],
            'creator_email' => $authUser['email'] ?? null,
        ]);

        return redirect()
            ->route('dashboard.participant.gallery')
            ->with('status', 'Karya berhasil diunggah dan tersimpan.');
    }

    private function renderParticipantPage(Request $request, string $page, array $extraData = []): View
    {
        $user = $this->resolveSidebarUser($request);
        $dashboard = $this->getParticipantDashboardConfig($page);

        return view($dashboard['view'], array_merge([
            'user' => $user,
            'dashboard' => $dashboard,
        ], $extraData));
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
            'gallery-upload' => [
                'view' => 'dashboard.participant.upload-gallery',
                'title' => 'Upload Karya',
                'subtitle' => 'Bagikan karya batik terbaik Anda dengan komunitas.',
                'headerGradient' => 'from-slate-900 to-blue-900',
            ],
            'profile' => [
                'view' => 'dashboard.participant.profile',
                'title' => 'Profil Pengguna - Peserta',
                'subtitle' => 'Lihat dan perbarui data profil peserta Anda.',
                'headerGradient' => 'from-slate-900 to-blue-900',
            ],
        ];

        $selected = $config[$activePage] ?? $config['home'];

        return [
            ...$selected,
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
            'achievements' => [
                'title' => 'Kelola Prestasi',
                'subtitle' => 'Atur data prestasi alumni yang ditampilkan di landing page.',
            ],
            'testimonials' => [
                'title' => 'Kelola Testimoni',
                'subtitle' => 'Kelola daftar testimoni peserta untuk ditampilkan di halaman beranda.',
            ],
            'facilities' => [
                'title' => 'Kelola Fasilitas',
                'subtitle' => 'Kelola fasilitas pada halaman Tentang Kami secara dinamis.',
            ],
            'partners' => [
                'title' => 'Kelola Mitra',
                'subtitle' => 'Kelola daftar mitra kerja sama pada halaman Tentang Kami.',
            ],
            'settings' => [
                'title' => 'Pengaturan',
                'subtitle' => 'Konfigurasi preferensi dasar sistem dan kontak operasional.',
            ],
            'profile' => [
                'title' => 'Profil Pengguna - Admin',
                'subtitle' => 'Lihat dan perbarui data akun admin.',
            ],
        ];

        $selected = $config[$activePage] ?? $config['home'];

        return [
            ...$selected,
            'headerGradient' => 'from-[#1f2937] to-[#374151]',
            'showNotification' => true,
            'roleBadgeClasses' => 'bg-slate-200 text-slate-700',
            'activeMenuClasses' => 'bg-slate-200 text-slate-900',
            'profileUrl' => route('dashboard.manager.profile'),
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
                    'label' => 'Kelola Prestasi',
                    'icon' => 'achievements',
                    'url' => route('dashboard.manager.achievements'),
                    'active' => $activePage === 'achievements',
                ],
                [
                    'label' => 'Kelola Testimoni',
                    'icon' => 'testimonials',
                    'url' => route('dashboard.manager.testimonials'),
                    'active' => $activePage === 'testimonials',
                ],
                [
                    'label' => 'Kelola Fasilitas',
                    'icon' => 'facilities',
                    'url' => route('dashboard.manager.facilities'),
                    'active' => $activePage === 'facilities',
                ],
                [
                    'label' => 'Kelola Mitra',
                    'icon' => 'partners',
                    'url' => route('dashboard.manager.partners'),
                    'active' => $activePage === 'partners',
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
        $individualParticipants = 0;
        $groupParticipants = 0;
        $activeInstructors = 0;
        $activePrograms = 0;

        if (Schema::hasTable('users')) {
            $individualParticipants = User::query()
                ->where('role', 'peserta')
                ->where(function ($query) {
                    $query->whereNull('group_name')
                        ->orWhere('group_name', '');
                })
                ->where(function ($query) {
                    $query->whereNull('status')
                        ->orWhereIn('status', ['Aktif', 'active']);
                })
                ->count();

            $groupParticipants = User::query()
                ->where('role', 'peserta')
                ->whereNotNull('group_name')
                ->where('group_name', '!=', '')
                ->where(function ($query) {
                    $query->whereNull('status')
                        ->orWhereIn('status', ['Aktif', 'active']);
                })
                ->count();

            $activeInstructors = User::query()
                ->where('role', 'pengajar')
                ->where(function ($query) {
                    $query->whereNull('status')
                        ->orWhereIn('status', ['Aktif', 'active']);
                })
                ->count();
        }

        if (Schema::hasTable('programs')) {
            $activePrograms = Program::query()->where('is_active', true)->count();
        }

        return [
            'individualParticipants' => $individualParticipants,
            'groupParticipants' => $groupParticipants,
            'activeInstructors' => $activeInstructors,
            'activePrograms' => $activePrograms,
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function getManagerActivities(): array
    {
        $activities = [];

        if (Schema::hasTable('registration_individuals')) {
            $latestIndividual = RegistrationIndividual::query()
                ->latest()
                ->first();

            if ($latestIndividual) {
                $activities[] = [
                    'time' => $latestIndividual->created_at?->format('H:i') ?? '-',
                    'title' => 'Pendaftaran individu baru',
                    'description' => $latestIndividual->nama_lengkap . ' menunggu proses verifikasi dengan status ' . $latestIndividual->status . '.',
                ];
            }
        }

        if (Schema::hasTable('registration_groups')) {
            $latestGroup = RegistrationGroup::query()
                ->latest()
                ->first();

            if ($latestGroup) {
                $activities[] = [
                    'time' => $latestGroup->created_at?->format('H:i') ?? '-',
                    'title' => 'Pendaftaran kelompok baru',
                    'description' => $latestGroup->nama_lembaga . ' mengirim ' . $latestGroup->jumlah_peserta . ' peserta dengan status ' . $latestGroup->status . '.',
                ];
            }
        }

        if (Schema::hasTable('programs')) {
            $latestProgram = Program::query()->latest()->first();

            if ($latestProgram) {
                $activities[] = [
                    'time' => $latestProgram->updated_at?->format('H:i') ?? '-',
                    'title' => 'Program diperbarui',
                    'description' => 'Program ' . $latestProgram->name . ' saat ini ' . ($latestProgram->is_active ? 'ditampilkan' : 'disembunyikan') . ' di dashboard.',
                ];
            }
        }

        if (Schema::hasTable('users')) {
            $latestInstructor = User::query()
                ->where('role', 'pengajar')
                ->latest()
                ->first();

            if ($latestInstructor) {
                $activities[] = [
                    'time' => $latestInstructor->updated_at?->format('H:i') ?? '-',
                    'title' => 'Pengajar aktif',
                    'description' => $latestInstructor->name . ' terdaftar sebagai pengajar aktif pada sistem.',
                ];
            }
        }

        if (empty($activities)) {
            return [
                ['time' => '-', 'title' => 'Belum ada aktivitas', 'description' => 'Data aktivitas akan muncul setelah ada transaksi pendaftaran atau pembaruan program.'],
            ];
        }

        return array_slice($activities, 0, 4);
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function getManagerAlumniAchievements(): array
    {
        if (! Schema::hasTable('achievements')) {
            return [];
        }

        return Achievement::query()
            ->where('is_active', true)
            ->latest()
            ->limit(5)
            ->get()
            ->map(function (Achievement $achievement): array {
                $rankText = $achievement->rank ? ('Juara ' . $achievement->rank) : 'Partisipasi';

                return [
                    'title' => (string) ($achievement->title ?? '-'),
                    'winner' => (string) ($achievement->winner_name ?? '-'),
                    'event' => (string) ($achievement->event_name ?? '-'),
                    'year' => (string) ($achievement->year ?? '-'),
                    'rank' => $rankText,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    private function getManagerRegistrationStatistics(): array
    {
        $months = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        $individualDates = Schema::hasTable('registration_individuals')
            ? RegistrationIndividual::query()->pluck('created_at')->filter()->values()
            : collect();
        $groupDates = Schema::hasTable('registration_groups')
            ? RegistrationGroup::query()->pluck('created_at')->filter()->values()
            : collect();

        $allDates = $individualDates->merge($groupDates)->filter()->values();

        if ($allDates->isEmpty()) {
            $fallbackDate = Carbon::now()->startOfMonth();
            $allDates = collect([$fallbackDate]);
        }

        $startDate = Carbon::parse($allDates->min())->startOfMonth();
        $endDate = Carbon::parse($allDates->max())->startOfMonth();

        $individualCounts = [];
        foreach ($individualDates as $date) {
            $key = Carbon::parse($date)->format('Y-m');
            $individualCounts[$key] = ($individualCounts[$key] ?? 0) + 1;
        }

        $groupCounts = [];
        foreach ($groupDates as $date) {
            $key = Carbon::parse($date)->format('Y-m');
            $groupCounts[$key] = ($groupCounts[$key] ?? 0) + 1;
        }

        $points = [];
        $totalRegistrations = 0;
        $peakMonthLabel = '-';
        $peakMonthCount = 0;
        $cursor = $startDate->copy();

        while ($cursor->lessThanOrEqualTo($endDate)) {
            $monthKey = $cursor->format('Y-m');
            $monthNumber = (int) $cursor->format('n');
            $monthLabel = $months[$monthNumber] . ' ' . $cursor->format('Y');
            $individualCount = $individualCounts[$monthKey] ?? 0;
            $groupCount = $groupCounts[$monthKey] ?? 0;
            $total = $individualCount + $groupCount;

            $points[] = [
                'key' => $monthKey,
                'label' => $monthLabel,
                'individual' => $individualCount,
                'group' => $groupCount,
                'total' => $total,
            ];

            $totalRegistrations += $total;

            if ($total > $peakMonthCount) {
                $peakMonthCount = $total;
                $peakMonthLabel = $monthLabel;
            }

            $cursor->addMonthNoOverflow();
        }

        $averageRegistration = count($points) > 0 ? (int) round($totalRegistrations / count($points)) : 0;
        $firstHalf = array_sum(array_slice(array_column($points, 'total'), 0, 6));
        $secondHalf = array_sum(array_slice(array_column($points, 'total'), 6, 6));
        $growth = $firstHalf > 0 ? round((($secondHalf - $firstHalf) / $firstHalf) * 100, 1) : 0.0;

        return [
            'points' => $points,
            'summary' => [
                'total' => $totalRegistrations,
                'average' => $averageRegistration,
                'peakMonth' => $peakMonthLabel,
                'growth' => $growth,
            ],
        ];
    }

    /**
     * @return array<int, array<string, string|int>>
     */
    private function getManagerIndividualParticipants(): array
    {
        if (! Schema::hasTable('users')) {
            return [];
        }

        $users = User::where('role', 'peserta')
            ->where(function ($query) {
                $query->whereNull('group_name')
                    ->orWhere('group_name', '');
            })
            ->orderByRaw('LOWER(name) asc')
            ->get();

        return $users->map(function ($user) {
            $status = $this->normalizeParticipantStatus((string) ($user->status ?? 'active'));

            return [
                'id' => $user->username ?: 'peserta-' . $user->id,
                'name' => $user->name,
                'program' => 'Program Individu',
                'progress' => 0,
                'status' => $status,
                'status_label' => $this->buildParticipantRoleLabel($status),
                'forgot_password_enabled' => (bool) ($user->forgot_password_enabled ?? false),
            ];
        })->toArray();
    }

    private function resolveSidebarUser(Request $request): array
    {
        $user = $request->session()->get('auth_user', []);

        if (($user['role'] ?? null) !== 'participant') {
            return $user;
        }

        $query = User::query()->where('role', 'peserta');

        if (!empty($user['email'])) {
            $query->where('email', $user['email']);
        } elseif (!empty($user['username'])) {
            $query->where('username', $user['username']);
        }

        $dbUser = $query->first();
        if (!$dbUser) {
            return $user;
        }

        $normalizedStatus = $this->normalizeParticipantStatus((string) ($dbUser->status ?? 'active'));
        $sidebarEmail = (string) ($dbUser->email ?? ($user['email'] ?? '-'));

        if (!empty($dbUser->group_name) && Schema::hasTable('registration_groups')) {
            $registrationGroup = RegistrationGroup::query()
                ->where('nama_lembaga', (string) $dbUser->group_name)
                ->orderByDesc('created_at')
                ->first();

            if ($registrationGroup && !empty($registrationGroup->email_pic)) {
                $sidebarEmail = (string) $registrationGroup->email_pic;
            }
        }

        $user['status'] = $normalizedStatus;
        $user['sidebar_role_label'] = $this->buildParticipantRoleLabel($normalizedStatus);
        $user['sidebar_email'] = $sidebarEmail;
        $request->session()->put('auth_user', $user);

        return $user;
    }

    private function resolveParticipantAccountUser(Request $request): User
    {
        $authUser = $request->session()->get('auth_user', []);
        $query = User::query()->where('role', 'peserta');

        if (!empty($authUser['email'])) {
            $query->where('email', $authUser['email']);
        } elseif (!empty($authUser['username'])) {
            $query->where('username', $authUser['username']);
        }

        $dbUser = $query->first();
        if ($dbUser) {
            return $dbUser;
        }

        $fallbackUser = new User();
        $fallbackUser->id = 0;
        $fallbackUser->name = (string) ($authUser['name'] ?? 'Peserta Batik');
        $fallbackUser->email = (string) ($authUser['email'] ?? 'peserta@lms-batik.local');
        $fallbackUser->role = 'peserta';

        return $fallbackUser;
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

    /**
     * @return array<int, array<string, string>>
     */
    private function getManagerPendingIndividualValidations(): array
    {
        $registrations = Schema::hasTable('registration_individuals')
            ? RegistrationIndividual::where('status', 'pending')->orderByDesc('created_at')->get()
            : collect();

        return $registrations->map(function ($reg) {
            return [
                'id' => 'individual-' . $reg->id,
                'registration_date' => $reg->created_at->format('Y-m-d'),
                'name' => $reg->nama_lengkap,
                'email' => $reg->email,
                'phone' => $reg->no_handphone,
                'address' => $reg->alamat,
                'education' => $reg->pendidikan_terakhir,
                'motivation' => $reg->motivasi,
                'program' => 'Program Individu',
                'whatsapp' => $reg->no_handphone,
            ];
        })->toArray();
    }

    /**
     * @return array<int, array<string, string|int>>
     */
    private function getManagerGroupParticipants(): array
    {
        if (!Schema::hasTable('registration_groups')) {
            return [];
        }

        return RegistrationGroup::query()
            ->whereNotIn('status', ['pending', 'rejected'])
            ->orderByDesc('created_at')
            ->get()
            ->map(function (RegistrationGroup $registration): array {
                $groupUsers = User::query()
                    ->where('role', 'peserta')
                    ->where('group_name', $registration->nama_lembaga)
                    ->orderBy('username')
                    ->get();
                $detailPerPage = 5;
                $detailPageName = 'group_' . $registration->id . '_page';
                $detailCurrentPage = max(1, (int) request()->query($detailPageName, 1));
                $detailItems = $groupUsers->slice(($detailCurrentPage - 1) * $detailPerPage, $detailPerPage)->values();
                $participantCredentialsPaginator = new LengthAwarePaginator(
                    $detailItems,
                    $groupUsers->count(),
                    $detailPerPage,
                    $detailCurrentPage,
                    [
                        'path' => request()->url(),
                        'query' => request()->query(),
                        'pageName' => $detailPageName,
                    ]
                );

                $status = User::query()
                    ->where('role', 'peserta')
                    ->where('group_name', $registration->nama_lembaga)
                    ->pluck('status')
                    ->map(fn ($value) => $this->normalizeParticipantStatus((string) $value))
                    ->countBy()
                    ->sortDesc()
                    ->keys()
                    ->first() ?? 'active';

                $passwordChangedCount = $groupUsers->where('password_changed', true)->count();
                $passwordTotal = $groupUsers->count();

                return [
                    'id' => 'group-' . $registration->id,
                    'group_name' => $registration->nama_lembaga,
                    'pic_name' => $registration->nama_pic,
                    'pic_phone' => $registration->no_handphone_pic,
                    'members' => (int) $registration->jumlah_peserta,
                    'program' => 'Program Kelompok',
                    'status' => $status,
                    'status_label' => $this->buildParticipantRoleLabel($status),
                    'password_changed_count' => $passwordChangedCount,
                    'password_total' => $passwordTotal,
                    'password_change_summary' => $passwordTotal > 0
                        ? $passwordChangedCount . '/' . $passwordTotal . ' peserta sudah mengganti password'
                        : 'Belum ada peserta aktif',
                    'forgot_password_enabled' => $groupUsers->isNotEmpty()
                        && $groupUsers->every(fn (User $user): bool => (bool) ($user->forgot_password_enabled ?? false)),
                    'participant_credentials' => $groupUsers->map(function (User $user): array {
                        return [
                            'username' => (string) ($user->username ?? '-'),
                            'current_password' => (string) ($user->current_password ?? ''),
                            'password_changed' => (bool) ($user->password_changed ?? false),
                            'password_status' => (bool) ($user->password_changed ?? false) ? 'Sudah diubah' : 'Belum diubah',
                            'forgot_password_enabled' => (bool) ($user->forgot_password_enabled ?? false),
                        ];
                    })->toArray(),
                    'participant_credentials_paginator' => $participantCredentialsPaginator,
                ];
            })
            ->toArray();
    }

    /**
     * @return array<int, array<string, string|int>>
     */
    private function getManagerPendingGroupValidations(): array
    {
        $registrations = Schema::hasTable('registration_groups')
            ? RegistrationGroup::where('status', 'pending')->orderByDesc('created_at')->get()
            : collect();

        return $registrations->map(function ($reg) {
            return [
                'id' => 'group-' . $reg->id,
                'registration_date' => $reg->created_at->format('Y-m-d'),
                'group_name' => $reg->nama_lembaga,
                'pic_name' => $reg->nama_pic,
                'pic_email' => $reg->email_pic,
                'pic_phone' => $reg->no_handphone_pic,
                'pic_address' => $reg->alamat_pic,
                'members' => $reg->jumlah_peserta,
                'official_letter' => $reg->surat_resmi ? basename($reg->surat_resmi) : null,
                'official_letter_path' => $reg->surat_resmi ? $reg->surat_resmi : null,
                'program' => 'Program Kelompok',
            ];
        })->toArray();
    }

    private function resolveForgotPasswordWhatsappNumber(User $user): string
    {
        $isGroupParticipant = !empty($user->group_name);
        $candidatePhone = $isGroupParticipant
            ? (string) ($user->personal_phone ?? '')
            : (string) ($user->phone ?? '');

        return $this->normalizeWhatsappNumber($candidatePhone);
    }

    private function maskPhoneNumber(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if (strlen($digits) <= 6) {
            return $digits;
        }

        return substr($digits, 0, 3) . str_repeat('*', max(0, strlen($digits) - 6)) . substr($digits, -3);
    }

    /**
     * @return array<int, array<string, string|int>>
     */
    private function getManagerInstructors(): array
    {
        if (!Schema::hasTable('users')) {
            return [];
        }

        return User::where('role', 'pengajar')
            ->orderBy('name')
            ->get()
            ->map(fn (User $user) => $this->mapManagerInstructor($user))
            ->all();
    }

        private function getSessionRoleFromDbRole(string $role): string
    {
        return match ($role) {
            'pengajar' => 'instructor',
            'pengelola' => 'manager',
            default => 'participant',
        };
    }

    private function mapManagerInstructor(User $user, array $passwordCache = []): array
    {
        return [
            'id' => $user->username ?: 'instr-' . $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'password' => $passwordCache[$user->username] ?? null,
            'email' => $user->email,
            'phone' => $user->phone ?? '',
            'address' => $user->address ?? '',
            'education' => $user->education ?? '',
            'salary' => (int) ($user->salary ?? 0),
            'certificate' => $user->certificate,
            'status' => $user->status ?? 'Aktif',
        ];
    }

    /**
     * @return array<int, array<string, string|null>>
     */
    private function getManagerManagedInstructors(Request $request): array
    {
        $passwordCache = $request->session()->get('manager_instructor_passwords', []);

        if (!Schema::hasTable('users')) {
            return [];
        }

        $dbInstructors = User::where('role', 'pengajar')
            ->orderBy('name')
            ->get();

        return $dbInstructors
            ->map(fn (User $user) => $this->mapManagerInstructor($user, $passwordCache))
            ->all();
    }

    /**
     * @return array<int, array<string, string|int>>
     */
    /**
     * @return array<string, int|string>
     */
    /**
     * Get monthly report data with all required fields
     */
    private function getMonthlyReportData(int $month, int $year): array
    {
        $monthIndividual = [];
        $monthGroup = [];
        $registrationByDate = [];
        $programRates = $this->getReportProgramRates();
        $individualCost = (int) $programRates['individual_cost'];
        $groupCost = (int) $programRates['group_cost'];
        $individualProgramName = (string) $programRates['individual_program_name'];
        $groupProgramName = (string) $programRates['group_program_name'];

        if (Schema::hasTable('registration_individuals')) {
            $individualRegistrations = RegistrationIndividual::query()
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->orderBy('created_at')
                ->get();

            $monthIndividual = $individualRegistrations->map(function (RegistrationIndividual $registration) use (&$registrationByDate, $individualCost, $individualProgramName): array {
                $registrationDate = optional($registration->created_at)->format('Y-m-d') ?? '';
                if ($registrationDate !== '') {
                    $registrationByDate[$registrationDate] = ($registrationByDate[$registrationDate] ?? 0) + 1;
                }

                return [
                    'id' => 'individual-' . $registration->id,
                    'name' => (string) $registration->nama_lengkap,
                    'program' => $individualProgramName,
                    'registration_date' => $registrationDate,
                    'status' => $this->formatReportStatus((string) ($registration->status ?? 'pending')),
                    'cost' => $individualCost,
                ];
            })->all();
        }

        if (Schema::hasTable('registration_groups')) {
            $groupRegistrations = RegistrationGroup::query()
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->orderBy('created_at')
                ->get();

            $monthGroup = $groupRegistrations->map(function (RegistrationGroup $registration) use (&$registrationByDate, $groupCost, $groupProgramName): array {
                $registrationDate = optional($registration->created_at)->format('Y-m-d') ?? '';
                if ($registrationDate !== '') {
                    $registrationByDate[$registrationDate] = ($registrationByDate[$registrationDate] ?? 0) + (int) $registration->jumlah_peserta;
                }

                return [
                    'id' => 'group-' . $registration->id,
                    'group_name' => (string) $registration->nama_lembaga,
                    'members' => (int) $registration->jumlah_peserta,
                    'program' => $groupProgramName,
                    'status' => $this->formatReportStatus((string) ($registration->status ?? 'pending')),
                    'registration_date' => $registrationDate,
                    'cost' => $groupCost,
                ];
            })->all();
        }

        $totalIndividual = count($monthIndividual);
        $totalGroup = count($monthGroup);
        $totalGroupMembers = array_sum(array_column($monthGroup, 'members'));
        $totalParticipants = $totalIndividual + $totalGroupMembers;
        $totalProfit = ($totalIndividual * $individualCost) + ($totalGroup * $groupCost);
        $instructorSalaries = $this->getReportInstructorSalaries();
        $totalExpenditure = array_sum(array_column($instructorSalaries, 'salary'));
        $totalOverall = $totalProfit - $totalExpenditure;

        $peakDate = 'Tidak ada data tersedia';
        $peakCount = 0;
        foreach ($registrationByDate as $date => $count) {
            if ($count > $peakCount) {
                $peakCount = $count;
                $peakDate = Carbon::createFromFormat('Y-m-d', $date)->format('d M Y');
            }
        }

        return [
            'month' => $month,
            'year' => $year,
            'month_name' => $this->getMonthName($month),
            'total_individual_registrations' => $totalIndividual,
            'total_group_registrations' => $totalGroup,
            'total_group_members' => $totalGroupMembers,
            'total_participants' => $totalParticipants,
            'individual_cost' => $individualCost,
            'group_cost' => $groupCost,
            'total_cost' => $totalIndividual * $individualCost,
            'group_income' => $totalGroup * $groupCost,
            'total_profit' => $totalProfit,
            'total_expenditure' => $totalExpenditure,
            'total_overall' => $totalOverall,
            'peak_registration_date' => $peakDate,
            'instructor_salaries' => $instructorSalaries,
            'individual_participants' => $monthIndividual,
            'group_participants' => $monthGroup,
        ];
    }

    /**
     * Resolve report program rates from database with safe fallbacks.
     *
     * @return array<string, int|string>
     */
    private function getReportProgramRates(): array
    {
        $fallback = [
            'individual_cost' => 125000,
            'group_cost' => 100000,
            'individual_program_name' => 'Program Individu',
            'group_program_name' => 'Program Kelompok',
        ];

        if (!Schema::hasTable('programs')) {
            return $fallback;
        }

        $programs = Program::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->get();

        if ($programs->isEmpty()) {
            $programs = Program::query()
                ->orderBy('id')
                ->get();
        }

        if ($programs->isEmpty()) {
            return $fallback;
        }

        $individualProgram = $programs->first(function (Program $program): bool {
            return str_contains(Str::lower((string) $program->name), 'individu');
        }) ?: $programs->first(function (Program $program): bool {
            return str_contains(Str::lower((string) $program->fee_unit), 'peserta');
        });

        $groupProgram = $programs->first(function (Program $program): bool {
            $name = Str::lower((string) $program->name);
            return str_contains($name, 'kelompok') || str_contains($name, 'group');
        }) ?: $programs->first(function (Program $program): bool {
            $unit = Str::lower((string) $program->fee_unit);
            return str_contains($unit, 'lembaga') || str_contains($unit, 'kelompok') || str_contains($unit, 'grup');
        });

        $individualCost = $individualProgram ? (int) round((float) $individualProgram->fee_amount) : (int) $fallback['individual_cost'];
        $groupCost = $groupProgram ? (int) round((float) $groupProgram->fee_amount) : (int) $fallback['group_cost'];

        return [
            'individual_cost' => $individualCost,
            'group_cost' => $groupCost,
            'individual_program_name' => $individualProgram ? (string) $individualProgram->name : (string) $fallback['individual_program_name'],
            'group_program_name' => $groupProgram ? (string) $groupProgram->name : (string) $fallback['group_program_name'],
        ];
    }

    /**
     * @return array<int, array<string, string|int>>
     */
    private function getReportInstructorSalaries(): array
    {
        if (!Schema::hasTable('users')) {
            return [];
        }

        return User::query()
            ->where('role', 'pengajar')
            ->orderBy('name')
            ->get()
            ->map(function (User $user): array {
                $isActive = in_array(strtolower(trim((string) ($user->status ?? 'Aktif'))), ['aktif', 'active'], true);
                $salary = (int) ($user->salary ?? 0);
                $date = optional($user->created_at)->format('d M Y') ?? '-';

                return [
                    'type' => 'Pengajar',
                    'name' => $user->name,
                    'date' => $date,
                    'status' => $isActive ? 'Aktif' : 'Tidak Aktif',
                    'salary' => $salary,
                ];
            })
            ->all();
    }

    /**
     * Get all months data for a specific year
     */
    private function getAllMonthsData(int $year): array
    {
        $monthsData = [];
        for ($month = 1; $month <= 12; $month++) {
            $data = $this->getMonthlyReportData($month, $year);
            $monthsData[] = [
                'month' => $month,
                'month_name' => $this->getMonthName($month),
                'total_registrations' => $data['total_participants'],
                'total_profit' => $data['total_profit'],
            ];
        }

        $hasYearData = (int) array_sum(array_column($monthsData, 'total_registrations')) > 0;
        if (!$hasYearData) {
            return array_map(function (array $monthData): array {
                $monthData['total_registrations'] = null;
                $monthData['total_profit'] = null;

                return $monthData;
            }, $monthsData);
        }

        return $monthsData;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildMonthlyReportExportData(int $month, int $year): array
    {
        $monthlyData = $this->getMonthlyReportData($month, $year);

        $individualRows = array_map(function (array $participant) use ($monthlyData): array {
            return [
                'type' => 'Individu',
                'name' => (string) ($participant['name'] ?? '-'),
                'program' => (string) ($participant['program'] ?? '-'),
                'date' => (string) ($participant['registration_date'] ?? '-'),
                'members' => '1',
                'status' => (string) ($participant['status'] ?? '-'),
                'revenue' => (int) ($participant['cost'] ?? $monthlyData['individual_cost']),
            ];
        }, $monthlyData['individual_participants']);

        $groupRows = array_map(function (array $group) use ($monthlyData): array {
            $members = (int) ($group['members'] ?? 0);
            return [
                'type' => 'Kelompok',
                'name' => (string) ($group['group_name'] ?? '-'),
                'program' => (string) ($group['program'] ?? '-'),
                'date' => (string) ($group['registration_date'] ?? '-'),
                'members' => (string) $members,
                'status' => (string) ($group['status'] ?? '-'),
                'revenue' => (int) ($group['cost'] ?? $monthlyData['group_cost']),
            ];
        }, $monthlyData['group_participants']);

        return [
            'mode' => 'monthly',
            'title' => 'Laporan Bulan ' . $monthlyData['month_name'] . ' ' . $monthlyData['year'],
            'summary' => [
                'Pendaftaran Individu' => $monthlyData['total_individual_registrations'],
                'Pendaftaran Kelompok' => $monthlyData['total_group_registrations'],
                'Total Pendapatan' => (int) $monthlyData['total_profit'],
                'Total Pengeluaran' => (int) $monthlyData['total_expenditure'],
                'Total Keseluruhan' => (int) $monthlyData['total_overall'],
                'Tanggal Pendaftaran Tertinggi' => $monthlyData['peak_registration_date'],
            ],
            'rows' => array_merge($individualRows, $groupRows),
            'salary_rows' => $monthlyData['instructor_salaries'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildAnnualReportExportData(int $year): array
    {
        $monthlyRows = [];
        $totalIndividual = 0;
        $totalGroup = 0;
        $totalRevenue = 0;
        $totalExpenditure = 0;
        $totalOverall = 0;
        $peakMonth = '-';
        $peakRevenue = 0;
        $hasData = false;

        foreach ($this->getAllMonthsData($year) as $monthData) {
            $monthNumber = (int) ($monthData['month'] ?? 0);
            $monthlyBreakdown = $this->getMonthlyReportData($monthNumber, $year);
            $totalIndividual += (int) $monthlyBreakdown['total_individual_registrations'];
            $totalGroup += (int) $monthlyBreakdown['total_group_registrations'];
            $totalRevenue += (int) $monthlyBreakdown['total_profit'];
            $totalExpenditure += (int) $monthlyBreakdown['total_expenditure'];
            $totalOverall += (int) $monthlyBreakdown['total_overall'];
            if ((int) ($monthData['total_registrations'] ?? 0) > 0) {
                $hasData = true;
            }

            if ((int) $monthData['total_profit'] > $peakRevenue) {
                $peakRevenue = (int) $monthData['total_profit'];
                $peakMonth = $monthData['month_name'];
            }

            $monthlyRows[] = [
                'month' => $monthData['month_name'],
                'registrations' => (int) $monthData['total_registrations'],
                'revenue' => (int) $monthData['total_profit'],
            ];
        }

        return [
            'mode' => 'annual',
            'title' => 'Laporan Tahunan ' . $year,
            'summary' => [
                'Pendaftaran Individu' => $totalIndividual,
                'Pendaftaran Kelompok' => $totalGroup,
                'Total Pendapatan' => $totalRevenue,
                'Total Pengeluaran' => $totalExpenditure,
                'Total Keseluruhan' => $totalOverall,
                'Tanggal Pendaftaran Tertinggi' => $peakMonth,
            ],
            'rows' => $hasData ? $monthlyRows : [],
        ];
    }

    /**
     * @param array<string, mixed> $reportData
     */
    private function buildReportCsv(array $reportData): string
    {
        $stream = fopen('php://temp', 'r+');
        fputcsv($stream, [$reportData['title']]);
        fputcsv($stream, []);
        fputcsv($stream, ['Ringkasan']);

        foreach ($reportData['summary'] as $label => $value) {
            fputcsv($stream, [$label, is_int($value) ? number_format($value, 0, ',', '.') : (string) $value]);
        }

        fputcsv($stream, []);
        fputcsv($stream, ['Data']);

        if (($reportData['mode'] ?? '') === 'annual') {
            fputcsv($stream, ['Bulan', 'Total Pendaftaran', 'Total Pendapatan']);
            foreach ($reportData['rows'] as $row) {
                fputcsv($stream, [
                    $row['month'],
                    $row['registrations'],
                    $row['revenue'],
                ]);
            }
        } else {
            fputcsv($stream, ['Tipe', 'Nama', 'Program', 'Tanggal', 'Jumlah Anggota', 'Status', 'Pendapatan']);
            foreach ($reportData['rows'] as $row) {
                fputcsv($stream, [
                    $row['type'],
                    $row['name'],
                    $row['program'],
                    $row['date'],
                    $row['members'],
                    $row['status'],
                    $row['revenue'],
                ]);
            }

            if (!empty($reportData['salary_rows']) && is_array($reportData['salary_rows'])) {
                fputcsv($stream, []);
                fputcsv($stream, ['Laporan Pengeluaran Gaji Pengajar']);
                fputcsv($stream, ['Tipe', 'Nama Pengajar', 'Tanggal', 'Status', 'Gaji']);
                foreach ($reportData['salary_rows'] as $row) {
                    fputcsv($stream, [
                        $row['type'] ?? '-',
                        $row['name'] ?? '-',
                        $row['date'] ?? '-',
                        $row['status'] ?? '-',
                        $row['salary'] ?? 0,
                    ]);
                }
            }
        }

        rewind($stream);
        $csv = stream_get_contents($stream) ?: '';
        fclose($stream);

        return "\xEF\xBB\xBF" . $csv;
    }

    /**
     * @param array<int, array{member_no:int, username:string, password:string}> $credentials
     */
    private function buildGroupCredentialExportCsv(string $picName, array $credentials): string
    {
        $stream = fopen('php://temp', 'r+');
        fputcsv($stream, ['PIC Name', 'Participant Username', 'Participant Password']);

        foreach ($credentials as $credential) {
            fputcsv($stream, [
                $picName,
                (string) ($credential['username'] ?? ''),
                (string) ($credential['password'] ?? ''),
            ]);
        }

        rewind($stream);
        $csv = stream_get_contents($stream) ?: '';
        fclose($stream);

        return "\xEF\xBB\xBF" . $csv;
    }

    /**
     * @param array<int, User> $groupUsers
     */
    private function buildGroupDataExportCsv(RegistrationGroup $registration, array $groupUsers): string
    {
        $picEmail = (string) ($registration->email_pic ?? '');

        $stream = fopen('php://temp', 'r+');
        fputcsv($stream, [
            'Group Name',
            'PIC Name',
            'PIC Email',
            'PIC Phone',
            'Member Username',
            'Current Password',
            'Password Changed',
            'Forgot Password Enabled',
            'Member Status',
        ]);

        foreach ($groupUsers as $user) {
            fputcsv($stream, [
                (string) $registration->nama_lembaga,
                (string) $registration->nama_pic,
                $picEmail,
                (string) $registration->no_handphone_pic,
                (string) ($user->username ?? ''),
                (string) ($user->current_password ?? ''),
                (bool) ($user->password_changed ?? false) ? 'Yes' : 'No',
                (bool) ($user->forgot_password_enabled ?? false) ? 'Yes' : 'No',
                (string) ($user->status ?? 'active'),
            ]);
        }

        rewind($stream);
        $csv = stream_get_contents($stream) ?: '';
        fclose($stream);

        return "\xEF\xBB\xBF" . $csv;
    }

    /**
     * @param array<string, mixed> $reportData
     */
    private function buildFallbackReportPdf(array $reportData): string
    {
        $lines = [];
        $branding = $reportData['branding'] ?? [];

        $lines[] = (string) ($branding['organization_name'] ?? 'LPK Kama Praja Madiun');
        $lines[] = 'Alamat: ' . (string) ($branding['address'] ?? '-');
        $lines[] = 'No. Telepon: ' . (string) ($branding['phone'] ?? '-');
        $lines[] = '';
        $lines[] = (string) ($reportData['title'] ?? 'Laporan');
        $lines[] = '';
        $lines[] = 'Ringkasan';

        foreach (($reportData['summary'] ?? []) as $label => $value) {
            $lines[] = $label . ': ' . (is_int($value) ? number_format($value, 0, ',', '.') : (string) $value);
        }

        $lines[] = '';
        $lines[] = 'Data';

        if (empty($reportData['rows'])) {
            $lines[] = 'Tidak ada data tersedia.';
        } elseif (($reportData['mode'] ?? '') === 'annual') {
            foreach ($reportData['rows'] as $row) {
                $lines[] = ($row['month'] ?? '-') . ' | Pendaftaran: ' . number_format((int) ($row['registrations'] ?? 0), 0, ',', '.')
                    . ' | Pendapatan: Rp ' . number_format((int) ($row['revenue'] ?? 0), 0, ',', '.');
            }
        } else {
            foreach ($reportData['rows'] as $row) {
                $lines[] = ($row['type'] ?? '-') . ' | ' . ($row['name'] ?? '-') . ' | ' . ($row['program'] ?? '-')
                    . ' | ' . ($row['date'] ?? '-') . ' | ' . ($row['status'] ?? '-') . ' | Rp '
                    . number_format((int) ($row['revenue'] ?? 0), 0, ',', '.');
            }
        }

        return $this->createSimplePdf($lines);
    }

    /**
     * @param array<int, string> $lines
     */
    private function createSimplePdf(array $lines): string
    {
        $escapedLines = array_map(fn (string $line): string => $this->escapePdfText($line), $lines);

        $content = "BT\n/F1 11 Tf\n50 800 Td\n";
        foreach ($escapedLines as $index => $line) {
            if ($index > 0) {
                $content .= "T*\n";
            }

            $content .= '(' . $line . ') Tj\n';
        }
        $content .= "ET";

        $objects = [];
        $objects[] = "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n";
        $objects[] = "2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n";
        $objects[] = "3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>\nendobj\n";
        $objects[] = "4 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\n";
        $objects[] = '5 0 obj' . "\n<< /Length " . strlen($content) . " >>\nstream\n" . $content . "\nendstream\nendobj\n";

        $pdf = "%PDF-1.4\n";
        $offsets = [0];

        foreach ($objects as $object) {
            $offsets[] = strlen($pdf);
            $pdf .= $object;
        }

        $xrefPosition = strlen($pdf);
        $pdf .= "xref\n0 6\n";
        $pdf .= "0000000000 65535 f \n";

        for ($i = 1; $i <= 5; $i++) {
            $pdf .= sprintf('%010d 00000 n ', $offsets[$i]) . "\n";
        }

        $pdf .= "trailer\n<< /Size 6 /Root 1 0 R >>\nstartxref\n" . $xrefPosition . "\n%%EOF";

        return $pdf;
    }

    private function escapePdfText(string $text): string
    {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    }



    /**
     * Get available years (current year and 2 future years)
     */
    private function getAvailableYears(): array
    {
        $currentYear = now()->year;
        $startYear = $currentYear - 5;
        $endYear = $currentYear + 5;
        $years = range($startYear, $endYear);

        $dbYears = collect();

        if (Schema::hasTable('registration_individuals')) {
            $dbYears = $dbYears->merge(
                RegistrationIndividual::query()
                    ->selectRaw('YEAR(created_at) as year_value')
                    ->pluck('year_value')
                    ->filter()
                    ->map(fn ($value) => (int) $value)
            );
        }

        if (Schema::hasTable('registration_groups')) {
            $dbYears = $dbYears->merge(
                RegistrationGroup::query()
                    ->selectRaw('YEAR(created_at) as year_value')
                    ->pluck('year_value')
                    ->filter()
                    ->map(fn ($value) => (int) $value)
            );
        }

        return $dbYears
            ->merge($years)
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    /**
     * Parse date string format YYYY-MM-DD
     */
    private function parseDate(string $date): array
    {
        $parts = explode('-', $date);
        return [
            'year' => (int) ($parts[0] ?? now()->year),
            'month' => (int) ($parts[1] ?? now()->month),
            'day' => (int) ($parts[2] ?? 1),
        ];
    }

    /**
     * Get month name in Indonesian
     */
    private function getMonthName(int $month): string
    {
        $months = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];
        return $months[$month] ?? 'Invalid';
    }

    /**
     * Get enhanced individual participants with registration dates and costs
     */
    private function getEnhancedIndividualParticipants(): array
    {
        if (!Schema::hasTable('registration_individuals')) {
            return [];
        }

        return RegistrationIndividual::query()
            ->orderByDesc('created_at')
            ->get()
            ->map(function (RegistrationIndividual $registration): array {
                return [
                    'id' => 'individual-' . $registration->id,
                    'name' => (string) $registration->nama_lengkap,
                    'program' => 'Program Individu',
                    'status' => $this->formatReportStatus((string) ($registration->status ?? 'pending')),
                    'registration_date' => optional($registration->created_at)->format('Y-m-d') ?? '',
                    'cost' => 125000,
                ];
            })
            ->all();
    }

    /**
     * Get enhanced group participants with registration dates and costs
     */
    private function getEnhancedGroupParticipants(): array
    {
        if (!Schema::hasTable('registration_groups')) {
            return [];
        }

        return RegistrationGroup::query()
            ->orderByDesc('created_at')
            ->get()
            ->map(function (RegistrationGroup $registration): array {
                return [
                    'id' => 'group-' . $registration->id,
                    'group_name' => (string) $registration->nama_lembaga,
                    'members' => (int) $registration->jumlah_peserta,
                    'program' => 'Program Kelompok',
                    'status' => $this->formatReportStatus((string) ($registration->status ?? 'pending')),
                    'registration_date' => optional($registration->created_at)->format('Y-m-d') ?? '',
                    'cost' => 100000,
                ];
            })
            ->all();
    }

    /**
     * @return array<string, string>
     */
    private function getManagerSettingsData(): array
    {
        return [
            'organization_name' => 'LPK Kama Praja Madiun',
            'organization_address' => 'Kantor LPK Kama Praja Madiun',
            'support_email' => 'support@lmsbatik.test',
            'timezone' => 'Asia/Jakarta',
            'logo_filename' => 'komunitasbatik.png',
            'logo_fit' => 'contain',
            'contacts' => [
                'googlemaps' => 'https://maps.google.com/?q=LPK+Kama+Praja+Madiun',
                'whatsapp' => '+6287876543210',
                'facebook' => 'https://facebook.com/kamapraja.madiun',
                'instagram' => 'https://instagram.com/kamapraja.madiun',
                'youtube' => 'https://youtube.com/@kamapraja.madiun',
            ],
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
            'profile' => [
                'title' => 'Profil Pengguna - Pengajar',
                'subtitle' => 'Lihat dan perbarui data profil pengajar.',
            ],
        ];

        $selected = $config[$activePage] ?? $config['home'];

        return [
            ...$selected,
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
        ];
    }

    /**
     * @return array<string, int>
     */
    private function getInstructorDashboardSummary(): array
    {
        return [
            'activeParticipants' => $this->getInstructorHomeParticipantsCount(),
            'pendingReviews' => $this->getInstructorPendingWorksCount(),
            'totalModules' => $this->getInstructorModulesCount(),
        ];
    }

    private function getInstructorHomeParticipantsCount(): int
    {
        if (!Schema::hasTable('users')) {
            return 0;
        }

        return User::query()
            ->where('role', 'peserta')
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhereIn('status', ['active', 'Aktif']);
            })
            ->count();
    }

    private function getInstructorPendingWorksCount(): int
    {
        if (!Schema::hasTable('participant_assignments')) {
            return 0;
        }

        return ParticipantAssignment::query()
            ->whereNull('graded_at')
            ->count();
    }

    private function getInstructorModulesCount(): int
    {
        if (!Schema::hasTable('modules')) {
            return 0;
        }

        return Module::query()->count();
    }

    private function formatInstructorStatus(string $status): string
    {
        $normalized = strtolower(trim($status));

        return match ($normalized) {
            'aktif', 'active' => 'Aktif',
            'nonaktif', 'non-active', 'non active' => 'Nonaktif',
            'lulus', 'graduated' => 'Lulus',
            default => ucfirst($status),
        };
    }

    private function formatInstructorModuleStatus(string $status): string
    {
        $normalized = strtolower(trim($status));

        return match ($normalized) {
            'aktif', 'active' => 'Aktif',
            'revisi', 'revision', 'perlu revisi' => 'Perlu Revisi',
            'nonaktif', 'non-active', 'non active' => 'Nonaktif',
            default => ucfirst($status),
        };
    }

    private function formatReportStatus(string $status): string
    {
        $normalized = strtolower(trim($status));

        return match ($normalized) {
            'pending', 'menunggu' => 'Pending',
            'approved', 'disetujui' => 'Disetujui',
            'rejected', 'ditolak' => 'Ditolak',
            default => $status !== '' ? ucfirst($status) : '-',
        };
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function getInstructorHomeParticipants(): array
    {
        if (!Schema::hasTable('users')) {
            return [];
        }

        return User::query()
            ->where('role', 'peserta')
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhereIn('status', ['active', 'Aktif']);
            })
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get()
            ->map(function (User $user): array {
                $latestProgress = ParticipantProgress::query()
                    ->with('module')
                    ->where('user_id', $user->id)
                    ->latest('updated_at')
                    ->first();

                return [
                    'id' => $user->username ?: 'p-' . $user->id,
                    'name' => $user->name,
                    'program_type' => $latestProgress?->module?->title ?: ($user->group_name ?: 'Program Individu'),
                    'status' => $this->formatInstructorStatus((string) ($user->status ?? 'Aktif')),
                ];
            })
            ->toArray();
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function getInstructorHomeModules(): array
    {
        if (!Schema::hasTable('modules')) {
            return [];
        }

        return Module::query()
            ->latest('updated_at')
            ->limit(4)
            ->get()
            ->map(function (Module $module): array {
                return [
                    'id' => 'm-' . $module->id,
                    'title' => $module->title,
                    'summary' => Str::limit((string) ($module->description ?? ''), 120),
                    'status' => $this->formatInstructorModuleStatus((string) ($module->status ?? 'Aktif')),
                ];
            })
            ->toArray();
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function getInstructorPendingWorks(): array
    {
        if (!Schema::hasTable('participant_assignments')) {
            return [];
        }

        return ParticipantAssignment::query()
            ->with(['user', 'module'])
            ->whereNull('graded_at')
            ->latest('submitted_at')
            ->limit(5)
            ->get()
            ->map(function (ParticipantAssignment $assignment): array {
                return [
                    'id' => 's-' . $assignment->id,
                    'participant' => $assignment->user?->name ?? 'Peserta',
                    'module' => $assignment->module?->title ?? 'Modul',
                    'submitted_at' => $assignment->submitted_at?->format('d M Y, H:i') ?? '-',
                    'status' => 'Menunggu Penilaian',
                ];
            })
            ->toArray();
    }

    /**
     * @return array<int, array<string, string|int>>
     */
    private function getInstructorModules(): array
    {
        if (!Schema::hasTable('modules')) {
            return [];
        }

        return Module::query()
            ->latest('updated_at')
            ->limit(4)
            ->get()
            ->map(function (Module $module): array {
                $lessonCount = $module->materials()->count();

                return [
                    'id' => 'm-' . $module->id,
                    'title' => $module->title,
                    'category' => 'Modul',
                    'lessons' => $lessonCount,
                    'participants' => (int) ($module->participants_count ?? 0),
                    'status' => $this->formatInstructorModuleStatus((string) ($module->status ?? 'Aktif')),
                    'updated_at' => optional($module->updated_at)->format('d M Y') ?? '-',
                ];
            })
            ->toArray();
    }

    /**
     * @return array<int, array<string, string|int>>
     */
    private function getInstructorParticipants(): array
    {
        if (!Schema::hasTable('users')) {
            return [];
        }

        return User::query()
            ->where('role', 'peserta')
            ->orderByDesc('updated_at')
            ->limit(4)
            ->get()
            ->map(function (User $user): array {
                $latestProgress = ParticipantProgress::query()
                    ->where('user_id', $user->id)
                    ->latest('updated_at')
                    ->first();

                return [
                    'id' => $user->username ?: 'p-' . $user->id,
                    'name' => $user->name,
                    'batch' => $user->group_name ?: 'Batch Individu',
                    'progress' => $latestProgress?->progress_percentage ?? 0,
                    'last_activity' => $latestProgress?->updated_at?->diffForHumans() ?? 'Belum ada aktivitas',
                ];
            })
            ->toArray();
    }

    /**
     * @param array $items
     * @param int $currentPage
     * @param int $perPage
     * @return array<string, mixed>
     */
    private function paginateArray(array $items, int $currentPage = 1, int $perPage = 5): array
    {
        $total = count($items);
        $totalPages = (int) ceil($total / $perPage);
        $currentPage = max(1, min($currentPage, $totalPages ?: 1));
        $offset = ($currentPage - 1) * $perPage;

        return [
            'items' => array_slice($items, $offset, $perPage),
            'current_page' => $currentPage,
            'total_pages' => $totalPages,
            'total' => $total,
            'per_page' => $perPage,
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function getInstructorIndividualParticipants(): array
    {
        $participants = Schema::hasTable('users')
            ? User::where('role', 'peserta')
                ->where(function ($query) {
                    $query->whereNull('group_name')
                        ->orWhere('group_name', '');
                })
                ->get()
                ->sortBy(fn ($user) => $user->name, SORT_NATURAL | SORT_FLAG_CASE)
                ->values()
            : collect();

        return $participants->map(function ($user) {
            return [
                'id' => 'individual-' . $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'no_handphone' => $user->phone,
                'alamat' => $user->address,
                'pendidikan_terakhir' => $user->education,
                'motivasi' => $user->motivation,
            ];
        })->toArray();
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function getInstructorGroupParticipants(): array
    {
        $participants = Schema::hasTable('users')
            ? User::where('role', 'peserta')
                ->whereNotNull('group_name')
                ->where('group_name', '!=', '')
                ->get()
                ->sortBy(fn ($user) => $user->name, SORT_NATURAL | SORT_FLAG_CASE)
                ->values()
            : collect();

        return $participants->map(function ($user) {
            return [
                'id' => 'group-' . $user->id,
                'name' => $user->name,
                'group' => $user->group_name,
                'email' => $user->email,
                'no_handphone' => $user->phone,
                'alamat' => $user->address,
                'jumlah_peserta' => '',
                'surat_resmi' => '',
            ];
        })->toArray();
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
        $authUser = $request->session()->get('auth_user');

        if ($authUser && $authUser['role'] === 'participant') {
            $participantQuery = User::query()->where('role', 'peserta');
            if (!empty($authUser['id'])) {
                $participantQuery->where('id', (int) $authUser['id']);
            } else {
                $participantQuery->where('username', (string) ($authUser['username'] ?? ''));
            }

            $dbUser = $participantQuery->first();
            if ($dbUser && !$dbUser->password_changed) {
                return redirect()->route('dashboard.participant.profile')
                    ->with('force_password_change', true);
            }
        }

        Auth::logout();
        $request->session()->forget('auth_user');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Anda berhasil logout.');
    }
}
