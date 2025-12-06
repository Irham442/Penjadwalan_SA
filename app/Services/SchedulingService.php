<?php

namespace App\Services;

use App\Models\BebanAjar;
use App\Models\HariWaktu;
use App\Models\Kelas;
use App\Models\Ruangan;
use App\Models\TahunAjaran; 
use App\Models\TeacherAvailability;
use Illuminate\Support\Facades\Log;

class SchedulingService
{
    protected $kelases, $ruangans, $waktus, $waktuPerHari, $mapels;
    protected $classesToSchedule = [];
    protected $guruAvailabilities = [];

    protected $temperaturAwal = 1000; 
    protected $temperaturAkhir = 0.1;
    protected $coolingRate = 0.003;

    public function __construct()
    {
        $tahunAktif = TahunAjaran::where('is_active', 1)->first();
        if (!$tahunAktif) return;

        $this->ruangans = Ruangan::all();
        $this->waktus = HariWaktu::orderByRaw("FIELD(hari, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu')")->orderBy('jam_mulai')->get();
        $this->kelases = Kelas::all()->keyBy('id_kelas');
        $this->mapels = \App\Models\MataPelajaran::all()->keyBy('id_mapel');
        
        $semuaBebanAjar = BebanAjar::with(['guru', 'mapel', 'kelas'])->whereNotNull('id_guru')->get();

        $availabilities = TeacherAvailability::where('tahun_ajaran_id', $tahunAktif->id)->get();
        foreach ($availabilities as $av) {
            $this->guruAvailabilities[$av->guru_id][$av->day][] = [
                'start' => $av->start_time,
                'end'   => $av->end_time
            ];
        }

        $this->waktuPerHari = $this->waktus
            ->groupBy('hari')
            ->map(fn ($item) => $item->sortBy('jam_mulai')->values());

        foreach ($semuaBebanAjar as $beban) {
            $jamPerBlok = $beban->jam_per_blok > 0 ? $beban->jam_per_blok : 1;
            $totalJam = $beban->jumlah_jam_seminggu;

            if (!$totalJam) continue;

            $jumlahBlok = floor($totalJam / $jamPerBlok);

            for ($i = 0; $i < $jumlahBlok; $i++) {
                $this->classesToSchedule[] = [
                    'id_kelas'          => $beban->id_kelas,
                    'id_guru'           => $beban->id_guru,
                    'id_mata_pelajaran' => $beban->id_mapel,
                    'durasi'            => $jamPerBlok,
                    'id_hari_waktu'     => $beban->id_hari_waktu,
                    'sesi_id_unik'      => 'k' . $beban->id_kelas . '-g' . $beban->id_guru . '-m' . $beban->id_mapel . '-b' . $i,
                ];
            }
        }
    }

    public function run()
    {
        if (empty($this->classesToSchedule)) return false;

        $solusiSaatIni = $this->buatSolusiAwal();
        $biayaSaatIni = $this->hitungBiaya($solusiSaatIni);

        $solusiTerbaik = $solusiSaatIni;
        $biayaTerbaik = $biayaSaatIni;

        $temperatur = $this->temperaturAwal;

        while ($temperatur > $this->temperaturAkhir) {
            $solusiBaru = $this->buatSolusiTetangga($solusiSaatIni);
            $biayaBaru = $this->hitungBiaya($solusiBaru);

            if ($this->hitungProbabilitas($biayaSaatIni, $biayaBaru, $temperatur) > lcg_value()) {
                $solusiSaatIni = $solusiBaru;
                $biayaSaatIni = $biayaBaru;
            }

            if ($biayaSaatIni < $biayaTerbaik) {
                $solusiTerbaik = $solusiSaatIni;
                $biayaTerbaik = $biayaSaatIni;
                if ($biayaTerbaik == 0) break;
            }

            $temperatur *= (1 - $this->coolingRate);
        }

        return [
            'jadwal_terbaik' => $solusiTerbaik,
            'biaya_final'    => $biayaTerbaik
        ];
    }

    private function hitungBiaya($jadwal)
    {
        $biaya = 0;
        $slotMap = $this->waktus->keyBy('id');
        
        $jadwalPerRuangan = [];
        foreach ($jadwal as $id_waktu => $di) {
            foreach ($di as $id_ruangan => $sesi) {
                $jadwalPerRuangan[$id_ruangan][$id_waktu] = $sesi;
            }
        }

        $guruSlotUsage = []; 
        $kelasSlotUsage = [];

        foreach ($jadwalPerRuangan as $id_ruangan => $items) {
            foreach ($items as $slotId => $sesi) {
                if (!isset($sesi['id_guru'], $sesi['id_kelas'])) continue;

                $guru = $sesi['id_guru'];
                $kelas = $sesi['id_kelas'];
                
                if (isset($guruSlotUsage[$slotId][$guru])) $biaya += 1000;
                $guruSlotUsage[$slotId][$guru] = true;

                if (isset($kelasSlotUsage[$slotId][$kelas])) $biaya += 1000;
                $kelasSlotUsage[$slotId][$kelas] = true;

                $waktuObj = $slotMap[$slotId] ?? null;
                if ($waktuObj && $this->isGuruTidakBisa($guru, $waktuObj)) {
                    $biaya += 2000;
                }

                $kelasObj = $this->kelases->get($kelas);
                if ($kelasObj && $kelasObj->ruangan_id && $kelasObj->ruangan_id != $id_ruangan) {
                    $biaya += 50;
                }
            }
        }
        return $biaya;
    }

    private function isGuruTidakBisa($guruId, $waktuObj)
    {
        if (!isset($this->guruAvailabilities[$guruId][$waktuObj->hari])) return false;

        $slotStart = $waktuObj->jam_mulai;
        $slotEnd = $waktuObj->jam_selesai;

        foreach ($this->guruAvailabilities[$guruId][$waktuObj->hari] as $blocked) {
            if ($slotStart < $blocked['end'] && $slotEnd > $blocked['start']) {
                return true;
            }
        }
        return false;
    }

    private function buatSolusiAwal()
    {
        $jadwal = [];
        $daftarSesi = $this->classesToSchedule;
        shuffle($daftarSesi);

        foreach ($daftarSesi as $sesi) {
            $this->tempatkanSesiSecaraAcak($sesi, $jadwal);
        }
        return $jadwal;
    }

    private function buatSolusiTetangga($jadwal)
    {
        if (empty($jadwal)) return $jadwal;

        $sesiList = [];
        foreach ($jadwal as $sId => $ruangans) {
            foreach ($ruangans as $rId => $sesi) {
                if (!empty($sesi['id_hari_waktu'])) continue; // Skip locked
                $sesi['current_slot'] = $sId;
                $sesi['current_ruangan'] = $rId;
                $sesiList[] = $sesi;
            }
        }

        if (empty($sesiList)) return $jadwal;

        $sesiPindah = $sesiList[array_rand($sesiList)];
        
        // Hapus sesi LAMA (Semua blok durasi harus dihapus)
        $idUnik = $sesiPindah['sesi_id_unik'];
        foreach ($jadwal as $sId => &$ruangansRef) {
            foreach ($ruangansRef as $rId => $s) {
                if (isset($s['sesi_id_unik']) && $s['sesi_id_unik'] == $idUnik) {
                    unset($ruangansRef[$rId]);
                }
            }
        }

        // Tempatkan di posisi BARU
        $this->tempatkanSesiSecaraAcak($sesiPindah, $jadwal);

        return $jadwal;
    }

    // === [PERBAIKAN LOGIKA SKIP ISTIRAHAT] ===
    private function tempatkanSesiSecaraAcak($sesi, &$jadwal)
    {
        $durasi = $sesi['durasi'];

        // 1. LOGIKA LOCKED (MANUAL LOCK)
        if (!empty($sesi['id_hari_waktu'])) {
            $slotMulai = $this->waktus->firstWhere('id', $sesi['id_hari_waktu']);
            if($slotMulai) {
                $detailKelas = $this->kelases->get($sesi['id_kelas']);
                $ruanganId = $detailKelas->ruangan_id ?? $this->ruangans->first()->id_ruangan;
                
                $slotsDiHari = $this->waktuPerHari[$slotMulai->hari];
                $startIndex = $slotsDiHari->search(fn($w) => $w->id == $slotMulai->id);
                
                if ($startIndex !== false) {
                    // Cari slot berurutan DENGAN MELONCATI ISTIRAHAT
                    $count = 0;
                    $idx = $startIndex;
                    $maxIdx = $slotsDiHari->count();

                    while ($count < $durasi && $idx < $maxIdx) {
                        $slot = $slotsDiHari[$idx];
                        
                        // HANYA MASUKKAN JIKA BISA DIJADWALKAN (BUKAN ISTIRAHAT)
                        if ($slot->bisa_dijadwalkan == 1) {
                            $jadwal[$slot->id][$ruanganId] = $sesi;
                            $count++; // Baru nambah counter durasi
                        }
                        
                        $idx++; // Lanjut ke jam berikutnya
                    }
                }
            }
            return;
        }

        // 2. LOGIKA RANDOM (AUTO)
        $limit = 100;
        for ($k = 0; $k < $limit; $k++) {
            $ruangan = $this->ruangans->random();
            $hariRandom = $this->waktuPerHari->keys()->random();
            $slotsDiHari = $this->waktuPerHari[$hariRandom];
            
            $maxIdx = $slotsDiHari->count();
            // Start index random
            $startIndex = rand(0, max(0, $maxIdx - 1));

            // Cek apakah cukup durasinya (dengan memperhitungkan skip istirahat)
            $validSlotsFound = [];
            $idx = $startIndex;
            $bentrok = false;

            while (count($validSlotsFound) < $durasi && $idx < $maxIdx) {
                $slot = $slotsDiHari[$idx];

                if ($slot->bisa_dijadwalkan == 1) {
                    // Cek bentrok di jadwal existing
                    if (isset($jadwal[$slot->id][$ruangan->id_ruangan])) {
                        $bentrok = true; break; 
                    }
                    $validSlotsFound[] = $slot;
                }
                $idx++;
            }

            // Jika berhasil menemukan slot sejumlah durasi TANPA bentrok
            if (!$bentrok && count($validSlotsFound) == $durasi) {
                foreach ($validSlotsFound as $slot) {
                    $jadwal[$slot->id][$ruangan->id_ruangan] = $sesi;
                }
                return;
            }
        }
        
        // 3. FALLBACK (Paksa masuk jika susah cari tempat)
        // Tetap gunakan logika skip istirahat
        $hari = $this->waktuPerHari->keys()->random();
        $slots = $this->waktuPerHari[$hari];
        $r = $this->ruangans->random()->id_ruangan;
        
        $maxIdx = $slots->count();
        $startIndex = rand(0, max(0, $maxIdx - 1));
        
        $count = 0;
        $idx = $startIndex;
        while ($count < $durasi && $idx < $maxIdx) {
            $slot = $slots[$idx];
            if ($slot->bisa_dijadwalkan == 1) {
                $jadwal[$slot->id][$r] = $sesi;
                $count++;
            }
            $idx++;
        }
    }

    private function hitungProbabilitas($lama, $baru, $T)
    {
        if ($baru < $lama) return 1.0;
        return exp(($lama - $baru) / $T);
    }
}