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
            Schema::create('guru_aktif', function (Blueprint $table) {
                $table->id();

                // 1. KONEK KE TAHUN AJARAN
                // Karena di screenshot 'tahun_ajaran' id-nya BigInt Unsigned, kita pakai ini:
                $table->unsignedBigInteger('tahun_ajaran_id');
                $table->foreign('tahun_ajaran_id')->references('id')->on('tahun_ajaran')->onDelete('cascade');

                // 2. KONEK KE GURU
                // Karena di screenshot 'guru' id-nya int(11) dan namanya 'id_guru', kita pakai Integer biasa:
                $table->integer('guru_id'); // Pakai integer biasa (Signed) biar cocok sama int(11)
                
                // PENTING: Kita harus kasih tau Laravel kalau nama kolom kuncinya 'id_guru', bukan 'id'
                $table->foreign('guru_id')->references('id_guru')->on('guru')->onDelete('cascade');

                $table->timestamps();
            });
        }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_year_teacher');
    }
};
