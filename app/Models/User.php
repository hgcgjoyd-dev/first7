<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'user';

    protected $primaryKey = 'id_user';

    protected $fillable = [
        'username',
        'nama',
        'email',
        'password',
        'role',
        'status_aktif',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'status_aktif' => 'boolean',
        ];
    }

    public function siswa(): HasOne
    {
        return $this->hasOne(Siswa::class, 'id_user', 'id_user');
    }

    public function guru(): HasOne
    {
        return $this->hasOne(Guru::class, 'id_user', 'id_user');
    }

    public function dashboardRoute(): string
    {
        return match ($this->role) {
            'siswa' => 'dashboard.siswa',
            'guru' => 'dashboard.guru',
            'guru_bk' => 'dashboard.guru_bk',
            'admin' => 'dashboard.admin',
            default => abort(403),
        };
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'siswa' => 'Siswa',
            'guru_bk' => 'Guru BK',
            'guru' => 'Guru biasa',
            'admin' => 'Admin',
            default => 'Tidak dikenal',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status_aktif ? 'Aktif' : 'Nonaktif';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isGuru(): bool
    {
        return $this->role === 'guru';
    }

    public function isSiswa(): bool
    {
        return $this->role === 'siswa';
    }

    public function isGuruBk(): bool
    {
        return $this->role === 'guru_bk';
    }

    public function isActive(): bool
    {
        return ! array_key_exists('status_aktif', $this->getAttributes()) || (bool) $this->status_aktif;
    }
}
