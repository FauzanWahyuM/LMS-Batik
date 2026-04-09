<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\Artwork;
use App\Models\ParticipantAssignment;
use App\Models\RegistrationGroup;
use App\Models\RegistrationIndividual;
use App\Models\User;
use App\Services\ForumDiscussionService;
use App\Services\ParticipantModuleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Hardcoded users for temporary authentication simulation.
     *
      * @var array<string, array{name: string, email: string, password: string, role: string}>
     */

    /**
     * @return array<string, string>
     */
    private function getManagerProfileData(Request $request): array
    {
        $authUser = $request->session()->get('auth_user', []);
        $defaults = [
            'photo' => '',
            'full_name' => (string) ($authUser['name'] ?? 'Demo Manager'),
            'username' => (string) ($authUser['username'] ?? 'manager01'),
            'password' => 'manager123',
            'email' => (string) ($authUser['email'] ?? 'manager@lmsbatik.test'),
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
        $defaults = [
            'photo' => '',
            'full_name' => (string) ($authUser['name'] ?? 'Demo Instructor'),
            'username' => (string) ($authUser['username'] ?? 'instructor01'),
            'password' => 'instructor123',
            'email' => (string) ($authUser['email'] ?? 'instructor@lmsbatik.test'),
            'phone' => '081298765432',
            'address' => 'Jl. Batik Madiun No. 21',
            'role_label' => 'Pengajar',
        ];

        $sessionData = $request->session()->get('profile_instructor_data', []);

        if (!is_array($sessionData)) {
            return $defaults;
        }

        return array_merge($defaults, $sessionData);
    }

    /**
     * @return array<string, string>
     */
    private function getParticipantProfileData(Request $request): array
    {
        $authUser = $request->session()->get('auth_user', []);
        $defaults = [
            'photo' => '',
            'participant_type' => 'individual',
            'full_name' => (string) ($authUser['name'] ?? 'Demo Participant'),
            'username' => (string) ($authUser['username'] ?? 'participant01'),
            'email' => (string) ($authUser['email'] ?? 'participant@lmsbatik.test'),
            'phone' => '081300112233',
            'address' => 'Jl. Karya Batik No. 8',
            'motivation' => 'Ingin memperdalam teknik membatik untuk usaha mandiri.',
            'group_name' => '',
            'pic_name' => '',
            'role_label' => 'Peserta Individu',
        ];

        $sessionData = $request->session()->get('profile_participant_data', []);

        if (is_array($sessionData) && count($sessionData) > 0) {
            return array_merge($defaults, $sessionData);
        }

        $authUser = $request->session()->get('auth_user', []);
        $dbUser = null;

        if (!empty($authUser['email']) || !empty($authUser['username'])) {
            $query = User::query();
            if (!empty($authUser['email'])) {
                $query->where('email', $authUser['email']);
            }
            if (!empty($authUser['username'])) {
                $query->orWhere('username', $authUser['username']);
            }
            $dbUser = $query->where('role', 'peserta')->first();
        }

        if ($dbUser) {
            return array_merge($defaults, [
                'full_name' => $dbUser->name,
                'username' => $dbUser->username,
                'email' => $dbUser->email,
                'phone' => $dbUser->phone ?? '',
                'address' => $dbUser->address ?? '',
                'motivation' => $dbUser->role === 'peserta' ? ($dbUser->education ? '' : $defaults['motivation']) : $defaults['motivation'],
                'group_name' => '',
                'pic_name' => '',
                'role_label' => 'Peserta Individu',
            ]);
        }

        return $defaults;
    }

    private array $users = [
        'participant01' => [
            'name' => 'Demo Participant',
            'email' => 'participant@lmsbatik.test',
            'password' => 'participant123',
            'role' => 'participant',
        ],
        'instructor01' => [
            'name' => 'Demo Instructor',
            'email' => 'instructor@lmsbatik.test',
            'password' => 'instructor123',
            'role' => 'instructor',
        ],
        'manager01' => [
            'name' => 'Demo Manager',
            'email' => 'manager@lmsbatik.test',
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
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $username = strtolower(trim($credentials['username']));

        $dbUser = User::where('username', $username)
            ->orWhere('email', $username)
            ->first();

        if ($dbUser && Hash::check($credentials['password'], $dbUser->password)) {
            $request->session()->put('auth_user', [
                'name' => $dbUser->name,
                'username' => $dbUser->username ?: $dbUser->email,
                'email' => $dbUser->email,
                'role' => $this->getSessionRoleFromDbRole($dbUser->role),
            ]);

            $request->session()->regenerate();

            // Check if participant needs to change password
            if ($dbUser->role === 'peserta' && !$dbUser->password_changed) {
                return redirect()->route('dashboard.participant.profile')
                    ->with('force_password_change', true);
            }

            return redirect()->route('dashboard.index');
        }

        $user = $this->users[$username] ?? null;

        if ((! $user) || ($user['password'] !== $credentials['password'])) {
            return back()
                ->withErrors(['username' => 'Username atau password tidak valid.'])
                ->withInput($request->except('password'));
        }

        $request->session()->put('auth_user', [
            'name' => $user['name'],
            'username' => $username,
            'email' => $user['email'],
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

        $pendingParticipants = $this->getManagerPendingIndividualValidations();
        $managedParticipants = $this->getManagerIndividualParticipants();

        return view('dashboard.manager.participants-individual', [
            'user' => $request->session()->get('auth_user'),
            'dashboard' => $this->getManagerDashboardConfig('participants-individual'),
            'participants' => $managedParticipants,
            'pendingParticipants' => $pendingParticipants,
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

        if (User::where('email', $registration->email)->exists()) {
            return redirect()
                ->route('dashboard.manager.participants.individual')
                ->withErrors(['credential' => 'Email peserta sudah digunakan pada akun lain.']);
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

        User::create([
            'name' => $registration->nama_lengkap,
            'username' => $username,
            'email' => $registration->email,
            'password' => Hash::make($password),
            'role' => 'peserta',
            'phone' => $registration->no_handphone,
            'address' => $registration->alamat,
            'education' => $registration->pendidikan_terakhir,
            'status' => 'Aktif',
            'password_changed' => false,
        ]);

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

        $sentParticipantIds = $request->session()->get('manager_sent_individual_ids', []);
        if (!in_array($participant, $sentParticipantIds, true)) {
            $sentParticipantIds[] = $participant;
        }

        $request->session()->put('manager_sent_individual_ids', $sentParticipantIds);
        $request->session()->forget('manager_generated_credential');

        return redirect()
            ->route('dashboard.manager.participants.individual')
            ->with('status', 'Kredensial login awal berhasil dikirim via WhatsApp (simulasi). Peserta dipindahkan ke daftar kelola peserta.');
    }

    public function managerIndividualParticipantsUpdate(Request $request, string $participant): RedirectResponse
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(['Aktif', 'Lulus', 'Nonaktif'])],
        ]);

        $status = $validated['status'];

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
        $user->save();

        return redirect()
            ->route('dashboard.manager.participants.individual')
            ->with('status', 'Status peserta individu ' . $user->name . ' berhasil diperbarui menjadi ' . $status . '.');
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

        $managedGroups = $this->getManagerGroupParticipants();
        $managedGroupIds = array_column($managedGroups, 'id');

        foreach ($this->getManagerPendingGroupValidations() as $group) {
            if (in_array($group['id'], $sentGroupIds, true) && !in_array($group['id'], $managedGroupIds, true)) {
                $managedGroups[] = [
                    'id' => $group['id'],
                    'group_name' => $group['group_name'],
                    'members' => $group['members'],
                    'program' => $group['program'],
                    'status' => 'Aktif',
                ];
            }
        }

        return view('dashboard.manager.participants-group', [
            'user' => $request->session()->get('auth_user'),
            'dashboard' => $this->getManagerDashboardConfig('participants-group'),
            'groups' => $managedGroups,
            'pendingGroups' => $pendingGroups,
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
        $prefix = strtolower(Str::slug($selectedGroup['group_name'], ''));
        $prefix = substr($prefix ?: 'group', 0, 8);

        for ($index = 1; $index <= (int) $selectedGroup['members']; $index++) {
            $credentials[] = [
                'member_no' => $index,
                'username' => $prefix . str_pad((string) $index, 2, '0', STR_PAD_LEFT) . rand(1, 9),
                'password' => Str::upper(Str::random(2)) . rand(10, 99) . Str::lower(Str::random(2)) . '!',
            ];
        }

        $request->session()->put('manager_generated_group_credential', [
            'group_id' => $selectedGroup['id'],
            'group_name' => $selectedGroup['group_name'],
            'pic_name' => $selectedGroup['pic_name'],
            'pic_whatsapp' => $selectedGroup['pic_phone'],
            'pic_email' => $selectedGroup['pic_email'],
            'credentials' => $credentials,
        ]);

        return redirect()
            ->route('dashboard.manager.participants.group')
            ->with('status', 'Kredensial anggota kelompok berhasil digenerate. Lanjutkan export Excel dan kirim ke PIC.');
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

        $csvRows = [
            ['Group', 'PIC', 'PIC WhatsApp', 'PIC Email', 'Member No', 'Username', 'Password'],
        ];

        foreach ($generated['credentials'] as $credential) {
            $csvRows[] = [
                $generated['group_name'],
                $generated['pic_name'],
                $generated['pic_whatsapp'],
                $generated['pic_email'],
                (string) $credential['member_no'],
                $credential['username'],
                $credential['password'],
            ];
        }

        $csvContent = '';
        foreach ($csvRows as $row) {
            $csvContent .= implode(',', array_map(function (string $value): string {
                return '"' . str_replace('"', '""', $value) . '"';
            }, $row)) . "\n";
        }

        $filename = 'group-credentials-' . $group . '-' . now()->format('YmdHis') . '.csv';
        $directory = storage_path('app/private/exports');
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $path = $directory . DIRECTORY_SEPARATOR . $filename;
        file_put_contents($path, $csvContent);

        $sentGroupIds = $request->session()->get('manager_sent_group_ids', []);
        if (!in_array($group, $sentGroupIds, true)) {
            $sentGroupIds[] = $group;
        }

        $request->session()->put('manager_sent_group_ids', $sentGroupIds);
        $request->session()->put('manager_group_last_export', [
            'group_id' => $group,
            'group_name' => $generated['group_name'],
            'pic_name' => $generated['pic_name'],
            'path' => $path,
            'filename' => $filename,
        ]);
        $request->session()->forget('manager_generated_group_credential');

        return redirect()
            ->route('dashboard.manager.participants.group')
            ->with('status', 'Kredensial berhasil diexport (CSV kompatibel Excel) dan dikirim ke PIC via WhatsApp (simulasi). Data kelompok dipindahkan ke kelola peserta kelompok.');
    }

    public function managerGroupParticipantsDownloadCredentialExport(Request $request)
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        $exportMeta = $request->session()->get('manager_group_last_export');
        $path = $exportMeta['path'] ?? null;
        $filename = $exportMeta['filename'] ?? 'group-credentials.csv';

        if (!$path || !file_exists($path)) {
            return redirect()
                ->route('dashboard.manager.participants.group')
                ->withErrors(['group_credential' => 'File export belum tersedia. Silakan kirim kredensial kelompok terlebih dahulu.']);
        }

        return response()->download($path, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function managerGroupParticipantsUpdate(Request $request, string $group): RedirectResponse
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        $request->validate([
            'status' => ['required', 'string', 'in:Aktif,Lulus,Nonaktif'],
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
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'phone' => ['required', 'string', 'min:8', 'max:20'],
            'address' => ['required', 'string', 'min:5', 'max:255'],
            'education' => ['required', 'string', 'min:2', 'max:100'],
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

        $instructors = $this->getManagerManagedInstructors($request);
        $updatedInstructors = array_map(function (array $item) use ($instructor, $request): array {
            if ($item['id'] === $instructor) {
                $item['status'] = (string) $request->input('status');
            }

            return $item;
        }, $instructors);

        $request->session()->put('manager_instructors_data', array_values($updatedInstructors));

        return redirect()
            ->route('dashboard.manager.instructors')
            ->with('status', 'Status pengajar ' . strtoupper($instructor) . ' diperbarui (simulasi).');
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

        $request->session()->put('manager_instructors_data', array_values($updated));

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

        $instructors = $this->getManagerManagedInstructors($request);
        $remaining = array_values(array_filter($instructors, function (array $item) use ($instructor): bool {
            return $item['id'] !== $instructor;
        }));

        $request->session()->put('manager_instructors_data', $remaining);

        return redirect()
            ->route('dashboard.manager.instructors')
            ->with('status', 'Data pengajar berhasil dihapus.');
    }

    public function managerPrograms(Request $request): View|RedirectResponse
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        $search = strtolower(trim((string) $request->query('search', '')));
        $programs = $this->getManagerManagedPrograms($request);

        if ($search !== '') {
            $programs = array_values(array_filter($programs, function (array $program) use ($search): bool {
                return (strpos(strtolower((string) $program['name']), $search) !== false)
                    || (strpos(strtolower((string) $program['description']), $search) !== false);
            }));
        }

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

        $payload = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:120'],
            'duration' => ['required', 'string', 'min:2', 'max:60'],
            'description' => ['required', 'string', 'min:10', 'max:500'],
            'cost' => ['required', 'integer', 'min:0'],
        ]);

        $programs = $this->getManagerManagedPrograms($request);
        $programs[] = [
            'id' => 'pr-' . strtolower(Str::random(6)),
            'name' => $payload['name'],
            'duration' => $payload['duration'],
            'description' => $payload['description'],
            'cost' => (int) $payload['cost'],
            'status' => 'Aktif',
        ];

        $request->session()->put('manager_programs_data', $programs);

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

        $payload = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:120'],
            'duration' => ['required', 'string', 'min:2', 'max:60'],
            'description' => ['required', 'string', 'min:10', 'max:500'],
            'cost' => ['required', 'integer', 'min:0'],
        ]);

        $programs = $this->getManagerManagedPrograms($request);
        $found = false;

        $updated = array_map(function (array $item) use ($program, $payload, &$found): array {
            if ($item['id'] === $program) {
                $found = true;
                $item['name'] = $payload['name'];
                $item['duration'] = $payload['duration'];
                $item['description'] = $payload['description'];
                $item['cost'] = (int) $payload['cost'];
            }

            return $item;
        }, $programs);

        if (!$found) {
            return redirect()
                ->route('dashboard.manager.programs')
                ->withErrors(['programs' => 'Program tidak ditemukan untuk diedit.']);
        }

        $request->session()->put('manager_programs_data', array_values($updated));

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

        $programs = $this->getManagerManagedPrograms($request);
        $remaining = array_values(array_filter($programs, function (array $item) use ($program): bool {
            return $item['id'] !== $program;
        }));

        $request->session()->put('manager_programs_data', $remaining);

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

        return view('dashboard.manager.reports', [
            'user' => $request->session()->get('auth_user'),
            'dashboard' => $this->getManagerDashboardConfig('reports'),
            'monthlyData' => $monthlyData,
            'selectedMonth' => $month,
            'selectedYear' => $year,
            'availableYears' => $yearMonths,
            'allMonthsData' => $this->getAllMonthsData($year),
            'monthNames' => $monthNames,
        ]);
    }

    public function managerReportsExport(Request $request): RedirectResponse
    {
        $guard = $this->ensureManagerRole($request);

        if ($guard) {
            return $guard;
        }

        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);
        $exportType = $request->input('export_type', 'pdf');

        if ($exportType === 'pdf') {
            $filename = 'Laporan-' . $this->getMonthName($month) . '-' . $year . '.pdf';
            return redirect()
                ->route('dashboard.manager.reports')
                ->with('status', 'Laporan bulan ' . $this->getMonthName($month) . ' ' . $year . ' berhasil diunduh (simulasi).');
        } else {
            $filename = 'Laporan-Tahunan-' . $year . '.csv';
            return redirect()
                ->route('dashboard.manager.reports')
                ->with('status', 'Laporan tahunan ' . $year . ' berhasil diunduh (simulasi).');
        }
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

        $validated = $request->validate([
            'organization_name' => ['required', 'string', 'min:3', 'max:120'],
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

        // Store in session for demo purposes
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
            'photo' => ['nullable', 'image', 'max:2048'],
            'full_name' => ['required', 'string', 'min:3', 'max:120'],
            'username' => ['required', 'string', 'min:4', 'max:40'],
            'password' => ['required', 'string', 'min:6', 'max:40'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'string', 'min:7', 'max:20'],
            'address' => ['required', 'string', 'min:5', 'max:255'],
            'role_label' => ['required', 'string', 'min:3', 'max:40'],
        ]);

        $profile = $this->getManagerProfileData($request);

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoName = 'manager-' . now()->timestamp . '.' . $photo->getClientOriginalExtension();
            $photo->storeAs('profiles', $photoName, 'public');
            $profile['photo'] = $photoName;
        }

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
            'photo' => ['nullable', 'image', 'max:2048'],
            'full_name' => ['required', 'string', 'min:3', 'max:120'],
            'username' => ['required', 'string', 'min:4', 'max:40'],
            'password' => ['required', 'string', 'min:6', 'max:40'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'string', 'min:7', 'max:20'],
            'address' => ['required', 'string', 'min:5', 'max:255'],
            'role_label' => ['required', 'string', 'min:3', 'max:40'],
        ]);

        $profile = $this->getInstructorProfileData($request);

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoName = 'instructor-' . now()->timestamp . '.' . $photo->getClientOriginalExtension();
            $photo->storeAs('profiles', $photoName, 'public');
            $profile['photo'] = $photoName;
        }

        $profile['full_name'] = $validated['full_name'];
        $profile['username'] = $validated['username'];
        $profile['password'] = $validated['password'];
        $profile['email'] = $validated['email'];
        $profile['phone'] = $validated['phone'];
        $profile['address'] = $validated['address'];
        $profile['role_label'] = $validated['role_label'];

        $request->session()->put('profile_instructor_data', $profile);

        $authUser = $request->session()->get('auth_user', []);
        $authUser['name'] = $validated['full_name'];
        $authUser['username'] = $validated['username'];
        $authUser['email'] = $validated['email'];
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

        return view('dashboard.instructor.participants', [
            'user' => $request->session()->get('auth_user'),
            'dashboard' => $this->getInstructorDashboardConfig('participants'),
            'individualParticipants' => $this->getInstructorIndividualParticipants(),
            'groupParticipants' => $this->getInstructorGroupParticipants(),
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

        return $this->renderParticipantPage($request, 'home');
    }

    public function participantProfile(Request $request): View|RedirectResponse
    {
        $guard = $this->ensureParticipantRole($request);

        if ($guard) {
            return $guard;
        }

        return view('dashboard.participant.profile', [
            'user' => $request->session()->get('auth_user'),
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

        $validated = $request->validate([
            'photo' => ['nullable', 'image', 'max:2048'],
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
        ]);

        if ($validated['participant_type'] === 'individual' && empty($validated['motivation'])) {
            return back()->withErrors(['motivation' => 'Motivasi singkat wajib diisi untuk peserta individu.'])->withInput();
        }

        if ($validated['participant_type'] === 'group' && (empty($validated['group_name']) || empty($validated['pic_name']))) {
            return back()->withErrors(['group_name' => 'Nama kelompok/lembaga dan nama PIC wajib diisi untuk peserta kelompok.'])->withInput();
        }

        $authUser = $request->session()->get('auth_user', []);
        $dbUser = User::where('email', $authUser['email'])->first();

        if (!$dbUser) {
            return back()->withErrors(['general' => 'User tidak ditemukan.'])->withInput();
        }

        // Update user data
        $dbUser->name = $validated['full_name'];
        $dbUser->username = $validated['username'];
        $dbUser->email = $validated['email'];
        $dbUser->password = Hash::make($validated['password']);
        $dbUser->phone = $validated['phone'];
        $dbUser->address = $validated['address'];
        $dbUser->password_changed = true; // Mark as changed

        $dbUser->save();

        // Update session
        $authUser['name'] = $validated['full_name'];
        $authUser['username'] = $validated['username'];
        $authUser['email'] = $validated['email'];
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
        $artworksQuery = Schema::hasTable('artworks') ? Artwork::query()->latest() : null;

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
        $user = $request->session()->get('auth_user');
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
        if (! Schema::hasTable('users')) {
            return [];
        }

        $users = User::where('role', 'peserta')
            ->orderByDesc('created_at')
            ->get();

        return $users->map(function ($user) {
            return [
                'id' => $user->username ?: 'peserta-' . $user->id,
                'name' => $user->name,
                'program' => 'Program Individu',
                'progress' => 0,
                'status' => $user->status ?? 'Aktif',
            ];
        })->toArray();
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
        return [
            ['id' => 'pg-01', 'group_name' => 'Batik Lestari', 'members' => 5, 'program' => 'Teknik Canting Dasar', 'status' => 'Aktif'],
            ['id' => 'pg-02', 'group_name' => 'Motif Muda', 'members' => 4, 'program' => 'Teknik Warna Dasar', 'status' => 'Lulus'],
            ['id' => 'pg-03', 'group_name' => 'Sanggar Nawasena', 'members' => 6, 'program' => 'Komposisi Motif', 'status' => 'Nonaktif'],
        ];
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

    /**
     * @return array<int, array<string, string|int>>
     */
    private function getManagerInstructors(): array
    {
        return [
            [
                'id' => 'ig-01',
                'name' => 'Anonymouse',
                'username' => 'anonymouse321',
                'password' => 'AK12xyz!',
                'email' => 'anonymouse@gmail.com',
                'phone' => '0877956624',
                'address' => 'Lorem ipsum dolor sit amet consectetur.',
                'education' => 'SMAN 1 Purwokerto',
                'certificate' => 'sertifikat-anonymouse.pdf',
                'status' => 'Aktif',
            ],
            [
                'id' => 'ig-02',
                'name' => 'Anonymouse B',
                'username' => 'anonymouseb17',
                'password' => 'DF45mno!',
                'email' => 'anonymouseb@gmail.com',
                'phone' => '082178889900',
                'address' => 'Jl. Melati 2',
                'education' => 'S1 Pendidikan Seni',
                'certificate' => null,
                'status' => 'Nonaktif',
            ],
        ];
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

        $dbInstructors = User::where('role', 'pengajar')
            ->orderBy('name')
            ->get();

        if ($dbInstructors->isNotEmpty()) {
            return $dbInstructors
                ->map(fn (User $user) => $this->mapManagerInstructor($user, $passwordCache))
                ->all();
        }

        $sessionData = $request->session()->get('manager_instructors_data');

        if (is_array($sessionData)) {
            return $sessionData;
        }

        $defaults = $this->getManagerInstructors();
        $request->session()->put('manager_instructors_data', $defaults);

        return $defaults;
    }

    /**
     * @return array<int, array<string, string|int>>
     */
    private function getManagerPrograms(): array
    {
        return [
            [
                'id' => 'pr-01',
                'name' => 'Program Individu',
                'duration' => '2 Januari 2026',
                'description' => 'Deskripsi: Lorem ipsum dolor sit amet consectetur.',
                'cost' => 350000,
                'status' => 'Aktif',
            ],
            [
                'id' => 'pr-02',
                'name' => 'Program Kelompok',
                'duration' => '2 Januari 2026',
                'description' => 'Deskripsi: Lorem ipsum dolor sit amet consectetur.',
                'cost' => 500000,
                'status' => 'Aktif',
            ],
        ];
    }

    /**
     * @return array<int, array<string, string|int>>
     */
    private function getManagerManagedPrograms(Request $request): array
    {
        $sessionData = $request->session()->get('manager_programs_data');

        if (is_array($sessionData)) {
            return $sessionData;
        }

        $defaults = $this->getManagerPrograms();
        $request->session()->put('manager_programs_data', $defaults);

        return $defaults;
    }

    /**
     * @return array<string, int|string>
     */
    /**
     * Get monthly report data with all required fields
     */
    private function getMonthlyReportData(int $month, int $year): array
    {
        $individualParticipants = $this->getEnhancedIndividualParticipants();
        $groupParticipants = $this->getEnhancedGroupParticipants();
        $instructors = $this->getManagerInstructors();

        // Filter by month/year
        $monthIndividual = [];
        $monthGroup = [];
        $registrationByDate = [];

        foreach ($individualParticipants as $participant) {
            $regDate = $this->parseDate($participant['registration_date'] ?? '');
            if ($regDate['month'] === $month && $regDate['year'] === $year) {
                $monthIndividual[] = $participant;
                $dateKey = $regDate['day'];
                $registrationByDate[$dateKey] = ($registrationByDate[$dateKey] ?? 0) + 1;
            }
        }

        foreach ($groupParticipants as $group) {
            $regDate = $this->parseDate($group['registration_date'] ?? '');
            if ($regDate['month'] === $month && $regDate['year'] === $year) {
                $monthGroup[] = $group;
                $dateKey = $regDate['day'];
                $registrationByDate[$dateKey] = ($registrationByDate[$dateKey] ?? 0) + ($group['members'] ?? 0);
            }
        }

        // Calculate totals
        $totalIndividual = count($monthIndividual);
        $totalGroup = count($monthGroup);
        $totalGroupMembers = array_sum(array_column($monthGroup, 'members'));
        $totalParticipants = $totalIndividual + $totalGroupMembers;

        // Calculate profit/cost
        $individualCost = 125000; // IDR
        $groupCost = 100000; // IDR per member
        $totalProfit = ($totalIndividual * $individualCost) + ($totalGroupMembers * $groupCost);

        // Find peak registration date
        $peakDate = 'N/A';
        $peakCount = 0;
        foreach ($registrationByDate as $day => $count) {
            if ($count > $peakCount) {
                $peakCount = $count;
                $peakDate = $day . ' ' . $this->getMonthName($month) . ' ' . $year;
            }
        }

        // Calculate instructor salary (commission-based)
        $instructorSalaries = [];
        foreach ($instructors as $instructor) {
            $instructorSalaries[] = [
                'name' => $instructor['name'],
                'status' => $instructor['status'],
                'base_salary' => 2000000,
                'classes_handled' => rand(2, 5),
                'commission' => rand(500000, 2000000),
                'total' => 2000000 + rand(500000, 2000000),
            ];
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
            'group_income' => $totalGroupMembers * $groupCost,
            'total_profit' => $totalProfit,
            'peak_registration_date' => $peakDate,
            'instructor_salaries' => $instructorSalaries,
            'individual_participants' => $monthIndividual,
            'group_participants' => $monthGroup,
        ];
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
        return $monthsData;
    }

    /**
     * Get available years (current year and 2 future years)
     */
    private function getAvailableYears(): array
    {
        $years = [];
        $currentYear = now()->year;
        for ($i = $currentYear; $i <= $currentYear + 2; $i++) {
            $years[] = $i;
        }
        return $years;
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
        return [
            ['id' => 'pi-01', 'name' => 'Nadia Putri', 'program' => 'Teknik Canting Dasar', 'progress' => 88, 'status' => 'Aktif', 'registration_date' => '2026-03-15', 'cost' => 125000],
            ['id' => 'pi-02', 'name' => 'Rafi Akbar', 'program' => 'Teknik Warna Dasar', 'progress' => 61, 'status' => 'Lulus', 'registration_date' => '2026-03-18', 'cost' => 125000],
            ['id' => 'pi-03', 'name' => 'Salsa Wicaksono', 'program' => 'Komposisi Motif', 'progress' => 75, 'status' => 'Aktif', 'registration_date' => '2026-03-12', 'cost' => 125000],
            ['id' => 'pi-04', 'name' => 'Tio Ramadhan', 'program' => 'Teknik Canting Dasar', 'progress' => 42, 'status' => 'Nonaktif', 'registration_date' => '2026-02-22', 'cost' => 125000],
            ['id' => 'pi-05', 'name' => 'Siti Nurhaliza', 'program' => 'Teknik Warna Dasar', 'progress' => 95, 'status' => 'Lulus', 'registration_date' => '2026-01-10', 'cost' => 125000],
            ['id' => 'pi-06', 'name' => 'Bambang Saputra', 'program' => 'Komposisi Motif', 'progress' => 80, 'status' => 'Aktif', 'registration_date' => '2026-03-25', 'cost' => 125000],
        ];
    }

    /**
     * Get enhanced group participants with registration dates and costs
     */
    private function getEnhancedGroupParticipants(): array
    {
        return [
            ['id' => 'pg-01', 'group_name' => 'Batik Lestari', 'members' => 5, 'program' => 'Teknik Canting Dasar', 'status' => 'Aktif', 'registration_date' => '2026-03-20'],
            ['id' => 'pg-02', 'group_name' => 'Motif Muda', 'members' => 4, 'program' => 'Teknik Warna Dasar', 'status' => 'Lulus', 'registration_date' => '2026-02-14'],
            ['id' => 'pg-03', 'group_name' => 'Sanggar Nawasena', 'members' => 6, 'program' => 'Komposisi Motif', 'status' => 'Nonaktif', 'registration_date' => '2026-01-28'],
            ['id' => 'pg-04', 'group_name' => 'Kuncup Batik', 'members' => 8, 'program' => 'Teknik Canting Dasar', 'status' => 'Aktif', 'registration_date' => '2026-03-10'],
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
     * @return array<int, array<string, string>>
     */
    private function getInstructorIndividualParticipants(): array
    {
        $registrations = Schema::hasTable('registration_individuals')
            ? \App\Models\RegistrationIndividual::orderByDesc('created_at')->get()
            : collect();

        return $registrations->map(function ($reg) {
            return [
                'id' => 'individual-' . $reg->id,
                'name' => $reg->nama_lengkap,
                'email' => $reg->email,
                'no_handphone' => $reg->no_handphone,
                'alamat' => $reg->alamat,
                'pendidikan_terakhir' => $reg->pendidikan_terakhir,
                'motivasi' => $reg->motivasi,
            ];
        })->toArray();
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function getInstructorGroupParticipants(): array
    {
        $registrations = Schema::hasTable('registration_groups')
            ? \App\Models\RegistrationGroup::orderByDesc('created_at')->get()
            : collect();

        return $registrations->map(function ($reg) {
            return [
                'id' => 'group-' . $reg->id,
                'name' => $reg->nama_pic,
                'group' => $reg->nama_lembaga,
                'email' => $reg->email_pic,
                'no_handphone' => $reg->no_handphone_pic,
                'alamat' => $reg->alamat_pic,
                'jumlah_peserta' => $reg->jumlah_peserta,
                'surat_resmi' => $reg->surat_resmi,
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
            $dbUser = User::where('email', $authUser['email'])->first();
            if ($dbUser && !$dbUser->password_changed) {
                return redirect()->route('dashboard.participant.profile')
                    ->with('force_password_change', true);
            }
        }

        $request->session()->forget('auth_user');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Anda berhasil logout.');
    }
}
