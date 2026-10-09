@extends('layouts.dashboard')

@section('title', $title)

@section('content')
    <section class="rounded-2xl bg-white p-6 shadow-sm">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-2xl font-bold">{{ $title }}</h1>
            <a href="{{ route($indexRoute) }}" class="text-sm font-semibold text-blue-700 hover:underline">Kembali ke daftar</a>
        </div>
        <form method="POST" action="{{ $record ? route($submitRoute, $record) : route($submitRoute) }}" class="space-y-5">
            @csrf
            @if ($record)
                @method('PUT')
            @endif

            <div class="grid gap-5 sm:grid-cols-2">
                @foreach ($fields as $field)
                    @php
                        $fieldName = $field['name'];
                        $fieldType = $field['type'] ?? 'text';
                        $fieldValue = old($fieldName, $field['value'] ?? $record?->getAttribute($fieldName));
                        if ($fieldValue instanceof \DateTimeInterface && $fieldType === 'date') {
                            $fieldValue = $fieldValue->format('Y-m-d');
                        } elseif ($fieldType === 'time' && is_string($fieldValue)) {
                            $fieldValue = substr($fieldValue, 0, 5);
                        }
                    @endphp
                    <label class="block text-sm font-medium text-slate-700 {{ $fieldType === 'textarea' ? 'sm:col-span-2' : '' }}">
                        <span>{{ $field['label'] }}</span>
                        @if ($fieldType === 'select')
                            <select name="{{ $fieldName }}" @required($field['required'] ?? false) class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5">
                                @foreach ($field['options'] as $value => $label)
                                    <option value="{{ $value }}" @selected((string) $fieldValue === (string) $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        @elseif ($fieldType === 'textarea')
                            <textarea name="{{ $fieldName }}" rows="4" @required($field['required'] ?? false) class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2.5">{{ $fieldValue }}</textarea>
                        @else
                            <input type="{{ $fieldType }}" name="{{ $fieldName }}" value="{{ $fieldType === 'password' ? '' : $fieldValue }}" @required($field['required'] ?? false) class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2.5">
                        @endif
                        @error($fieldName)
                            <span class="mt-1 block text-xs text-rose-700">{{ $message }}</span>
                        @enderror
                    </label>
                @endforeach
            </div>

            <div class="flex flex-wrap gap-3 border-t border-slate-200 pt-5">
                <button type="submit" class="rounded-xl bg-blue-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-800">Simpan</button>
                <a href="{{ route($indexRoute) }}" class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700">Batal</a>
            </div>
        </form>
    </section>
@endsection
