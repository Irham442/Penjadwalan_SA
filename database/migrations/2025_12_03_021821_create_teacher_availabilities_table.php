<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('teacher_availabilities', function (Blueprint $table) {
            $table->id();

            // 1. KONEK KE GURU (Tipe Integer, Ref ke id_guru)
            $table->integer('guru_id'); 
            $table->foreign('guru_id')->references('id_guru')->on('guru')->onDelete('cascade');

            // 2. KONEK KE TAHUN AJARAN (Tipe BigInteger, Ref ke id)
            $table->unsignedBigInteger('tahun_ajaran_id');
            $table->foreign('tahun_ajaran_id')->references('id')->on('tahun_ajaran')->onDelete('cascade');

            // Data Ketersediaan
            $table->string('day'); // Contoh: "Senin"
            $table->time('start_time'); // Jam mulai tidak bisa
            $table->time('end_time');   // Jam selesai tidak bisa
            $table->text('reason')->nullable(); // Alasan (misal: "Rapat MGMP")
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_availabilities');
    }
};
