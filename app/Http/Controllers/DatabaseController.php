<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDO;
use Throwable;

class DatabaseController extends Controller
{
    /**
     * Memeriksa koneksi database, menampilkan tabel,
     * dan dapat membuat database otomatis jika belum ada.
     */
    public function check(Request $request)
    {
        $host = config('database.connections.mysql.host', '127.0.0.1');
        $port = config('database.connections.mysql.port', '3306');
        $user = config('database.connections.mysql.username', 'root');
        $pass = config('database.connections.mysql.password', '');
        $targetDb = config('database.connections.mysql.database', 'db_kesiswaan');

        try {
            $pdoServer = new PDO("mysql:host={$host};port={$port}", $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            $existingDatabases = $pdoServer->query('SHOW DATABASES')->fetchAll(PDO::FETCH_COLUMN);

            // Jika user klik buat otomatis atau meminta pembuatan
            if ($request->has('buat')) {
                $pdoServer->exec("CREATE DATABASE IF NOT EXISTS `{$targetDb}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                return view('cek-db', [
                    'status' => 'created',
                    'targetDb' => $targetDb,
                    'host' => $host,
                    'port' => $port,
                    'user' => $user,
                    'tables' => [],
                    'existingDatabases' => $pdoServer->query('SHOW DATABASES')->fetchAll(PDO::FETCH_COLUMN),
                    'errorMsg' => null,
                ]);
            }

            // Cek apakah database target sudah ada
            if (in_array($targetDb, $existingDatabases)) {
                DB::connection()->getPdo();
                $tables = DB::select('SHOW TABLES');
                return view('cek-db', [
                    'status' => 'success',
                    'targetDb' => $targetDb,
                    'host' => $host,
                    'port' => $port,
                    'user' => $user,
                    'tables' => $tables,
                    'existingDatabases' => $existingDatabases,
                    'errorMsg' => null,
                ]);
            }

            return view('cek-db', [
                'status' => 'not_found',
                'targetDb' => $targetDb,
                'host' => $host,
                'port' => $port,
                'user' => $user,
                'tables' => [],
                'existingDatabases' => $existingDatabases,
                'errorMsg' => "Database '{$targetDb}' belum terdaftar di MySQL Server.",
            ]);

        } catch (Throwable $e) {
            return view('cek-db', [
                'status' => 'error',
                'targetDb' => $targetDb,
                'host' => $host,
                'port' => $port,
                'user' => $user,
                'tables' => [],
                'existingDatabases' => [],
                'errorMsg' => $e->getMessage(),
            ]);
        }
    }
}

