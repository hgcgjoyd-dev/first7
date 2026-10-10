<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\GuruBk;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public static function roleDashboardRoutes(): array
    {
        return [
            'siswa' => ['siswa', 'dashboard.siswa'],
            'guru biasa' => ['guru', 'dashboard.guru'],
            'guru BK' => ['guru_bk', 'dashboard.guru_bk'],
            'admin' => ['admin', 'dashboard.admin'],
        ];
    }

    public function test_login_form_requests_an_email_without_prefilled_credentials(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('type="email"', false)
            ->assertDontSee('password123');
    }

    #[DataProvider('roleDashboardRoutes')]
    public function test_valid_email_and_password_redirect_to_the_matching_role_dashboard(
        string $role,
        string $dashboardRoute,
    ): void {
        $user = $this->createAccount($role);

        $response = $this->post(route('login.post'), [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route($dashboardRoute));
        $this->assertAuthenticatedAs($user);
        $this->get(route('dashboard'))->assertRedirect(route($dashboardRoute));
        $this->get(route($dashboardRoute))->assertOk()->assertSee($user->nama);
    }

    public function test_invalid_password_returns_a_safe_login_error(): void
    {
        $user = $this->createAccount('admin');

        $response = $this->post(route('login.post'), [
            'email' => $user->email,
            'password' => 'incorrect-password',
        ]);

        $response->assertRedirect(route('login'))
            ->assertSessionHasErrors(['email' => 'Email atau kata sandi tidak cocok.'])
            ->assertSessionHas('_old_input.email')
            ->assertSessionMissing('_old_input.password');
        $this->assertGuest();
    }

    public function test_passwords_are_hashed_and_student_numbers_are_not_login_identifiers(): void
    {
        $student = $this->createAccount('siswa');

        $this->assertNotSame('password123', $student->password);
        $this->assertTrue(Hash::check('password123', $student->password));

        $this->post(route('login.post'), [
            'email' => $student->siswa->no_siswa,
            'password' => 'password123',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_student_without_profile_can_login_only_to_an_incomplete_student_dashboard(): void
    {
        $orphanStudent = User::factory()->create(['role' => 'siswa']);

        $this->post(route('login.post'), [
            'email' => $orphanStudent->email,
            'password' => 'password123',
        ])->assertRedirect(route('dashboard.siswa'));

        $this->assertAuthenticatedAs($orphanStudent);
        $this->get(route('dashboard.siswa'))
            ->assertOk()
            ->assertSee($orphanStudent->nama)
            ->assertSee('Profil siswa')
            ->assertSee('Belum dilengkapi');
        $this->get(route('profil'))->assertForbidden();

        $counselorMisclassifiedAsTeacher = $this->createAccount('guru_bk');
        $counselorMisclassifiedAsTeacher->update(['role' => 'guru']);

        $this->post(route('logout'));
        $this->post(route('login.post'), [
            'email' => $counselorMisclassifiedAsTeacher->email,
            'password' => 'password123',
        ])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_supports_existing_user_tables_without_an_activation_column(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Schema::table('user', function (Blueprint $table): void {
            $table->dropColumn('status_aktif');
        });

        $this->post(route('login.post'), [
            'email' => $admin->email,
            'password' => 'password123',
        ])->assertRedirect(route('dashboard.admin'));

        $this->assertAuthenticatedAs($admin);
        $this->get(route('dashboard.admin'))->assertOk();
    }

    public function test_missing_and_invalid_email_inputs_are_rejected(): void
    {
        $this->from(route('login'))
            ->post(route('login.post'), ['password' => 'password123'])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->from(route('login'))
            ->post(route('login.post'), ['email' => 'not-an-email', 'password' => 'password123'])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');
    }

    public function test_student_cannot_view_another_student_or_a_different_role_dashboard(): void
    {
        $student = $this->createAccount('siswa');
        $otherStudent = $this->createAccount('siswa');

        $this->actingAs($student)
            ->get(route('dashboard.siswa'))
            ->assertOk()
            ->assertSee($student->siswa->nama_siswa)
            ->assertDontSee($otherStudent->siswa->nama_siswa);

        $this->get(route('dashboard.admin'))->assertForbidden();
        $this->get(route('dashboard.guru_bk'))->assertForbidden();
    }

    public function test_regular_teacher_cannot_access_the_counselor_dashboard(): void
    {
        $teacher = $this->createAccount('guru');

        $this->actingAs($teacher)
            ->get(route('dashboard.guru_bk'))
            ->assertForbidden();

        $this->get(route('dashboard.admin'))->assertForbidden();
    }

    public function test_counselor_dashboard_shows_student_points_and_is_not_available_to_regular_teachers(): void
    {
        $counselor = $this->createAccount('guru_bk');
        $student = $this->createAccount('siswa');
        $student->siswa->update(['poin_pelanggaran' => 15]);

        $this->actingAs($counselor)
            ->get(route('dashboard.guru_bk'))
            ->assertOk()
            ->assertSee($student->siswa->nama_siswa)
            ->assertSee('15');
    }

    public function test_logout_invalidates_the_authenticated_session(): void
    {
        $user = $this->createAccount('admin');

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('landing'));

        $this->assertGuest();
    }

    public function test_inactive_accounts_cannot_login_or_continue_an_existing_session(): void
    {
        $inactiveUser = User::factory()->create(['role' => 'admin', 'status_aktif' => false]);

        $this->post(route('login.post'), [
            'email' => $inactiveUser->email,
            'password' => 'password123',
        ])->assertSessionHasErrors('email');
        $this->assertGuest();

        $activeUser = User::factory()->create(['role' => 'admin']);
        $this->actingAs($activeUser);
        $activeUser->update(['status_aktif' => false]);

        $this->get(route('dashboard.admin'))->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_student_attendance_is_written_only_to_the_authenticated_profile(): void
    {
        $student = $this->createAccount('siswa');
        $otherStudent = $this->createAccount('siswa');

        $this->actingAs($student)
            ->post(route('presensi.store'), [
                'tipe' => 'datang',
                'latitude' => '-8.12345678',
                'longitude' => '115.12345678',
            ])
            ->assertOk()
            ->assertJsonPath('data.id_siswa', $student->siswa->id_siswa);

        $this->assertDatabaseHas('absensi', ['id_siswa' => $student->siswa->id_siswa]);
        $this->assertDatabaseMissing('absensi', ['id_siswa' => $otherStudent->siswa->id_siswa]);

        $this->post(route('scan.post'), ['code' => $otherStudent->siswa->no_siswa])
            ->assertForbidden();
        $this->assertDatabaseMissing('absensi', ['id_siswa' => $otherStudent->siswa->id_siswa]);

        $this->post(route('scan.post'), ['code' => $student->siswa->no_siswa])
            ->assertRedirect(route('dashboard.siswa'));
    }

    public function test_dashboard_and_student_pages_require_authentication(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
        $this->get(route('dashboard.admin'))->assertRedirect(route('login'));
        $this->get(route('profil'))->assertRedirect(route('login'));
    }

    private function createAccount(string $role): User
    {
        $user = User::factory()->create(['role' => $role]);

        if ($role === 'siswa') {
            $kelas = $this->createClass();

            Siswa::create([
                'id_user' => $user->id_user,
                'no_siswa' => fake()->unique()->numerify('#######'),
                'nama_siswa' => $user->nama,
                'id_kelas' => $kelas->id_kelas,
                'jenis_kelamin' => 'L',
            ]);
        } elseif (in_array($role, ['guru', 'guru_bk'], true)) {
            $guru = Guru::create([
                'id_user' => $user->id_user,
                'no_guru' => fake()->unique()->numerify('######'),
                'nama_guru' => $user->nama,
                'jenis_kelamin' => 'L',
            ]);

            if ($role === 'guru_bk') {
                GuruBk::create(['id_guru' => $guru->id_guru]);
            }
        }

        return $user->refresh();
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
}
