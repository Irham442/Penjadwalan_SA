<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MataPelajaran; // Pastikan ini sesuai nama file Model Anda
use Illuminate\Http\Request;

class MapelController extends Controller
{
    public function index()
    {
        // Ubah Mapel:: menjadi MataPelajaran::
        $mapels = MataPelajaran::orderBy('nama_mapel')->get();
        return view('admin.mapel.index', compact('mapels'));
    }

    public function create()
    {
        return view('admin.mapel.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            // Ubah 'unique:mapel' menjadi 'unique:mata_pelajaran' sesuai $table di Model
            'kode_mapel' => 'required|unique:mata_pelajaran,kode_mapel', 
            'nama_mapel' => 'required',
        ]);

        // Ubah Mapel:: menjadi MataPelajaran::
        MataPelajaran::create($request->all());

        return redirect()->route('admin.mapel.index')->with('success', 'Mapel berhasil ditambahkan');
    }

    public function edit($id)
    {
        // Ubah Mapel:: menjadi MataPelajaran::
        $mapel = MataPelajaran::findOrFail($id);
        return view('admin.mapel.edit', compact('mapel'));
    }

    public function update(Request $request, $id)
    {
        // Ubah Mapel:: menjadi MataPelajaran::
        $mapel = MataPelajaran::findOrFail($id);

        $request->validate([
            // Validasi unique ignore id_mapel pada tabel mata_pelajaran
            'kode_mapel' => 'required|unique:mata_pelajaran,kode_mapel,' . $mapel->id_mapel . ',id_mapel',
            'nama_mapel' => 'required',
        ]);

        $mapel->update($request->all());

        return redirect()->route('admin.mapel.index')->with('success', 'Mapel berhasil diupdate');
    }

    public function destroy($id)
    {
        // Ubah Mapel:: menjadi MataPelajaran::
        MataPelajaran::findOrFail($id)->delete();
        return redirect()->route('admin.mapel.index')->with('success', 'Mapel berhasil dihapus');
    }
}