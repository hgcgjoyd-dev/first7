<?php

namespace Tests\Feature;

use App\Models\GuruBk;
use App\Models\Siswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseRelationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_all_web_routes_are_accessible(): void
    {
        $routes = [
            '/',
            '/login',
            '/scan',
            '/dashboard',
            '/presensi',
            '/izin',
            '/riwayat',
            '/mapel',
            '/bk',
            '/piket',
            '/profil',
            '/cek-db',
        ];

        foreach ($routes as $route) {
            $response = $this->get($route);
            $response->assertStatus(200);
        }
    }

    public function test_database_has_seeded_users_with_roles(): void
    {
        $this->assertDatabaseHas('users', ['email' => 'admin@smktibaliglobal.sch.id', 'role' => 'admin']);
        $this->assertDatabaseHas('users', ['email' => 'made.wijaya@smktibaliglobal.sch.id', 'role' => 'guru']);
        $this->assertDatabaseHas('users', ['email' => 'suastini.bk@smktibaliglobal.sch.id', 'role' => 'guru_bk']);
        $this->assertDatabaseHas('users', ['email' => 'wahyu.pratama@smktibaliglobal.sch.id', 'role' => 'siswa']);
    }

    public function test_models_and_foreign_keys_are_connected_correctly(): void
    {
        $siswa = Siswa::where('nomor_siswa', '2411001')->first();
        $this->assertNotNull($siswa);
        $this->assertEquals(7, strlen($siswa->nomor_siswa));

        // siswa -> kelas
        $this->assertNotNull($siswa->kelas);
        $this->assertEquals('XI PPLG 1', $siswa->kelas->nama_kelas);

        // kelas -> wali kelas (guru)
        $wali = $siswa->kelas->waliKelas;
        $this->assertNotNull($wali);
        $this->assertEquals(6, strlen($wali->nomor_guru));
        $this->assertEquals('I Made Wijaya, S.Kom', $wali->nama_guru);

        // guru -> guru_bk
        $guruBkModel = GuruBk::first();
        $this->assertNotNull($guruBkModel);
        $this->assertNotNull($guruBkModel->guru);
        $this->assertEquals('Dra. Ni Luh Suastini, S.Pd', $guruBkModel->guru->nama_guru);

        // siswa -> absensi
        $this->assertTrue($siswa->absensi()->count() > 0);

        // siswa -> konseling
        $this->assertTrue($siswa->konseling()->count() > 0);

        // siswa -> pelanggaran_siswa
        $this->assertTrue($siswa->pelanggaranSiswa()->count() > 0);

        // siswa -> ekstrakurikuler
        $this->assertTrue($siswa->ekstrakurikuler()->count() > 0);

        // siswa -> jadwal_piket
        $this->assertTrue($siswa->jadwalPiket()->count() > 0);

        // siswa -> prestasi
        $this->assertTrue($siswa->prestasi()->count() > 0);
    }
}
