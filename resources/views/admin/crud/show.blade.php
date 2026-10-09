@extends('layouts.dashboard')

@section('title', $title)

@section('content')
    <section class="rounded-2xl bg-white p-6 shadow-sm">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-2xl font-bold">{{ $title }}</h1>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route($indexRoute) }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Kembali</a>
                <a href="{{ route($editRoute, $record) }}" class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white">Edit</a>
            </div>
        </div>
        <dl class="grid gap-4 sm:grid-cols-2">
            @foreach ($details as $label => $value)
                <div class="rounded-xl bg-slate-50 p-4">
                    <dt class="text-sm text-slate-500">{{ $label }}</dt>
                    <dd class="mt-1 whitespace-pre-line font-semibold">{{ $value ?? '-' }}</dd>
                </div>
            @endforeach
        </dl>
        <form method="POST" action="{{ route($deleteRoute, $record) }}" class="mt-6 border-t border-slate-200 pt-5" onsubmit="return confirm('Yakin ingin menghapus atau menonaktifkan data ini?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="rounded-xl border border-rose-300 px-4 py-2.5 text-sm font-semibold text-rose-700 hover:bg-rose-50">Hapus / Nonaktifkan</button>
        </form>
    </section>
@endsection
