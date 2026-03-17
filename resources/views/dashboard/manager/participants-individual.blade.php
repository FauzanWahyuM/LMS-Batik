@extends('dashboard.layouts.app')

@section('dashboard-content')
    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-lg font-bold text-slate-900">Data Peserta Individu</h2>
            <a href="{{ route('dashboard.manager.home') }}"
                class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100">Kembali
                ke dashboard</a>
        </div>

        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead>
                    <tr class="text-xs uppercase tracking-wider text-slate-500">
                        <th class="px-3 py-3">Nama</th>
                        <th class="px-3 py-3">Program</th>
                        <th class="px-3 py-3">Progres</th>
                        <th class="px-3 py-3">Status</th>
                        <th class="px-3 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($participants as $participant)
                        <tr>
                            <td class="px-3 py-3 font-semibold text-slate-900">{{ $participant['name'] }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ $participant['program'] }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ $participant['progress'] }}%</td>
                            <td class="px-3 py-3">
                                <span
                                    class="rounded-full bg-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-700">{{ $participant['status'] }}</span>
                            </td>
                            <td class="px-3 py-3">
                                <form method="POST"
                                    action="{{ route('dashboard.manager.participants.individual.update', ['participant' => $participant['id']]) }}"
                                    class="flex flex-col gap-2 sm:flex-row">
                                    @csrf
                                    <select name="status"
                                        class="rounded-lg border border-slate-300 bg-white px-2 py-1.5 text-xs text-slate-700">
                                        <option value="Aktif" @selected($participant['status'] === 'Aktif')>Aktif</option>
                                        <option value="Perlu Verifikasi" @selected($participant['status'] === 'Perlu Verifikasi')>Perlu Verifikasi
                                        </option>
                                        <option value="Nonaktif" @selected($participant['status'] === 'Nonaktif')>Nonaktif</option>
                                    </select>
                                    <button type="submit"
                                        class="rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white hover:bg-slate-700">Update</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
