# Hybrid Architecture (Blade + REST API) - LMS Batik

## Prinsip Arsitektur

1. Blade dipakai untuk rendering halaman utama (landing, login, dashboard).
2. Seluruh proses data (CRUD) dipindahkan ke endpoint API berbasis JSON.
3. Routing dipisah:
   - `routes/web.php`: halaman (GET, return view/redirect view flow)
   - `routes/api.php`: endpoint API (JSON response)
4. Session-based authentication dipertahankan, namun eksekusi login/logout/forgot password dilakukan melalui API.
5. Semua API menggunakan format respons konsisten:

```json
{
  "success": true,
  "message": "Deskripsi hasil",
  "data": {}
}
```

## Mapping Fitur: Blade vs API

### Blade (View Layer)
- Landing page: `/`, `/tentang`, `/program`, `/galeri`, `/pendaftaran`
- Login page: `/login`
- Dashboard pages:
  - `/dashboard`
  - `/dashboard/pengelola/*` (GET)
  - `/dashboard/penguji/*` (GET)
  - `/dashboard/peserta/*` (GET)

### API (Data Layer)
- Authentication: login, logout, forgot password request/reset
- Forum diskusi: list/create/update/delete discussion + replies
- Modul instruktur: create/read/update/delete modul + upload image
- Progress peserta: progress + material state updates
- Upload tugas: assignment upload
- User management: CRUD participant/instructor/manager + update status
- Manajemen testimoni manager: CRUD

## Daftar Endpoint RESTful API

Base URL: `/api/v1`

### Authentication
- `POST /auth/login`
- `POST /auth/logout`
- `POST /auth/forgot-password/request`
- `POST /auth/forgot-password/reset`

### Pendaftaran Landing
- `POST /registrations/individual`
- `POST /registrations/group`

### Forum Diskusi
- `GET /forum/modules`
- `GET /forum/discussions`
- `POST /forum/discussions`
- `PUT /forum/discussions/{discussion}`
- `DELETE /forum/discussions/{discussion}`
- `PUT /forum/discussions/{discussion}/pin`
- `PUT /forum/discussions/{discussion}/close`
- `POST /forum/discussions/{discussion}/replies`
- `PUT /forum/replies/{reply}`
- `DELETE /forum/replies/{reply}`

### Modul Instruktur
- `GET /instructor/modules`
- `POST /instructor/modules`
- `POST /instructor/modules/content-image`
- `GET /instructor/modules/{module}`
- `PUT /instructor/modules/{module}`
- `PATCH /instructor/modules/{module}`
- `DELETE /instructor/modules/{module}`

### Progress & Tugas Peserta
- `GET /participant/modules`
- `GET /participant/modules/{moduleSlug}`
- `GET /participant/modules/{moduleSlug}/progress`
- `POST /participant/modules/{moduleSlug}/materials/{materialSlug}/start`
- `PUT /participant/modules/{moduleSlug}/materials/{materialSlug}/read`
- `PUT /participant/modules/{moduleSlug}/materials/{materialSlug}/watch`
- `POST /participant/modules/{moduleSlug}/assignments`

### User Management (Manager)
- `GET /manager/users?role=participant|instructor|manager`
- `POST /manager/users`
- `PUT /manager/users/{user}`
- `PATCH /manager/users/{user}`
- `PATCH /manager/users/{user}/status`
- `DELETE /manager/users/{user}`

### Testimoni (Manager)
- `GET /manager/testimonials`
- `POST /manager/testimonials`
- `PUT /manager/testimonials/{testimonial}`
- `PATCH /manager/testimonials/{testimonial}`
- `DELETE /manager/testimonials/{testimonial}`

## CSRF dan Session

Agar request API dari Blade aman dan tetap session-based:

1. Gunakan middleware `web` pada route API.
2. Ambil token dari meta tag:

```html
<meta name="csrf-token" content="{{ csrf_token() }}">
```

3. Kirim header berikut di `fetch`/`axios`:

```js
{
  'X-CSRF-TOKEN': csrfToken,
  'X-Requested-With': 'XMLHttpRequest',
  'Accept': 'application/json',
  'Content-Type': 'application/json'
}
```

## Alur Login hingga Dashboard (via API)

1. User membuka halaman `/login` (Blade).
2. Form login melakukan `POST /api/v1/auth/login` via fetch.
3. API validasi username/password.
4. Jika valid:
   - Session `auth_user` dibuat/diupdate
   - Session diregenerate
   - API kirim `redirect_url`
5. Frontend redirect ke `redirect_url` (umumnya `/dashboard` atau profile peserta jika force change password).
6. Route dashboard (web) membaca session dan menampilkan view sesuai role.

## Catatan Bisnis Logic yang Dipertahankan

- Multi-role login (`participant`, `instructor`, `manager`) tetap digunakan.
- Forgot password via WhatsApp tetap dipertahankan.
- Session-based auth tetap dipertahankan.
