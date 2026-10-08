<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Siswa extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'siswa';

    protected $fillable = [
        'nis',
        'nisn',
        'nama',
        'email',
        'password',
        'kelas',
        'jurusan',
        'rfid_card',
        'foto',
        'poin_bk',
        'poin_prestasi',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function presensi()
    {
        return $this->hasMany(Presensi::class, 'siswa_id');
    }

    public function izin()
    {
        return $this->hasMany(Izin::class, 'siswa_id');
    }

    public function bk()
    {
        return $this->hasMany(Bk::class, 'siswa_id');
    }
}

