<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ruangan extends Model
{
    use HasFactory;
    protected $table = 'ruangan';
    protected $primaryKey = 'id_ruangan'; // Pastikan ini ada
    protected $fillable = ['nama_ruangan', 'kapasitas']; // <-- Tambahkan ini
}