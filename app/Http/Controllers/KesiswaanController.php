<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KesiswaanController extends Controller
{
    /**
     * Halaman Utama Gateway
     */
    public function landing()
    {
        return view('auth.landing');
    }

    /**
     * Halaman Login Manual Siswa
     */
    public function login()
    {
        return view('auth.login');
    }

    /**
     * Halaman Scan Kartu Pelajar
     */
    public function scan()
    {
        return view('auth.scan');
    }

    /**
     * Dashboard Siswa
     */
    public function dashboard()
    {
        return view('dashboard');
    }

    /**
     * Presensi Biometrik & Geolokasi
     */
    public function presensi()
    {
        return view('presensi.index');
    }

    /**
     * Formulir Pengajuan Izin / Sakit
     */
    public function izin()
    {
        return view('izin.index');
    }

    /**
     * Riwayat Kehadiran Siswa
     */
    public function riwayat()
    {
        return view('riwayat.index');
    }

    /**
     * Tugas Mata Pelajaran (Akademik)
     */
    public function mapel()
    {
        return view('mapel.index');
    }

    /**
     * Konseling & Buku BK
     */
    public function bk()
    {
        return view('bk.index');
    }

    /**
     * Jadwal Piket Kebersihan Kelas
     */
    public function piket()
    {
        return view('piket.index');
    }

    /**
     * Profil Siswa
     */
    public function profil()
    {
        return view('profil.index');
    }

    /**
     * Logout
     */
    public function logout()
    {
        return redirect()->route('landing');
    }
}

