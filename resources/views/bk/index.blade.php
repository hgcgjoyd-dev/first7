@extends('layouts.app')

@section('title', 'Konseling & Tugas BK')

@section('content')
<div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-soft"
     x-data="{
        uploadDone: false
     }">
    
    <!-- Top Tab Navigation Pills (Exact from Image 3 Header) -->
    <div class="flex items-center space-x-2 bg-slate-100 p-1.5 rounded-2xl mb-6">
        <a href="{{ route('mapel') }}" class="flex-1 py-2.5 px-4 rounded-xl font-bold text-xs text-slate-600 hover:text-slate-900 flex items-center justify-center space-x-2">
            <i data-lucide="book-open" class="w-4 h-4"></i>
            <span>Mapel</span>
        </a>
        <a href="{{ route('bk') }}" class="flex-1 py-2.5 px-4 rounded-xl font-bold text-xs bg-white text-blue-600 shadow-xs flex items-center justify-center space-x-2">
            <i data-lucide="shield-alert" class="w-4 h-4"></i>
            <span>Tugas BK</span>
            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
        </a>
        <a href="{{ route('piket') }}" class="flex-1 py-2.5 px-4 rounded-xl font-bold text-xs text-slate-600 hover:text-slate-900 flex items-center justify-center space-x-2">
            <i data-lucide="sparkles" class="w-4 h-4"></i>
            <span>Piket</span>
        </a>
    </div>

    <!-- TOP STATUS POIN CARD: BUKU DISIPLIN & PELANGGARAN (Exact from Image 3 Middle) -->
    <div class="p-6 rounded-3xl bg-rose-50/70 border border-rose-200/80 space-y-4">
        <div class="flex items-start justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-2xl bg-rose-500 text-white flex items-center justify-center">
                    <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                </div>
                <div>
                    <span class="text-[11px] font-bold text-rose-800 uppercase tracking-wider">BUKU DISIPLIN & PELANGGARAN</span>
                    <h4 class="font-extrabold text-base text-slate-900">Status Poin Tata Tertib</h4>
                </div>
            </div>
            <div class="text-right">
                <span class="text-2xl font-black text-rose-600">15</span>
                <span class="text-xs text-slate-500 font-semibold">/ 30 Poin</span>
            </div>
        </div>

        <!-- Progress Bar (50%) -->
        <div class="w-full bg-rose-200/70 h-2.5 rounded-full overflow-hidden">
            <div class="bg-rose-500 h-full rounded-full" style="width: 50%;"></div>
        </div>

        <div class="flex items-center justify-between text-xs font-bold text-rose-700 pt-1">
            <span class="flex items-center space-x-1.5">
                <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                <span>Peringatan 1 (SP-1 Aktif)</span>
            </span>
            <span class="text-slate-500 font-medium">Batas Surat Panggilan: 30 Poin</span>
        </div>
    </div>

    <!-- MANDATORY COUNSELING ASSIGNMENT: TUGAS PEMBINAAN WAJIB (Exact from Image 3 Middle) -->
    <div class="mt-6 p-6 rounded-3xl border border-slate-200 bg-white space-y-5">
        <div class="flex items-center justify-between">
            <span class="text-xs font-black text-rose-600 bg-rose-50 px-3 py-1 rounded-full uppercase tracking-wider">
                📌 TUGAS PEMBINAAN WAJIB
            </span>
            <span class="text-xs font-black bg-rose-100 text-rose-700 px-3 py-1 rounded-full">
                Poin -10
            </span>
        </div>

        <div>
            <h3 class="text-lg font-black text-slate-900">Resume Pedoman Tata Tertib & Refleksi Kedisiplinan</h3>
            <p class="text-xs font-bold text-rose-600 mt-1 flex items-center space-x-1.5">
                <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                <span>Kasus: Terlambat Masuk Sekolah 3x Berturut-turut</span>
            </p>
        </div>

        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 space-y-2 text-xs leading-relaxed text-slate-700">
            <p class="font-extrabold text-slate-900">Instruksi Guru BK:</p>
            <p>
                Tulis tangan resume <em>Bab III (Kedisiplinan Waktu & Sanksi)</em> minimal 2 lembar folio bergaris. Wajib ditandatangani oleh <strong>Orang Tua / Wali</strong> dan <strong>Wali Kelas</strong>, kemudian lampirkan foto fisiknya di bawah ini.
            </p>
            <div class="pt-2 flex items-center space-x-2 text-blue-600 font-bold">
                <i data-lucide="user" class="w-4 h-4"></i>
                <span>Guru Pembimbing: Dra. Ni Luh Suastini, S.Pd</span>
            </div>
        </div>

        <div class="flex items-center justify-between text-xs text-slate-500 font-semibold">
            <span>Batas Pengumpulan:</span>
            <span class="text-rose-600 font-bold">📅 26 Sep 2026 • 12:00 WITA</span>
        </div>

        <!-- Upload Area -->
        <div class="border-2 border-dashed border-slate-200 hover:border-blue-500 rounded-2xl p-6 text-center bg-slate-50/50 cursor-pointer transition-all"
             @click="$refs.bkFile.click()">
            <input type="file" x-ref="bkFile" class="hidden" @change="uploadDone = true">
            <div class="flex flex-col items-center justify-center space-y-2">
                <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center">
                    <i data-lucide="camera" class="w-5 h-5"></i>
                </div>
                <p class="text-xs font-bold text-slate-800" x-text="uploadDone ? 'Berkas Folio Siap Dikirim' : 'Ambil Foto / Pilih File Bukti Sanksi'">Ambil Foto / Pilih File Bukti Sanksi</p>
                <p class="text-[11px] text-slate-500">Format: JPG, PNG, atau PDF (Maksimal 5MB)</p>
            </div>
        </div>

        <button @click="alert('Tugas pembinaan kedisiplinan berhasil dikumpulkan ke Dra. Ni Luh Suastini, S.Pd!')" class="w-full bg-rose-600 hover:bg-rose-700 text-white font-extrabold py-3.5 rounded-2xl shadow-lg shadow-rose-500/20 flex items-center justify-center space-x-2 transition-all">
            <i data-lucide="send" class="w-4 h-4"></i>
            <span>Kumpulkan Tugas ke Guru BK</span>
        </button>
    </div>

    <!-- RIWAYAT PENYELESAIAN SANKSI (Exact from Image 3 Middle Bottom) -->
    <div class="mt-8 space-y-3">
        <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-400">RIWAYAT PENYELESAIAN SANKSI</h4>
        
        <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/60 flex items-center justify-between text-xs">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                    <i data-lucide="check" class="w-4 h-4"></i>
                </div>
                <div>
                    <p class="font-bold text-slate-900">Literasi Karakter di Perpustakaan (45 Menit)</p>
                    <p class="text-[11px] text-slate-500">Diverifikasi 14 Agu 2026 • Guru: Bpk. Gede</p>
                </div>
            </div>
            <span class="bg-emerald-100 text-emerald-800 font-bold px-2.5 py-1 rounded-full text-[11px]">Poin -5</span>
        </div>

        <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/60 flex items-center justify-between text-xs">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                    <i data-lucide="user-check" class="w-4 h-4"></i>
                </div>
                <div>
                    <p class="font-bold text-slate-900">Konseling Individual 1-on-1 Ruang BK</p>
                    <p class="text-[11px] text-slate-500">Selesai • Pembinaan Perilaku Disiplin</p>
                </div>
            </div>
            <span class="bg-blue-100 text-blue-800 font-bold px-2.5 py-1 rounded-full text-[11px]">Selesai</span>
        </div>
    </div>

    <!-- WHATSAPP CONTACT ACTION CARD (Exact from Image 3 Middle Bottom) -->
    <div class="mt-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center space-x-3 text-xs">
            <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0">
                <i data-lucide="phone-call" class="w-5 h-5"></i>
            </div>
            <div>
                <h5 class="font-extrabold text-slate-900">Hubungi Guru BK via WhatsApp</h5>
                <p class="text-[11px] text-slate-600">Klarifikasi status poin atau jadwal konsultasi siswa</p>
            </div>
        </div>
        <a href="https://wa.me/6281234567890?text=Halo%20Bu%20Suastini,%20saya%20Wahyu%20Pratama%20XI%20PPLG%201" target="_blank" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs py-2.5 px-5 rounded-xl transition-all text-center">
            Chat WA
        </a>
    </div>
</div>
@endsection

