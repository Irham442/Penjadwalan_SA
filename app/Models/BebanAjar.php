<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BebanAjar extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model ini.
     */
    protected $table = 'beban_ajar';

    /**
     * Memberitahu Laravel bahwa tabel ini tidak memiliki kolom created_at & updated_at.
     */
    public $timestamps = false;

    /**
     * Kolom-kolom yang boleh diisi secara massal (untuk method create()).
     */
    protected $fillable = [
        'id_guru',
        'id_mapel',
        'id_kelas',
        'jumlah_jam_seminggu',
        'jam_per_blok',
        'id_hari_waktu', // <-- Tambahkan ini
    ];

    /**
     * Mendefinisikan relasi "belongsTo" ke model Guru.
     */
    public function guru()
    {
        return $this->belongsTo(Guru::class, 'id_guru', 'id_guru');
    }

    /**
     * Mendefinisikan relasi "belongsTo" ke model MataPelajaran.
     */
    public function mapel()
    {
        return $this->belongsTo(MataPelajaran::class, 'id_mapel', 'id_mapel');
    }

    /**
     * Mendefinisikan relasi "belongsTo" ke model Kelas.
     */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }
    /**
 * Mendefinisikan relasi "belongsTo" ke model HariWaktu.
 */
    public function waktu()
    {
        return $this->belongsTo(HariWaktu::class, 'id_hari_waktu', 'id');
    }
}