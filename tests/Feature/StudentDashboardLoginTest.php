<?php

namespace Tests\Feature;

use App\Models\Siswa;
use App\Models\User;
use Database\Seeders\SiswaSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentDashboardLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config()->set('kesiswaan.initial_student_password', 'Siswa@2026');
    }

    public function test_student_login_redirects_to_student_dashboard_and_displays_individual_profile(): void
    {
        $this->seed(UserSeeder::class);
        $this->seed(SiswaSeeder::class);

        // 1. Login sebagai Siswa A (Andhika Maraville Gazelle)
        $studentA = User::where('email', 'maraville@Kesiswaan.id')->firstOrFail();

        $responseA = $this->post(route('login.post'), [
            'email' => 'maraville@Kesiswaan.id',
            'password' => 'Siswa@2026',
        ]);

        $responseA->assertRedirect(route('dashboard.siswa'));
        $this->assertAuthenticatedAs($studentA);

        $dashboardA = $this->get(route('dashboard.siswa'));
        $dashboardA->assertOk();
        $dashboardA->assertSee('Andhika Maraville Gazelle');
        $dashboardA->assertDontSee('Antonius Alfa Danielo Lotu');

        // Logout Siswa A
        $this->post(route('logout'));
        $this->assertGuest();

        // 2. Login sebagai Siswa B (Antonius Alfa Danielo Lotu)
        $studentB = User::where('email', 'antonius@Kesiswaan.id')->firstOrFail();

        $responseB = $this->post(route('login.post'), [
            'email' => 'antonius@Kesiswaan.id',
            'password' => 'Siswa@2026',
        ]);

        $responseB->assertRedirect(route('dashboard.siswa'));
        $this->assertAuthenticatedAs($studentB);

        $dashboardB = $this->get(route('dashboard.siswa'));
        $dashboardB->assertOk();
        $dashboardB->assertSee('Antonius Alfa Danielo Lotu');
        $dashboardB->assertDontSee('Andhika Maraville Gazelle');
    }
}
