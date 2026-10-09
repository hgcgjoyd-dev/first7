@extends('layouts.dashboard')

@section('title', 'Dashboard ' . $roleLabel)

@section('content')
    <section class="rounded-2xl bg-white p-6 shadow-sm">
        <p class="text-sm font-semibold uppercase tracking-wide text-blue-700">{{ $roleLabel }}</p>
        <h1 class="mt-2 text-2xl font-bold">Selamat datang, {{ $name }}</h1>
        <dl class="mt-5 grid gap-4 sm:grid-cols-2">
            @foreach ($profileDetails as $label => $value)
                <div class="rounded-xl bg-slate-50 p-4">
                    <dt class="text-sm text-slate-500">{{ $label }}</dt>
                    <dd class="mt-1 font-semibold">{{ $value }}</dd>
                </div>
            @endforeach
        </dl>
    </section>

    @if ($metrics !== [])
        <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($metrics as $label => $value)
                <div class="rounded-2xl bg-white p-5 shadow-sm">
                    <p class="text-sm text-slate-500">{{ $label }}</p>
                    <p class="mt-2 text-2xl font-bold">{{ $value }}</p>
                </div>
            @endforeach
        </section>
    @endif

    @if ($roleLabel === 'Guru BK')
        <section class="overflow-hidden rounded-2xl bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4">
                <h2 class="font-bold">Poin pelanggaran siswa</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-5 py-3 font-semibold">Siswa</th>
                            <th class="px-5 py-3 font-semibold">Kelas</th>
                            <th class="px-5 py-3 font-semibold">Poin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($students as $student)
                            <tr>
                                <td class="px-5 py-3">{{ $student->nama_siswa }}</td>
                                <td class="px-5 py-3">{{ $student->kelas->nama_kelas }}</td>
                                <td class="px-5 py-3">{{ $student->poin_pelanggaran }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-5 py-6 text-center text-slate-500">Belum ada data siswa.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    @endif
@endsection
