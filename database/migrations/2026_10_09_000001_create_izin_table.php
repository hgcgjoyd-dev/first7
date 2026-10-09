<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('izin', function (Blueprint $table) {
            $table->id('id_izin');
            $table->foreignId('id_siswa')->constrained('siswa', 'id_siswa')->cascadeOnDelete();
            $table->string('jenis', 30);
            $table->date('tgl_mulai');
            $table->date('tgl_selesai');
            $table->unsignedInteger('durasi_hari')->default(1);
            $table->text('alasan');
            $table->string('bukti_file')->nullable();
            $table->string('status', 30)->default('Menunggu');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('izin');
    }
};
