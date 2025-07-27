<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HariWaktu extends Model
{
    use HasFactory;

    // Beritahu Laravel nama tabel yang benar
    protected $table = 'hari_waktu';
}