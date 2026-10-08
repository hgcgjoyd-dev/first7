<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnggotaEkskul extends Model
{
    use HasFactory;

    protected $table = 'anggota_ekskul';

    protected $primaryKey = 'id_anggota_ekskul';

    protected $fillable = [
        'id_siswa',
        'id_ekskul',
        'jabatan',
        'tanggal_bergabung',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_bergabung' => 'date',
        ];
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa');
    }

    public function ekstrakurikuler()
    {
        return $this->belongsTo(Ekstrakurikuler::class, 'id_ekskul', 'id_ekskul');
    }
}
