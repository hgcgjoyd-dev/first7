<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\Siswa;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AbsensiController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'tanggal' => ['nullable', 'date'],
            'siswa' => ['nullable', 'integer', 'exists:siswa,id_siswa'],
            'kelas' => ['nullable', 'integer', 'exists:kelas,id_kelas'],
        ]);

        $attendance = Absensi::query()
            ->with(['siswa.kelas', 'guru'])
            ->when($filters['tanggal'] ?? null, fn (Builder $query, string $date) => $query->whereDate('tanggal', $date))
            ->when($filters['siswa'] ?? null, fn (Builder $query, int $studentId) => $query->where('id_siswa', $studentId))
            ->when($filters['kelas'] ?? null, fn (Builder $query, int $classId) => $query->whereHas('siswa', fn (Builder $query) => $query->where('id_kelas', $classId)))
            ->orderByDesc('tanggal')
            ->orderBy('id_siswa')
            ->paginate(20)
            ->withQueryString();

        return view('admin.crud.index', [
            'title' => 'Data Absensi',
            'createRoute' => 'admin.absensi.create',
            'indexRoute' => 'admin.absensi.index',
            'columns' => [
                ['key' => 'tanggal', 'label' => 'Tanggal'],
                ['key' => 'siswa.nama_siswa', 'label' => 'Siswa'],
                ['key' => 'siswa.kelas.nama_kelas', 'label' => 'Kelas'],
                ['key' => 'status_label', 'label' => 'Status'],
                ['key' => 'jam_masuk', 'label' => 'Jam masuk'],
            ],
            'records' => $attendance,
            'filters' => [
                ['name' => 'tanggal', 'label' => 'Tanggal', 'type' => 'date', 'value' => $filters['tanggal'] ?? ''],
                ['name' => 'siswa', 'label' => 'Siswa', 'type' => 'select', 'value' => $filters['siswa'] ?? '', 'options' => $this->studentOptions()],
                ['name' => 'kelas', 'label' => 'Kelas', 'type' => 'select', 'value' => $filters['kelas'] ?? '', 'options' => $this->classOptions()],
            ],
        ]);
    }

    public function create(): View
    {
        return $this->form(null, 'Tambah Absensi', 'admin.absensi.store');
    }

    public function store(Request $request): RedirectResponse
    {
        $attributes = $request->validate($this->rules());
        $attributes['status'] = $this->storedStatus($attributes['status']);

        $attendance = Absensi::create($attributes);

        return redirect()->route('admin.absensi.show', $attendance)->with('success', 'Absensi berhasil dicatat.');
    }

    public function show(Absensi $absensi): View
    {
        $absensi->load(['siswa.kelas', 'guru']);

        return view('admin.crud.show', [
            'title' => 'Detail Absensi',
            'record' => $absensi,
            'indexRoute' => 'admin.absensi.index',
            'editRoute' => 'admin.absensi.edit',
            'deleteRoute' => 'admin.absensi.destroy',
            'details' => [
                'Tanggal' => $absensi->tanggal?->format('d-m-Y'),
                'Siswa' => $absensi->siswa->nama_siswa,
                'Kelas' => $absensi->siswa->kelas->nama_kelas,
                'Status' => $absensi->status_label,
                'Jam masuk' => $absensi->jam_masuk,
                'Jam pulang' => $absensi->jam_pulang,
                'Keterangan' => $absensi->keterangan,
            ],
        ]);
    }

    public function edit(Absensi $absensi): View
    {
        return $this->form($absensi, 'Edit Absensi', 'admin.absensi.update');
    }

    public function update(Request $request, Absensi $absensi): RedirectResponse
    {
        $attributes = $request->validate($this->rules($absensi));
        $attributes['status'] = $this->storedStatus($attributes['status']);
        $absensi->update($attributes);

        return redirect()->route('admin.absensi.show', $absensi)->with('success', 'Absensi berhasil diperbarui.');
    }

    public function destroy(Absensi $absensi): RedirectResponse
    {
        $absensi->delete();

        return redirect()->route('admin.absensi.index')->with('success', 'Catatan absensi berhasil dihapus.');
    }

    public function report(Request $request): View
    {
        $filters = $request->validate([
            'mulai' => ['nullable', 'date'],
            'selesai' => ['nullable', 'date', 'after_or_equal:mulai'],
            'siswa' => ['nullable', 'integer', 'exists:siswa,id_siswa'],
            'kelas' => ['nullable', 'integer', 'exists:kelas,id_kelas'],
        ]);

        $attendanceFilter = fn (Builder $query) => $query
            ->when($filters['mulai'] ?? null, fn (Builder $query, string $date) => $query->whereDate('tanggal', '>=', $date))
            ->when($filters['selesai'] ?? null, fn (Builder $query, string $date) => $query->whereDate('tanggal', '<=', $date));

        $students = Siswa::query()
            ->with('kelas')
            ->withCount([
                'absensi as total_absensi' => $attendanceFilter,
                'absensi as hadir_count' => fn (Builder $query) => $attendanceFilter($query)->whereIn('status', ['Hadir', 'Terlambat']),
                'absensi as izin_count' => fn (Builder $query) => $attendanceFilter($query)->where('status', 'Izin'),
                'absensi as sakit_count' => fn (Builder $query) => $attendanceFilter($query)->where('status', 'Sakit'),
                'absensi as alpha_count' => fn (Builder $query) => $attendanceFilter($query)->whereIn('status', ['Alpha', 'Alpa']),
            ])
            ->when($filters['siswa'] ?? null, fn (Builder $query, int $studentId) => $query->where('id_siswa', $studentId))
            ->when($filters['kelas'] ?? null, fn (Builder $query, int $classId) => $query->where('id_kelas', $classId))
            ->orderBy('nama_siswa')
            ->paginate(30)
            ->withQueryString();

        return view('admin.absensi-report', [
            'students' => $students,
            'filters' => $filters,
            'classes' => $this->classOptions(),
            'studentOptions' => $this->studentOptions(),
        ]);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function rules(?Absensi $attendance = null): array
    {
        return [
            'id_siswa' => ['required', 'integer', 'exists:siswa,id_siswa'],
            'tanggal' => [
                'required',
                'date',
                function (string $attribute, mixed $value, Closure $fail) use ($attendance): void {
                    $query = Absensi::query()
                        ->where('id_siswa', request()->integer('id_siswa'))
                        ->whereDate('tanggal', $value);

                    if ($attendance !== null) {
                        $query->where('id_absensi', '!=', $attendance->id_absensi);
                    }

                    if ($query->exists()) {
                        $fail('Siswa sudah memiliki absensi pada tanggal tersebut.');
                    }
                },
            ],
            'status' => ['required', Rule::in(['Hadir', 'Izin', 'Sakit', 'Alpha', 'Alpa', 'Terlambat'])],
            'jam_masuk' => ['nullable', 'date_format:H:i'],
            'jam_pulang' => ['nullable', 'date_format:H:i'],
            'keterangan' => ['nullable', 'string', 'max:5000'],
        ];
    }

    private function form(?Absensi $attendance, string $title, string $submitRoute): View
    {
        return view('admin.crud.form', [
            'title' => $title,
            'indexRoute' => 'admin.absensi.index',
            'submitRoute' => $submitRoute,
            'record' => $attendance,
            'fields' => [
                ['name' => 'id_siswa', 'label' => 'Siswa', 'type' => 'select', 'required' => true, 'options' => $this->studentOptions()],
                ['name' => 'tanggal', 'label' => 'Tanggal', 'type' => 'date', 'required' => true],
                ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'required' => true, 'value' => $attendance?->status_label, 'options' => ['Hadir' => 'Hadir', 'Izin' => 'Izin', 'Sakit' => 'Sakit', 'Alpha' => 'Alpha', 'Terlambat' => 'Terlambat']],
                ['name' => 'jam_masuk', 'label' => 'Jam masuk', 'type' => 'time'],
                ['name' => 'jam_pulang', 'label' => 'Jam pulang', 'type' => 'time'],
                ['name' => 'keterangan', 'label' => 'Keterangan', 'type' => 'textarea'],
            ],
        ]);
    }

    private function storedStatus(string $status): string
    {
        return $status === 'Alpha' ? 'Alpa' : $status;
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

    /**
     * @return array<int|string, string>
     */
    private function classOptions(): array
    {
        return ['' => 'Semua kelas'] + Kelas::query()
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->pluck('nama_kelas', 'id_kelas')
            ->all();
    }
}
