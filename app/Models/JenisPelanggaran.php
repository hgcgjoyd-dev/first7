<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisPelanggaran extends Model
{
    use HasFactory;

    protected $table = 'jenis_pelanggaran';

    protected $primaryKey = 'id_pelanggaran';

    protected $fillable = [
        'nama_pelanggaran',
        'kategori',
        'poin',
        'sanksi',
    ];

    public function pelanggaranSiswa()
    {
        return $this->hasMany(PelanggaranSiswa::class, 'id_pelanggaran', 'id_pelanggaran');
    }
}
