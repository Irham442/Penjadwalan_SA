<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    use HasFactory;
    protected $table = 'guru'; // Sesuaikan jika nama tabel beda
    protected $primaryKey = 'id_guru'; // Beritahu primary key-nya
    public $timestamps = false; // Beritahu agar tidak mencari kolom timestamps

    // Relasi 1: Untuk tahu guru ini aktif di tahun ajaran mana saja
public function tahunAjaran()
{
    // Parameter: Model Tujuan, Nama Tabel Pivot, FK di Pivot (Guru), FK di Pivot (Tahun)
    return $this->belongsToMany(TahunAjaran::class, 'guru_aktif', 'guru_id', 'tahun_ajaran_id')
                ->withTimestamps();
}

// Relasi 2: Untuk mengambil data jam 'tidak bersedia' guru ini
public function availabilities()
{
    return $this->hasMany(TeacherAvailability::class, 'guru_id', 'id_guru');
}
}
