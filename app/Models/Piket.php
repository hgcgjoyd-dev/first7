<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Piket extends Model
{
    use HasFactory;

    protected $table = 'piket';

    protected $fillable = [
        'hari',
        'kelas',
        'anggota',
        'status',
    ];
}

