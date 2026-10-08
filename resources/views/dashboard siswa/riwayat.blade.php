@extends('dashboard siswa.app')

@section('title', 'Riwayat Kehadiran Siswa')

@section('content')
<div class="space-y-6">
    <!-- Header Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-soft flex items-center space-x-4">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                <i data-lucide="percent" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-400">Tingkat Kehadiran</p>
                <h3 class="text-2xl font-black text-slate-800">{{ $persenHadir ?? 95 }}%</h3>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-soft flex items-center space-x-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                <i data-lucide="calendar-check" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-400">Total Hadir Bulan Ini</p>
                <h3 class="text-2xl font-black text-slate-800">{{ count($daftarPresensi) }} Hari</h3>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-soft flex items-center space-x-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                <i data-lucide="file-text" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-400">Izin / Sakit</p>
                <h3 class="text-2xl font-black text-slate-800">{{ $totalIzin ?? 0 }} Hari</h3>
            </div>
        </div>
    </div>

    <!-- Tabel Riwayat Presensi -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-soft space-y-6">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div>
                <h3 class="font-extrabold text-lg text-slate-800">Catatan Presensi Harian</h3>
                <p class="text-xs text-slate-500 mt-0.5">30 riwayat absensi kehadiran terbaru</p>
            </div>
            <a href="{{ route('presensi') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl flex items-center space-x-2 transition-all shadow-md shadow-blue-500/20">
                <i data-lucide="scan" class="w-4 h-4"></i>
                <span>Absen Hari Ini</span>
            </a>
        </div>

        @if(count($daftarPresensi) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-slate-400 uppercase tracking-wider font-extrabold border-b border-slate-100">
                            <th class="py-3 px-4">Tanggal</th>
                            <th class="py-3 px-4">Jam Masuk</th>
                            <th class="py-3 px-4">Jam Pulang</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @foreach($daftarPresensi as $p)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3.5 px-4 font-bold text-slate-800">{{ \Carbon\Carbon::parse($p->tanggal)->translatedFormat('d F Y') }}</td>
                                <td class="py-3.5 px-4 text-slate-600">{{ $p->jam_masuk ? substr($p->jam_masuk, 0, 5) . ' WITA' : '-' }}</td>
                                <td class="py-3.5 px-4 text-slate-600">{{ $p->jam_pulang ? substr($p->jam_pulang, 0, 5) . ' WITA' : '-' }}</td>
                                <td class="py-3.5 px-4">
                                    @if($p->status === 'Hadir')
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">Hadir</span>
                                    @elseif($p->status === 'Terlambat')
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">Terlambat</span>
                                    @elseif($p->status === 'Izin' || $p->status === 'Sakit')
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-100 text-blue-800">{{ $p->status }}</span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800">{{ $p->status }}</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-slate-500">{{ $p->keterangan ?? 'Hadir Tepat Waktu' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12 text-slate-400 space-y-3">
                <i data-lucide="calendar-x" class="w-12 h-12 mx-auto text-slate-300"></i>
                <p class="font-semibold text-sm">Belum ada riwayat absensi tercatat di database.</p>
            </div>
        @endif
    </div>

    <!-- Riwayat Pengajuan Izin -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-soft space-y-4">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div>
                <h3 class="font-extrabold text-lg text-slate-800">Riwayat Pengajuan Izin / Sakit</h3>
                <p class="text-xs text-slate-500 mt-0.5">Daftar permohonan dispensasi siswa</p>
            </div>
            <a href="{{ route('izin') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-blue-600 font-bold text-xs rounded-xl flex items-center space-x-2 transition-all">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Ajukan Izin</span>
            </a>
        </div>

        @if(count($daftarIzin) > 0)
            <div class="space-y-3">
                @foreach($daftarIzin as $iz)
                    <div class="p-4 rounded-2xl border border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="space-y-1">
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase {{ $iz->jenis === 'Sakit' ? 'bg-rose-100 text-rose-700' : 'bg-blue-100 text-blue-700' }}">{{ $iz->jenis }}</span>
                                <span class="text-xs font-bold text-slate-800">{{ \Carbon\Carbon::parse($iz->tgl_mulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($iz->tgl_selesai)->format('d/m/Y') }} ({{ $iz->durasi_hari }} Hari)</span>
                            </div>
                            <p class="text-xs text-slate-600">{{ $iz->alasan }}</p>
                        </div>
                        <div>
                            @if($iz->status === 'Disetujui')
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">Disetujui</span>
                            @elseif($iz->status === 'Ditolak')
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800">Ditolak</span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">Menunggu Verifikasi</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-xs text-slate-400 text-center py-6">Tidak ada riwayat permohonan izin.</p>
        @endif
    </div>
</div>
@endsection
