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
        // 1. Tabel Siswa
        if (!Schema::hasTable('siswa')) {
            Schema::create('siswa', function (Blueprint $table) {
                $table->id();
                $table->string('nis', 20)->unique();
                $table->string('nisn', 20)->nullable();
                $table->string('nama', 100);
                $table->string('email', 100)->unique();
                $table->string('password');
                $table->string('kelas', 50)->default('XI PPLG 1');
                $table->string('jurusan', 50)->default('PPLG');
                $table->string('rfid_card', 50)->nullable()->unique();
                $table->string('foto')->nullable();
                $table->integer('poin_bk')->default(15);
                $table->integer('poin_prestasi')->default(50);
                $table->rememberToken();
                $table->timestamps();
            });
        }

        // 2. Tabel Presensi
        if (!Schema::hasTable('presensi')) {
            Schema::create('presensi', function (Blueprint $table) {
                $table->id();
                $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
                $table->date('tanggal');
                $table->time('jam_masuk')->nullable();
                $table->time('jam_pulang')->nullable();
                $table->string('status', 30)->default('Hadir');
                $table->string('latitude', 50)->nullable();
                $table->string('longitude', 50)->nullable();
                $table->text('foto')->nullable();
                $table->text('keterangan')->nullable();
                $table->timestamps();
            });
        }

        // 3. Tabel Izin
        if (!Schema::hasTable('izin')) {
            Schema::create('izin', function (Blueprint $table) {
                $table->id();
                $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
                $table->string('jenis', 30);
                $table->date('tgl_mulai');
                $table->date('tgl_selesai');
                $table->integer('durasi_hari')->default(1);
                $table->text('alasan');
                $table->string('bukti_file')->nullable();
                $table->string('status', 30)->default('Menunggu');
                $table->timestamps();
            });
        }

        // 4. Tabel Mata Pelajaran & Tugas
        if (!Schema::hasTable('mapel')) {
            Schema::create('mapel', function (Blueprint $table) {
                $table->id();
                $table->string('nama_mapel', 100);
                $table->string('guru', 100);
                $table->string('judul_tugas', 150);
                $table->text('deskripsi')->nullable();
                $table->date('deadline')->nullable();
                $table->string('status', 30)->default('Aktif');
                $table->timestamps();
            });
        }

        // 5. Tabel Bimbingan Konseling (BK)
        if (!Schema::hasTable('bk')) {
            Schema::create('bk', function (Blueprint $table) {
                $table->id();
                $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
                $table->string('jenis', 50);
                $table->integer('poin')->default(5);
                $table->text('keterangan');
                $table->date('tanggal');
                $table->string('guru_bk', 100)->default('Ibu Guru BK');
                $table->timestamps();
            });
        }

        // 6. Tabel Piket Kebersihan
        if (!Schema::hasTable('piket')) {
            Schema::create('piket', function (Blueprint $table) {
                $table->id();
                $table->string('hari', 20);
                $table->string('kelas', 50)->default('XI PPLG 1');
                $table->text('anggota');
                $table->string('status', 30)->default('Belum Selesai');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('piket');
        Schema::dropIfExists('bk');
        Schema::dropIfExists('mapel');
        Schema::dropIfExists('izin');
        Schema::dropIfExists('presensi');
        Schema::dropIfExists('siswa');
    }
};

