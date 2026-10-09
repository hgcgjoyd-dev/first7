<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Izin extends Model
{
    use HasFactory;

    protected $table = 'izin';

    protected $primaryKey = 'id_izin';

    protected $fillable = [
        'id_siswa',
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
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa');
    }
}
