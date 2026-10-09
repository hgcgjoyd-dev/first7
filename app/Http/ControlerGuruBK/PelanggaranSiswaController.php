<?php

namespace App\Http\Controllers\GuruBk;

use App\Http\Controllers\Controller;
use App\Models\GuruBk;
use App\Models\JenisPelanggaran;
use App\Models\PelanggaranSiswa;
use App\Models\Siswa;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PelanggaranSiswaController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'siswa' => ['nullable', 'integer', 'exists:siswa,id_siswa'],
            'mulai' => ['nullable', 'date'],
            'selesai' => ['nullable', 'date', 'after_or_equal:mulai'],
        ]);

        $records = PelanggaranSiswa::query()
            ->with(['siswa.kelas', 'jenisPelanggaran', 'guruBk.guru'])
            ->when($filters['siswa'] ?? null, fn (Builder $query, int $studentId) => $query->where('id_siswa', $studentId))
            ->when($filters['mulai'] ?? null, fn (Builder $query, string $date) => $query->whereDate('tanggal_kejadian', '>=', $date))
            ->when($filters['selesai'] ?? null, fn (Builder $query, string $date) => $query->whereDate('tanggal_kejadian', '<=', $date))
            ->orderByDesc('tanggal_kejadian')
            ->orderByDesc('id_pelanggaran_siswa')
            ->paginate(20)
            ->withQueryString();

        return view('admin.crud.index', [
            'title' => 'Catatan Pelanggaran',
            'createRoute' => 'bk.pelanggaran.create',
            'indexRoute' => 'bk.pelanggaran.index',
            'columns' => [
                ['key' => 'tanggal_kejadian', 'label' => 'Tanggal'],
                ['key' => 'siswa.nama_siswa', 'label' => 'Siswa'],
                ['key' => 'jenisPelanggaran.nama_pelanggaran', 'label' => 'Jenis pelanggaran'],
                ['key' => 'poin', 'label' => 'Poin'],
                ['key' => 'guruBk.guru.nama_guru', 'label' => 'Dicatat oleh'],
            ],
            'records' => $records,
            'filters' => [
                ['name' => 'siswa', 'label' => 'Siswa', 'type' => 'select', 'value' => $filters['siswa'] ?? '', 'options' => $this->studentOptions()],
                ['name' => 'mulai', 'label' => 'Dari tanggal', 'type' => 'date', 'value' => $filters['mulai'] ?? ''],
                ['name' => 'selesai', 'label' => 'Sampai tanggal', 'type' => 'date', 'value' => $filters['selesai'] ?? ''],
            ],
        ]);
    }

    public function create(): View
    {
        return $this->form(null, 'Tambah Pelanggaran', 'bk.pelanggaran.store');
    }

    public function store(Request $request): RedirectResponse
    {
        $attributes = $request->validate($this->rules());
        $attributes['id_guru_bk'] = $this->recorderId($request, $attributes);

        $record = DB::transaction(function () use ($attributes): PelanggaranSiswa {
            $record = PelanggaranSiswa::create($attributes);
            $this->recalculatePoints((int) $record->id_siswa);

            return $record;
        });

        return redirect()->route('bk.pelanggaran.show', $record)->with('success', 'Catatan pelanggaran berhasil disimpan.');
    }

    public function show(PelanggaranSiswa $pelanggaran): View
    {
        $pelanggaran->load(['siswa.kelas', 'jenisPelanggaran', 'guruBk.guru']);

        return view('admin.crud.show', [
            'title' => 'Detail Pelanggaran',
            'record' => $pelanggaran,
            'indexRoute' => 'bk.pelanggaran.index',
            'editRoute' => 'bk.pelanggaran.edit',
            'deleteRoute' => 'bk.pelanggaran.destroy',
            'details' => [
                'Siswa' => $pelanggaran->siswa->nama_siswa,
                'Kelas' => $pelanggaran->siswa->kelas->nama_kelas,
                'Jenis pelanggaran' => $pelanggaran->jenisPelanggaran->nama_pelanggaran,
                'Tanggal' => $pelanggaran->tanggal_kejadian?->format('d-m-Y'),
                'Poin' => $pelanggaran->poin,
                'Keterangan' => $pelanggaran->keterangan,
                'Pencatat' => $pelanggaran->guruBk?->guru?->nama_guru ?? 'Tidak diketahui',
            ],
        ]);
    }

    public function edit(PelanggaranSiswa $pelanggaran): View
    {
        return $this->form($pelanggaran, 'Edit Pelanggaran', 'bk.pelanggaran.update');
    }

    public function update(Request $request, PelanggaranSiswa $pelanggaran): RedirectResponse
    {
        $attributes = $request->validate($this->rules());
        $attributes['id_guru_bk'] = $this->recorderId($request, $attributes);
        $previousStudentId = (int) $pelanggaran->id_siswa;

        DB::transaction(function () use ($attributes, $pelanggaran, $previousStudentId): void {
            $pelanggaran->update($attributes);
            $this->recalculatePoints($previousStudentId);
            $this->recalculatePoints((int) $pelanggaran->id_siswa);
        });

        return redirect()->route('bk.pelanggaran.show', $pelanggaran)->with('success', 'Catatan pelanggaran berhasil diperbarui.');
    }

    public function destroy(PelanggaranSiswa $pelanggaran): RedirectResponse
    {
        $studentId = (int) $pelanggaran->id_siswa;

        DB::transaction(function () use ($pelanggaran, $studentId): void {
            $pelanggaran->delete();
            $this->recalculatePoints($studentId);
        });

        return redirect()->route('bk.pelanggaran.index')->with('success', 'Catatan pelanggaran berhasil dihapus.');
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function rules(): array
    {
        return [
            'id_siswa' => ['required', 'integer', 'exists:siswa,id_siswa'],
            'id_pelanggaran' => ['required', 'integer', 'exists:jenis_pelanggaran,id_pelanggaran'],
            'tanggal_kejadian' => ['required', 'date', 'before_or_equal:today'],
            'poin' => ['required', 'integer', 'min:0', 'max:10000'],
            'keterangan' => ['required', 'string', 'max:5000'],
        ];
    }

    private function form(?PelanggaranSiswa $record, string $title, string $submitRoute): View
    {
        $types = JenisPelanggaran::query()
            ->orderBy('nama_pelanggaran')
            ->pluck('nama_pelanggaran', 'id_pelanggaran');

        $counselors = GuruBk::query()
            ->where('status_aktif', true)
            ->with('guru')
            ->get()
            ->mapWithKeys(fn (GuruBk $counselor): array => [$counselor->id_guru_bk => $counselor->guru->nama_guru]);

        return view('admin.crud.form', [
            'title' => $title,
            'indexRoute' => 'bk.pelanggaran.index',
            'submitRoute' => $submitRoute,
            'record' => $record,
            'fields' => [
                ['name' => 'id_siswa', 'label' => 'Siswa', 'type' => 'select', 'required' => true, 'options' => $this->studentOptions()],
                ['name' => 'id_pelanggaran', 'label' => 'Jenis pelanggaran', 'type' => 'select', 'required' => true, 'options' => $types],
                ['name' => 'tanggal_kejadian', 'label' => 'Tanggal kejadian', 'type' => 'date', 'required' => true],
                ['name' => 'poin', 'label' => 'Poin', 'type' => 'number', 'required' => true, 'value' => $record?->poin],
                ['name' => 'keterangan', 'label' => 'Keterangan', 'type' => 'textarea', 'required' => true],
                ...(request()->user()->isAdmin()
                    ? [['name' => 'id_guru_bk', 'label' => 'Guru pencatat', 'type' => 'select', 'required' => true, 'value' => $record?->id_guru_bk, 'options' => $counselors]]
                    : []),
            ],
        ]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function recorderId(Request $request, array $attributes): int
    {
        if ($request->user()->isAdmin()) {
            $validated = $request->validate([
                'id_guru_bk' => [
                    'required',
                    'integer',
                    Rule::exists('guru_bk', 'id_guru_bk')->where('status_aktif', true),
                ],
            ]);

            return (int) $validated['id_guru_bk'];
        }

        $counselorId = $request->user()->guru?->guruBk?->id_guru_bk;
        abort_unless($counselorId !== null, 403);

        return (int) $counselorId;
    }

    private function recalculatePoints(int $studentId): void
    {
        $total = PelanggaranSiswa::query()->where('id_siswa', $studentId)->sum('poin');
        Siswa::query()->whereKey($studentId)->update(['poin_pelanggaran' => $total]);
    }

    /**
     * @return array<int|string, string>
     */
    private function studentOptions(): array
    {
        return ['' => 'Pilih siswa'] + Siswa::query()
            ->orderBy('nama_siswa')
            ->get(['id_siswa', 'no_siswa', 'nama_siswa'])
            ->mapWithKeys(fn (Siswa $student): array => [$student->id_siswa => "{$student->nama_siswa} ({$student->no_siswa})"])
            ->all();
    }
}
