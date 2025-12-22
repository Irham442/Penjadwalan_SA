<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    use HasFactory;

    protected $table = 'guru'; 
    protected $primaryKey = 'id_guru';
    public $timestamps = false; // Sesuai permintaan Anda (database lama biasanya tidak ada created_at/updated_at)

    // Wajib ada agar fungsi Create & Update di Controller berjalan
    protected $fillable = [
        'user_id',
        'nama',
        'nip',
        'nuptk',
        'jabatan',
        'pangkat',
        'status_kepegawaian',
        'tanggal_masuk',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'agama',
        'phone',
        'alamat',
        'mapel_id'
    ];

    // ================= RELASI DATABASE =================

    // Relasi 1: Guru aktif di tahun ajaran mana saja
    public function tahunAjaran()
    {
        // Parameter: Model Tujuan, Nama Tabel Pivot, FK di Pivot (Guru), FK di Pivot (Tahun)
        return $this->belongsToMany(TahunAjaran::class, 'guru_aktif', 'guru_id', 'tahun_ajaran_id')
                    ->withTimestamps();
    }

    // Relasi 2: Mengambil data jam 'tidak bersedia' (Availability)
    public function availabilities()
    {
        return $this->hasMany(TeacherAvailability::class, 'guru_id', 'id_guru');
    }

    // Relasi 3 (Opsional): Ke Mata Pelajaran (karena ada kolom mapel_id)
    public function mapel()
    {
        return $this->belongsTo(Mapel::class, 'mapel_id', 'id_mapel');
    }
}