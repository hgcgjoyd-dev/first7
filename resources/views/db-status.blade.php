<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Koneksi Database - Laravel</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            color: #334155;
            margin: 0;
            padding: 2rem;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
        }
        .card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -2px rgba(0,0,0,0.1);
            max-width: 600px;
            width: 100%;
            padding: 2rem;
            border: 1px solid #e2e8f0;
        }
        .badge {
            display: inline-block;
            padding: 0.35rem 0.75rem;
            border-radius: 9999px;
            font-weight: 600;
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }
        .badge-success {
            background-color: #dcfce7;
            color: #166534;
        }
        .badge-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }
        h1 {
            font-size: 1.5rem;
            margin-top: 0;
            margin-bottom: 0.5rem;
            color: #0f172a;
        }
        p {
            line-height: 1.5;
            margin-bottom: 1.5rem;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5rem;
        }
        .info-table td {
            padding: 0.6rem 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .info-table td:first-child {
            font-weight: 600;
            color: #64748b;
            width: 35%;
        }
        .tips {
            background-color: #f1f5f9;
            border-left: 4px solid #3b82f6;
            padding: 1rem;
            border-radius: 4px;
            font-size: 0.875rem;
        }
        .tips code {
            background: #e2e8f0;
            padding: 0.2rem 0.4rem;
            border-radius: 4px;
            font-family: monospace;
        }
        a.btn {
            display: inline-block;
            margin-top: 1rem;
            color: #3b82f6;
            text-decoration: none;
            font-weight: 500;
        }
        a.btn:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="card">
        @if($status)
            <span class="badge badge-success">✓ Terhubung</span>
            <h1>Koneksi Database Berhasil!</h1>
            <p style="color: #166534;">{{ $message }}</p>
        @else
            <span class="badge badge-danger">✗ Gagal Terhubung</span>
            <h1>Koneksi Database Gagal</h1>
            <p style="color: #991b1b;">{{ $message }}</p>
        @endif

        <table class="info-table">
            <tr>
                <td>Driver Database</td>
                <td><strong>{{ strtoupper($details['driver'] ?? '-') }}</strong></td>
            </tr>
            <tr>
                <td>Host</td>
                <td>{{ $details['host'] ?? '-' }}</td>
            </tr>
            <tr>
                <td>Port</td>
                <td>{{ $details['port'] ?? '-' }}</td>
            </tr>
            <tr>
                <td>Nama Database</td>
                <td><code>{{ $details['database'] ?? '-' }}</code></td>
            </tr>
            @if($status && !empty($details['tables']))
            <tr>
                <td>Tabel Ditemukan</td>
                <td>{{ count($details['tables']) }} tabel</td>
            </tr>
            @endif
        </table>

        @if(!$status)
            <div class="tips">
                <strong>Cara Mengatasi:</strong>
                <ol style="margin: 0.5rem 0 0 1rem; padding-left: 0.5rem;">
                    <li>Pastikan aplikasi database (seperti <strong>MySQL di XAMPP / Laragon</strong>) sudah dalam status <strong>Start / Running</strong>.</li>
                    <li>Pastikan database dengan nama <code>{{ $details['database'] }}</code> sudah dibuat di phpMyAdmin / MySQL.</li>
                    <li>Periksa pengaturan di file <code>.env</code> (DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD).</li>
                </ol>
            </div>
        @else
            <div class="tips" style="border-left-color: #10b981;">
                <strong>Semua Siap!</strong> Database Anda telah berhasil dikonfigurasi dan siap untuk query / migrasi data.
            </div>
        @endif

        <div>
            <a href="{{ url('/') }}" class="btn">&larr; Kembali ke Beranda</a>
        </div>
    </div>
</body>
</html>

