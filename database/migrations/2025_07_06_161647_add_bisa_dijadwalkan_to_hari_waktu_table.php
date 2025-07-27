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
            $table->boolean('bisa_dijadwalkan')->default(true)->after('jam_selesai');
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
