@extends('layouts.dashboard')

@section('title', 'Rekap Absensi')

@section('content')
    <section class="rounded-2xl bg-white p-6 shadow-sm">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-blue-700">Laporan</p>
                <h1 class="mt-1 text-2xl font-bold">Rekap Absensi</h1>
            </div>
            <a href="{{ route('admin.absensi.index') }}" class="font-semibold text-blue-700 hover:underline">Kelola absensi</a>
        </div>
        <form method="GET" class="mb-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
            <label class="text-sm font-medium">Dari tanggal<input type="date" name="mulai" value="{{ $filters['mulai'] ?? '' }}" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2"></label>
            <label class="text-sm font-medium">Sampai tanggal<input type="date" name="selesai" value="{{ $filters['selesai'] ?? '' }}" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2"></label>
            <label class="text-sm font-medium">Kelas<select name="kelas" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2"><option value="">Semua kelas</option>@foreach ($classes as $id => $name)<option value="{{ $id }}" @selected((string) ($filters['kelas'] ?? '') === (string) $id)>{{ $name }}</option>@endforeach</select></label>
            <label class="text-sm font-medium">Siswa<select name="siswa" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2"><option value="">Semua siswa</option>@foreach ($studentOptions as $id => $name)<option value="{{ $id }}" @selected((string) ($filters['siswa'] ?? '') === (string) $id)>{{ $name }}</option>@endforeach</select></label>
            <div class="flex items-end"><button type="submit" class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white">Tampilkan</button></div>
        </form>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50"><tr><th class="px-3 py-3">Siswa</th><th class="px-3 py-3">Kelas</th><th class="px-3 py-3">Hadir</th><th class="px-3 py-3">Izin</th><th class="px-3 py-3">Sakit</th><th class="px-3 py-3">Alpha</th><th class="px-3 py-3">Total</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($students as $student)
                        <tr><td class="px-3 py-3">{{ $student->nama_siswa }}</td><td class="px-3 py-3">{{ $student->kelas->nama_kelas }}</td><td class="px-3 py-3">{{ $student->hadir_count }}</td><td class="px-3 py-3">{{ $student->izin_count }}</td><td class="px-3 py-3">{{ $student->sakit_count }}</td><td class="px-3 py-3">{{ $student->alpha_count }}</td><td class="px-3 py-3 font-semibold">{{ $student->total_absensi }}</td></tr>
                    @empty
                        <tr><td colspan="7" class="px-3 py-6 text-center text-slate-500">Tidak ada data pada periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $students->links() }}</div>
    </section>
@endsection
