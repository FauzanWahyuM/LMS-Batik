<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\Artwork;
use App\Models\Facility;
use App\Models\Module;
use App\Models\Partner;
use App\Models\Program;
use App\Models\RegistrationIndividual;
use App\Models\RegistrationGroup;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class LandingController extends Controller
{
    public function index()
    {
        $latestArtworks = Schema::hasTable('artworks')
            ? Artwork::latest()->take(6)->get()
            : collect();

        $testimonials = Schema::hasTable('testimonials')
            ? Testimonial::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->latest()
                ->get()
            : collect();

        $programs = Schema::hasTable('programs')
            ? Program::query()
                ->where('is_active', true)
                ->latest()
                ->take(6)
                ->get()
            : collect();

        $featuredAchievements = Schema::hasTable('achievements')
            ? Achievement::query()
                ->where('is_active', true)
                ->orderByRaw('CASE WHEN rank IS NULL THEN 99 ELSE rank END')
                ->orderByDesc('year')
                ->take(6)
                ->get()
            : collect();

        return view('landing.index', [
            'latestArtworks' => $latestArtworks,
            'featuredAchievements' => $featuredAchievements,
            'programs' => $programs,
            'testimonials' => $testimonials,
        ]);
    }

    public function about()
    {
        $facilities = Schema::hasTable('facilities')
            ? Facility::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->latest()
                ->get()
            : collect();

        $partners = Schema::hasTable('partners')
            ? Partner::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->latest()
                ->get()
            : collect();

        return view('landing.about', [
            'facilities' => $facilities,
            'partners' => $partners,
        ]);
    }

    public function registration()
    {
        return view('landing.registration');
    }

    public function submitIndividu(Request $request)
    {
        $request->validate([
            'nama_lengkap'      => ['required', 'string', 'max:255'],
            'email'             => ['required', 'email', 'max:255'],
            'no_handphone'      => ['required', 'string', 'max:20'],
            'alamat'            => ['required', 'string', 'max:500'],
            'pendidikan_terakhir' => ['required', 'string', 'max:100'],
            'motivasi'          => ['required', 'string', 'max:1000'],
        ]);

        RegistrationIndividual::create($request->only([
            'nama_lengkap',
            'email',
            'no_handphone',
            'alamat',
            'pendidikan_terakhir',
            'motivasi',
        ]) + ['status' => 'pending']);

        return redirect()->route('landing.registration')
            ->with('success', 'Pendaftaran individu berhasil dikirim. Kami akan menghubungi Anda segera.');
    }

    public function submitKelompok(Request $request)
    {
        $request->validate([
            'nama_lembaga'      => ['required', 'string', 'max:255'],
            'alamat_pic'        => ['required', 'string', 'max:500'],
            'email_pic'         => ['required', 'email', 'max:255'],
            'no_handphone_pic'  => ['required', 'string', 'max:20'],
            'nama_pic'          => ['required', 'string', 'max:255'],
            'jumlah_peserta'    => ['required', 'integer', 'min:1'],
            'surat_resmi'       => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $data = $request->only([
            'nama_lembaga',
            'alamat_pic',
            'email_pic',
            'no_handphone_pic',
            'nama_pic',
            'jumlah_peserta',
        ]);

        // Handle file upload
        if ($request->hasFile('surat_resmi')) {
            $file = $request->file('surat_resmi');
            $filePath = $file->store('registration_documents', 'public');
            $data['surat_resmi'] = $filePath;
        }

        $data['status'] = 'pending';

        RegistrationGroup::create($data);

        return redirect()->route('landing.registration')
            ->with('success', 'Pendaftaran kelompok berhasil dikirim. Kami akan menghubungi Anda segera.');
    }

    public function programs()
    {
        $programs = Schema::hasTable('programs')
            ? Program::query()
                ->where('is_active', true)
                ->latest()
                ->get()
            : collect();

        return view('landing.programs', [
            'programs' => $programs,
        ]);
    }

    public function gallery()
    {
        $gallery = Schema::hasTable('artworks')
            ? Artwork::latest()->get()
            : collect();

        $achievements = Schema::hasTable('achievements')
            ? Achievement::query()
                ->where('is_active', true)
                ->orderByRaw('CASE WHEN rank IS NULL THEN 99 ELSE rank END')
                ->orderByDesc('year')
                ->get()
            : collect();

        return view('landing.gallery', compact('gallery', 'achievements'));
    }

    private function buildProgramContent(): array
    {
        $modules = Schema::hasTable('modules')
            ? Module::query()
                ->latest()
                ->get(['title', 'description', 'duration'])
            : collect();

        $individualModules = $modules
            ->values()
            ->filter(fn ($module, int $index): bool => $index % 2 === 0)
            ->take(5)
            ->values();

        $groupModules = $modules
            ->values()
            ->filter(fn ($module, int $index): bool => $index % 2 !== 0)
            ->take(5)
            ->values();

        if ($individualModules->isEmpty() && $modules->isNotEmpty()) {
            $individualModules = $modules->take(5)->values();
        }

        if ($groupModules->isEmpty() && $modules->isNotEmpty()) {
            $groupModules = $modules->skip(1)->take(5)->values();
        }

        $types = [
            'individual' => [
                'title' => 'Program Individu',
                'description' => $this->buildProgramDescription(
                    $individualModules,
                    'Pembelajaran personal dengan pendampingan langsung untuk memperkuat teknik membatik sesuai ritme belajar peserta.'
                ),
            ],
            'group' => [
                'title' => 'Program Kelompok',
                'description' => $this->buildProgramDescription(
                    $groupModules,
                    'Pelatihan kolaboratif untuk sekolah, komunitas, dan instansi dengan materi yang menyesuaikan kebutuhan kelompok.'
                ),
            ],
        ];

        $packages = [
            'individual' => [
                'duration' => $this->buildDurationLabel($individualModules, '20 Hari'),
                'features' => $this->buildFeatureList($individualModules),
                'price' => 'Hubungi Admin untuk Info Biaya / Orang',
            ],
            'group' => [
                'duration' => $this->buildDurationLabel($groupModules, 'Sesuai Pemesanan'),
                'features' => $this->buildFeatureList($groupModules),
                'price' => 'Hubungi Admin untuk Info Biaya / Kelompok',
            ],
        ];

        return [
            'types' => $types,
            'packages' => $packages,
        ];
    }

    private function buildProgramDescription($modules, string $fallback): string
    {
        $description = $modules
            ->pluck('description')
            ->filter()
            ->map(fn (string $value): string => trim(strip_tags($value)))
            ->first();

        if (!empty($description)) {
            return Str::limit($description, 220);
        }

        $titles = $modules
            ->pluck('title')
            ->filter()
            ->take(2)
            ->implode(', ');

        if ($titles !== '') {
            return $fallback . ' Materi utama: ' . $titles . '.';
        }

        return $fallback;
    }

    private function buildDurationLabel($modules, string $fallback): string
    {
        $duration = $modules
            ->pluck('duration')
            ->filter()
            ->first();

        return !empty($duration) ? (string) $duration : $fallback;
    }

    private function buildFeatureList($modules): array
    {
        $features = $modules
            ->pluck('title')
            ->filter()
            ->map(fn (string $title): string => 'Materi ' . $title)
            ->unique()
            ->take(5)
            ->values()
            ->all();

        if (!empty($features)) {
            return $features;
        }

        return [
            'Pendampingan oleh pengajar berpengalaman',
            'Praktik langsung teknik canting dan pewarnaan',
            'Evaluasi hasil karya di akhir sesi',
            'Sertifikat penyelesaian program',
            'Konsultasi lanjutan pengembangan karya',
        ];
    }
}
