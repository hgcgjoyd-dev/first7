<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Izin extends Model
{
    use HasFactory;

    protected $table = 'izin';

    protected $fillable = [
        'siswa_id',
        'jenis',
        'tgl_mulai',
        'tgl_selesai',
        'durasi_hari',
        'alasan',
        'bukti_file',
        'status',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }
}

