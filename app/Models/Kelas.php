<?php

namespace App\Models;

// ANDA BENAR, HARUS ADA 'use' DARI ILLUMINATE DI SINI
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    // Baris ini sekarang menjadi valid karena alamat 'HasFactory' sudah didefinisikan di atas
    use HasFactory;

    protected $table = 'kelas';
    protected $primaryKey = 'id_kelas';
    public $timestamps = false;
}