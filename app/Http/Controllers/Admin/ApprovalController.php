<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\TahunAjaran; // Jangan lupa import ini
use App\Models\TeacherAvailability; // Jangan lupa import ini
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function dashboard()
    {
        // 1. Ambil Jadwal yang Menunggu Persetujuan
        $jadwalMenunggu = Jadwal::where('status', 'MENUNGGU_PERSETUJUAN')
                                ->orderBy('created_at', 'desc')
                                ->get();

        // 2. LOGIKA BARU: Ambil Rekap Ketersediaan Guru (Copy dari Dashboard Admin)
        $tahunAktif = TahunAjaran::where('is_active', 1)->first();
        
        $rekapKetersediaan = collect(); // Default collection kosong
        
        if($tahunAktif) {
            $rekapKetersediaan = TeacherAvailability::with('guru')
                ->where('tahun_ajaran_id', $tahunAktif->id)
                ->get()
                ->groupBy('guru_id');
        }

        // 3. Kirim SEMUA variabel ke View
        return view('approval.dashboard', compact('jadwalMenunggu', 'rekapKetersediaan'));
    }
    
    // ... (Function approve & reject biarkan sama) ...
    public function approve(Jadwal $jadwal)
    {
        $jadwal->status = 'DIPUBLIKASIKAN';
        $jadwal->id_penyetuju = auth()->id();
        $jadwal->tanggal_publikasi = now();
        $jadwal->save();

        return redirect()->route('approval.dashboard')->with('success', 'Jadwal #'.$jadwal->id.' telah disetujui dan dipublikasikan.');
    }

    public function reject(Request $request, Jadwal $jadwal)
    {
        $jadwal->status = 'REVISI';
        $jadwal->catatan_revisi = $request->input('catatan_revisi');
        $jadwal->save();
        
        return redirect()->route('approval.dashboard')->with('success', 'Jadwal #'.$jadwal->id.' telah dikembalikan untuk direvisi.');
    }
}
