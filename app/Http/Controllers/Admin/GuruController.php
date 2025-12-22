<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use Illuminate\Http\Request;

class GuruController extends Controller
{
    public function index()
    {
        $gurus = Guru::orderBy('nama', 'asc')->get();
        return view('admin.guru.index', compact('gurus'));
    }

    public function create()
    {
        return view('admin.guru.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nip' => 'nullable|numeric|unique:guru,nip', // Cek unik ke tabel 'guru'
            'jenis_kelamin' => 'required',
        ]);

        Guru::create($request->all());

        return redirect()->route('admin.guru.index')->with('success', 'Data Guru berhasil ditambahkan');
    }

    public function edit($id)
    {
        // Gunakan findOrFail karena id kustom tetap bisa dicari
        $guru = Guru::findOrFail($id);
        return view('admin.guru.edit', compact('guru'));
    }

    public function update(Request $request, $id)
    {
        $guru = Guru::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            // Pengecualian unik untuk id yang sedang diedit (gunakan id_guru)
            'nip' => 'nullable|numeric|unique:guru,nip,' . $guru->id_guru . ',id_guru', 
        ]);

        $guru->update($request->all());

        return redirect()->route('admin.guru.index')->with('success', 'Data Guru berhasil diupdate');
    }

    public function destroy($id)
    {
        $guru = Guru::findOrFail($id);
        $guru->delete();
        return redirect()->route('admin.guru.index')->with('success', 'Data Guru berhasil dihapus');
    }
}