<?php

namespace App\Http\Controllers\Api;

use App\Models\WarehouseMaterial;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class ManagerStorageApiController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        if ($response = $this->ensureManagerRole($request)) {
            return $response;
        }

        if (! Schema::hasTable('warehouse_materials')) {
            return $this->successResponse('Daftar bahan gudang berhasil diambil.', []);
        }

        $search = trim((string) $request->query('search', ''));
        $query = WarehouseMaterial::query()->latest();

        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder->where('name', 'like', '%' . $search . '%')
                    ->orWhere('category', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        return $this->successResponse('Daftar bahan gudang berhasil diambil.', $query->get());
    }

    public function store(Request $request): JsonResponse
    {
        if ($response = $this->ensureManagerRole($request)) {
            return $response;
        }

        if (! Schema::hasTable('warehouse_materials')) {
            return $this->errorResponse('Tabel gudang belum tersedia. Jalankan migrasi terlebih dahulu.', null, 422);
        }

        $payload = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:150'],
            'category' => ['required', 'string', 'min:2', 'max:120'],
            'unit' => ['required', 'string', 'min:1', 'max:50'],
            'stock' => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string', 'max:1000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('storage/materials', 'public');
        }

        $material = WarehouseMaterial::create([
            'name' => $payload['name'],
            'category' => $payload['category'],
            'unit' => $payload['unit'],
            'stock' => (int) $payload['stock'],
            'minimum_stock' => (int) $payload['minimum_stock'],
            'description' => $payload['description'] ?? null,
            'image_path' => $imagePath,
        ]);

        return $this->successResponse('Bahan gudang berhasil ditambahkan.', $material, 201);
    }

    public function update(Request $request, WarehouseMaterial $material): JsonResponse
    {
        if ($response = $this->ensureManagerRole($request)) {
            return $response;
        }

        if (! Schema::hasTable('warehouse_materials')) {
            return $this->errorResponse('Tabel gudang belum tersedia. Jalankan migrasi terlebih dahulu.', null, 422);
        }

        $payload = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:150'],
            'category' => ['required', 'string', 'min:2', 'max:120'],
            'unit' => ['required', 'string', 'min:1', 'max:50'],
            'stock' => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string', 'max:1000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        if ($request->hasFile('image')) {
            if (! empty($material->image_path) && Storage::disk('public')->exists((string) $material->image_path)) {
                Storage::disk('public')->delete((string) $material->image_path);
            }

            $material->image_path = $request->file('image')->store('storage/materials', 'public');
        }

        $material->name = $payload['name'];
        $material->category = $payload['category'];
        $material->unit = $payload['unit'];
        $material->stock = (int) $payload['stock'];
        $material->minimum_stock = (int) $payload['minimum_stock'];
        $material->description = $payload['description'] ?? null;
        $material->save();

        return $this->successResponse('Bahan gudang berhasil diperbarui.', $material->fresh());
    }

    public function destroy(Request $request, WarehouseMaterial $material): JsonResponse
    {
        if ($response = $this->ensureManagerRole($request)) {
            return $response;
        }

        if (! Schema::hasTable('warehouse_materials')) {
            return $this->errorResponse('Tabel gudang belum tersedia. Jalankan migrasi terlebih dahulu.', null, 422);
        }

        if (! empty($material->image_path) && Storage::disk('public')->exists((string) $material->image_path)) {
            Storage::disk('public')->delete((string) $material->image_path);
        }

        WarehouseMaterial::query()->whereKey($material->id)->delete();

        return $this->successResponse('Bahan gudang berhasil dihapus.');
    }

    private function ensureManagerRole(Request $request): ?JsonResponse
    {
        $authUser = $request->session()->get('auth_user', []);

        if (($authUser['role'] ?? null) !== 'manager') {
            return $this->errorResponse('Akses ditolak. Hanya pengelola yang diizinkan.', null, 403);
        }

        return null;
    }
}
