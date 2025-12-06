<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ruangan; // Impor model Ruangan
use App\Models\Kelas;
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

    public function edit(Ruangan $ruangan) // Menggunakan Route Model Binding
    {
        // $ruangan otomatis ditemukan oleh Laravel berdasarkan ID di URL
        return view('admin.ruangan.edit', compact('ruangan'));
        // ^ Ini adalah file view yang saya berikan di respons sebelumnya
    }

    public function update(Request $request, Ruangan $ruangan)
    {
        // Setelah Model diperbaiki, $ruangan->id_ruangan sekarang akan berisi nilai
        $request->validate([
            // [PERBAIKAN VALIDASI]
            // unique:<table>,<column>,<id_to_ignore>,<id_column_name>
            'nama_ruangan' => 'required|string|max:255|unique:ruangan,nama_ruangan,' . $ruangan->id_ruangan . ',id_ruangan',
            'kapasitas' => 'required|integer|min:1',
        ]);

        $ruangan->update($request->all());

        return redirect()->route('admin.ruangan.index')
                         ->with('success', 'Ruangan berhasil diperbarui.');
    }

    public function destroy(Ruangan $ruangan)
    {
        // --- PENGECEKAN KEAMANAN (PENTING!) ---
        // Kita tidak boleh menghapus ruangan jika ruangan itu 
        // masih terdaftar sebagai 'home room' sebuah Kelas.
        
        // Asumsi: tabel 'kelas' punya kolom 'ruangan_id'
        $isUsed = Kelas::where('ruangan_id', $ruangan->id)->exists();

        if ($isUsed) {
            // JANGAN HAPUS! Kembalikan dengan pesan error.
            return redirect()->route('admin.ruangan.index')
                             ->with('error', 'Gagal menghapus! Ruangan "' . $ruangan->nama_ruangan . '" masih digunakan oleh data Kelas.');
        }

        // Jika aman (tidak terpakai), baru hapus
        $ruangan->delete();

        // Redirect kembali ke halaman index
        return redirect()->route('admin.ruangan.index')
                         ->with('success', 'Ruangan berhasil dihapus.');
    }
    
    // ... method lainnya akan kita isi nanti ...
}