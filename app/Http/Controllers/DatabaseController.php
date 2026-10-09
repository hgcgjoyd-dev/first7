<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Throwable;

class DatabaseController extends Controller
{
    /**
     * Check the configured database connection without changing the schema.
     */
    public function check(): View
    {
        $connection = (string) config('database.default');
        $connectionConfig = (array) config("database.connections.{$connection}", []);
        $host = (string) ($connectionConfig['host'] ?? 'local');
        $port = (string) ($connectionConfig['port'] ?? 'n/a');
        $user = (string) ($connectionConfig['username'] ?? 'n/a');
        $targetDb = (string) ($connectionConfig['database'] ?? 'n/a');

        try {
            DB::connection()->getPdo();
            $tables = Schema::getTableListing();

            return view('cek-db', [
                'status' => 'success',
                'targetDb' => $targetDb,
                'host' => $host,
                'port' => $port,
                'user' => $user,
                'tables' => $tables,
                'errorMsg' => null,
            ]);
        } catch (Throwable $e) {
            return view('cek-db', [
                'status' => 'error',
                'targetDb' => $targetDb,
                'host' => $host,
                'port' => $port,
                'user' => $user,
                'tables' => [],
                'errorMsg' => $e->getMessage(),
            ]);
        }
    }
}
