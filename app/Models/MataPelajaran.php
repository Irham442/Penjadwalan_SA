<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MataPelajaran extends Model
{
    use HasFactory;

    // Sesuaikan dengan konfigurasi existing Anda
    protected $table = 'mata_pelajaran'; 
    protected $primaryKey = 'id_mapel';
    public $timestamps = false; // Non-aktifkan timestamps

    // Tetap perlukan fillable untuk fungsi Create/Update di Controller
    protected $fillable = [
        'kode_mapel',
        'nama_mapel',
        'kategori',
        'kelompok_id',
        'jurusan_id'
    ];
}