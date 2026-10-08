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
        Schema::create('jenis_pelanggaran', function (Blueprint $table) {
            $table->id('id_pelanggaran');
            $table->string('nama_pelanggaran', 150);
            $table->enum('kategori', ['Ringan', 'Sedang', 'Berat'])->default('Ringan');
            $table->integer('poin')->default(5);
            $table->text('sanksi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenis_pelanggaran');
    }
};

