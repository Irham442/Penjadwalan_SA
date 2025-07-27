<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ruangan; // Impor model Ruangan
use Illuminate\Http\Request;

class RuanganController extends Controller
{
    /**
     * Menampilkan daftar semua ruangan.
     */
    public function index()
    {
        $semuaRuangan = Ruangan::orderBy('nama_ruangan')->get();
        return view('admin.ruangan.index', ['semuaRuangan' => $semuaRuangan]);
    }

    /**
     * Menampilkan form untuk membuat ruangan baru.
     */
    public function create()
    {
        return view('admin.ruangan.create');
    }

    public function store(Request $request)
    {
        // 1. Validasi input dari form
        $request->validate([
            'nama_ruangan' => 'required|string|max:255',
            'kapasitas' => 'required|integer|min:1',
        ]);

        // 2. Jika validasi berhasil, buat record baru di database
        Ruangan::create([
            'nama_ruangan' => $request->nama_ruangan,
            'kapasitas' => $request->kapasitas,
        ]);

        // 3. Arahkan kembali ke halaman daftar dengan pesan sukses
        return redirect()->route('admin.ruangan.index')->with('success', 'Ruangan baru berhasil ditambahkan.');
    }
    
    // ... method lainnya akan kita isi nanti ...
}