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
        Schema::table('beban_ajar', function (Blueprint $table) {
            // Kolom untuk mengunci jadwal. Boleh NULL jika tidak dikunci.
            $table->unsignedBigInteger('id_hari_waktu')->nullable()->after('jam_per_blok');

            // Membuat hubungan foreign key ke tabel hari_waktu
            $table->foreign('id_hari_waktu')->references('id')->on('hari_waktu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beban_ajar', function (Blueprint $table) {
            //
        });
    }
};
