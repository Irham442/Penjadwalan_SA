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
            // Kolom ini akan berisi berapa jam untuk satu sesi/blok (misal: 2 untuk blok 2 jam)
            $table->integer('jam_per_blok')->default(1)->after('jumlah_jam_seminggu');
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
