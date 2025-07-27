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
        Schema::table('kelas', function (Blueprint $table) {
            // Kolom ini boleh null, jika ada kelas yang tidak punya ruangan tetap
            $table->integer('ruangan_id')->nullable()->after('nama_kelas');

            // Membuat hubungan foreign key ke tabel ruangan
            $table->foreign('ruangan_id')->references('id_ruangan')->on('ruangan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            //
        });
    }
};
