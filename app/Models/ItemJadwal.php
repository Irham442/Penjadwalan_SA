<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemJadwal extends Model
{
    use HasFactory;
    protected $table = 'item_jadwal';
    protected $fillable = ['id_jadwal', 'id_guru', 'id_mata_pelajaran', 'id_kelas', 'id_ruangan', 'id_hari_waktu'];

    // Definisi relasi
    public function guru() { return $this->belongsTo(Guru::class, 'id_guru', 'id_guru'); }
    public function kelas() { return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas'); }
    public function mapel() { return $this->belongsTo(MataPelajaran::class, 'id_mata_pelajaran', 'id_mapel'); }
    public function ruangan() { return $this->belongsTo(Ruangan::class, 'id_ruangan', 'id_ruangan'); }
    public function waktu() { return $this->belongsTo(HariWaktu::class, 'id_hari_waktu', 'id'); }
    public function jadwal() { return $this->belongsTo(Jadwal::class, 'id_jadwal', 'id'); }
}