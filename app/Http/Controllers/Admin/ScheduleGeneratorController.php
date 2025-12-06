<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TahunAjaran;
use App\Models\Jadwal;
use App\Models\ItemJadwal; 
use App\Models\Ruangan; // <--- WAJIB IMPORT
use App\Models\HariWaktu; // <--- WAJIB IMPORT
use App\Services\SchedulingService; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ScheduleGeneratorController extends Controller
{
    // ... (Fungsi generate biarkan sama) ...
    public function generate(Request $request)
    {
        // ... (Kode generate sama persis seperti sebelumnya) ...
        // ... Cukup copy paste fungsi generate dari versi sebelumnya jika perlu ...
        
        $tahunAktif = TahunAjaran::where('is_active', 1)->first();
        if (!$tahunAktif) return redirect()->back()->with('error', 'Gagal: Belum ada Tahun Ajaran aktif.');

        $jadwalLama = Jadwal::where('tahun_ajaran', $tahunAktif->tahun)->where('semester', $tahunAktif->semester)->first();
        if ($jadwalLama && $jadwalLama->status == 'DIPUBLIKASIKAN') return redirect()->back()->with('error', 'Jadwal sudah dipublikasikan.');

        try {
            $scheduler = new SchedulingService();
            $hasil = $scheduler->run();

            if ($hasil === false || empty($hasil['jadwal_terbaik'])) {
                return redirect()->back()->with('error', 'Gagal Generate: Data kosong atau tidak valid.');
            }

            DB::beginTransaction();
            if ($jadwalLama) {
                ItemJadwal::where('id_jadwal', $jadwalLama->id)->delete();
                $jadwalLama->delete();
            }

            $jadwalBaru = Jadwal::create([
                'tahun_ajaran' => $tahunAktif->tahun,
                'semester'     => $tahunAktif->semester,
                'status'       => 'DRAFT',
                'versi'        => ($jadwalLama ? $jadwalLama->versi + 1 : 1),
                'id_admin_pembuat' => auth()->id(),
            ]);

            $batchInsert = [];
            foreach ($hasil['jadwal_terbaik'] as $idWaktu => $ruangans) {
                foreach ($ruangans as $idRuangan => $sesi) {
                    if (!isset($sesi['id_guru'], $sesi['id_mata_pelajaran'], $sesi['id_kelas'])) continue; 
                    
                    $batchInsert[] = [
                        'id_jadwal'     => $jadwalBaru->id,
                        'id_hari_waktu' => $idWaktu,
                        'id_ruangan'    => $idRuangan,
                        'id_guru'       => $sesi['id_guru'],
                        'id_mata_pelajaran' => $sesi['id_mata_pelajaran'], 
                        'id_kelas'      => $sesi['id_kelas'],
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ];
                }
            }

            if (!empty($batchInsert)) ItemJadwal::insert($batchInsert);
            DB::commit();

            return redirect()->route('admin.jadwal.show', $jadwalBaru->id)->with('success', 'Jadwal berhasil digenerate!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error Sistem: ' . $e->getMessage());
        }
    }

    // === METODE SHOW KHUSUS VIEW RUANGAN ===
    public function show($id)
    {
        $jadwal = Jadwal::findOrFail($id);
        
        // 1. Ambil Item Jadwal
        $items = ItemJadwal::with(['guru', 'mapel', 'kelas', 'ruangan', 'waktu'])
                    ->where('id_jadwal', $id)
                    ->get();

        // 2. Ambil Semua Waktu & Group per Hari
        // Tujuannya untuk tahu: "Jam ID 50 itu sebenarnya Jam Ke-berapa di hari Selasa?"
        $allWaktu = \App\Models\HariWaktu::orderBy('jam_mulai')->get();
        $waktuByHari = $allWaktu->groupBy('hari');

        // 3. Mapping Data ke Grid [Hari][Jam_Ke]
        // Kita pakai "Jam Ke" (0, 1, 2...) sebagai kunci kolom agar rapi
        $jadwalGrid = [];
        
        foreach ($items as $item) {
            if (!$item->waktu) continue;
            
            $hari = $item->waktu->hari;
            
            // Cari tahu ini jam ke berapa (index) di hari tersebut
            // values() mereset key array agar jadi 0,1,2...
            $urutan = $waktuByHari[$hari]->values()->search(function($w) use ($item) {
                return $w->id === $item->waktu->id;
            });

            if ($urutan !== false) {
                $jadwalGrid[$hari][$urutan][] = $item;
            }
        }

        // 4. Siapkan Header Kolom (Kita ambil contoh dari hari Senin sebagai patokan)
        // Asumsi: Struktur jam hari lain mirip dengan Senin
        $refHari = 'Senin';
        if (!isset($waktuByHari[$refHari])) {
            $refHari = $waktuByHari->keys()->first(); // Ambil hari pertama yg ada kalau Senin kosong
        }
        $jamHeaders = $waktuByHari[$refHari] ? $waktuByHari[$refHari]->values() : collect();

        return view('admin.jadwal.show', compact('jadwal', 'jadwalGrid', 'jamHeaders'));
    }
    
    public function regenerate(Request $request, $id)
    {
        return $this->generate($request);
    }
}