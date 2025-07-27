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
        Schema::create('beban_ajar', function (Blueprint $table) {
            $table->id();
            $table->integer('id_guru');
            $table->integer('id_mapel');
            $table->integer('id_kelas');
            $table->integer('jumlah_jam_seminggu');
            // $table->integer('durasi_per_sesi')->default(1); // Opsional, bisa ditambahkan nanti

            // Definisikan foreign key ke tabel-tabel master
            // Pastikan nama kolom di references() sesuai dengan Primary Key di tabel tujuan
            $table->foreign('id_guru')->references('id_guru')->on('guru')->onDelete('cascade');
            $table->foreign('id_mapel')->references('id_mapel')->on('mata_pelajaran')->onDelete('cascade');
            $table->foreign('id_kelas')->references('id_kelas')->on('kelas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beban_ajar');
    }
};
