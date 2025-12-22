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
    // UBAH 'beban_ajars' MENJADI 'beban_ajar'
    Schema::table('beban_ajar', function (Blueprint $table) {
        
        $table->unsignedBigInteger('id_tahun_ajaran')->nullable()->after('id');
        
        // PENTING: Cek nama tabel tahun ajaran kamu.
        // Jika tabelnya 'tahun_ajaran' (tanpa s), pakai 'tahun_ajaran'.
        // Jika tabelnya 'tahun_ajarans' (pakai s), pakai 'tahun_ajarans'.
        // Mengikuti pola tabel 'beban_ajar' kamu, kemungkinan besar tabelnya 'tahun_ajaran'.
        
        $table->foreign('id_tahun_ajaran')
                ->references('id') // Asumsi primary key di tahun_ajaran adalah 'id'
                ->on('tahun_ajaran') // Sesuaikan nama tabel ini
                ->onDelete('cascade');
    });
}

    public function down()
    {
        Schema::table('beban_ajars', function (Blueprint $table) {
            $table->dropForeign(['id_tahun_ajaran']);
            $table->dropColumn('id_tahun_ajaran');
        });
    }
};
