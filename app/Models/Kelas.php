<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';
    protected $primaryKey = 'id_kelas';
    public $timestamps = false; // Non-aktifkan timestamps sesuai database

    // Fillable wajib ada agar Controller bisa melakukan Create/Update
    protected $fillable = [
        'nama_kelas',
        'ruangan_id',
        'tingkat',
        'jurusan_id',
        'wali_kelas', // Menyimpan ID guru
        'kapasitas',
        'lokasi_ruangan'
    ];

    // --- Definisi Relasi (Opsional tapi disarankan) ---

    // Relasi ke Model Guru (Wali Kelas)
    public function waliKelas()
    {
        return $this->belongsTo(Guru::class, 'wali_kelas', 'id_guru');
    }
    
    // Relasi ke Model Ruangan (jika ada)
    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id', 'id'); // Sesuaikan 'id' dengan PK tabel ruangan
    }
}