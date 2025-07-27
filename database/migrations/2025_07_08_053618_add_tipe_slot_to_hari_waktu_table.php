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
        Schema::table('hari_waktu', function (Blueprint $table) {
            // KBM = Kegiatan Belajar Mengajar
            $table->enum('tipe_slot', ['KBM', 'Istirahat'])->default('KBM')->after('jam_selesai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hari_waktu', function (Blueprint $table) {
            //
        });
    }
};
