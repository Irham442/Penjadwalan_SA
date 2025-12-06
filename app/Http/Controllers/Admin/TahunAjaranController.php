<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; // <-- Penting karena beda folder
use App\Models\TahunAjaran;          // <-- INI YANG BIKIN ERROR (Wajib Ada)
use App\Models\Guru;                 // <-- Ini juga perlu buat fitur guru
use Illuminate\Http\Request;

class TahunAjaranController extends Controller
{
    // 1. Tampilkan Daftar Tahun Ajaran
    public function index()
    {
        $tahunAjarans = TahunAjaran::orderBy('created_at', 'desc')->get();
        // Pastikan view-nya ada di resources/views/tahun_ajaran/index.blade.php
        // Atau jika folder view juga dipindah ke admin, sesuaikan jadi 'admin.tahun_ajaran.index'
        return view('admin.tahun_ajaran.index', compact('tahunAjarans'));
    }

    // 2. Simpan Tahun Ajaran Baru
    public function store(Request $request)
    {
        $request->validate([
            'tahun' => 'required',
            'semester' => 'required',
        ]);

        TahunAjaran::create([
            'tahun' => $request->tahun,
            'semester' => $request->semester,
            'is_active' => false 
        ]);

        return redirect()->back()->with('success', 'Tahun Ajaran berhasil ditambahkan');
    }

    // 3. Aktifkan Satu Tahun
    public function activate($id)
    {
        TahunAjaran::query()->update(['is_active' => 0]);

        $tahun = TahunAjaran::findOrFail($id);
        $tahun->update(['is_active' => 1]);

        return redirect()->back()->with('success', 'Tahun Ajaran aktif berhasil diubah!');
    }

    // 4. Tampilkan Form Pilih Guru
    public function manageTeachers($id)
    {
        $tahun = TahunAjaran::findOrFail($id);
        $gurus = Guru::orderBy('nama', 'asc')->get();
        $activeGuruIds = $tahun->gurus->pluck('id_guru')->toArray();

        return view('admin.tahun_ajaran.manage_teachers', compact('tahun', 'gurus', 'activeGuruIds'));
    }

    // 5. Simpan Pilihan Guru (Sync)
    public function updateTeachers(Request $request, $id)
    {
        $tahun = TahunAjaran::findOrFail($id);
        
        $request->validate([
            'guru_ids' => 'array'
        ]);

        $tahun->gurus()->sync($request->guru_ids ?? []);

        // Redirect ke route index (pastikan nama routenya benar)
        return redirect()->route('admin.tahun_ajaran.index')->with('success', 'Data Guru Aktif berhasil diperbarui!');
    }

    // 6. Hapus Tahun Ajaran
    public function destroy($id)
    {
        $tahun = TahunAjaran::findOrFail($id);
        $tahun->delete();
        return redirect()->back()->with('success', 'Tahun Ajaran dihapus');
    }
}