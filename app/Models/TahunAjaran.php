<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    use HasFactory;

    protected $table = 'tahun_ajaran'; // Nama tabel di database
    
    // Agar kolom 'id' tidak perlu diisi manual (Auto Increment)
    protected $guarded = ['id']; 

    // Relasi ke Guru (Kebalikan dari yang ada di Model Guru)
    public function gurus()
    {
        // Parameter: Model, Tabel Pivot, FK Tahun di Pivot, FK Guru di Pivot
        return $this->belongsToMany(Guru::class, 'guru_aktif', 'tahun_ajaran_id', 'guru_id')
                    ->withTimestamps();
    }
}