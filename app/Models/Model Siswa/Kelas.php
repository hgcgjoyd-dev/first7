<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';

    protected $primaryKey = 'id_kelas';

    protected $fillable = [
        'nama_kelas',
        'tingkat',
        'jurusan',
        'wali_kelas_id',
        'tahun_ajaran',
    ];

    public function waliKelas()
    {
        return $this->belongsTo(Guru::class, 'wali_kelas_id', 'id_guru');
    }

    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'id_kelas', 'id_kelas');
    }

    public function jadwalPelajaran()
    {
        return $this->hasMany(JadwalPelajaran::class, 'id_kelas', 'id_kelas');
    }
}
