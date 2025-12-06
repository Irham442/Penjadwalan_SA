<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Ruangan;
use App\Models\Jadwal;
use App\Models\TahunAjaran; 
use App\Models\TeacherAvailability; // Import model ini biar rapi

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Data Statistik
        $jumlahGuru = Guru::count();
        $jumlahKelas = Kelas::count();
        $jumlahMapel = MataPelajaran::count();
        $jumlahRuangan = Ruangan::count();

        // 2. Data Tabel (Data Reference)
        $semuaGuru = Guru::get();
        $semuaKelas = Kelas::get();
        $semuaMapel = MataPelajaran::get();

        // 3. Ambil Tahun Ajaran Aktif
        $tahunAktif = TahunAjaran::where('is_active', 1)->first();

        // 4. LOGIKA TOMBOL GENERATE (3 KONDISI: BARU, NONAKTIF, REVISI)
        $bisaGenerateBaru = false;
        $jadwalRevisi = null; // Variabel penampung jadwal revisi
        
        if ($tahunAktif) {
            // Cek apakah sudah ada jadwal APAPUN (Draft, Revisi, Published)
            $jadwalExist = Jadwal::where('tahun_ajaran', $tahunAktif->tahun)
                                 ->where('semester', $tahunAktif->semester)
                                 ->exists();
            
            // Kondisi 1: Jika BELUM ADA jadwal sama sekali -> Bisa Generate Baru
            $bisaGenerateBaru = !$jadwalExist;

            // Kondisi 3: Cek apakah ada jadwal spesifik yang statusnya REVISI?
            // Kita ambil objeknya agar view bisa mengambil ID-nya untuk route regenerate
            $jadwalRevisi = Jadwal::where('tahun_ajaran', $tahunAktif->tahun)
                                  ->where('semester', $tahunAktif->semester)
                                  ->where('status', 'REVISI')
                                  ->first();
        }

        // 5. Data Draft & Riwayat
        $drafts = Jadwal::whereIn('status', ['DRAFT', 'REVISI'])->get();
        $riwayat = Jadwal::whereNotIn('status', ['DRAFT', 'REVISI'])->latest()->get();

        // 6. Data Monitoring Ketersediaan Guru
        $rekapKetersediaan = collect(); // Inisialisasi collection kosong biar aman
        if($tahunAktif) {
            $rekapKetersediaan = TeacherAvailability::with('guru')
                ->where('tahun_ajaran_id', $tahunAktif->id)
                ->get()
                ->groupBy('guru_id'); // Kelompokkan per guru
        }

        // Kirim semua variabel ke View
        return view('admin.dashboard', compact(
            'jumlahGuru', 'jumlahKelas', 'jumlahMapel', 'jumlahRuangan',
            'semuaGuru', 'semuaKelas', 'semuaMapel',
            'bisaGenerateBaru', 'jadwalRevisi', // <--- PENTING: Variabel logika tombol
            'drafts', 'riwayat',
            'tahunAktif', 'rekapKetersediaan'
        ));
    }
    
    public function submitForApproval($id)
    {
        $jadwal = Jadwal::findOrFail($id);
        
        // Pastikan hanya draft yang bisa dikirim
        if ($jadwal->status == 'DRAFT') {
            $jadwal->update([
                'status' => 'MENUNGGU_PERSETUJUAN'
            ]);
            
            return redirect()->back()->with('success', 'Jadwal berhasil dikirim untuk persetujuan.');
        }

        return redirect()->back()->with('error', 'Hanya jadwal berstatus Draft yang bisa dikirim.');
    }

    public function destroyDraft($id)
    {
        $jadwal = Jadwal::findOrFail($id);
        
        // Hapus jadwal (Otomatis hapus item_jadwal karena on delete cascade di migration)
        $jadwal->delete();

        return redirect()->route('admin.dashboard')->with('success', 'Draft jadwal berhasil dihapus. Anda bisa membuat jadwal baru sekarang.');
    }

    public function resetAvailability($guruId)
    {
        $tahunAktif = TahunAjaran::where('is_active', 1)->first();
        
        if($tahunAktif) {
            TeacherAvailability::where('guru_id', $guruId)
                ->where('tahun_ajaran_id', $tahunAktif->id)
                ->delete();
        }
        
        return back()->with('success', 'Data ketersediaan guru tersebut berhasil di-reset.');
    }
}
