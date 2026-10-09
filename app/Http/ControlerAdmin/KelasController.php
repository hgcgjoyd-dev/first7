<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Kelas;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KelasController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'tingkat' => ['nullable', Rule::in(['X', 'XI', 'XII'])],
        ]);

        $classes = Kelas::query()
            ->with('waliKelas')
            ->withCount('siswa')
            ->when($filters['q'] ?? null, fn (Builder $query, string $search) => $query->where('nama_kelas', 'like', "%{$search}%"))
            ->when($filters['tingkat'] ?? null, fn (Builder $query, string $grade) => $query->where('tingkat', $grade))
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->orderBy('id_kelas')
            ->paginate(15)
            ->withQueryString();

        return view('admin.crud.index', [
            'title' => 'Data Kelas',
            'createRoute' => 'admin.kelas.create',
            'indexRoute' => 'admin.kelas.index',
            'columns' => [
                ['key' => 'nama_kelas', 'label' => 'Nama kelas'],
                ['key' => 'tingkat', 'label' => 'Tingkat'],
                ['key' => 'jurusan', 'label' => 'Jurusan'],
                ['key' => 'siswa_count', 'label' => 'Jumlah siswa'],
                ['key' => 'waliKelas.nama_guru', 'label' => 'Wali kelas'],
            ],
            'records' => $classes,
            'filters' => [
                ['name' => 'q', 'label' => 'Nama kelas', 'type' => 'search', 'value' => $filters['q'] ?? ''],
                ['name' => 'tingkat', 'label' => 'Tingkat', 'type' => 'select', 'value' => $filters['tingkat'] ?? '', 'options' => ['X' => 'X', 'XI' => 'XI', 'XII' => 'XII']],
            ],
        ]);
    }

    public function create(): View
    {
        return view('admin.crud.form', [
            'title' => 'Tambah Kelas',
            'indexRoute' => 'admin.kelas.index',
            'submitRoute' => 'admin.kelas.store',
            'record' => null,
            'fields' => $this->fields(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $class = Kelas::create($request->validate($this->rules()));

        return redirect()->route('admin.kelas.show', $class)->with('success', 'Data kelas berhasil ditambahkan.');
    }

    public function show(Kelas $kelas): View
    {
        $kelas->load('waliKelas')->loadCount('siswa');

        return view('admin.crud.show', [
            'title' => 'Detail Kelas',
            'record' => $kelas,
            'indexRoute' => 'admin.kelas.index',
            'editRoute' => 'admin.kelas.edit',
            'deleteRoute' => 'admin.kelas.destroy',
            'details' => [
                'Nama kelas' => $kelas->nama_kelas,
                'Tingkat' => $kelas->tingkat,
                'Jurusan' => $kelas->jurusan,
                'Tahun ajaran' => $kelas->tahun_ajaran,
                'Wali kelas' => $kelas->waliKelas?->nama_guru ?? 'Belum ditetapkan',
                'Jumlah siswa' => $kelas->siswa_count,
            ],
        ]);
    }

    public function edit(Kelas $kelas): View
    {
        return view('admin.crud.form', [
            'title' => 'Edit Kelas',
            'indexRoute' => 'admin.kelas.index',
            'submitRoute' => 'admin.kelas.update',
            'record' => $kelas,
            'fields' => $this->fields($kelas),
        ]);
    }

    public function update(Request $request, Kelas $kelas): RedirectResponse
    {
        $kelas->update($request->validate($this->rules($kelas)));

        return redirect()->route('admin.kelas.show', $kelas)->with('success', 'Data kelas berhasil diperbarui.');
    }

    public function destroy(Kelas $kelas): RedirectResponse
    {
        if ($kelas->siswa()->exists() || $kelas->jadwalPelajaran()->exists()) {
            return back()->withErrors(['delete' => 'Kelas masih memiliki siswa atau jadwal pelajaran. Pindahkan data terkait sebelum menghapus kelas.']);
        }

        $kelas->delete();

        return redirect()->route('admin.kelas.index')->with('success', 'Data kelas berhasil dihapus.');
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function rules(?Kelas $class = null): array
    {
        return [
            'nama_kelas' => ['required', 'string', 'max:50', Rule::unique('kelas', 'nama_kelas')
                ->where('tahun_ajaran', request()->input('tahun_ajaran'))
                ->ignore($class?->id_kelas, 'id_kelas')],
            'tingkat' => ['required', Rule::in(['X', 'XI', 'XII'])],
            'jurusan' => ['required', 'string', 'max:50'],
            'tahun_ajaran' => ['required', 'string', 'max:20'],
            'wali_kelas_id' => ['nullable', 'integer', 'exists:guru,id_guru', Rule::unique('kelas', 'wali_kelas_id')->ignore($class?->id_kelas, 'id_kelas')],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function fields(?Kelas $class = null): array
    {
        return [
            ['name' => 'nama_kelas', 'label' => 'Nama rombongan belajar', 'required' => true],
            ['name' => 'tingkat', 'label' => 'Tingkat', 'type' => 'select', 'required' => true, 'options' => ['X' => 'X', 'XI' => 'XI', 'XII' => 'XII']],
            ['name' => 'jurusan', 'label' => 'Jurusan', 'required' => true],
            ['name' => 'tahun_ajaran', 'label' => 'Tahun ajaran', 'required' => true, 'value' => $class?->tahun_ajaran ?? '2026/2027'],
            ['name' => 'wali_kelas_id', 'label' => 'Wali kelas', 'type' => 'select', 'options' => ['' => 'Belum ditetapkan'] + Guru::query()->orderBy('nama_guru')->pluck('nama_guru', 'id_guru')->all()],
        ];
    }
}
