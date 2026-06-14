<?php

namespace App\Http\Controllers\Api;

use App\Models\Program;
use App\Models\RegistrationGroup;
use App\Models\RegistrationIndividual;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LandingRegistrationApiController extends BaseApiController
{
    public function getPrograms(): JsonResponse
    {
        $programs = Program::where('is_active', true)
            ->select('id', 'name')
            ->get();

        return $this->successResponse('Data program berhasil diambil.', $programs);
    }

    public function submitIndividu(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'no_handphone' => ['required', 'string', 'max:20'],
            'alamat' => ['required', 'string', 'max:500'],
            'pendidikan_terakhir' => ['required', 'string', 'max:100'],
            'motivasi' => ['required', 'string', 'max:1000'],
            'program_id' => ['required', 'integer', 'exists:programs,id'],
        ]);

        $registration = RegistrationIndividual::create($validated + [
            'status' => 'pending',
        ]);

        return $this->successResponse('Pendaftaran individu berhasil dikirim. Kami akan menghubungi Anda segera.', $registration, 201);
    }

    public function submitKelompok(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama_lembaga' => ['required', 'string', 'max:255'],
            'alamat_pic' => ['required', 'string', 'max:500'],
            'email_pic' => ['required', 'email', 'max:255'],
            'no_handphone_pic' => ['required', 'string', 'max:20'],
            'nama_pic' => ['required', 'string', 'max:255'],
            'jumlah_peserta' => ['required', 'integer', 'min:1'],
            'program_id' => ['required', 'integer', 'exists:programs,id'],
            'surat_resmi' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $payload = [
            'nama_lembaga' => $validated['nama_lembaga'],
            'alamat_pic' => $validated['alamat_pic'],
            'email_pic' => $validated['email_pic'],
            'no_handphone_pic' => $validated['no_handphone_pic'],
            'nama_pic' => $validated['nama_pic'],
            'jumlah_peserta' => $validated['jumlah_peserta'],
            'program_id' => $validated['program_id'],
            'status' => 'pending',
        ];

        if ($request->hasFile('surat_resmi')) {
            $payload['surat_resmi'] = $request->file('surat_resmi')->store('registration_documents', 'public');
        }

        $registration = RegistrationGroup::create($payload);

        return $this->successResponse('Pendaftaran kelompok berhasil dikirim. Kami akan menghubungi Anda segera.', $registration, 201);
    }

    public function updateIndividuProgram(Request $request, RegistrationIndividual $registration): JsonResponse
    {
        $validated = $request->validate([
            'program_id' => ['required', 'integer', 'exists:programs,id'],
        ]);

        $registration->update($validated);

        return $this->successResponse('Program peserta berhasil diperbarui.', $registration);
    }

    public function updateKelompokProgram(Request $request, RegistrationGroup $registration): JsonResponse
    {
        $validated = $request->validate([
            'program_id' => ['required', 'integer', 'exists:programs,id'],
        ]);

        $registration->update($validated);

        return $this->successResponse('Program peserta berhasil diperbarui.', $registration);
    }
}
