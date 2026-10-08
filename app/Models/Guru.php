<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function kelasWali()
    {
        return $this->hasOne(Kelas::class, 'wali_kelas_id', 'id_guru');
    }

    public function guruBk()
    {
        return $this->hasOne(GuruBk::class, 'id_guru', 'id_guru');
    }

    public function jadwalPelajaran()
    {
        return $this->hasMany(JadwalPelajaran::class, 'id_guru', 'id_guru');
    }

    public function absensi()
    {
        return $this->hasMany(Absensi::class, 'id_guru', 'id_guru');
    }
}
