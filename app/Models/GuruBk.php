<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuruBk extends Model
{
    use HasFactory;

    protected $table = 'guru_bk';

    protected $primaryKey = 'id_guru_bk';

    protected $fillable = [
        'id_guru',
        'status_aktif',
    ];

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'id_guru', 'id_guru');
    }

    public function konseling()
    {
        return $this->hasMany(Konseling::class, 'id_guru_bk', 'id_guru_bk');
    }

    public function pelanggaranSiswa()
    {
        return $this->hasMany(PelanggaranSiswa::class, 'id_guru_bk', 'id_guru_bk');
    }
}
