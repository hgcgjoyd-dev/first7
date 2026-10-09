<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PelanggaranSiswa extends Model
{
    use HasFactory;

    protected $table = 'pelanggaran_siswa';

    protected $primaryKey = 'id_pelanggaran_siswa';

    protected $fillable = [
        'id_siswa',
        'id_pelanggaran',
        'id_guru_bk',
        'tanggal_kejadian',
        'poin',
        'keterangan',
        'tindak_lanjut',
        'status_penanganan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_kejadian' => 'date',
        ];
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa');
    }

    public function jenisPelanggaran(): BelongsTo
    {
        return $this->belongsTo(JenisPelanggaran::class, 'id_pelanggaran', 'id_pelanggaran');
    }

    public function guruBk(): BelongsTo
    {
        return $this->belongsTo(GuruBk::class, 'id_guru_bk', 'id_guru_bk');
    }
}
