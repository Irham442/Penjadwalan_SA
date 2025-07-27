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
        // di dalam method up()
        Schema::create('jadwal', function (Blueprint $table) {
            $table->id();
            $table->string('tahun_ajaran');
            $table->string('semester');
            $table->integer('versi')->default(1);
            $table->enum('status', [
                'DRAFT',
                'MENUNGGU_PERSETUJUAN',
                'REVISI',
                'DISETUJUI',
                'DIPUBLIKASIKAN'
            ])->default('DRAFT');
            $table->text('catatan_revisi')->nullable();

            // INI WAJIB ADA untuk melacak siapa pembuatnya
            $table->foreignId('id_admin_pembuat')->constrained('users');

            // INI WAJIB ADA untuk melacak siapa penyetujunya
            $table->foreignId('id_penyetuju')->nullable()->constrained('users');

            $table->timestamp('tanggal_publikasi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal');
    }
};