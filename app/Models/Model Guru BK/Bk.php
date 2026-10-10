<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bk extends Model
{
    use HasFactory;

    protected $table = 'bk';

    protected $fillable = [
        'siswa_id',
        'jenis',
        'poin',
        'keterangan',
        'tanggal',
        'guru_bk',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }
}
