<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\GuruBk;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseRelationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_requests_to_student_routes_are_redirected_to_login(): void
    {
        foreach (['/dashboard', '/scan', '/presensi', '/izin', '/riwayat', '/mapel', '/bk', '/piket', '/profil', '/cek-db'] as $path) {
            $this->get($path)->assertRedirect(route('login'));
        }
    }

    public function test_authentication_and_profile_tables_match_the_normalized_schema(): void
    {
        $this->assertTrue(Schema::hasColumns('user', ['id_user', 'username', 'nama', 'email', 'password', 'role']));
        $this->assertTrue(Schema::hasColumns('user', ['status_aktif']));
        $this->assertTrue(Schema::hasColumns('siswa', ['id_siswa', 'id_user', 'no_siswa', 'id_kelas', 'nomor_absen', 'nama_wali', 'no_telp_wali']));
        $this->assertTrue(Schema::hasColumns('guru', ['id_guru', 'id_user', 'no_guru']));
        $this->assertTrue(Schema::hasColumns('pelanggaran_siswa', ['id_pelanggaran_siswa', 'poin']));
        $this->assertFalse(Schema::hasColumn('user', 'name'));
    }

    public function test_user_profiles_link_to_the_correct_student_and_counselor_records(): void
    {
        $kelas = Kelas::create([
            'nama_kelas' => 'XI PPLG 1',
            'tingkat' => 'XI',
            'jurusan' => 'PPLG',
            'tahun_ajaran' => '2026/2027',
        ]);

        $studentUser = User::factory()->create(['role' => 'siswa']);
        $student = Siswa::create([
            'id_user' => $studentUser->id_user,
            'no_siswa' => '2411001',
            'nama_siswa' => 'Siswa Tes',
            'id_kelas' => $kelas->id_kelas,
            'jenis_kelamin' => 'L',
        ]);

        $counselorUser = User::factory()->create(['role' => 'guru_bk']);
        $counselor = Guru::create([
            'id_user' => $counselorUser->id_user,
            'no_guru' => '123456',
            'nama_guru' => 'Guru BK Tes',
            'jenis_kelamin' => 'P',
        ]);
        $guruBk = GuruBk::create(['id_guru' => $counselor->id_guru]);

        $this->assertSame($student->id_siswa, $studentUser->siswa->id_siswa);
        $this->assertSame($kelas->id_kelas, $student->kelas->id_kelas);
        $this->assertSame($counselor->id_guru, $counselorUser->guru->id_guru);
        $this->assertSame($guruBk->id_guru_bk, $counselorUser->guru->guruBk->id_guru_bk);
    }
}
