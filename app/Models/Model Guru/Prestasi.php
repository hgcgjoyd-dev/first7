<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prestasi extends Model
{
    use HasFactory;

    protected $table = 'prestasi';

    protected $primaryKey = 'id_prestasi';

    protected $fillable = [
        'id_siswa',
        'nama_prestasi',
        'tingkat',
        'poin',
        'tanggal_perolehan',
        'sertifikat',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_perolehan' => 'date',
        ];
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa');
    }
}
