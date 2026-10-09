@extends('layouts.dashboard')

@section('title', $title)

@section('content')
    <section class="flex flex-wrap items-end justify-between gap-4 rounded-2xl bg-white p-6 shadow-sm">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wide text-blue-700">Pengelolaan data</p>
            <h1 class="mt-2 text-2xl font-bold">{{ $title }}</h1>
        </div>
        <a href="{{ route($createRoute) }}" class="rounded-xl bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-800">Tambah data</a>
    </section>

    @if ($filters !== [])
        <form method="GET" action="{{ route($indexRoute) }}" class="grid gap-3 rounded-2xl bg-white p-5 shadow-sm sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($filters as $filter)
                <label class="block text-sm font-medium text-slate-700">
                    <span>{{ $filter['label'] }}</span>
                    @if (($filter['type'] ?? '') === 'select')
                        <select name="{{ $filter['name'] }}" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2">
                            <option value="">Semua</option>
                            @foreach ($filter['options'] as $value => $label)
                                <option value="{{ $value }}" @selected((string) $filter['value'] === (string) $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    @else
                        <input type="{{ $filter['type'] }}" name="{{ $filter['name'] }}" value="{{ $filter['value'] }}" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">
                    @endif
                </label>
            @endforeach
            <div class="flex items-end gap-2">
                <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-semibold text-white">Filter</button>
                <a href="{{ route($indexRoute) }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Reset</a>
            </div>
        </form>
    @endif

    <section class="overflow-hidden rounded-2xl bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        @foreach ($columns as $column)
                            <th class="whitespace-nowrap px-4 py-3 font-semibold">{{ $column['label'] }}</th>
                        @endforeach
                        <th class="px-4 py-3 font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($records as $record)
                        <tr>
                            @foreach ($columns as $column)
                                <td class="px-4 py-3">{{ data_get($record, $column['key']) ?? '-' }}</td>
                            @endforeach
                            <td class="whitespace-nowrap px-4 py-3">
                                <a href="{{ route(str_replace('.index', '.show', $indexRoute), $record) }}" class="font-semibold text-blue-700 hover:underline">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($columns) + 1 }}" class="px-4 py-8 text-center text-slate-500">Belum ada data.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-200 px-4 py-3">{{ $records->links() }}</div>
    </section>
@endsection
