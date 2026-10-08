<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pelanggaran_siswa', function (Blueprint $table) {
            $table->id('id_pelanggaran_siswa');
            $table->foreignId('id_siswa')->constrained('siswa', 'id_siswa')->cascadeOnDelete();
            $table->foreignId('id_pelanggaran')->constrained('jenis_pelanggaran', 'id_pelanggaran')->cascadeOnDelete();
            $table->foreignId('id_guru_bk')->nullable()->constrained('guru_bk', 'id_guru_bk')->nullOnDelete();
            $table->date('tanggal_kejadian');
            $table->text('keterangan')->nullable();
            $table->text('tindak_lanjut')->nullable();
            $table->enum('status_penanganan', ['Diproses', 'Diberi SP-1', 'Diberi SP-2', 'Diberi SP-3', 'Selesai'])->default('Diproses');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelanggaran_siswa');
    }
};

