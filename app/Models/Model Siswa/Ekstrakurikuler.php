<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ekstrakurikuler extends Model
{
    use HasFactory;

    protected $table = 'ekstrakurikuler';

    protected $primaryKey = 'id_ekskul';

    protected $fillable = [
        'nama_ekskul',
        'pembina',
        'hari_kegiatan',
        'jam_kegiatan',
        'tempat',
    ];

    public function anggotaEkskul()
    {
        return $this->hasMany(AnggotaEkskul::class, 'id_ekskul', 'id_ekskul');
    }

    public function siswa()
    {
        return $this->belongsToMany(Siswa::class, 'anggota_ekskul', 'id_ekskul', 'id_siswa');
    }
}
