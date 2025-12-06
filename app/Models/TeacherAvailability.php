<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // <--- INI YANG KURANG (WAJIB ADA)
use Illuminate\Database\Eloquent\Model;

class TeacherAvailability extends Model
{
    use HasFactory; 

    // Sesuaikan nama tabel kalau Laravel salah tebak
    protected $table = 'teacher_availabilities'; 

    protected $fillable = [
        'guru_id', 
        'tahun_ajaran_id', 
        'day', 
        'start_time', 
        'end_time', 
        'reason'
    ];

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'guru_id', 'id_guru');
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }
}