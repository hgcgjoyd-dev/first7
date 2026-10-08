@extends('dashboard siswa.app')

@section('title', 'Formulir Pengajuan Izin')

@section('content')
<div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-soft max-w-3xl mx-auto"
     x-data="{
        jenis: 'Izin',
        startDate: '2026-09-20',
        endDate: '2026-09-24',
        alasan: '',
        uploadedFileName: '',

        calculateDuration() {
            if (!this.startDate || !this.endDate) return 1;
            const d1 = new Date(this.startDate);
            const d2 = new Date(this.endDate);
            const diffTime = Math.abs(d2 - d1);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
            return isNaN(diffDays) ? 1 : diffDays;
        },

        handleFileUpload(event) {
            const file = event.target.files[0];
            if (file) {
                this.uploadedFileName = `${file.name} (${(file.size / 1024).toFixed(1)} KB)`;
            }
        },

        submitForm() {
            alert(`Pengajuan ${this.jenis} berhasil dikirimkan untuk durasi ${this.calculateDuration()} hari. Menunggu verifikasi dari Wali Kelas.`);
            window.location.href = '{{ route('riwayat') }}';
        }
     }">
    
    <div class="flex items-center space-x-3 pb-6 border-b border-slate-100">
        <a href="{{ route('dashboard') }}" class="p-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <div>
            <p class="text-xs font-semibold text-blue-600 uppercase tracking-wider">Formulir Pengajuan</p>
            <h2 class="text-xl sm:text-2xl font-black text-slate-900">Form Izin</h2>
        </div>
    </div>

    <form @submit.prevent="submitForm()" class="mt-6 space-y-6">
        <!-- 1. PILIH JENIS KETERANGAN (Exact from Image 2 Middle) -->
        <div>
            <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-3">PILIH JENIS KETERANGAN</label>
            <div class="grid grid-cols-3 gap-3">
                <button type="button" @click="jenis = 'Izin'" 
                        :class="jenis === 'Izin' ? 'bg-blue-600 text-white border-blue-600 shadow-md shadow-blue-500/20' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'"
                        class="py-4 px-3 rounded-2xl border text-center transition-all flex flex-col items-center justify-center space-y-1.5 font-bold text-xs sm:text-sm">
                    <i data-lucide="mail" class="w-5 h-5"></i>
                    <span>Izin</span>
                </button>

                <button type="button" @click="jenis = 'Cuti'" 
                        :class="jenis === 'Cuti' ? 'bg-blue-600 text-white border-blue-600 shadow-md shadow-blue-500/20' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'"
                        class="py-4 px-3 rounded-2xl border text-center transition-all flex flex-col items-center justify-center space-y-1.5 font-bold text-xs sm:text-sm">
                    <i data-lucide="briefcase" class="w-5 h-5"></i>
                    <span>Cuti</span>
                </button>

                <button type="button" @click="jenis = 'Sakit'" 
                        :class="jenis === 'Sakit' ? 'bg-blue-600 text-white border-blue-600 shadow-md shadow-blue-500/20' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'"
                        class="py-4 px-3 rounded-2xl border text-center transition-all flex flex-col items-center justify-center space-y-1.5 font-bold text-xs sm:text-sm">
                    <i data-lucide="cross" class="w-5 h-5"></i>
                    <span>Sakit</span>
                </button>
            </div>
        </div>

        <!-- 2. TANGGAL KAPAN IZIN & DURASI (Exact from Image 2 Middle) -->
        <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100 space-y-4">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center space-x-2">
                    <i data-lucide="calendar" class="w-4 h-4 text-blue-600"></i>
                    <span>TANGGAL KAPAN IZIN</span>
                </span>
                <span class="text-xs bg-blue-100 text-blue-700 font-extrabold px-3 py-1 rounded-full" x-text="'Durasi: ' + calculateDuration() + ' Hari'">
                    Durasi: 1 Hari
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Mulai Tanggal</label>
                    <input type="date" x-model="startDate" class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-medium focus:ring-2 focus:ring-blue-500 focus:outline-hidden">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Sampai Tanggal</label>
                    <input type="date" x-model="endDate" class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-medium focus:ring-2 focus:ring-blue-500 focus:outline-hidden">
                </div>
            </div>
        </div>

        <!-- 3. ALASAN KETERANGAN -->
        <div>
            <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-2 flex items-center space-x-1.5">
                <i data-lucide="align-left" class="w-3.5 h-3.5 text-blue-600"></i>
                <span>ALASAN KETERANGAN</span>
            </label>
            <textarea x-model="alasan" rows="4" placeholder="Tuliskan alasan lengkap mengenai izin atau sakit yang dialami..." class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-4 text-xs font-medium focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-hidden transition-all"></textarea>
        </div>

        <!-- 4. LAMPIRKAN SURAT BUKTI (OPSIONAL) -->
        <div>
            <div class="flex items-center justify-between mb-2">
                <label class="text-xs font-extrabold uppercase tracking-wider text-slate-500 flex items-center space-x-1.5">
                    <i data-lucide="paperclip" class="w-3.5 h-3.5 text-blue-600"></i>
                    <span>LAMPIRKAN SURAT BUKTI</span>
                </label>
                <span class="text-[11px] text-slate-400 font-semibold">Opsional</span>
            </div>
            
            <div class="border-2 border-dashed border-slate-300 hover:border-blue-500 rounded-2xl p-6 text-center bg-slate-50/50 hover:bg-blue-50/20 transition-all cursor-pointer relative"
                 @click="$refs.fileInput.click()">
                <input type="file" x-ref="fileInput" @change="handleFileUpload($event)" class="hidden" accept="image/*,application/pdf">
                <div class="flex flex-col items-center justify-center space-y-2">
                    <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                        <i data-lucide="upload-cloud" class="w-6 h-6"></i>
                    </div>
                    <p class="text-xs font-bold text-slate-800" x-text="uploadedFileName || 'Unggah Foto Surat / Berkas'">Unggah Foto Surat / Berkas</p>
                    <p class="text-[11px] text-slate-500">Format JPG, PNG, atau PDF (Maks. 5MB)</p>
                </div>
            </div>
        </div>

        <!-- SUBMIT BUTTON -->
        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-extrabold py-4 px-6 rounded-2xl shadow-lg shadow-blue-500/25 flex items-center justify-center space-x-2 transition-all">
            <i data-lucide="send" class="w-4 h-4"></i>
            <span>Kirim Pengajuan Izin</span>
        </button>
    </form>
</div>
@endsection

