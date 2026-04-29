# Ringkasan Perubahan: Durasi dan Jadwal Pelatihan Program

## 📋 Perubahan yang Dilakukan

### 1. **Perubahan Database Schema**
**File:** `database/migrations/2026_04_29_000002_update_programs_duration_and_add_training_schedules.php`

- ✅ Mengganti field `duration_hours` (decimal) dan `duration_unit` (string) dengan satu field `duration` (string)
- ✅ Menambah field `training_schedules` (JSON) untuk menyimpan gelombang dan waktu pelatihan
- ✅ Migrasi otomatis mengkonversi data lama ke format baru

**Struktur Data Training Schedules:**
```php
[
    'gelombang' => ['Gelombang 1', 'Gelombang 2', ...],
    'waktu' => ['10 April - 20 April', '25 April - 5 Mei', ...]
]
```

### 2. **Update Model Program**
**File:** `app/Models/Program.php`

- Ubah `$fillable` array untuk menggunakan field baru
- Update `$casts` untuk menambahkan `training_schedules` sebagai array
- Sederhanakan `getDurationLabelAttribute()` untuk langsung mengembalikan nilai durasi

### 3. **Update View Manager (Backend Form)**
**File:** `resources/views/dashboard/manager/programs.blade.php`

#### Perubahan Form Input:
- Ganti 2 field (durasi + satuan durasi) dengan 1 field text
- Tambah field "Jadwal Pelatihan" dengan input dinamis untuk gelombang dan waktu
- Field jadwal dapat ditambah/dihapus seperti benefit

#### Struktur Field Baru:
```html
<div>
    <input type="text" name="duration" placeholder="Contoh: 24 jam, 120 menit, 2 hari">
</div>

<div>
    <input type="text" name="training_schedules[gelombang][]" placeholder="Contoh: Gelombang 1">
    <input type="text" name="training_schedules[waktu][]" placeholder="Contoh: 10 April - 20 April">
</div>
```

#### Fitur JavaScript:
- Tambah handler untuk tombol `add-training-schedule-btn`
- Tambah handler untuk tombol `remove-training-schedule-btn`
- Hapus fungsi `syncDurationInput` yang sudah tidak diperlukan

### 4. **Update View Landing Page**
**File:** `resources/views/landing/programs.blade.php`

- Tambah display jadwal pelatihan di bawah benefit
- Tampilkan gelombang dan waktu secara terstruktur
- Tampilkan di kedua layout (single program dan multi-program)
- Styling konsisten dengan benefit section (border biru, background semi-transparan)

### 5. **Update Controller**
**File:** `app/Http/Controllers/AuthController.php`

#### Method: `managerProgramsStore()`
- Update validation rules untuk menggunakan field baru
- Hapus validasi durasi minimal (karena sekarang format string)
- Process training_schedules dari input array
- Filter dan simpan training_schedules dengan benar

#### Method: `managerProgramsEdit()`
- Update validation rules untuk menggunakan field baru
- Hapus validasi durasi minimal
- Process training_schedules dengan cara yang sama

## 🎯 Fitur Fleksibilitas Durasi

Dengan format string, durasi sekarang bisa:
- `24 jam`
- `120 menit`
- `2 hari`
- `1 minggu`
- `2 bulan`
- Atau kombinasi apapun sesuai kebutuhan

## 📅 Jadwal Pelatihan - Fitur Dinamis

### Fitur:
- ✅ Tambah multiple gelombang dan waktu
- ✅ Edit setiap gelombang dan waktu
- ✅ Hapus gelombang yang tidak diperlukan
- ✅ Tampilkan di landing page dengan format rapi

### Contoh Data:
```
Gelombang 1: 10 April - 20 April
Gelombang 2: 25 April - 5 Mei
Gelombang 3: 10 Mei - 20 Mei
```

## 🔄 Migrasi Database

Jalankan command:
```bash
php artisan migrate
```

Migrasi akan:
1. Menambah field `duration` (nullable)
2. Menambah field `training_schedules` (JSON)
3. Konversi data durasi lama ke format string
4. Hapus field `duration_hours` dan `duration_unit`

## ✅ Testing Checklist

Setelah implementasi, test fitur berikut:

- [ ] Buat program baru dengan durasi fleksibel
- [ ] Tambah multiple jadwal pelatihan
- [ ] Edit program existing dan update durasi/jadwal
- [ ] Lihat program di landing page dengan jadwal tampil benar
- [ ] Hapus jadwal pelatihan
- [ ] Verifikasi data tersimpan di database dengan benar

## 📝 Catatan Penting

1. **Backward Compatibility**: Migrasi secara otomatis mengkonversi data lama
2. **Validasi**: Training schedules bersifat opsional (nullable)
3. **Format Fleksibel**: Tidak ada batasan format durasi string
4. **JSON Storage**: Data disimpan dengan struktur array di JSON
