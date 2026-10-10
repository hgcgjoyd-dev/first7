<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class SiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! Schema::hasTable('siswa') || ! Schema::hasTable('kelas')) {
            return;
        }

        $kelas = Kelas::firstOrCreate(
            ['nama_kelas' => 'XI PPLG 1'],
            [
                'tingkat' => 'XI',
                'jurusan' => 'PPLG',
                'tahun_ajaran' => '2026/2027',
            ]
        );

        $femaleKeys = ['pradnyani', 'diahpurnama'];

        $index = 1;
        foreach (UserSeeder::STUDENTS as $username => $name) {
            $user = Schema::hasTable('user')
                ? User::query()->where('email', "{$username}@Kesiswaan.id")
                    ->orWhere('username', $username)
                    ->orWhere('nama', $name)
                    ->first()
                : null;

            $noSiswa = sprintf('%07d', $index);
            $gender = in_array($username, $femaleKeys, true) ? 'P' : 'L';

            $siswa = Siswa::where('nama_siswa', $name)->first();

            if (! $siswa) {
                $siswa = Siswa::where('no_siswa', $noSiswa)->first();
            }

            if (! $siswa) {
                $siswa = Siswa::create([
                    'id_user' => $user?->id_user,
                    'no_siswa' => $noSiswa,
                    'nama_siswa' => $name,
                    'id_kelas' => $kelas->id_kelas,
                    'jenis_kelamin' => $gender,
                    'nomor_absen' => $index,
                ]);
            } else {
                if ($user && $siswa->id_user === null) {
                    $siswa->id_user = $user->id_user;
                }
                $siswa->nama_siswa = $name;
                $siswa->save();
            }

            $index++;
        }
    }
}
