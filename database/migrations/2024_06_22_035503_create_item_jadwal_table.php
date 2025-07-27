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
    Schema::create('item_jadwal', function (Blueprint $table) {
        $table->id();
        $table->timestamps();

        // --- DEFINISI KOLOM FOREIGN KEY ---

        // Kolom yang terhubung ke tabel buatan Laravel
        $table->unsignedBigInteger('id_jadwal');
        $table->unsignedBigInteger('id_hari_waktu');

        // Kolom yang terhubung ke tabel impor dari sekolah
        // Tipenya harus 'integer' agar cocok
        $table->integer('id_guru');
        $table->integer('id_mata_pelajaran');
        $table->integer('id_kelas');
        $table->integer('id_ruangan'); // <-- DIUBAH MENJADI INTEGER

        // --- DEFINISI HUBUNGAN FOREIGN KEY ---
        
        // Hubungan ke tabel buatan Laravel (references 'id')
        $table->foreign('id_jadwal')->references('id')->on('jadwal')->onDelete('cascade');
        $table->foreign('id_hari_waktu')->references('id')->on('hari_waktu')->onDelete('cascade');

        // Hubungan ke tabel impor (references nama PK spesifik)
        $table->foreign('id_guru')->references('id_guru')->on('guru');
        $table->foreign('id_mata_pelajaran')->references('id_mapel')->on('mata_pelajaran');
        $table->foreign('id_kelas')->references('id_kelas')->on('kelas');
        $table->foreign('id_ruangan')->references('id_ruangan')->on('ruangan'); // <-- Sekarang ini sudah benar
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_jadwal');
    }
};