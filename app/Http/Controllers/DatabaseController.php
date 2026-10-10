<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DatabaseController extends Controller
{
    /**
     * Memeriksa status koneksi ke database.
     */
    public function check(Request $request)
    {
        return $this->checkConnection($request);
    }

    public function checkConnection(Request $request)
    {
        $status = false;
        $message = '';
        $defaultDriver = config('database.default');
        $details = [
            'driver' => $defaultDriver,
            'database' => config("database.connections.{$defaultDriver}.database"),
            'host' => config("database.connections.{$defaultDriver}.host", 'localhost'),
            'port' => config("database.connections.{$defaultDriver}.port", '3306'),
            'tables' => [],
        ];

        try {
            // Cek koneksi menggunakan PDO
            DB::connection()->getPdo();
            $status = true;
            $message = 'Berhasil terhubung ke database server!';

            // Coba ambil daftar tabel jika driver mysql atau sqlite
            if ($defaultDriver === 'mysql') {
                $tables = DB::select('SHOW TABLES');
                $dbName = $details['database'];
                $columnName = 'Tables_in_'.$dbName;
                foreach ($tables as $t) {
                    $details['tables'][] = $t->$columnName ?? (string) array_values((array) $t)[0];
                }
            } elseif ($defaultDriver === 'sqlite') {
                $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
                foreach ($tables as $t) {
                    $details['tables'][] = $t->name;
                }
            }
        } catch (Exception $e) {
            $status = false;
            $message = $e->getMessage();
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => $status,
                'message' => $message,
                'data' => $details,
            ], $status ? 200 : 500);
        }

        return view('db-status', compact('status', 'message', 'details'));
    }

    /**
     * Contoh mengambil data dari tabel users.
     */
    public function getUsers()
    {
        try {
            $users = User::all();

            return response()->json([
                'success' => true,
                'total' => $users->count(),
                'data' => $users,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data: '.$e->getMessage(),
            ], 500);
        }
    }
}
