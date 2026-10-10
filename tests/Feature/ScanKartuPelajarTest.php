<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use Database\Seeders\SiswaSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScanKartuPelajarTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config()->set('kesiswaan.initial_student_password', 'Siswa@2026');
    }

    public function test_student_can_scan_using_exact_seeded_name(): void
    {
        $this->seed();

        $response = $this->postJson(route('scan.post'), [
            'code' => 'Andhika Maraville Gazelle',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('siswa.nama', 'Andhika Maraville Gazelle');

        $this->assertAuthenticated();
        $this->assertDatabaseHas('absensi', [
            'keterangan' => 'Scan kartu pelajar',
        ]);
    }

    public function test_student_can_scan_using_noisy_ocr_text_with_name(): void
    {
        $this->seed();

        $ocrText = "PEMERINTAH PROVINSI BALI\nDINAS PENDIDIKAN\nSMK TI BALI GLOBAL DENPASAR\nKARTU TANDA PELAJAR\nNama : ANDHIKA MARAVILLE GAZELLE\nNIS : 2411001";

        $response = $this->postJson(route('scan.post'), [
            'code' => 'KARTU TANDA PELAJAR',
            'raw_text' => $ocrText,
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('siswa.nama', 'Andhika Maraville Gazelle');
    }

    public function test_student_can_scan_using_partial_or_fuzzy_balinese_name(): void
    {
        $this->seed();

        $response = $this->postJson(route('scan.post'), [
            'code' => 'Made Arie Pinandhita',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('siswa.nama', 'I Made Arie Pinandhita');
    }

    public function test_student_can_scan_using_card_number(): void
    {
        $class = Kelas::create([
            'nama_kelas' => 'XI PPLG 1',
            'tingkat' => 'XI',
            'jurusan' => 'PPLG',
            'tahun_ajaran' => '2026/2027',
        ]);

        $user = User::factory()->create([
            'nama' => 'Andhika Maraville Gazelle',
            'role' => 'siswa',
            'status_aktif' => true,
        ]);

        $student = Siswa::create([
            'id_user' => $user->id_user,
            'no_siswa' => '0000001',
            'nama_siswa' => $user->nama,
            'id_kelas' => $class->id_kelas,
            'jenis_kelamin' => 'L',
        ]);

        $response = $this->postJson(route('scan.post'), [
            'code' => '0000001',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('siswa.no_siswa', '0000001');
    }

    public function test_unregistered_name_returns_not_found(): void
    {
        $this->seed();

        $response = $this->postJson(route('scan.post'), [
            'code' => 'Nama Yang Tidak Pernah Ada Di Data Sekolah',
        ]);

        $response->assertNotFound()
            ->assertJsonPath('success', false);
    }

    public function test_card_scan_synchronizes_4_character_nis_under_student_name(): void
    {
        $this->seed();
        $this->seed(SiswaSeeder::class);

        $student = Siswa::where('nama_siswa', 'Andhika Maraville Gazelle')->first();
        $this->assertNotNull($student);
        $this->assertNotEquals('2401', $student->no_siswa);

        $ocrText = "KARTU TANDA PELAJAR\nSMK TI BALI GLOBAL\nANDHIKA MARAVILLE GAZELLE\n2401\nXI PPLG 1";

        $response = $this->postJson(route('scan.post'), [
            'code' => 'Andhika Maraville Gazelle',
            'raw_text' => $ocrText,
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('siswa.nama', 'Andhika Maraville Gazelle')
            ->assertJsonPath('siswa.no_siswa', '2401');

        $this->assertSame('2401', $student->fresh()->no_siswa);
    }
}
