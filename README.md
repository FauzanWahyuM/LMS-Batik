# LMS Batik

LMS Batik adalah website pembelajaran batik berbasis Laravel yang dirancang untuk membantu peserta belajar secara terstruktur melalui modul materi, video, tugas, forum diskusi, dan galeri karya.

## Tujuan Proyek

- Menyediakan platform belajar batik yang mudah digunakan.
- Mendukung proses belajar dari pengenalan materi sampai pengumpulan tugas.
- Menyediakan antarmuka dashboard peserta yang responsif untuk desktop dan mobile.

## Fitur Utama

### 1. Landing Page

- Halaman beranda website.
- Navigasi informasi seperti tentang, program, galeri, dan pendaftaran.
- Form pendaftaran dengan dua opsi: individu dan kelompok.

### 2. Login dan Akses Role

- Sistem login sederhana berbasis sesi.
- Mendukung tiga role:
  - Peserta
  - Pengajar
  - Pengelola

### 3. Dashboard Peserta

- Menu utama:
  - Dashboard
  - Modul Pembelajaran
  - Forum Diskusi
  - Galeri Karya
- Sidebar responsif dengan perilaku collapse pada layar kecil.

### 4. Modul Pembelajaran

- Daftar modul menampilkan progress, durasi, dan ringkasan materi.
- Ringkasan materi per modul dapat di-expand dan di-collapse.
- Klik materi akan membuka detail modul langsung pada materi yang dipilih.

### 5. Detail Modul

- Tab Materi, Video, Tugas, dan Diskusi.
- Tombol Kembali untuk kembali ke daftar modul.
- Tampilan materi fokus pada materi terpilih dengan opsi pindah materi cepat.

### 6. Tugas

- Upload file tugas dari perangkat pengguna.
- Validasi file saat upload.
- Menampilkan status berhasil upload setelah submit.

### 7. Forum Diskusi

- Area input pertanyaan atau komentar.
- Menampilkan daftar diskusi pada modul.

## Teknologi yang Digunakan

- Laravel 12
- PHP 8.2
- Blade Template Engine
- Tailwind CSS via CDN
- Session Authentication sederhana

## Struktur Alur Singkat

1. Pengguna membuka landing page.
2. Pengguna login menggunakan akun role.
3. Peserta masuk ke dashboard peserta.
4. Peserta memilih modul, melihat ringkasan materi, lalu membuka materi detail.
5. Peserta mengerjakan dan mengunggah tugas pada tab Tugas.

## Akun Demo

- Peserta
  - Email: participant@lmsbatik.test
  - Password: participant123
- Pengajar
  - Email: instructor@lmsbatik.test
  - Password: instructor123
- Pengelola
  - Email: manager@lmsbatik.test
  - Password: manager123

## Cara Menjalankan Proyek

1. Install dependency backend

	composer install

2. Install dependency frontend

	npm install

3. Salin file environment

	copy .env.example .env

4. Generate application key

	php artisan key:generate

5. Jalankan migrasi database

	php artisan migrate

6. Jalankan server Laravel

	php artisan serve

7. Jika menggunakan Vite untuk aset, jalankan juga

	npm run dev

## Catatan Pengembangan

- Saat ini autentikasi masih menggunakan data akun hardcoded untuk kebutuhan pengembangan.
- Upload tugas disimpan ke storage lokal aplikasi.
- Proyek dapat dikembangkan lebih lanjut ke autentikasi database penuh, manajemen kelas, dan penilaian otomatis.

## Rencana Pengembangan Lanjutan

- Riwayat upload tugas per peserta.
- Penilaian tugas oleh pengajar.
- Notifikasi aktivitas pembelajaran.
- Integrasi komentar forum yang tersimpan ke database.
