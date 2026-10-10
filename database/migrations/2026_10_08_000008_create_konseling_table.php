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
        Schema::create('konseling', function (Blueprint $table) {
            $table->id('id_konseling');
            $table->foreignId('id_siswa')->constrained('siswa', 'id_siswa')->cascadeOnDelete();
            $table->foreignId('id_guru_bk')->constrained('guru_bk', 'id_guru_bk')->cascadeOnDelete();
            $table->date('tanggal_konseling');
            $table->time('jam_konseling')->nullable();
            $table->string('jenis_layanan', 100)->default('Konseling Individual');
            $table->text('topik_pembahasan');
            $table->text('hasil_konseling')->nullable();
            $table->enum('status', ['Dijadwalkan', 'Berlangsung', 'Selesai', 'Dibatalkan'])->default('Dijadwalkan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('konseling');
    }
};
