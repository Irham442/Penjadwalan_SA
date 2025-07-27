<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BebanAjar;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;

class BebanAjarController extends Controller
{
    public function index()
    {
        $bebanAjars = BebanAjar::with(['guru', 'mapel', 'kelas'])->orderBy('id_kelas')->get();
        return view('admin.beban_ajar.index', compact('bebanAjars'));
    }

    public function create()
    {
        $gurus = Guru::orderBy('nama')->get();
        $mapels = MataPelajaran::orderBy('nama_mapel')->get();
        $kelases = Kelas::orderBy('nama_kelas')->get();
        $waktus = \App\Models\HariWaktu::all();
        return view('admin.beban_ajar.create', compact('gurus', 'mapels', 'kelases', 'waktus'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'id_guru' => 'required|exists:guru,id_guru',
            'id_mapel' => 'required|exists:mata_pelajaran,id_mapel',
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'jumlah_jam_seminggu' => 'required|integer|min:1',
            'jam_per_blok' => 'required|integer|min:1',
            'id_hari_waktu' => 'nullable|exists:hari_waktu,id',
        ]);
        BebanAjar::create($validatedData);
        return redirect()->route('admin.beban-ajar.index')->with('success', 'Beban ajar berhasil ditambahkan.');
    }

    // --- METHOD BARU YANG DILENGKAPI ---

    public function edit(BebanAjar $bebanAjar)
    {
        $gurus = Guru::orderBy('nama')->get();
        $mapels = MataPelajaran::orderBy('nama_mapel')->get();
        $kelases = Kelas::orderBy('nama_kelas')->get();
        $waktus = \App\Models\HariWaktu::all();
        return view('admin.beban_ajar.edit', compact('bebanAjar', 'gurus', 'mapels', 'kelases', 'waktus'));
    }

    public function update(Request $request, BebanAjar $bebanAjar)
    {
        $validatedData = $request->validate([
            'id_guru' => 'required|exists:guru,id_guru',
            'id_mapel' => 'required|exists:mata_pelajaran,id_mapel',
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'jumlah_jam_seminggu' => 'required|integer|min:1',
            'jam_per_blok' => 'required|integer|min:1',
            'id_hari_waktu' => 'nullable|exists:hari_waktu,id',
        ]);
        $bebanAjar->update($validatedData);
        return redirect()->route('admin.beban-ajar.index')->with('success', 'Beban ajar berhasil diperbarui.');
    }

    public function destroy(BebanAjar $bebanAjar)
    {
        $bebanAjar->delete();
        return redirect()->route('admin.beban-ajar.index')->with('success', 'Beban ajar berhasil dihapus.');
    }
}