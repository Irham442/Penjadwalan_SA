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
}
