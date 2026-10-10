<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensi';

    protected $primaryKey = 'id_absensi';

    protected $fillable = [
        'id_siswa',
        'id_guru',
        'tanggal',
        'jam_masuk',
        'jam_pulang',
        'status',
        'latitude',
        'longitude',
        'foto_biometrik',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status === 'Alpa' ? 'Alpha' : $this->status;
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa');
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'id_guru', 'id_guru');
    }
}
