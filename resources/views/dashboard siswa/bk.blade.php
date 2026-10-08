@extends('dashboard siswa.app')

@section('title', 'Bimbingan Konseling & Disiplin')

@section('content')
<div class="space-y-6">
    <!-- Header Card -->
    <div class="bg-gradient-to-tr from-indigo-700 via-blue-600 to-blue-500 rounded-3xl p-6 sm:p-8 text-white shadow-soft relative overflow-hidden">
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-xs font-bold uppercase tracking-wider">Layanan Siswa</span>
                <h2 class="text-2xl font-black mt-2">Bimbingan & Konseling (BK)</h2>
                <p class="text-xs text-blue-100 mt-1">Konsultasi akademik, kedisiplinan, dan bimbingan kepribadian siswa</p>
            </div>
            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/20 text-center min-w-[140px]">
                <p class="text-[11px] font-bold text-blue-100 uppercase tracking-wider">Catatan Poin BK</p>
                <h3 class="text-3xl font-black text-rose-300 mt-1">{{ $siswa->poin_bk ?? 0 }}</h3>
                <p class="text-[10px] text-blue-200 mt-0.5">Maksimum toleransi 100 poin</p>
            </div>
        </div>
    </div>

    <!-- Catatan Sesi Konseling & Surat Disiplin -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-soft space-y-6">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div>
                <h3 class="font-extrabold text-lg text-slate-800">Catatan Bimbingan & Pemanggilan</h3>
                <p class="text-xs text-slate-500 mt-0.5">Histori sesi konsultasi guru BK</p>
            </div>
        </div>

        @if(count($daftarBk) > 0)
            <div class="space-y-4">
                @foreach($daftarBk as $b)
                    <div class="p-5 rounded-2xl border border-slate-100 bg-slate-50/60 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:border-slate-200 transition-all">
                        <div class="space-y-1.5">
                            <div class="flex items-center space-x-2">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase {{ $b->jenis === 'Prestasi' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                    {{ $b->jenis }}
                                </span>
                                <span class="text-xs font-bold text-slate-800">{{ \Carbon\Carbon::parse($b->tanggal)->translatedFormat('d F Y') }}</span>
                            </div>
                            <h4 class="font-bold text-sm text-slate-900">{{ $b->judul ?? 'Konseling Perkembangan Akademik' }}</h4>
                            <p class="text-xs text-slate-600">{{ $b->keterangan }}</p>
                            <p class="text-[11px] font-medium text-slate-400">Guru Pembimbing: <span class="text-slate-600 font-semibold">{{ $b->guru_bk ?? 'Dra. Ni Luh Suastini' }}</span></p>
                        </div>
                        <div class="text-right">
                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $b->status === 'Selesai' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ $b->status ?? 'Terjadwal' }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Default mock bimbingan jika belum ada record -->
            <div class="space-y-4">
                <div class="p-5 rounded-2xl border border-blue-100 bg-blue-50/40 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="space-y-1.5">
                        <div class="flex items-center space-x-2">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-blue-100 text-blue-800">
                                Bimbingan Karir & PKL
                            </span>
                            <span class="text-xs font-bold text-slate-800">{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</span>
                        </div>
                        <h4 class="font-bold text-sm text-slate-900">Konsultasi Penempatan Industri & Portofolio</h4>
                        <p class="text-xs text-slate-600">Diskusi persiapan Praktek Kerja Lapangan (PKL) mitra IT semester depan.</p>
                        <p class="text-[11px] font-medium text-slate-400">Guru Pembimbing: <span class="text-slate-600 font-semibold">Dra. Ni Luh Suastini</span></p>
                    </div>
                    <div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                            Terjadwal
                        </span>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
