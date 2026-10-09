<?php

namespace Tests\Feature\Admin;

use App\Models\Absensi;
use App\Models\Guru;
use App\Models\GuruBk;
use App\Models\JenisPelanggaran;
use App\Models\Kelas;
use App\Models\PelanggaranSiswa;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CrudAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_view_edit_and_delete_a_student_without_history(): void
    {
        $admin = $this->admin();
        $class = $this->createClass();

        $response = $this->actingAs($admin)->post(route('admin.siswa.store'), [
            'no_siswa' => '2411001',
            'nama_siswa' => 'Siswa CRUD',
            'id_kelas' => $class->id_kelas,
            'nomor_absen' => 7,
            'jenis_kelamin' => 'P',
            'tanggal_lahir' => '2009-04-15',
            'nama_wali' => 'Wali CRUD',
            'no_telp_wali' => '081234567890',
        ]);

        $student = Siswa::query()->where('no_siswa', '2411001')->firstOrFail();
        $response->assertRedirect(route('admin.siswa.show', $student));
        $this->assertDatabaseHas('siswa', ['id_siswa' => $student->id_siswa, 'nama_wali' => 'Wali CRUD']);
        $this->get(route('admin.siswa.show', $student))->assertOk()->assertSee('Siswa CRUD');

        $this->put(route('admin.siswa.update', $student), [
            'no_siswa' => '2411001',
            'nama_siswa' => 'Siswa Diperbarui',
            'id_kelas' => $class->id_kelas,
            'nomor_absen' => 8,
            'jenis_kelamin' => 'P',
            'tanggal_lahir' => '2009-04-15',
            'nama_wali' => 'Wali Diperbarui',
            'no_telp_wali' => '081234567891',
        ])->assertRedirect(route('admin.siswa.show', $student));

        $this->assertDatabaseHas('siswa', ['id_siswa' => $student->id_siswa, 'nama_siswa' => 'Siswa Diperbarui']);
        $this->delete(route('admin.siswa.destroy', $student))->assertRedirect(route('admin.siswa.index'));
        $this->assertDatabaseMissing('siswa', ['id_siswa' => $student->id_siswa]);
    }

    public function test_student_nis_must_be_seven_digits_and_unique(): void
    {
        $class = $this->createClass();

        $this->actingAs($this->admin())->post(route('admin.siswa.store'), [
            'no_siswa' => '123456',
            'nama_siswa' => 'NIS Tidak Valid',
            'id_kelas' => $class->id_kelas,
            'jenis_kelamin' => 'L',
        ])->assertSessionHasErrors('no_siswa');

        $this->post(route('admin.siswa.store'), [
            'no_siswa' => '2411001',
            'nama_siswa' => 'Siswa Pertama',
            'id_kelas' => $class->id_kelas,
            'jenis_kelamin' => 'L',
        ])->assertRedirect();

        $this->post(route('admin.siswa.store'), [
            'no_siswa' => '2411001',
            'nama_siswa' => 'Siswa Duplikat',
            'id_kelas' => $class->id_kelas,
            'jenis_kelamin' => 'L',
        ])->assertSessionHasErrors('no_siswa');
    }

    public function test_admin_can_create_and_edit_teacher_role_and_validates_six_digit_nip(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.guru.store'), [
                'no_guru' => '12345',
                'nama_guru' => 'Guru Salah NIP',
                'jenis_kelamin' => 'L',
                'tipe_guru' => 'guru',
            ])
            ->assertSessionHasErrors('no_guru');

        $this->post(route('admin.guru.store'), [
            'no_guru' => '123456',
            'nama_guru' => 'Guru BK CRUD',
            'jenis_kelamin' => 'P',
            'no_telp' => '0812345678',
            'alamat' => 'Badung',
            'tipe_guru' => 'guru_bk',
        ])->assertRedirect();

        $teacher = Guru::query()->where('no_guru', '123456')->firstOrFail();
        $this->assertTrue($teacher->guruBk->status_aktif);
        $this->get(route('admin.guru.show', $teacher))->assertOk()->assertSee('Guru BK CRUD');
        $this->get(route('admin.guru.edit', $teacher))->assertOk();

        $this->post(route('admin.guru.store'), [
            'no_guru' => '123456',
            'nama_guru' => 'Guru NIP Duplikat',
            'jenis_kelamin' => 'L',
            'tipe_guru' => 'guru',
        ])->assertSessionHasErrors('no_guru');

        $this->put(route('admin.guru.update', $teacher), [
            'no_guru' => '123456',
            'nama_guru' => 'Guru Biasa CRUD',
            'jenis_kelamin' => 'P',
            'tipe_guru' => 'guru',
        ])->assertRedirect(route('admin.guru.show', $teacher));

        $this->assertFalse($teacher->fresh()->guruBk->status_aktif);
        $this->delete(route('admin.guru.destroy', $teacher))->assertRedirect(route('admin.guru.index'));
        $this->assertDatabaseMissing('guru', ['id_guru' => $teacher->id_guru]);
    }

    public function test_admin_can_manage_classes_and_cannot_delete_a_class_that_has_students(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.kelas.store'), [
            'nama_kelas' => 'X PPLG 1',
            'tingkat' => 'X',
            'jurusan' => 'PPLG',
            'tahun_ajaran' => '2026/2027',
        ])->assertRedirect();

        $class = Kelas::query()->where('nama_kelas', 'X PPLG 1')->firstOrFail();
        $this->get(route('admin.kelas.index'))->assertOk()->assertSee('0');
        $this->get(route('admin.kelas.show', $class))->assertOk()->assertSee('X PPLG 1');

        $this->put(route('admin.kelas.update', $class), [
            'nama_kelas' => 'X PPLG 2',
            'tingkat' => 'X',
            'jurusan' => 'PPLG',
            'tahun_ajaran' => '2026/2027',
        ])->assertRedirect(route('admin.kelas.show', $class));
        $this->assertDatabaseHas('kelas', ['id_kelas' => $class->id_kelas, 'nama_kelas' => 'X PPLG 2']);

        $student = $this->student($class);
        $this->delete(route('admin.kelas.destroy', $class))->assertSessionHasErrors('delete');
        $this->assertModelExists($student);

        $emptyClass = $this->createClass();
        $this->delete(route('admin.kelas.destroy', $emptyClass))
            ->assertRedirect(route('admin.kelas.index'));
        $this->assertDatabaseMissing('kelas', ['id_kelas' => $emptyClass->id_kelas]);
    }

    public function test_admin_can_manage_daily_attendance_and_prevent_duplicate_student_dates(): void
    {
        $class = $this->createClass();
        $student = $this->student($class);
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.absensi.store'), [
            'id_siswa' => $student->id_siswa,
            'tanggal' => '2026-10-09',
            'status' => 'Alpha',
            'keterangan' => 'Tidak hadir',
        ])->assertRedirect();

        $attendance = Absensi::query()->firstOrFail();
        $this->assertDatabaseHas('absensi', ['id_absensi' => $attendance->id_absensi, 'status' => 'Alpa']);
        $this->get(route('admin.absensi.show', $attendance))->assertOk()->assertSee('Alpha');
        $this->get(route('admin.absensi.report', ['mulai' => '2026-10-01', 'selesai' => '2026-10-31']))
            ->assertOk()
            ->assertSee('Alpha');

        $this->post(route('admin.absensi.store'), [
            'id_siswa' => $student->id_siswa,
            'tanggal' => '2026-10-09',
            'status' => 'Hadir',
        ])->assertSessionHasErrors('tanggal');

        $this->put(route('admin.absensi.update', $attendance), [
            'id_siswa' => $student->id_siswa,
            'tanggal' => '2026-10-09',
            'status' => 'Sakit',
        ])->assertRedirect(route('admin.absensi.show', $attendance));
        $this->assertDatabaseHas('absensi', ['id_absensi' => $attendance->id_absensi, 'status' => 'Sakit']);

        $this->delete(route('admin.absensi.destroy', $attendance))->assertRedirect(route('admin.absensi.index'));
        $this->assertDatabaseMissing('absensi', ['id_absensi' => $attendance->id_absensi]);
    }

    public function test_counselor_can_manage_incident_records_and_points_are_recalculated(): void
    {
        $counselorAccount = User::factory()->create(['role' => 'guru_bk']);
        $teacher = Guru::create([
            'id_user' => $counselorAccount->id_user,
            'no_guru' => '345678',
            'nama_guru' => 'Guru BK CRUD',
            'jenis_kelamin' => 'P',
        ]);
        $counselor = GuruBk::create(['id_guru' => $teacher->id_guru]);
        $student = $this->student($this->createClass());
        $violationType = JenisPelanggaran::create([
            'nama_pelanggaran' => 'Terlambat',
            'kategori' => 'Ringan',
            'poin' => 5,
        ]);

        $this->actingAs($counselorAccount)->post(route('bk.pelanggaran.store'), [
            'id_siswa' => $student->id_siswa,
            'id_pelanggaran' => $violationType->id_pelanggaran,
            'tanggal_kejadian' => '2026-10-09',
            'poin' => 5,
            'keterangan' => 'Datang terlambat',
        ])->assertRedirect();

        $incident = PelanggaranSiswa::query()->firstOrFail();
        $this->assertSame($counselor->id_guru_bk, $incident->id_guru_bk);
        $this->assertDatabaseHas('siswa', ['id_siswa' => $student->id_siswa, 'poin_pelanggaran' => 5]);
        $this->get(route('bk.pelanggaran.show', $incident))->assertOk()->assertSee('Datang terlambat');

        $this->put(route('bk.pelanggaran.update', $incident), [
            'id_siswa' => $student->id_siswa,
            'id_pelanggaran' => $violationType->id_pelanggaran,
            'tanggal_kejadian' => '2026-10-09',
            'poin' => 9,
            'keterangan' => 'Diperbarui',
        ])->assertRedirect(route('bk.pelanggaran.show', $incident));
        $this->assertDatabaseHas('siswa', ['id_siswa' => $student->id_siswa, 'poin_pelanggaran' => 9]);

        $this->delete(route('bk.pelanggaran.destroy', $incident))->assertRedirect(route('bk.pelanggaran.index'));
        $this->assertDatabaseMissing('pelanggaran_siswa', ['id_pelanggaran_siswa' => $incident->id_pelanggaran_siswa]);
        $this->assertDatabaseHas('siswa', ['id_siswa' => $student->id_siswa, 'poin_pelanggaran' => 0]);
    }

    public function test_admin_and_counselor_crud_forms_render(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin);
        foreach ([
            'admin.siswa.create',
            'admin.guru.create',
            'admin.kelas.create',
            'admin.absensi.create',
            'admin.users.create',
            'admin.absensi.report',
            'bk.pelanggaran.create',
        ] as $routeName) {
            $this->get(route($routeName))->assertOk();
        }
    }

    public function test_student_detail_escapes_user_supplied_html(): void
    {
        $student = $this->student($this->createClass());
        $student->update(['nama_siswa' => '<script>alert(1)</script>']);

        $this->actingAs($this->admin())
            ->get(route('admin.siswa.show', $student))
            ->assertOk()
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_admin_can_create_link_edit_and_deactivate_an_account_without_plaintext_passwords(): void
    {
        $admin = $this->admin();
        $student = $this->student($this->createClass());

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'username' => 'murid-crud',
            'nama' => 'Akun Siswa',
            'email' => 'murid-crud@example.test',
            'password' => 'strong-password-123',
            'password_confirmation' => 'strong-password-123',
            'role' => 'siswa',
            'status_aktif' => '1',
            'id_siswa' => $student->id_siswa,
        ])->assertRedirect()->assertSessionHasNoErrors();

        $account = User::query()->where('email', 'murid-crud@example.test')->firstOrFail();
        $this->assertTrue(Hash::check('strong-password-123', $account->password));
        $this->assertSame($student->id_siswa, $account->siswa->id_siswa);
        $this->get(route('admin.users.show', $account))->assertOk()->assertSee('Akun Siswa');

        $this->put(route('admin.users.update', $account), [
            'username' => 'murid-crud',
            'nama' => 'Akun Siswa Diperbarui',
            'email' => 'murid-crud@example.test',
            'password' => '',
            'password_confirmation' => '',
            'role' => 'siswa',
            'status_aktif' => '1',
            'id_siswa' => $student->id_siswa,
        ])->assertRedirect(route('admin.users.show', $account));

        $this->put(route('admin.users.update', $account), [
            'username' => 'murid-crud',
            'nama' => 'Akun Admin',
            'email' => 'murid-crud@example.test',
            'password' => '',
            'password_confirmation' => '',
            'role' => 'admin',
            'status_aktif' => '1',
        ])->assertRedirect(route('admin.users.show', $account));
        $this->assertDatabaseHas('siswa', ['id_siswa' => $student->id_siswa, 'id_user' => null]);

        $this->delete(route('admin.users.destroy', $account))->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('user', ['id_user' => $account->id_user, 'status_aktif' => false]);
    }

    public function test_account_creation_links_regular_teacher_counselor_and_admin_profiles_correctly(): void
    {
        $admin = $this->admin();
        $teacher = Guru::create([
            'no_guru' => '456789',
            'nama_guru' => 'Guru Akun',
            'jenis_kelamin' => 'L',
        ]);
        $counselor = Guru::create([
            'no_guru' => '567890',
            'nama_guru' => 'Guru BK Akun',
            'jenis_kelamin' => 'P',
        ]);

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'username' => 'guru-akun',
            'nama' => 'Akun Guru',
            'email' => 'guru-akun@example.test',
            'password' => 'strong-password-456',
            'password_confirmation' => 'strong-password-456',
            'role' => 'guru',
            'status_aktif' => '1',
            'id_guru' => $teacher->id_guru,
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertDatabaseHas('user', ['email' => 'guru-akun@example.test', 'role' => 'guru']);
        $teacherAccount = User::query()->where('email', 'guru-akun@example.test')->firstOrFail();
        $this->assertSame($teacher->id_guru, $teacherAccount->guru->id_guru);
        $this->assertNull($teacherAccount->guru->guruBk);

        $this->post(route('admin.users.store'), [
            'username' => 'guru-bk-akun',
            'nama' => 'Akun Guru BK',
            'email' => 'guru-bk-akun@example.test',
            'password' => 'strong-password-789',
            'password_confirmation' => 'strong-password-789',
            'role' => 'guru_bk',
            'status_aktif' => '1',
            'id_guru' => $counselor->id_guru,
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertDatabaseHas('user', ['email' => 'guru-bk-akun@example.test', 'role' => 'guru_bk']);
        $counselorAccount = User::query()->where('email', 'guru-bk-akun@example.test')->firstOrFail();
        $this->assertSame($counselor->id_guru, $counselorAccount->guru->id_guru);
        $this->assertTrue($counselorAccount->guru->guruBk->status_aktif);

        $this->post(route('admin.users.store'), [
            'username' => 'admin-akun',
            'nama' => 'Admin CRUD',
            'email' => 'admin-akun@example.test',
            'password' => 'strong-password-999',
            'password_confirmation' => 'strong-password-999',
            'role' => 'admin',
            'status_aktif' => '1',
        ])->assertRedirect();

        $this->assertDatabaseHas('user', ['email' => 'admin-akun@example.test', 'role' => 'admin']);
    }

    public function test_last_admin_cannot_be_disabled_and_roles_cannot_access_other_role_resources(): void
    {
        $admin = $this->admin();
        $studentAccount = User::factory()->create(['role' => 'siswa']);
        $this->student($this->createClass(), $studentAccount);

        $this->actingAs($admin)->delete(route('admin.users.destroy', $admin))->assertSessionHasErrors('delete');
        $this->assertTrue($admin->fresh()->status_aktif);

        $this->actingAs($studentAccount)->get(route('admin.siswa.index'))->assertForbidden();

        $teacherAccount = User::factory()->create(['role' => 'guru']);
        $this->actingAs($teacherAccount)->get(route('bk.pelanggaran.index'))->assertForbidden();

        $counselorAccount = User::factory()->create(['role' => 'guru_bk']);
        $this->actingAs($counselorAccount)->get(route('admin.absensi.index'))->assertForbidden();
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function createClass(): Kelas
    {
        return Kelas::create([
            'nama_kelas' => fake()->unique()->words(2, true),
            'tingkat' => 'XI',
            'jurusan' => 'PPLG',
            'tahun_ajaran' => '2026/2027',
        ]);
    }

    private function student(Kelas $class, ?User $account = null): Siswa
    {
        return Siswa::create([
            'id_user' => $account?->id_user,
            'no_siswa' => fake()->unique()->numerify('#######'),
            'nama_siswa' => fake()->name(),
            'id_kelas' => $class->id_kelas,
            'jenis_kelamin' => 'L',
        ]);
    }
}
