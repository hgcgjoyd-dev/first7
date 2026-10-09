<?php

namespace App\Console\Commands;

use App\Models\Guru;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CreateUserAccount extends Command
{
    protected $signature = 'app:create-user-account {role : admin, guru_bk, guru, or siswa}';

    protected $description = 'Create an account and link its profile without a default password';

    public function handle(): int
    {
        $role = (string) $this->argument('role');

        if (! in_array($role, ['admin', 'guru_bk', 'guru', 'siswa'], true)) {
            $this->error('Role harus admin, guru_bk, guru, atau siswa.');

            return self::FAILURE;
        }

        $profileId = null;

        if ($role !== 'admin') {
            $profileId = filter_var($this->ask('ID profil yang akan dihubungkan'), FILTER_VALIDATE_INT);

            if (! $profileId || ! $this->profileCanUseRole($role, $profileId)) {
                $this->error('Profil tidak ditemukan, sudah tertaut, atau tidak sesuai dengan role.');

                return self::FAILURE;
            }
        }

        $input = [
            'username' => $this->ask('Username'),
            'nama' => $this->ask('Nama lengkap'),
            'email' => $this->ask('Email'),
            'password' => $this->secret('Kata sandi (minimal 12 karakter)'),
            'password_confirmation' => $this->secret('Ulangi kata sandi'),
        ];

        $validator = Validator::make($input, [
            'username' => ['required', 'string', 'max:50', 'unique:user,username'],
            'nama' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', 'unique:user,email'],
            'password' => ['required', 'string', 'min:12', 'confirmed'],
        ]);

        if ($validator->fails()) {
            $this->error($validator->errors()->first());

            return self::FAILURE;
        }

        $user = DB::transaction(function () use ($input, $profileId, $role): User {
            $user = User::create([
                'username' => $input['username'],
                'nama' => $input['nama'],
                'email' => $input['email'],
                'password' => $input['password'],
                'role' => $role,
            ]);

            if ($profileId !== null) {
                $profile = $role === 'siswa' ? new Siswa : new Guru;
                $updated = $profile->newQuery()
                    ->whereKey($profileId)
                    ->whereNull('id_user')
                    ->update(['id_user' => $user->id_user]);

                if ($updated !== 1) {
                    throw new \RuntimeException('Profil sudah ditautkan ke akun lain.');
                }
            }

            return $user;
        });

        $this->info("Akun {$role} berhasil dibuat: {$user->email}");

        return self::SUCCESS;
    }

    private function profileCanUseRole(string $role, int $profileId): bool
    {
        if ($role === 'siswa') {
            return Siswa::query()
                ->whereKey($profileId)
                ->whereNull('id_user')
                ->exists();
        }

        $query = Guru::query()
            ->whereKey($profileId)
            ->whereNull('id_user');

        if ($role === 'guru_bk') {
            $query->whereHas('guruBk', fn ($guruBkQuery) => $guruBkQuery->where('status_aktif', true));
        } else {
            $query->whereDoesntHave('guruBk');
        }

        return $query->exists();
    }
}
