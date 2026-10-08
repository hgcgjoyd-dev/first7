<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Konseling extends Model
{
    use HasFactory;

    protected $table = 'konseling';

    protected $primaryKey = 'id_konseling';

    protected $fillable = [
        'id_siswa',
        'id_guru_bk',
        'tanggal_konseling',
        'jam_konseling',
        'jenis_layanan',
        'topik_pembahasan',
        'hasil_konseling',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_konseling' => 'date',
        ];
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa');
    }

    public function guruBk()
    {
        return $this->belongsTo(GuruBk::class, 'id_guru_bk', 'id_guru_bk');
    }
}
