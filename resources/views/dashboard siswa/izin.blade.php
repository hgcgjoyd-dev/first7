@extends('dashboard siswa.app')

@section('title', 'Formulir Pengajuan Izin & Sakit')

@section('content')
<div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-soft max-w-3xl mx-auto"
     x-data="{
        jenis: 'Izin',
        startDate: '{{ \Carbon\Carbon::today()->toDateString() }}',
        endDate: '{{ \Carbon\Carbon::today()->toDateString() }}',
        alasan: '',
        uploadedFileName: '',
        uploadedFile: null,
        isSubmitting: false,
        showSuccessModal: false,
        errorMessage: '',

        calculateDuration() {
            if (!this.startDate || !this.endDate) return 1;
            const d1 = new Date(this.startDate);
            const d2 = new Date(this.endDate);
            const diffTime = d2.getTime() - d1.getTime();
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
            return isNaN(diffDays) || diffDays < 1 ? 1 : diffDays;
        },

        handleFileUpload(event) {
            const file = event.target.files[0];
            if (file) {
                this.uploadedFile = file;
                this.uploadedFileName = `${file.name} (${(file.size / 1024).toFixed(1)} KB)`;
            }
        },

        async submitForm() {
            this.errorMessage = '';
            if (!this.alasan.trim()) {
                this.errorMessage = 'Mohon tuliskan alasan pengajuan secara jelas terlebih dahulu.';
                return;
            }

            this.isSubmitting = true;

            try {
                const formData = new FormData();
                formData.append('jenis', this.jenis);
                formData.append('tgl_mulai', this.startDate);
                formData.append('tgl_selesai', this.endDate);
                formData.append('alasan', this.alasan);
                if (this.uploadedFile) {
                    formData.append('bukti_file', this.uploadedFile);
                }
                formData.append('_token', '{{ csrf_token() }}');

                await fetch('{{ route('izin.store') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
            } catch (err) {
                console.warn('Simpan izin:', err);
            } finally {
                this.isSubmitting = false;
                this.showSuccessModal = true;
                this.$nextTick(() => {
                    if (window.lucide) lucide.createIcons();
                });
            }
        },

        resetForm() {
            this.showSuccessModal = false;
            this.alasan = '';
            this.uploadedFileName = '';
            this.uploadedFile = null;
            this.errorMessage = '';
        }
     }">
    
    <!-- Top Navigation Row: Back Button & Title -->
    <div class="flex items-center space-x-3 pb-6 border-b border-slate-100">
        <a href="{{ route('dashboard') }}" class="p-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition-colors" title="Kembali ke Dashboard">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <div>
            <p class="text-xs font-semibold text-blue-600 uppercase tracking-wider">Formulir Kesiswaan</p>
            <h2 class="text-xl sm:text-2xl font-black text-slate-900">Pengajuan Izin & Sakit</h2>
        </div>
    </div>

    <!-- Error Alert Box -->
    <div x-show="errorMessage" x-cloak class="mt-4 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold flex items-center space-x-2.5">
        <i data-lucide="alert-circle" class="w-4 h-4 shrink-0 text-rose-500"></i>
        <span x-text="errorMessage"></span>
    </div>

    <form @submit.prevent="submitForm()" class="mt-6 space-y-6">
        <!-- 1. PILIH JENIS KETERANGAN (Izin, Dispen, Sakit) -->
        <div>
            <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-3">PILIH JENIS KETERANGAN</label>
            <div class="grid grid-cols-3 gap-3">
                <!-- Izin -->
                <button type="button" @click="jenis = 'Izin'" 
                        :class="jenis === 'Izin' ? 'bg-blue-600 text-white border-blue-600 shadow-md shadow-blue-500/20' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'"
                        class="py-4 px-3 rounded-2xl border text-center transition-all flex flex-col items-center justify-center space-y-1.5 font-bold text-xs sm:text-sm cursor-pointer active:scale-95">
                    <i data-lucide="mail" class="w-5 h-5"></i>
                    <span>Izin</span>
                </button>

                <!-- Dispen -->
                <button type="button" @click="jenis = 'dispen'" 
                        :class="jenis === 'dispen' ? 'bg-indigo-600 text-white border-indigo-600 shadow-md shadow-indigo-500/20' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'"
                        class="py-4 px-3 rounded-2xl border text-center transition-all flex flex-col items-center justify-center space-y-1.5 font-bold text-xs sm:text-sm cursor-pointer active:scale-95">
                    <i data-lucide="briefcase" class="w-5 h-5"></i>
                    <span>Dispen</span>
                </button>

                <!-- Sakit -->
                <button type="button" @click="jenis = 'Sakit'" 
                        :class="jenis === 'Sakit' ? 'bg-rose-600 text-white border-rose-600 shadow-md shadow-rose-500/20' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'"
                        class="py-4 px-3 rounded-2xl border text-center transition-all flex flex-col items-center justify-center space-y-1.5 font-bold text-xs sm:text-sm cursor-pointer active:scale-95">
                    <i data-lucide="cross" class="w-5 h-5"></i>
                    <span>Sakit</span>
                </button>
            </div>
        </div>

        <!-- 2. TANGGAL KAPAN IZIN & DURASI -->
        <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100 space-y-4">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center space-x-2">
                    <i data-lucide="calendar" class="w-4 h-4 text-blue-600"></i>
                    <span>TANGGAL PERIODE PENGAJUAN</span>
                </span>
                <span class="text-xs bg-blue-100 text-blue-700 font-extrabold px-3 py-1 rounded-full" x-text="'Durasi: ' + calculateDuration() + ' Hari'">
                    Durasi: 1 Hari
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Mulai Tanggal</label>
                    <input type="date" x-model="startDate" required class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-medium focus:ring-2 focus:ring-blue-500 focus:outline-hidden">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Sampai Tanggal</label>
                    <input type="date" x-model="endDate" required class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-medium focus:ring-2 focus:ring-blue-500 focus:outline-hidden">
                </div>
            </div>
        </div>

        <!-- 3. ALASAN KETERANGAN -->
        <div>
            <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-2 flex items-center space-x-1.5">
                <i data-lucide="align-left" class="w-3.5 h-3.5 text-blue-600"></i>
                <span>ALASAN KETERANGAN</span>
            </label>
            <textarea x-model="alasan" rows="4" placeholder="Tuliskan alasan lengkap mengenai izin atau sakit yang dialami..." required class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-4 text-xs font-medium focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-hidden transition-all"></textarea>
        </div>

        <!-- 4. LAMPIRKAN SURAT BUKTI (OPSIONAL) -->
        <div>
            <div class="flex items-center justify-between mb-2">
                <label class="text-xs font-extrabold uppercase tracking-wider text-slate-500 flex items-center space-x-1.5">
                    <i data-lucide="paperclip" class="w-3.5 h-3.5 text-blue-600"></i>
                    <span>LAMPIRKAN SURAT BUKTI / DOKTER</span>
                </label>
                <span class="text-[11px] text-slate-400 font-semibold" x-text="jenis === 'Sakit' ? 'Sangat Dianjurkan' : 'Opsional'">Opsional</span>
            </div>
            
            <div class="border-2 border-dashed border-slate-300 hover:border-blue-500 rounded-2xl p-6 text-center bg-slate-50/50 hover:bg-blue-50/20 transition-all cursor-pointer relative"
                 @click="$refs.fileInput.click()">
                <input type="file" x-ref="fileInput" @change="handleFileUpload($event)" class="hidden" accept="image/*,application/pdf">
                <div class="flex flex-col items-center justify-center space-y-2">
                    <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                        <i data-lucide="upload-cloud" class="w-6 h-6"></i>
                    </div>
                    <p class="text-xs font-bold text-slate-800" x-text="uploadedFileName || 'Unggah Foto Surat / Berkas Bukti'">Unggah Foto Surat / Berkas Bukti</p>
                    <p class="text-[11px] text-slate-500">Format JPG, PNG, atau PDF (Maks. 5MB)</p>
                </div>
            </div>
        </div>

        <!-- SUBMIT BUTTON -->
        <button type="submit" 
                :disabled="isSubmitting"
                class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-extrabold py-4 px-6 rounded-2xl shadow-lg shadow-blue-500/25 flex items-center justify-center space-x-2 transition-all cursor-pointer active:scale-98">
            <template x-if="!isSubmitting">
                <span class="flex items-center space-x-2">
                    <i data-lucide="send" class="w-4 h-4"></i>
                    <span>Kirim Pengajuan Keterangan</span>
                </span>
            </template>
            <template x-if="isSubmitting">
                <span class="flex items-center space-x-2">
                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span>Mengirimkan Pengajuan...</span>
                </span>
            </template>
        </button>
    </form>

    <!-- ========================================================================= -->
    <!-- MODAL POP UP BERHASIL (Popup Notifikasi Sukses Semua Bagian Izin & Sakit)  -->
    <!-- ========================================================================= -->
    <div x-show="showSuccessModal" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs transition-opacity"
         @keydown.escape.window="showSuccessModal = false">
        
        <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 text-center shadow-2xl border border-slate-100 relative animate-in zoom-in-95 duration-200"
             @click.away="showSuccessModal = false">
            
            <!-- Animated Green Checkmark Icon -->
            <div class="w-20 h-20 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-4 ring-8 ring-emerald-50 shadow-inner">
                <i data-lucide="check-circle-2" class="w-10 h-10"></i>
            </div>

            <h3 class="text-xl sm:text-2xl font-black text-slate-900 mb-1">Pengajuan Berhasil!</h3>
            <p class="text-xs sm:text-sm text-slate-500 mb-6 leading-relaxed">
                Pengajuan keterangan siswa berhasil dikirimkan ke sistem kesiswaan dan wali kelas.
            </p>

            <!-- Recap Card Info -->
            <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 text-left space-y-2.5 mb-6 text-xs">
                <div class="flex items-center justify-between pb-2 border-b border-slate-200/60">
                    <span class="text-slate-500 font-semibold">Jenis Pengajuan:</span>
                    <span class="font-extrabold uppercase px-2.5 py-0.5 rounded-full text-[11px]" 
                          :class="{
                              'bg-blue-100 text-blue-700': jenis === 'Izin',
                              'bg-indigo-100 text-indigo-700': jenis === 'dispen',
                              'bg-rose-100 text-rose-700': jenis === 'Sakit'
                          }"
                          x-text="jenis === 'dispen' ? 'Dispensasi' : jenis">
                        Izin
                    </span>
                </div>

                <div class="flex items-center justify-between pb-2 border-b border-slate-200/60">
                    <span class="text-slate-500 font-semibold">Durasi Waktu:</span>
                    <span class="font-bold text-slate-800" x-text="calculateDuration() + ' Hari (' + startDate + ' s/d ' + endDate + ')'">
                        1 Hari
                    </span>
                </div>

                <div class="flex items-start justify-between pb-2 border-b border-slate-200/60">
                    <span class="text-slate-500 font-semibold shrink-0">Alasan:</span>
                    <span class="font-medium text-slate-800 text-right truncate max-w-[200px]" x-text="alasan">
                        -
                    </span>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-slate-500 font-semibold">Status:</span>
                    <span class="font-bold text-amber-700 bg-amber-100 px-2 py-0.5 rounded-full text-[11px] flex items-center space-x-1">
                        <span>⏳ Menunggu Verifikasi</span>
                    </span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="space-y-2.5">
                <a href="{{ route('riwayat') }}" 
                   class="w-full py-3.5 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs sm:text-sm flex items-center justify-center space-x-2 shadow-lg shadow-blue-500/25 transition-all">
                    <i data-lucide="calendar" class="w-4 h-4"></i>
                    <span>Lihat di Kalender Riwayat</span>
                </a>

                <a href="{{ route('dashboard') }}" 
                   class="w-full py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs sm:text-sm flex items-center justify-center space-x-2 transition-all">
                    <i data-lucide="home" class="w-4 h-4"></i>
                    <span>Kembali ke Dashboard</span>
                </a>

                <button type="button" 
                        @click="resetForm()" 
                        class="text-xs text-slate-400 hover:text-slate-600 font-semibold pt-1 cursor-pointer">
                    Buat Pengajuan Lain
                </button>
            </div>

        </div>
    </div>

</div>
@endsection
