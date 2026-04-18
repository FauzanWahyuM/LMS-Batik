<?php

namespace App\Http\Controllers\Api;

use App\Models\Testimonial;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ManagerTestimonialApiController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        if ($response = $this->ensureManagerRole($request)) {
            return $response;
        }

        if (! Schema::hasTable('testimonials')) {
            return $this->successResponse('Daftar testimoni berhasil diambil.', []);
        }

        $search = trim((string) $request->query('search', ''));
        $query = Testimonial::query()->orderBy('sort_order')->latest();

        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder->where('name', 'like', '%' . $search . '%')
                    ->orWhere('role_label', 'like', '%' . $search . '%')
                    ->orWhere('quote', 'like', '%' . $search . '%');
            });
        }

        return $this->successResponse('Daftar testimoni berhasil diambil.', $query->get());
    }

    public function store(Request $request): JsonResponse
    {
        if ($response = $this->ensureManagerRole($request)) {
            return $response;
        }

        if (! Schema::hasTable('testimonials')) {
            return $this->errorResponse('Tabel testimoni belum tersedia. Jalankan migrasi terlebih dahulu.', null, 422);
        }

        $payload = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:120'],
            'role_label' => ['nullable', 'string', 'max:150'],
            'quote' => ['required', 'string', 'min:10', 'max:1500'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $testimonial = Testimonial::create([
            'name' => $payload['name'],
            'role_label' => $payload['role_label'] ?? null,
            'quote' => $payload['quote'],
            'sort_order' => (int) ($payload['sort_order'] ?? 0),
            'is_active' => (bool) ($payload['is_active'] ?? false),
        ]);

        return $this->successResponse('Testimoni berhasil ditambahkan.', $testimonial, 201);
    }

    public function update(Request $request, Testimonial $testimonial): JsonResponse
    {
        if ($response = $this->ensureManagerRole($request)) {
            return $response;
        }

        if (! Schema::hasTable('testimonials')) {
            return $this->errorResponse('Tabel testimoni belum tersedia. Jalankan migrasi terlebih dahulu.', null, 422);
        }

        $payload = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:120'],
            'role_label' => ['nullable', 'string', 'max:150'],
            'quote' => ['required', 'string', 'min:10', 'max:1500'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $testimonial->update([
            'name' => $payload['name'],
            'role_label' => $payload['role_label'] ?? null,
            'quote' => $payload['quote'],
            'sort_order' => (int) ($payload['sort_order'] ?? 0),
            'is_active' => (bool) ($payload['is_active'] ?? false),
        ]);

        return $this->successResponse('Testimoni berhasil diperbarui.', $testimonial->fresh());
    }

    public function destroy(Request $request, Testimonial $testimonial): JsonResponse
    {
        if ($response = $this->ensureManagerRole($request)) {
            return $response;
        }

        if (! Schema::hasTable('testimonials')) {
            return $this->errorResponse('Tabel testimoni belum tersedia. Jalankan migrasi terlebih dahulu.', null, 422);
        }

        $testimonial->delete();

        return $this->successResponse('Testimoni berhasil dihapus.');
    }

    private function ensureManagerRole(Request $request): ?JsonResponse
    {
        $user = $request->session()->get('auth_user', []);

        if (($user['role'] ?? null) !== 'manager') {
            return $this->errorResponse('Akses ditolak. Hanya pengelola yang diizinkan.', null, 403);
        }

        return null;
    }
}
