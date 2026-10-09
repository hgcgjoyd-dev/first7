<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SiswaController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'kelas' => ['nullable', 'integer', 'exists:kelas,id_kelas'],
        ]);

        $students = Siswa::query()
            ->with(['kelas', 'user'])
            ->when($filters['q'] ?? null, function (Builder $query, string $search): void {
                $query->where(fn (Builder $query) => $query
                    ->where('nama_siswa', 'like', "%{$search}%")
                    ->orWhere('no_siswa', 'like', "%{$search}%"));
            })
            ->when($filters['kelas'] ?? null, fn (Builder $query, int $classId) => $query->where('id_kelas', $classId))
            ->orderBy('nama_siswa')
            ->orderBy('id_siswa')
            ->paginate(15)
            ->withQueryString();

        return view('admin.crud.index', [
            'title' => 'Data Siswa',
            'createRoute' => 'admin.siswa.create',
            'indexRoute' => 'admin.siswa.index',
            'columns' => [
                ['key' => 'no_siswa', 'label' => 'NIS'],
                ['key' => 'nama_siswa', 'label' => 'Nama'],
                ['key' => 'kelas.nama_kelas', 'label' => 'Kelas'],
                ['key' => 'user.email', 'label' => 'Akun'],
            ],
            'records' => $students,
            'filters' => [
                ['name' => 'q', 'label' => 'Nama atau NIS', 'type' => 'search', 'value' => $filters['q'] ?? ''],
                [
                    'name' => 'kelas',
                    'label' => 'Kelas',
                    'type' => 'select',
                    'value' => $filters['kelas'] ?? '',
                    'options' => Kelas::query()->orderBy('tingkat')->orderBy('nama_kelas')->pluck('nama_kelas', 'id_kelas'),
                ],
            ],
        ]);
    }

    public function create(): View
    {
        return view('admin.crud.form', [
            'title' => 'Tambah Siswa',
            'indexRoute' => 'admin.siswa.index',
            'submitRoute' => 'admin.siswa.store',
            'record' => null,
            'fields' => $this->fields(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $student = Siswa::create($request->validate($this->rules()));

        return redirect()->route('admin.siswa.show', $student)->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function show(Siswa $siswa): View
    {
        $siswa->load(['kelas', 'user']);

        return view('admin.crud.show', [
            'title' => 'Detail Siswa',
            'record' => $siswa,
            'indexRoute' => 'admin.siswa.index',
            'editRoute' => 'admin.siswa.edit',
            'deleteRoute' => 'admin.siswa.destroy',
            'details' => [
                'NIS' => $siswa->no_siswa,
                'Nama' => $siswa->nama_siswa,
                'Kelas' => $siswa->kelas->nama_kelas,
                'Nomor absen' => $siswa->nomor_absen,
                'Jenis kelamin' => $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan',
                'Tanggal lahir' => $siswa->tanggal_lahir?->format('d-m-Y'),
                'Nama wali' => $siswa->nama_wali,
                'Telepon wali' => $siswa->no_telp_wali,
                'Akun login' => $siswa->user?->email ?? 'Belum ditautkan',
                'Poin pelanggaran' => $siswa->poin_pelanggaran,
            ],
        ]);
    }

    public function edit(Siswa $siswa): View
    {
        return view('admin.crud.form', [
            'title' => 'Edit Siswa',
            'indexRoute' => 'admin.siswa.index',
            'submitRoute' => 'admin.siswa.update',
            'record' => $siswa,
            'fields' => $this->fields($siswa),
        ]);
    }

    public function update(Request $request, Siswa $siswa): RedirectResponse
    {
        $student = $request->validate($this->rules($siswa));
        DB::transaction(function () use ($student, $siswa): void {
            $previousAccountId = $siswa->id_user;
            $siswa->update($student);

            if ($previousAccountId && (int) $previousAccountId !== (int) ($student['id_user'] ?? 0)) {
                User::query()->whereKey($previousAccountId)->update(['status_aktif' => false]);
            }
        });

        return redirect()->route('admin.siswa.show', $siswa)->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Siswa $siswa): RedirectResponse
    {
        if ($siswa->absensi()->exists()
            || $siswa->pelanggaranSiswa()->exists()
            || $siswa->konseling()->exists()
            || $siswa->prestasi()->exists()
            || (Schema::hasTable('izin') && $siswa->izin()->exists())) {
            return back()->withErrors(['delete' => 'Siswa memiliki riwayat akademik atau pembinaan. Pindahkan atau arsipkan riwayatnya sebelum menghapus profil.']);
        }

        DB::transaction(function () use ($siswa): void {
            $siswa->user()->update(['status_aktif' => false]);
            $siswa->delete();
        });

        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa berhasil dihapus dan akun terkait dinonaktifkan.');
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function rules(?Siswa $student = null): array
    {
        return [
            'no_siswa' => ['required', 'digits:7', Rule::unique('siswa', 'no_siswa')->ignore($student?->id_siswa, 'id_siswa')],
            'nama_siswa' => ['required', 'string', 'max:100'],
            'id_kelas' => ['required', 'integer', 'exists:kelas,id_kelas'],
            'nomor_absen' => ['nullable', 'integer', 'between:1,999'],
            'jenis_kelamin' => ['required', Rule::in(['L', 'P'])],
            'tanggal_lahir' => ['nullable', 'date', 'before_or_equal:today'],
            'nama_wali' => ['nullable', 'string', 'max:100'],
            'no_telp_wali' => ['nullable', 'string', 'max:20'],
            'id_user' => [
                'nullable',
                'integer',
                Rule::exists('user', 'id_user')->where('role', 'siswa')->where('status_aktif', true),
                Rule::unique('siswa', 'id_user')->ignore($student?->id_siswa, 'id_siswa'),
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function fields(?Siswa $student = null): array
    {
        $availableAccounts = User::query()
            ->where('role', 'siswa')
            ->where('status_aktif', true)
            ->where(fn (Builder $query) => $query
                ->whereDoesntHave('siswa')
                ->when($student?->id_user, fn (Builder $query, int $id) => $query->orWhere('id_user', $id)))
            ->orderBy('nama')
            ->get()
            ->mapWithKeys(fn (User $user): array => [$user->id_user => "{$user->nama} ({$user->email})"]);

        return [
            ['name' => 'no_siswa', 'label' => 'NIS (7 digit)', 'required' => true],
            ['name' => 'nama_siswa', 'label' => 'Nama lengkap', 'required' => true],
            [
                'name' => 'id_kelas',
                'label' => 'Kelas',
                'type' => 'select',
                'required' => true,
                'options' => Kelas::query()->orderBy('tingkat')->orderBy('nama_kelas')->pluck('nama_kelas', 'id_kelas'),
            ],
            ['name' => 'nomor_absen', 'label' => 'Nomor absen', 'type' => 'number'],
            ['name' => 'jenis_kelamin', 'label' => 'Jenis kelamin', 'type' => 'select', 'required' => true, 'options' => ['L' => 'Laki-laki', 'P' => 'Perempuan']],
            ['name' => 'tanggal_lahir', 'label' => 'Tanggal lahir', 'type' => 'date'],
            ['name' => 'nama_wali', 'label' => 'Nama wali'],
            ['name' => 'no_telp_wali', 'label' => 'Nomor telepon wali', 'type' => 'tel'],
            ['name' => 'id_user', 'label' => 'Akun siswa', 'type' => 'select', 'options' => ['' => 'Belum ditautkan'] + $availableAccounts->all()],
        ];
    }
}
