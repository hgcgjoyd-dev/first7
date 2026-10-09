<?php

namespace Database\Seeders;

use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class UserSeeder extends Seeder
{
    /**
     * @var array<string, string>
     */
    public const STUDENTS = [
        'maraville' => 'Andhika Maraville Gazelle',
        'antonius' => 'Antonius Alfa Danielo Lotu',
        'arviogalvin' => 'Arviogalvin Evaldo Simanjuntak',
        'bintang' => 'Bintang Ramadani',
        'excel' => 'Excel Febriano',
        'utama' => 'Gede Satria Abda Utama',
        'given' => 'Given Aidenly Pilatus Kandores',
        'bayusena' => 'I Dewa Made Adyaksa Bayusena',
        'aditiya' => 'I Gede Agus Aditiya',
        'dwipa' => 'I Gede Made Dwipa Chandra Sedana',
        'mahesh' => 'I Gede Mahesh Weda Prayata',
        'prama' => 'I Gede Pande Prama Daniswara',
        'pradnyani' => 'I Gusti Ayu Intan Pradnyani',
        'wirawan' => 'I Kadek Abhyasastra Wirawan',
        'dwika' => 'I Kadek Dwika Pradnyana',
        'satya' => 'I Kadek Satya Semara Putra',
        'pradnya' => 'I Komang Dito Pradnya',
        'hendra' => 'I Komang Hendra Ardi Pratama',
        'pinandhita' => 'I Made Arie Pinandhita',
        'yasa' => 'I Made Dwi Dharma Yasa',
        'suranadi' => 'I Made Suranadi',
        'pandu' => 'I Nyoman Pandu Kusuma Wijaya',
        'widiardana' => 'I Putu Gede Widiardana',
        'paramartha' => 'I Putu Krishna Paramartha Putra',
        'bagus' => 'I Wayan Bagus Aditya Prawira',
        'altissimo' => 'Ida Bagus Altissimo Nareswara',
        'jefferson' => 'Jefferson Edbert Wiranata',
        'radithya' => 'Kadek Radithya Danadyaksa',
        'ramaadptr' => 'Kadek Rama Adiputra',
        'melkior' => 'Melkior Majesta Kapu',
        'akarim' => 'Muhamad Kevin Akarim',
        'diahpurnama' => 'Ni Kadek Diah Purnama Dewi',
        'kris' => 'Putu Mahesa Kris Mulyana',
        'revan' => 'Revan Zaelani',
        'ibni' => 'Tristan Ibni Pratama',
        'zala' => 'Zala Ilal Akbar',
    ];

    public function run(): void
    {
        $initialPassword = config('kesiswaan.initial_student_password');

        if (! is_string($initialPassword) || $initialPassword === '') {
            throw new RuntimeException('Set KESISWAAN_INITIAL_STUDENT_PASSWORD in .env before seeding student accounts.');
        }

        foreach (['username', 'nama', 'email', 'password', 'role'] as $column) {
            if (! Schema::hasColumn('user', $column)) {
                throw new RuntimeException("The user.{$column} column is required before seeding student accounts.");
            }
        }

        $canLinkProfiles = Schema::hasTable('siswa')
            && Schema::hasColumn('siswa', 'id_user')
            && Schema::hasColumn('siswa', 'nama_siswa');
        $hasAccountStatus = Schema::hasColumn('user', 'status_aktif');

        DB::transaction(function () use ($initialPassword, $canLinkProfiles, $hasAccountStatus): void {
            $profiles = $canLinkProfiles ? Siswa::query()
                ->with('user')
                ->whereIn('nama_siswa', array_values(self::STUDENTS))
                ->get()
                ->groupBy('nama_siswa') : collect();

            foreach (self::STUDENTS as $username => $name) {
                $email = "{$username}@Kesiswaan.id";
                $matchingProfiles = $profiles->get($name, collect());

                if ($matchingProfiles->count() > 1) {
                    throw new RuntimeException("Multiple siswa profiles exist for {$name}; no account changes were made.");
                }

                $student = $matchingProfiles->first();
                $account = User::query()->where('email', $email)->first();

                if ($student?->id_user !== null
                    && (! $account || (int) $student->id_user !== (int) $account->id_user)) {
                    throw new RuntimeException("The siswa profile for {$name} is already linked to a different account.");
                }

                if ($account) {
                    if ($account->username !== $username
                        || $account->nama !== $name
                        || $account->role !== 'siswa'
                        || ! $account->isActive()
                        || ($student?->id_user !== null && (int) $student->id_user !== (int) $account->id_user)) {
                        throw new RuntimeException("The existing account {$email} does not match the expected active student account.");
                    }

                    continue;
                }

                if (User::query()->where('username', $username)->exists()) {
                    throw new RuntimeException("The username {$username} is already used by a different account.");
                }

                if ($student?->id_user !== null) {
                    throw new RuntimeException("The siswa profile for {$name} is linked to an account with a different email.");
                }
            }

            foreach (self::STUDENTS as $username => $name) {
                $email = "{$username}@Kesiswaan.id";
                $student = $profiles->get($name, collect())->first();
                $attributes = [
                    'username' => $username,
                    'nama' => $name,
                    'password' => Hash::make($initialPassword),
                    'role' => 'siswa',
                ];

                if ($hasAccountStatus) {
                    $attributes['status_aktif'] = true;
                }

                $account = User::query()->firstOrCreate(
                    ['email' => $email],
                    $attributes,
                );

                if ($student && $student->id_user === null) {
                    $student->user()->associate($account);
                    $student->save();
                }
            }
        });
    }
}
