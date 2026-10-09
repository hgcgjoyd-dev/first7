<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Konfigurasi Geofence Absensi
    |--------------------------------------------------------------------------
    |
    | Koordinat pusat sekolah & radius (meter) untuk validasi GPS.
    | Bisa di-override via .env: ABSENSI_SCHOOL_LAT, ABSENSI_SCHOOL_LNG, ABSENSI_RADIUS_METER
    |
    */

    'school_lat' => env('ABSENSI_SCHOOL_LAT', -8.6264437),
    'school_lng' => env('ABSENSI_SCHOOL_LNG', 115.1816154),
    'radius_meter' => env('ABSENSI_RADIUS_METER', 50),

];
