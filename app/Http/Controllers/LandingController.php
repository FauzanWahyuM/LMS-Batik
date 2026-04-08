<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\Artwork;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class LandingController extends Controller
{
    public function index()
    {
        $latestArtworks = Schema::hasTable('artworks')
            ? Artwork::latest()->take(6)->get()
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
        ]);
    }

    public function about()
    {
        return view('landing.about');
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

        // TODO: store registration data
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

        // TODO: store registration data and handle file upload
        return redirect()->route('landing.registration')
            ->with('success', 'Pendaftaran kelompok berhasil dikirim. Kami akan menghubungi Anda segera.');
    }

    public function programs()
    {
        $programs = [
            [
                'title' => 'Batik Tulis Dasar',
                'description' => 'Pelajari teknik dasar batik tulis dari nol hingga mahir. Cocok untuk pemula yang ingin memulai perjalanan batik.',
                'schedule' => 'Senin & Rabu, 09:00 - 12:00',
                'instructor' => 'Ibu Sri Mulyani',
                'duration' => '3 Bulan',
                'level' => 'Pemula'
            ],
            [
                'title' => 'Batik Cap',
                'description' => 'Kuasai teknik batik cap dengan berbagai motif tradisional dan modern.',
                'schedule' => 'Selasa & Kamis, 13:00 - 16:00',
                'instructor' => 'Bapak Slamet Riyadi',
                'duration' => '2 Bulan',
                'level' => 'Menengah'
            ],
            [
                'title' => 'Batik Kombinasi',
                'description' => 'Pelajari teknik kombinasi batik tulis dan cap untuk hasil yang lebih artistik.',
                'schedule' => 'Jumat & Sabtu, 09:00 - 12:00',
                'instructor' => 'Ibu Dewi Kartika',
                'duration' => '4 Bulan',
                'level' => 'Lanjutan'
            ],
            [
                'title' => 'Desain Motif Batik',
                'description' => 'Kembangkan kreativitas dalam menciptakan motif batik kontemporer yang unik.',
                'schedule' => 'Rabu & Jumat, 14:00 - 17:00',
                'instructor' => 'Bapak Agus Prasetyo',
                'duration' => '2 Bulan',
                'level' => 'Semua Level'
            ]
        ];

        return view('landing.programs', compact('programs'));
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
}
