<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TeacherAvailability;
use App\Models\TahunAjaran; 
use App\Models\Guru;        
use Illuminate\Support\Facades\Auth;

class TeacherAvailabilityController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // 1. Cari data guru
        $guru = Guru::where('user_id', $user->id)->first();

        if(!$guru) {
            return redirect()->route('teacher.dashboard')->with('error', 'Data profil Guru belum ditemukan.');
        }

        // 2. Ambil Tahun Ajaran Aktif
        $tahunAktif = TahunAjaran::where('is_active', 1)->first();

        // 3. Ambil data ketersediaan (INI VARIABLE YANG HILANG TADI)
        // Kita inisialisasi query dulu
        $query = TeacherAvailability::where('guru_id', $guru->id_guru);
        
        // Filter berdasarkan tahun aktif jika ada
        if($tahunAktif) {
            $query->where('tahun_ajaran_id', $tahunAktif->id);
        }
        
        // Eksekusi query (get)
        $availabilities = $query->get();

        // 4. Kirim variabel ke View menggunakan compact
        return view('teacher.index', compact('guru', 'availabilities', 'tahunAktif'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'day' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'reason' => 'nullable|string'
        ]);

        $user = Auth::user();
        $guru = Guru::where('user_id', $user->id)->first();
        $tahunAktif = TahunAjaran::where('is_active', 1)->first();

        if(!$tahunAktif) return back()->with('error', 'Tidak ada tahun ajaran aktif.');

        TeacherAvailability::create([
            'guru_id' => $guru->id_guru,
            'tahun_ajaran_id' => $tahunAktif->id,
            'day' => $request->day,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'reason' => $request->reason
        ]);

        return back()->with('success', 'Jam berhalangan berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        $availability = TeacherAvailability::findOrFail($id);
        $availability->delete();
        return back()->with('success', 'Data berhasil dihapus.');
    }
}