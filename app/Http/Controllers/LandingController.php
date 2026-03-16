<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        return view('landing.index');
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
        $gallery = [
            [
                'title' => 'Batik Parang Rusak',
                'student' => 'Rina Wijaya',
                'image' => 'https://images.unsplash.com/photo-1610030469983-98e550d6193c?w=400'
            ],
            [
                'title' => 'Batik Kawung',
                'student' => 'Ahmad Fauzi',
                'image' => 'https://images.unsplash.com/photo-1572635148818-ef6fd45eb394?w=400'
            ],
            [
                'title' => 'Batik Truntum',
                'student' => 'Siti Nurhaliza',
                'image' => 'https://images.unsplash.com/photo-1583846593633-e0e8f8c6c0c8?w=400'
            ],
            [
                'title' => 'Batik Sekar Jagad',
                'student' => 'Budi Santoso',
                'image' => 'https://images.unsplash.com/photo-1590735213920-68192a487bc2?w=400'
            ],
            [
                'title' => 'Batik Mega Mendung',
                'student' => 'Fitri Handayani',
                'image' => 'https://images.unsplash.com/photo-1558769132-cb1aea6c8db8?w=400'
            ],
            [
                'title' => 'Batik Kontemporer',
                'student' => 'Dian Sastro',
                'image' => 'https://images.unsplash.com/photo-1594633313593-bab3825d0caf?w=400'
            ]
        ];

        return view('landing.gallery', compact('gallery'));
    }
}
