<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\GuruBk;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class GuruController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'role' => ['nullable', Rule::in(['guru', 'guru_bk'])],
        ]);

        $teachers = Guru::query()
            ->with(['user', 'guruBk'])
            ->when($filters['q'] ?? null, function (Builder $query, string $search): void {
                $query->where(fn (Builder $query) => $query
                    ->where('nama_guru', 'like', "%{$search}%")
                    ->orWhere('no_guru', 'like', "%{$search}%"));
            })
            ->when($filters['role'] ?? null, function (Builder $query, string $role): void {
                if ($role === 'guru_bk') {
                    $query->whereHas('guruBk', fn (Builder $query) => $query->where('status_aktif', true));
                } else {
                    $query->whereDoesntHave('guruBk', fn (Builder $query) => $query->where('status_aktif', true));
                }
            })
            ->orderBy('nama_guru')
            ->orderBy('id_guru')
            ->paginate(15)
            ->withQueryString();

        return view('admin.crud.index', [
            'title' => 'Data Guru',
            'createRoute' => 'admin.guru.create',
            'indexRoute' => 'admin.guru.index',
            'columns' => [
                ['key' => 'no_guru', 'label' => 'NIP'],
                ['key' => 'nama_guru', 'label' => 'Nama'],
                ['key' => 'user.email', 'label' => 'Akun'],
                ['key' => 'role_label', 'label' => 'Role'],
            ],
            'records' => $teachers,
            'filters' => [
                ['name' => 'q', 'label' => 'Nama atau NIP', 'type' => 'search', 'value' => $filters['q'] ?? ''],
                [
                    'name' => 'role',
                    'label' => 'Role',
                    'type' => 'select',
                    'value' => $filters['role'] ?? '',
                    'options' => ['guru' => 'Guru biasa', 'guru_bk' => 'Guru BK'],
                ],
            ],
        ]);
    }

    public function create(): View
    {
        return view('admin.crud.form', [
            'title' => 'Tambah Guru',
            'indexRoute' => 'admin.guru.index',
            'submitRoute' => 'admin.guru.store',
            'record' => null,
            'fields' => $this->fields(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $attributes = $request->validate($this->rules($request));
        $this->validateAccountRole($attributes);

        $teacher = DB::transaction(function () use ($attributes): Guru {
            $teacher = Guru::create([
                'id_user' => $attributes['id_user'] ?? null,
                'no_guru' => $attributes['no_guru'],
                'nama_guru' => $attributes['nama_guru'],
                'jenis_kelamin' => $attributes['jenis_kelamin'],
                'no_telp' => $attributes['no_telp'] ?? null,
                'alamat' => $attributes['alamat'] ?? null,
            ]);

            if ($attributes['tipe_guru'] === 'guru_bk') {
                GuruBk::create(['id_guru' => $teacher->id_guru]);
            }

            return $teacher;
        });

        return redirect()->route('admin.guru.show', $teacher)->with('success', 'Data guru berhasil ditambahkan.');
    }

    public function show(Guru $guru): View
    {
        $guru->load(['user', 'guruBk']);

        return view('admin.crud.show', [
            'title' => 'Detail Guru',
            'record' => $guru,
            'indexRoute' => 'admin.guru.index',
            'editRoute' => 'admin.guru.edit',
            'deleteRoute' => 'admin.guru.destroy',
            'details' => [
                'NIP' => $guru->no_guru,
                'Nama' => $guru->nama_guru,
                'Role' => $guru->guruBk?->status_aktif ? 'Guru BK' : 'Guru biasa',
                'Jenis kelamin' => $guru->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan',
                'Nomor telepon' => $guru->no_telp,
                'Alamat' => $guru->alamat,
                'Akun login' => $guru->user?->email ?? 'Belum ditautkan',
            ],
        ]);
    }

    public function edit(Guru $guru): View
    {
        $guru->load('guruBk');

        return view('admin.crud.form', [
            'title' => 'Edit Guru',
            'indexRoute' => 'admin.guru.index',
            'submitRoute' => 'admin.guru.update',
            'record' => $guru,
            'fields' => $this->fields($guru),
        ]);
    }

    public function update(Request $request, Guru $guru): RedirectResponse
    {
        $attributes = $request->validate($this->rules($request, $guru));
        $this->validateAccountRole($attributes, $guru);

        DB::transaction(function () use ($attributes, $guru): void {
            $previousAccountId = $guru->id_user;
            $guru->update([
                'id_user' => $attributes['id_user'] ?? null,
                'no_guru' => $attributes['no_guru'],
                'nama_guru' => $attributes['nama_guru'],
                'jenis_kelamin' => $attributes['jenis_kelamin'],
                'no_telp' => $attributes['no_telp'] ?? null,
                'alamat' => $attributes['alamat'] ?? null,
            ]);

            $guruBk = GuruBk::query()->firstOrNew(['id_guru' => $guru->id_guru]);

            if ($attributes['tipe_guru'] === 'guru_bk') {
                $guruBk->status_aktif = true;
                $guruBk->save();
            } elseif ($guruBk->exists && $guruBk->status_aktif) {
                $guruBk->status_aktif = false;
                $guruBk->save();
            }

            if ($attributes['id_user'] ?? null) {
                User::query()->whereKey($attributes['id_user'])->update(['role' => $attributes['tipe_guru']]);
            }

            if ($previousAccountId && (int) $previousAccountId !== (int) ($attributes['id_user'] ?? 0)) {
                User::query()->whereKey($previousAccountId)->update(['status_aktif' => false]);
            }
        });

        return redirect()->route('admin.guru.show', $guru)->with('success', 'Data guru berhasil diperbarui.');
    }

    public function destroy(Guru $guru): RedirectResponse
    {
        if ($guru->kelasWali()->exists()
            || $guru->jadwalPelajaran()->exists()
            || $guru->absensi()->exists()
            || $guru->guruBk()->whereHas('konseling')->exists()
            || $guru->guruBk()->whereHas('pelanggaranSiswa')->exists()) {
            return back()->withErrors(['delete' => 'Guru masih memiliki data kelas atau riwayat tugas. Pindahkan relasi tersebut sebelum menghapus profil.']);
        }

        DB::transaction(function () use ($guru): void {
            $guru->user()->update(['status_aktif' => false]);
            $guru->delete();
        });

        return redirect()->route('admin.guru.index')->with('success', 'Data guru berhasil dihapus dan akun terkait dinonaktifkan.');
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function rules(Request $request, ?Guru $teacher = null): array
    {
        return [
            'no_guru' => ['required', 'digits:6', Rule::unique('guru', 'no_guru')->ignore($teacher?->id_guru, 'id_guru')],
            'nama_guru' => ['required', 'string', 'max:100'],
            'jenis_kelamin' => ['required', Rule::in(['L', 'P'])],
            'no_telp' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string', 'max:5000'],
            'tipe_guru' => ['required', Rule::in(['guru', 'guru_bk'])],
            'id_user' => [
                'nullable',
                'integer',
                Rule::exists('user', 'id_user')->where('status_aktif', true),
                Rule::unique('guru', 'id_user')->ignore($teacher?->id_guru, 'id_guru'),
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function fields(?Guru $teacher = null): array
    {
        $accounts = User::query()
            ->whereIn('role', ['guru', 'guru_bk'])
            ->where('status_aktif', true)
            ->where(fn (Builder $query) => $query
                ->whereDoesntHave('guru')
                ->when($teacher?->id_user, fn (Builder $query, int $id) => $query->orWhere('id_user', $id)))
            ->orderBy('nama')
            ->get()
            ->mapWithKeys(fn (User $user): array => [$user->id_user => "{$user->nama} ({$user->email})"]);

        return [
            ['name' => 'no_guru', 'label' => 'NIP (6 digit)', 'required' => true],
            ['name' => 'nama_guru', 'label' => 'Nama lengkap', 'required' => true],
            ['name' => 'jenis_kelamin', 'label' => 'Jenis kelamin', 'type' => 'select', 'required' => true, 'options' => ['L' => 'Laki-laki', 'P' => 'Perempuan']],
            ['name' => 'no_telp', 'label' => 'Nomor telepon', 'type' => 'tel'],
            ['name' => 'alamat', 'label' => 'Alamat', 'type' => 'textarea'],
            [
                'name' => 'tipe_guru',
                'label' => 'Role guru',
                'type' => 'select',
                'required' => true,
                'value' => $teacher?->guruBk?->status_aktif ? 'guru_bk' : 'guru',
                'options' => ['guru' => 'Guru biasa', 'guru_bk' => 'Guru BK'],
            ],
            ['name' => 'id_user', 'label' => 'Akun guru', 'type' => 'select', 'options' => ['' => 'Belum ditautkan'] + $accounts->all()],
        ];
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function validateAccountRole(array $attributes, ?Guru $teacher = null): void
    {
        if (! ($attributes['id_user'] ?? null)) {
            return;
        }

        $account = User::query()->findOrFail($attributes['id_user']);

        if ($account->role !== $attributes['tipe_guru'] && $teacher?->id_user !== $account->id_user) {
            throw ValidationException::withMessages([
                'id_user' => 'Akun yang dipilih belum menggunakan role guru yang sesuai.',
            ]);
        }
    }
}
