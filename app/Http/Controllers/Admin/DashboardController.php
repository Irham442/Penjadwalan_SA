<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Impor semua Model yang kita butuhkan
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Ruangan;
use App\Models\Jadwal;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil data untuk kartu statistik
        $jumlahGuru = \App\Models\Guru::count();
        $jumlahKelas = \App\Models\Kelas::count();
        $jumlahMapel = \App\Models\MataPelajaran::count();
        $jumlahRuangan = \App\Models\Ruangan::count();

        // Ambil jadwal yang membutuhkan AKSI dari user ini (status DRAFT atau REVISI)
        $drafts = \App\Models\Jadwal::where('id_admin_pembuat', auth()->id())
                                    ->whereIn('status', ['DRAFT', 'REVISI'])
                                    ->orderBy('created_at', 'desc')
                                    ->get();

        // Ambil RIWAYAT jadwal yang sudah dikirim atau selesai
        $riwayat = \App\Models\Jadwal::where('id_admin_pembuat', auth()->id())
                                    ->whereIn('status', ['MENUNGGU_PERSETUJUAN', 'DIPUBLIKASIKAN'])
                                    ->orderBy('created_at', 'desc')
                                    ->get();

        // Kirim semua variabel yang dibutuhkan oleh view
        return view('admin.dashboard', [
            'jumlahGuru' => $jumlahGuru,
            'jumlahKelas' => $jumlahKelas,
            'jumlahMapel' => $jumlahMapel,
            'jumlahRuangan' => $jumlahRuangan,
            'semuaGuru' => \App\Models\Guru::all(), // Data untuk tabel dinamis
            'semuaKelas' => \App\Models\Kelas::all(),
            'semuaMapel' => \App\Models\MataPelajaran::all(),
            'semuaRuangan' => \App\Models\Ruangan::all(),
            'drafts' => $drafts, // Variabel untuk tabel Aksi
            'riwayat' => $riwayat, // Variabel untuk tabel Riwayat
        ]);
    }
    public function submitForApproval(Jadwal $jadwal)
    {
        // Langkah 1: Validasi (opsional tapi bagus)
        // Pastikan hanya pembuat draft yang bisa mengirimkannya.
        if ($jadwal->id_admin_pembuat !== auth()->id()) {
            // Jika bukan, tolak aksesnya.
            abort(403, 'AKSI DITOLAK.');
        }

        // Langkah 2: Ubah status jadwal menjadi 'MENUNGGU_PERSETUJUAN'
        $jadwal->status = 'MENUNGGU_PERSETUJUAN';
        $jadwal->save(); // Simpan perubahan ke database

        // Langkah 3: (Opsional) Buat catatan di log histori
        // LogHistoriJadwal::create([...]);

        // Langkah 4: Kembalikan user ke dashboard dengan pesan sukses
        return redirect()->route('admin.dashboard')->with('success', 'Draft Jadwal #'.$jadwal->id.' berhasil dikirim untuk persetujuan.');
    }
    public function destroyDraft(\App\Models\Jadwal $jadwal)
    {
        // Validasi: pastikan hanya pembuatnya yang bisa menghapus
        if ($jadwal->id_admin_pembuat !== auth()->id()) {
            abort(403);
        }

        // Hapus record jadwal utama
        // Karena kita sudah mengatur 'onDelete(cascade)' di migrasi item_jadwal,
        // semua item yang terhubung akan ikut terhapus otomatis.
        $jadwal->delete();

        return redirect()->route('admin.dashboard')->with('success', 'Draft Jadwal #'.$jadwal->id.' telah dihapus. Silakan buat jadwal baru.');
    }
}