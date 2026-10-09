<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\GuruBk;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'role' => ['nullable', Rule::in(['siswa', 'guru_bk', 'guru', 'admin'])],
        ]);

        $users = User::query()
            ->with(['siswa', 'guru'])
            ->when($filters['q'] ?? null, fn (Builder $query, string $search) => $query
                ->where(fn (Builder $query) => $query
                    ->where('nama', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")))
            ->when($filters['role'] ?? null, fn (Builder $query, string $role) => $query->where('role', $role))
            ->orderBy('nama')
            ->orderBy('id_user')
            ->paginate(20)
            ->withQueryString();

        return view('admin.crud.index', [
            'title' => 'Akun Pengguna',
            'createRoute' => 'admin.users.create',
            'indexRoute' => 'admin.users.index',
            'columns' => [
                ['key' => 'nama', 'label' => 'Nama'],
                ['key' => 'email', 'label' => 'Email'],
                ['key' => 'role_label', 'label' => 'Role'],
                ['key' => 'status_label', 'label' => 'Status'],
            ],
            'records' => $users,
            'filters' => [
                ['name' => 'q', 'label' => 'Nama, email, atau username', 'type' => 'search', 'value' => $filters['q'] ?? ''],
                [
                    'name' => 'role',
                    'label' => 'Role',
                    'type' => 'select',
                    'value' => $filters['role'] ?? '',
                    'options' => ['siswa' => 'Siswa', 'guru_bk' => 'Guru BK', 'guru' => 'Guru biasa', 'admin' => 'Admin'],
                ],
            ],
        ]);
    }

    public function create(): View
    {
        return $this->form(null, 'Tambah Akun', 'admin.users.store');
    }

    public function store(Request $request): RedirectResponse
    {
        $attributes = $request->validate($this->rules());

        $user = DB::transaction(function () use ($attributes): User {
            $user = User::create([
                'username' => $attributes['username'],
                'nama' => $attributes['nama'],
                'email' => $attributes['email'],
                'password' => $attributes['password'],
                'role' => $attributes['role'],
                'status_aktif' => $attributes['status_aktif'] ?? true,
            ]);

            $this->syncProfile($user, $attributes);

            return $user;
        });

        return redirect()->route('admin.users.show', $user)->with('success', 'Akun pengguna berhasil dibuat.');
    }

    public function show(User $user): View
    {
        $user->load(['siswa', 'guru.guruBk']);

        $profile = match ($user->role) {
            'siswa' => $user->siswa?->nama_siswa,
            'guru', 'guru_bk' => $user->guru?->nama_guru,
            default => 'Administrator',
        };

        return view('admin.crud.show', [
            'title' => 'Detail Akun',
            'record' => $user,
            'indexRoute' => 'admin.users.index',
            'editRoute' => 'admin.users.edit',
            'deleteRoute' => 'admin.users.destroy',
            'details' => [
                'Nama' => $user->nama,
                'Username' => $user->username,
                'Email' => $user->email,
                'Role' => $user->role_label,
                'Status' => $user->status_label,
                'Profil tertaut' => $profile ?? 'Belum tertaut',
            ],
        ]);
    }

    public function edit(User $user): View
    {
        $user->load(['siswa', 'guru']);

        return $this->form($user, 'Edit Akun', 'admin.users.update');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $attributes = $request->validate($this->rules($user));

        if ($request->user()->is($user)
            && ($attributes['role'] !== $user->role || ! $attributes['status_aktif'])) {
            return back()->withInput()->withErrors(['role' => 'Anda tidak dapat mengubah role atau menonaktifkan akun sendiri.']);
        }

        DB::transaction(function () use ($attributes, $user): void {
            if ($this->wouldRemoveLastActiveAdmin($user, $attributes['role'], (bool) $attributes['status_aktif'])) {
                throw ValidationException::withMessages([
                    'role' => 'Akun admin aktif terakhir tidak dapat dinonaktifkan atau diubah rolenya.',
                ]);
            }

            $this->detachChangedProfile($user, $attributes);
            $user->update([
                'username' => $attributes['username'],
                'nama' => $attributes['nama'],
                'email' => $attributes['email'],
                'role' => $attributes['role'],
                'status_aktif' => $attributes['status_aktif'],
                ...(filled($attributes['password'] ?? null) ? ['password' => $attributes['password']] : []),
            ]);

            $this->syncProfile($user, $attributes);
        });

        return redirect()->route('admin.users.show', $user)->with('success', 'Akun pengguna berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($request->user()->is($user)) {
            return back()->withErrors(['delete' => 'Anda tidak dapat menonaktifkan akun yang sedang digunakan.']);
        }

        if ($user->isAdmin() && $user->status_aktif && User::query()->where('role', 'admin')->where('status_aktif', true)->count() <= 1) {
            return back()->withErrors(['delete' => 'Admin aktif terakhir tidak dapat dinonaktifkan.']);
        }

        DB::transaction(function () use ($user): void {
            $user->refresh();

            if ($user->isAdmin()
                && $user->status_aktif
                && User::query()->where('role', 'admin')->where('status_aktif', true)->lockForUpdate()->get(['id_user'])->count() <= 1) {
                throw ValidationException::withMessages([
                    'delete' => 'Admin aktif terakhir tidak dapat dinonaktifkan.',
                ]);
            }

            $user->update(['status_aktif' => false]);
        });

        return redirect()->route('admin.users.index')->with('success', 'Akun berhasil dinonaktifkan.');
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function rules(?User $user = null): array
    {
        $role = request()->input('role');
        $availableStudent = Rule::exists('siswa', 'id_siswa')->where(fn ($query) => $query
            ->whereNull('id_user')
            ->when($user, fn ($query) => $query->orWhere('id_user', $user->id_user)));
        $availableTeacher = Rule::exists('guru', 'id_guru')->where(fn ($query) => $query
            ->whereNull('id_user')
            ->when($user, fn ($query) => $query->orWhere('id_user', $user->id_user)));

        return [
            'username' => ['required', 'string', 'max:50', Rule::unique('user', 'username')->ignore($user?->id_user, 'id_user')],
            'nama' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', Rule::unique('user', 'email')->ignore($user?->id_user, 'id_user')],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:12', 'confirmed'],
            'role' => ['required', Rule::in(['siswa', 'guru_bk', 'guru', 'admin'])],
            'status_aktif' => ['required', 'boolean'],
            'id_siswa' => [
                Rule::requiredIf($role === 'siswa'),
                'exclude_unless:role,siswa',
                'nullable',
                'integer',
                $availableStudent,
            ],
            'id_guru' => [
                Rule::requiredIf(in_array($role, ['guru', 'guru_bk'], true)),
                'exclude_unless:role,guru,guru_bk',
                'nullable',
                'integer',
                $availableTeacher,
            ],
        ];
    }

    private function form(?User $user, string $title, string $submitRoute): View
    {
        $studentOptions = Siswa::query()
            ->where(fn (Builder $query) => $query
                ->whereNull('id_user')
                ->when($user?->siswa, fn (Builder $query, Siswa $student) => $query->orWhereKey($student->id_siswa)))
            ->orderBy('nama_siswa')
            ->get(['id_siswa', 'no_siswa', 'nama_siswa'])
            ->mapWithKeys(fn (Siswa $student): array => [$student->id_siswa => "{$student->nama_siswa} ({$student->no_siswa})"]);

        $teacherOptions = Guru::query()
            ->where(fn (Builder $query) => $query
                ->whereNull('id_user')
                ->when($user?->guru, fn (Builder $query, Guru $teacher) => $query->orWhereKey($teacher->id_guru)))
            ->orderBy('nama_guru')
            ->get(['id_guru', 'no_guru', 'nama_guru'])
            ->mapWithKeys(fn (Guru $teacher): array => [$teacher->id_guru => "{$teacher->nama_guru} ({$teacher->no_guru})"]);

        return view('admin.crud.form', [
            'title' => $title,
            'indexRoute' => 'admin.users.index',
            'submitRoute' => $submitRoute,
            'record' => $user,
            'fields' => [
                ['name' => 'username', 'label' => 'Username', 'required' => true],
                ['name' => 'nama', 'label' => 'Nama lengkap', 'required' => true],
                ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true],
                ['name' => 'role', 'label' => 'Role', 'type' => 'select', 'required' => true, 'value' => $user?->role ?? 'siswa', 'options' => ['siswa' => 'Siswa', 'guru_bk' => 'Guru BK', 'guru' => 'Guru biasa', 'admin' => 'Admin']],
                ['name' => 'status_aktif', 'label' => 'Status akun', 'type' => 'select', 'required' => true, 'value' => $user?->status_aktif ?? true, 'options' => [1 => 'Aktif', 0 => 'Nonaktif']],
                ['name' => 'id_siswa', 'label' => 'Profil siswa', 'type' => 'select', 'value' => $user?->siswa?->id_siswa, 'options' => ['' => 'Pilih jika role siswa'] + $studentOptions->all()],
                ['name' => 'id_guru', 'label' => 'Profil guru', 'type' => 'select', 'value' => $user?->guru?->id_guru, 'options' => ['' => 'Pilih jika role guru'] + $teacherOptions->all()],
                ['name' => 'password', 'label' => $user ? 'Password baru (kosongkan jika tidak diubah)' : 'Password (minimal 12 karakter)', 'type' => 'password', 'required' => ! $user],
                ['name' => 'password_confirmation', 'label' => 'Konfirmasi password', 'type' => 'password', 'required' => ! $user],
            ],
        ]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function detachChangedProfile(User $user, array $attributes): void
    {
        if ($user->siswa && ($attributes['role'] !== 'siswa' || (int) $attributes['id_siswa'] !== (int) $user->siswa->id_siswa)) {
            $user->siswa()->update(['id_user' => null]);
        }

        if ($user->guru && (! in_array($attributes['role'], ['guru', 'guru_bk'], true) || (int) $attributes['id_guru'] !== (int) $user->guru->id_guru)) {
            $user->guru()->update(['id_user' => null]);
        }
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function syncProfile(User $user, array $attributes): void
    {
        if ($attributes['role'] === 'siswa') {
            Siswa::query()->whereKey($attributes['id_siswa'])->update(['id_user' => $user->id_user]);

            return;
        }

        if (in_array($attributes['role'], ['guru', 'guru_bk'], true)) {
            $teacher = Guru::query()->findOrFail($attributes['id_guru']);
            $teacher->update(['id_user' => $user->id_user]);

            $counselor = GuruBk::query()->firstOrNew(['id_guru' => $teacher->id_guru]);
            $counselor->status_aktif = $attributes['role'] === 'guru_bk';

            if ($attributes['role'] === 'guru_bk' || $counselor->exists) {
                $counselor->save();
            }
        }
    }

    private function wouldRemoveLastActiveAdmin(User $user, string $role, bool $active): bool
    {
        return $user->isAdmin()
            && $user->status_aktif
            && ($role !== 'admin' || ! $active)
            && User::query()->where('role', 'admin')->where('status_aktif', true)->lockForUpdate()->get(['id_user'])->count() <= 1;
    }
}
