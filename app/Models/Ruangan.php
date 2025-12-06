<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ruangan extends Model
{
    use HasFactory;

    // ▼▼▼ [TAMBAHKAN 3 BARIS INI] ▼▼▼
    protected $table = 'ruangan'; // Tentukan nama tabel (jika singular)
    protected $primaryKey = 'id_ruangan'; // Beri tahu Laravel nama Primary Key Anda
    public $incrementing = false; // Set ke true jika auto-increment, false jika tidak

    // Tentukan kolom apa saja yang boleh diisi
    protected $fillable = [
        'nama_ruangan',
        'kapasitas',
        // tambahkan kolom lain jika ada
    ];

    /**
     * Relasi ke Kelas (jika ada)
     * Asumsi: tabel 'kelas' punya 'ruangan_id'
     */
    public function kelas()
    {
        return $this->hasOne(Kelas::class, 'ruangan_id', 'id_ruangan');
    }
}