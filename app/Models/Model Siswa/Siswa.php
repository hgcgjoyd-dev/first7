<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswa';

    protected $primaryKey = 'id_siswa';

    protected $fillable = [
        'id_user',
        'no_siswa',
        'nomor_absen',
        'nama_siswa',
        'id_kelas',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'no_telp',
        'nama_wali',
        'no_telp_wali',
        'poin_pelanggaran',
        'poin_penghargaan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
        ];
    }

    public function getNisAttribute(): ?string
    {
        return $this->no_siswa;
    }

    public function getNamaAttribute(): ?string
    {
        return $this->nama_siswa;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }

    public function absensi(): HasMany
    {
        return $this->hasMany(Absensi::class, 'id_siswa', 'id_siswa');
    }

    public function izin(): HasMany
    {
        return $this->hasMany(Izin::class, 'id_siswa', 'id_siswa');
    }

    public function konseling(): HasMany
    {
        return $this->hasMany(Konseling::class, 'id_siswa', 'id_siswa');
    }

    public function pelanggaranSiswa(): HasMany
    {
        return $this->hasMany(PelanggaranSiswa::class, 'id_siswa', 'id_siswa');
    }

    public function prestasi(): HasMany
    {
        return $this->hasMany(Prestasi::class, 'id_siswa', 'id_siswa');
    }
}
