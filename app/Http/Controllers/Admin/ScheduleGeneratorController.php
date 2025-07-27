<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SchedulingService;
use App\Models\Jadwal;
use App\Models\ItemJadwal;
use App\Models\Ruangan;
use App\Models\HariWaktu;

class ScheduleGeneratorController extends Controller
{
    public function generate(Request $request)
    {
        // 1. Buat instance baru dan jalankan service penjadwalan
        $scheduler = new SchedulingService();
        $hasil = $scheduler->run();
        
        // Ambil hasilnya
        $jadwalTerbaik = $hasil['jadwal_terbaik'];
        $biayaFinal = $hasil['biaya_final'];

        // 2. Buat record utama untuk draft jadwal ini di tabel 'jadwal'
        $jadwalRecord = \App\Models\Jadwal::create([
            'tahun_ajaran' => '2025/2026', // Ini bisa dibuat dinamis nanti
            'semester' => 'Ganjil',     // Ini bisa dibuat dinamis nanti
            'status' => 'DRAFT',        // Status awal adalah DRAFT
            'id_admin_pembuat' => auth()->id(),
        ]);

        // 3. Simpan setiap item jadwal ke tabel 'item_jadwal'
        foreach ($jadwalTerbaik as $id_waktu => $sesiDiWaktu) {
            foreach ($sesiDiWaktu as $id_ruangan => $sesi) {
                \App\Models\ItemJadwal::create([
                    'id_jadwal' => $jadwalRecord->id,
                    'id_hari_waktu' => $id_waktu,
                    'id_ruangan' => $id_ruangan,
                    'id_guru' => $sesi['id_guru'],
                    'id_mata_pelajaran' => $sesi['id_mata_pelajaran'], 
                    'id_kelas' => $sesi['id_kelas'],
                ]);
            }
        }

        // 4. Hapus dd() dan ganti dengan redirect ke halaman untuk menampilkan hasilnya
        return redirect()->route('admin.jadwal.show', ['jadwal' => $jadwalRecord->id])
                         ->with('success', 'Jadwal baru berhasil dibuat dengan biaya konflik: ' . $biayaFinal);
    }
    // Ganti method show Anda dengan versi yang lebih bersih ini
    public function show(Jadwal $jadwal)
    {
        // Ambil semua data master untuk header tabel
        $ruangans = Ruangan::orderBy('id_ruangan')->get(); // Diurutkan agar konsisten
        $waktus = HariWaktu::orderBy('id')->get(); // Diurutkan agar konsisten
        
        // Ambil semua item jadwal yang terkait dengan draft jadwal ini
        $items = $jadwal->items()->with(['guru', 'kelas', 'mapel', 'ruangan', 'waktu'])->get();
        
        // 'Putar' data agar mudah ditampilkan dalam grid
        // Ini adalah langkah krusial
        $jadwalGrid = [];
        foreach ($items as $item) {
            // Pastikan semua relasi ada untuk menghindari error
            if ($item->waktu && $item->ruangan) {
                $jadwalGrid[$item->id_hari_waktu][$item->id_ruangan] = $item;
            }
        }

        // Kirim semua data yang dibutuhkan ke view
        return view('admin.jadwal.show', compact('jadwal', 'ruangans', 'waktus', 'jadwalGrid'));
    }
    // Di dalam class ScheduleGeneratorController

// ... method generate() dan show() yang sudah ada ...

    public function regenerate(Jadwal $jadwal)
    {
        // --- Langkah 1: Jalankan service untuk membuat jadwal baru ---
        $scheduler = new SchedulingService();
        $hasil = $scheduler->run();
        $jadwalBaruHasilGenerate = $hasil['jadwal_terbaik'];
        $biayaFinal = $hasil['biaya_final'];

        // --- Langkah 2: Buat record jadwal BARU dengan versi yang dinaikkan ---
        $jadwalBaruRecord = Jadwal::create([
            'tahun_ajaran' => $jadwal->tahun_ajaran, // Ambil info dari jadwal lama
            'semester' => $jadwal->semester,       // Ambil info dari jadwal lama
            'versi' => $jadwal->versi + 1,          // <-- VERSI DITAMBAHKAN 1
            'status' => 'MENUNGGU_PERSETUJUAN', // Langsung kirim untuk disetujui
            'id_admin_pembuat' => auth()->id(),
            // Catatan revisi bisa kita bawa juga jika perlu
            'catatan_revisi' => 'Direvisi dari draft #' . $jadwal->id . '. Catatan asli: ' . $jadwal->catatan_revisi,
        ]);

        // --- Langkah 3: Simpan item-item jadwal untuk jadwal BARU ---
        foreach ($jadwalBaruHasilGenerate as $id_waktu => $sesiDiWaktu) {
            foreach ($sesiDiWaktu as $id_ruangan => $sesi) {
                ItemJadwal::create([
                    'id_jadwal' => $jadwalBaruRecord->id, // <-- Gunakan ID dari jadwal baru
                    'id_hari_waktu' => $id_waktu,
                    'id_ruangan' => $id_ruangan,
                    'id_guru' => $sesi['id_guru'],
                    'id_mata_pelajaran' => $sesi['id_mata_pelajaran'],
                    'id_kelas' => $sesi['id_kelas'],
                ]);
            }
        }

        // --- Langkah 4: Hapus draft lama yang sudah direvisi ---
        $jadwal->delete();

        // --- Langkah 5: Redirect ke halaman detail jadwal BARU ---
        return redirect()->route('admin.jadwal.show', $jadwalBaruRecord->id)
                        ->with('success', 'Jadwal Revisi (v'.$jadwalBaruRecord->versi.') berhasil dibuat ulang dengan biaya konflik: ' . $biayaFinal);
    }
}