<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ekstrakurikuler', function (Blueprint $table) {
            $table->id('id_ekskul');
            $table->string('nama_ekskul', 100);
            $table->string('pembina', 100)->nullable();
            $table->string('hari_kegiatan', 50)->nullable();
            $table->string('jam_kegiatan', 50)->nullable();
            $table->string('tempat', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ekstrakurikuler');
    }
};

