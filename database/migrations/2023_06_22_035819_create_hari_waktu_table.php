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
        Schema::create('hari_waktu', function (Blueprint $table) {
            $table->id();
            $table->string('hari'); // eg: "Senin", "Selasa"
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hari_waktu');
    }
};
