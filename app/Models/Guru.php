<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Guru extends Model
{
    use HasFactory;

    protected $table = 'guru';

    protected $primaryKey = 'id_guru';

    protected $fillable = [
        'id_user',
        'no_guru',
        'nama_guru',
        'jenis_kelamin',
        'no_telp',
        'email',
        'alamat',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function kelasWali(): HasOne
    {
        return $this->hasOne(Kelas::class, 'wali_kelas_id', 'id_guru');
    }

    public function guruBk(): HasOne
    {
        return $this->hasOne(GuruBk::class, 'id_guru', 'id_guru');
    }

    public function jadwalPelajaran(): HasMany
    {
        return $this->hasMany(JadwalPelajaran::class, 'id_guru', 'id_guru');
    }

    public function absensi(): HasMany
    {
        return $this->hasMany(Absensi::class, 'id_guru', 'id_guru');
    }

    public function getRoleLabelAttribute(): string
    {
        return $this->guruBk?->status_aktif ? 'Guru BK' : 'Guru biasa';
    }
}
