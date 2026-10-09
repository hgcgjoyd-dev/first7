<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->unique('id_user');
        });

        Schema::table('guru', function (Blueprint $table) {
            $table->unique('id_user');
        });
    }

    public function down(): void
    {
        Schema::table('guru', function (Blueprint $table) {
            $table->dropUnique(['id_user']);
        });

        Schema::table('siswa', function (Blueprint $table) {
            $table->dropUnique(['id_user']);
        });
    }
};
