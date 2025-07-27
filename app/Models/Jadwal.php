<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    use HasFactory;
    protected $table = 'jadwal';
    protected $fillable = ['tahun_ajaran', 'semester', 'versi', 'status', 'id_admin_pembuat', 'id_penyetuju', 'tanggal_publikasi', 'catatan_revisi'];

    // TAMBAHKAN INI: Satu Jadwal memiliki banyak ItemJadwal
    public function items()
    {
        return $this->hasMany(ItemJadwal::class, 'id_jadwal', 'id');
    }
}