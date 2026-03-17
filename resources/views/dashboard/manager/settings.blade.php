@extends('dashboard.layouts.app')

@section('dashboard-content')
    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-lg font-bold text-slate-900">Pengaturan Sistem</h2>
            <a href="{{ route('dashboard.manager.home') }}"
                class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100">Kembali
                ke dashboard</a>
        </div>

        <form method="POST" action="{{ route('dashboard.manager.settings.update') }}" class="mt-5 grid gap-4 md:grid-cols-2">
            @csrf
            <div class="md:col-span-2">
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Nama
                    Organisasi</label>
                <input type="text" name="organization_name" required
                    value="{{ old('organization_name', $settingsData['organization_name']) }}"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-slate-500 focus:outline-none">
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Email Support</label>
                <input type="email" name="support_email" required
                    value="{{ old('support_email', $settingsData['support_email']) }}"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-slate-500 focus:outline-none">
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Timezone</label>
                <select name="timezone" required
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-slate-500 focus:outline-none">
                    <option value="Asia/Jakarta" @selected(old('timezone', $settingsData['timezone']) === 'Asia/Jakarta')>Asia/Jakarta</option>
                    <option value="Asia/Makassar" @selected(old('timezone', $settingsData['timezone']) === 'Asia/Makassar')>Asia/Makassar</option>
                    <option value="Asia/Jayapura" @selected(old('timezone', $settingsData['timezone']) === 'Asia/Jayapura')>Asia/Jayapura</option>
                </select>
            </div>

            <div class="md:col-span-2 flex justify-end">
                <button type="submit"
                    class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">Simpan
                    Pengaturan</button>
            </div>
        </form>
    </section>
@endsection
