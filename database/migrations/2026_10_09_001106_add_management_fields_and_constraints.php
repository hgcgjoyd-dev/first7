<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('absensi') && DB::table('absensi')
            ->select('id_siswa', 'tanggal')
            ->groupBy('id_siswa', 'tanggal')
            ->havingRaw('COUNT(*) > 1')
            ->exists()) {
            throw new RuntimeException('Absensi ganda ditemukan. Bersihkan duplikasi per siswa/tanggal sebelum menjalankan migrasi ini.');
        }

        if (Schema::hasTable('user') && ! Schema::hasColumn('user', 'status_aktif')) {
            Schema::table('user', function (Blueprint $table): void {
                $table->boolean('status_aktif')->default(true);
            });
        }

        if (Schema::hasTable('siswa')) {
            Schema::table('siswa', function (Blueprint $table): void {
                if (! Schema::hasColumn('siswa', 'nomor_absen')) {
                    $table->unsignedSmallInteger('nomor_absen')->nullable();
                }

                if (! Schema::hasColumn('siswa', 'nama_wali')) {
                    $table->string('nama_wali', 100)->nullable();
                }

                if (! Schema::hasColumn('siswa', 'no_telp_wali')) {
                    $table->string('no_telp_wali', 20)->nullable();
                }
            });
        }

        if (Schema::hasTable('pelanggaran_siswa') && ! Schema::hasColumn('pelanggaran_siswa', 'poin')) {
            Schema::table('pelanggaran_siswa', function (Blueprint $table): void {
                $table->unsignedInteger('poin')->default(0);
            });

            if (Schema::hasTable('jenis_pelanggaran')) {
                DB::table('pelanggaran_siswa')
                    ->join(
                        'jenis_pelanggaran',
                        'pelanggaran_siswa.id_pelanggaran',
                        '=',
                        'jenis_pelanggaran.id_pelanggaran',
                    )
                    ->select('pelanggaran_siswa.id_pelanggaran_siswa', 'jenis_pelanggaran.poin as legacy_poin')
                    ->orderBy('pelanggaran_siswa.id_pelanggaran_siswa')
                    ->chunkById(500, function ($records): void {
                        foreach ($records as $record) {
                            DB::table('pelanggaran_siswa')
                                ->where('id_pelanggaran_siswa', $record->id_pelanggaran_siswa)
                                ->update(['poin' => $record->legacy_poin]);
                        }
                    }, 'pelanggaran_siswa.id_pelanggaran_siswa');
            }
        }

        if (Schema::hasTable('absensi')) {
            $indexes = Schema::getIndexes('absensi');
            $hasDailyUniqueIndex = collect($indexes)->contains(
                fn (array $index): bool => $index['unique']
                    && $index['columns'] === ['id_siswa', 'tanggal'],
            );

            if (! $hasDailyUniqueIndex) {
                Schema::table('absensi', function (Blueprint $table): void {
                    $table->unique(['id_siswa', 'tanggal'], 'absensi_siswa_tanggal_unique');
                });
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('siswa') && DB::table('siswa')->where(function ($query): void {
            $query->whereNotNull('nomor_absen')
                ->orWhereNotNull('nama_wali')
                ->orWhereNotNull('no_telp_wali');
        })->exists()) {
            throw new RuntimeException('Migrasi tidak dapat dibatalkan karena data wali atau nomor absen sudah tersimpan.');
        }

        if (Schema::hasTable('user') && DB::table('user')->where('status_aktif', false)->exists()) {
            throw new RuntimeException('Migrasi tidak dapat dibatalkan karena ada akun yang sudah dinonaktifkan.');
        }

        if (Schema::hasTable('pelanggaran_siswa')
            && Schema::hasTable('jenis_pelanggaran')
            && DB::table('pelanggaran_siswa')
                ->join('jenis_pelanggaran', 'pelanggaran_siswa.id_pelanggaran', '=', 'jenis_pelanggaran.id_pelanggaran')
                ->whereColumn('pelanggaran_siswa.poin', '!=', 'jenis_pelanggaran.poin')
                ->exists()) {
            throw new RuntimeException('Migrasi tidak dapat dibatalkan karena ada nilai poin historis yang berbeda dari jenis pelanggarannya.');
        }

        if (Schema::hasTable('absensi')) {
            $indexes = Schema::getIndexes('absensi');

            if (collect($indexes)->contains(
                fn (array $index): bool => $index['name'] === 'absensi_siswa_tanggal_unique',
            )) {
                Schema::table('absensi', function (Blueprint $table): void {
                    $table->dropUnique('absensi_siswa_tanggal_unique');
                });
            }
        }

        if (Schema::hasTable('pelanggaran_siswa') && Schema::hasColumn('pelanggaran_siswa', 'poin')) {
            Schema::table('pelanggaran_siswa', function (Blueprint $table): void {
                $table->dropColumn('poin');
            });
        }

        if (Schema::hasTable('siswa')) {
            Schema::table('siswa', function (Blueprint $table): void {
                foreach (['nomor_absen', 'nama_wali', 'no_telp_wali'] as $column) {
                    if (Schema::hasColumn('siswa', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        if (Schema::hasTable('user') && Schema::hasColumn('user', 'status_aktif')) {
            Schema::table('user', function (Blueprint $table): void {
                $table->dropColumn('status_aktif');
            });
        }
    }
};
