<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ItemJadwal;
use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\TahunAjaran;
use App\Models\TeacherAvailability; // <--- 1. TAMBAHKAN IMPORT INI

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil Tahun Ajaran Aktif
        $tahunAktif = TahunAjaran::where('is_active', 1)->first();

        // 2. Ambil Input Filter dari URL
        $selectedTahun = $request->get('tahun_ajaran');
        $selectedSemester = $request->get('semester');

        // 3. Ambil Daftar Periode
        $daftarPeriode = Jadwal::where('status', 'DIPUBLIKASIKAN')
            ->select('tahun_ajaran', 'semester')
            ->distinct()
            ->orderBy('tahun_ajaran', 'desc')
            ->orderByRaw("FIELD(semester, 'Ganjil', 'Genap')")
            ->get();

        // 4. Tentukan Jadwal Mana yang Akan Ditampilkan
        $jadwalAktif = null;

        if ($selectedTahun && $selectedSemester) {
            $jadwalAktif = Jadwal::where('status', 'DIPUBLIKASIKAN')
                                ->where('tahun_ajaran', $selectedTahun)
                                ->where('semester', $selectedSemester)
                                ->first();
        } else {
            $jadwalAktif = Jadwal::where('status', 'DIPUBLIKASIKAN')
                                ->latest('updated_at')
                                ->first();
        }

        // 5. Siapkan Data Guru
        $user = Auth::user();
        $idGuru = $user->guru_id ?? null; 

        if (!$idGuru) {
            $guruData = Guru::where('user_id', $user->id)->first();
            $idGuru = $guruData ? $guruData->id_guru : null;
        }

        // --- [BARU] 6. AMBIL DATA AVAILABILITY (Agar tidak error undefined variable) ---
        $availabilities = collect(); // Default kosong
        if ($idGuru) {
            $query = TeacherAvailability::where('guru_id', $idGuru);
            if ($tahunAktif) {
                $query->where('tahun_ajaran_id', $tahunAktif->id);
            }
            $availabilities = $query->get();
        }
        // -------------------------------------------------------------------------

        // Jika jadwal tidak ada atau guru tidak terdaftar
        if (!$jadwalAktif || !$idGuru) {
            return view('teacher.dashboard', [
                'jadwalGabungan' => [], 
                'jadwalAktif' => $jadwalAktif,
                'daftarPeriode' => $daftarPeriode,
                'selectedTahun' => $selectedTahun,
                'selectedSemester' => $selectedSemester,
                'tahunAktif' => $tahunAktif,
                'availabilities' => $availabilities // <--- KIRIM KE VIEW DI SINI
            ]);
        }

        // 7. Ambil Item Jadwal Guru
        $items = ItemJadwal::where('id_jadwal', $jadwalAktif->id)
            ->where('id_guru', $idGuru)
            ->with(['mapel', 'kelas', 'waktu'])
            ->join('hari_waktu', 'item_jadwal.id_hari_waktu', '=', 'hari_waktu.id')
            ->select('item_jadwal.*') 
            ->orderByRaw("FIELD(hari_waktu.hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
            ->orderBy('hari_waktu.jam_mulai')
            ->get();

        // 8. Logika Grouping (Blok Jam)
        $jadwalGabungan = [];
        $blokSaatIni = null;

        foreach ($items as $item) {
            if(!$item->waktu) continue; 
            
            $sesiId = $item->id_mapel . '-' . $item->id_kelas;

            if ($blokSaatIni == null) {
                $blokSaatIni = $this->buatBlokBaru($item, $sesiId);
            } elseif (
                $item->waktu->hari == $blokSaatIni['hari'] &&
                $item->waktu->jam_mulai == $blokSaatIni['jam_selesai'] && 
                $sesiId == $blokSaatIni['sesi_id']
            ) {
                $blokSaatIni['jam_selesai'] = $item->waktu->jam_selesai;
            } else {
                if($blokSaatIni) { 
                    $jadwalGabungan[$blokSaatIni['hari']][] = $blokSaatIni;
                }
                $blokSaatIni = $this->buatBlokBaru($item, $sesiId);
            }
        }
        
        if ($blokSaatIni != null) {
            $jadwalGabungan[$blokSaatIni['hari']][] = $blokSaatIni;
        }

        // 9. Kirim ke View
        return view('teacher.dashboard', [
            'jadwalGabungan' => $jadwalGabungan,
            'jadwalAktif' => $jadwalAktif,
            'daftarPeriode' => $daftarPeriode,
            'selectedTahun' => $jadwalAktif->tahun_ajaran,
            'selectedSemester' => $jadwalAktif->semester,
            'tahunAktif' => $tahunAktif,
            'availabilities' => $availabilities // <--- KIRIM KE VIEW DI SINI JUGA
        ]);
    }

    private function buatBlokBaru($item, $sesiId)
    {
        if (!$item->waktu) return null; 
        return [
            'hari' => $item->waktu->hari,
            'mapel' => $item->mapel, 
            'kelas' => $item->kelas, 
            'jam_mulai' => $item->waktu->jam_mulai,
            'jam_selesai' => $item->waktu->jam_selesai,
            'sesi_id' => $sesiId
        ];
    }
}