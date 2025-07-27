<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function dashboard()
    {
        // Ambil semua data jadwal dari database yang statusnya 'MENUNGGU_PERSETUJUAN'
        $jadwalMenunggu = \App\Models\Jadwal::where('status', 'MENUNGGU_PERSETUJUAN')
                                            ->orderBy('created_at', 'desc')
                                            ->get();

        // Kirim data tersebut ke sebuah view baru yang akan kita buat
        return view('approval.dashboard', ['jadwalMenunggu' => $jadwalMenunggu]);
    }
    
    public function approve(Jadwal $jadwal)
    {
        // Ubah status jadwal menjadi DIPUBLIKASIKAN
        $jadwal->status = 'DIPUBLIKASIKAN';
        $jadwal->id_penyetuju = auth()->id();
        $jadwal->tanggal_publikasi = now();
        $jadwal->save();

        // Nanti kita bisa tambahkan Log Histori di sini

        return redirect()->route('admin.dashboard')->with('success', 'Jadwal #'.$jadwal->id.' telah disetujui dan dipublikasikan.');
    }

    public function reject(Request $request, Jadwal $jadwal)
    {
        // Ubah status jadwal menjadi REVISI
        $jadwal->status = 'REVISI';

        // Simpan catatan revisi dari input form
        $jadwal->catatan_revisi = $request->input('catatan_revisi');

        $jadwal->save();

        return redirect()->route('approval.dashboard')->with('success', 'Jadwal #'.$jadwal->id.' telah dikembalikan untuk direvisi.');
    }
}
