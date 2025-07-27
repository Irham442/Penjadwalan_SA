<?php

namespace App\Services;

use App\Models\BebanAjar;
use App\Models\HariWaktu;
use App\Models\Kelas;
use App\Models\Ruangan;

class SchedulingService
{
    protected $kelases, $ruangans, $waktus, $waktuPerHari, $mapels;

    protected $classesToSchedule = [];

    // Parameter Algoritma Final
    protected $temperaturAwal = 10000;
    protected $temperaturAkhir = 1;
    protected $coolingRate = 0.0003;

    public function __construct()
    {
        // Ambil semua data master
        $this->ruangans = Ruangan::all();
        $this->waktus = HariWaktu::where('bisa_dijadwalkan', 1)->orderBy('id')->get(); // Filter dari awal
        $this->kelases = Kelas::all()->keyBy('id_kelas');
        $this->mapels = \App\Models\MataPelajaran::all()->keyBy('id_mapel');
        $semuaBebanAjar = BebanAjar::all();

        // Kelompokkan waktu berdasarkan hari
        $this->waktuPerHari = $this->waktus->groupBy('hari');

        // Validasi data master
        if ($this->ruangans->isEmpty() || $this->waktus->isEmpty() || $semuaBebanAjar->isEmpty()) {
            return;
        }

        // Buat daftar sesi blok dari beban ajar
        foreach ($semuaBebanAjar as $beban) {
            if ($beban->jam_per_blok <= 0) continue; // Keamanan
            $jumlahBlok = floor($beban->jumlah_jam_seminggu / $beban->jam_per_blok);
            for ($i = 0; $i < $jumlahBlok; $i++) {
                $this->classesToSchedule[] = [
                    'id_kelas'          => $beban->id_kelas,
                    'id_guru'           => $beban->id_guru,
                    'id_mata_pelajaran' => $beban->id_mapel,
                    'durasi'            => $beban->jam_per_blok,
                    'id_hari_waktu'     => $beban->id_hari_waktu, // <-- TAMBAHKAN INI
                    'sesi_id_unik'      => 'k'.$beban->id_kelas.'-g'.$beban->id_guru.'-m'.$beban->id_mapel.'-b'.$i,
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
        return ['jadwal_terbaik' => $solusiTerbaik, 'biaya_final' => $biayaTerbaik];
    }
    
    private function buatSolusiAwal()
    {
        $jadwal = [];
        $daftarSesi = $this->classesToSchedule;
        shuffle($daftarSesi);

        $sesiTerkunci = [];
        $sesiBebas = [];

        // FASE 1: Pisahkan sesi yang waktunya sudah ditentukan (terkunci) dan yang bebas
        foreach ($daftarSesi as $sesi) {
            if (!empty($sesi['id_hari_waktu'])) {
                $sesiTerkunci[] = $sesi;
            } else {
                $sesiBebas[] = $sesi;
            }
        }

        // FASE 2: Tempatkan semua sesi yang TERKUNCI terlebih dahulu (dengan logika blok baru)
        foreach ($sesiTerkunci as $sesi) {
            $durasi = $sesi['durasi'];
            $waktuMulaiId = $sesi['id_hari_waktu'];
            $detailKelas = $this->kelases->get($sesi['id_kelas']);
            $ruanganTargetId = $detailKelas ? $detailKelas->ruangan_id : null;

            if (!$ruanganTargetId) continue; // Lewati jika kelas tidak punya homeroom

            // Cari posisi awal dari slot waktu yang dikunci
            $slotDiHari = $this->waktuPerHari[$this->waktus->find($waktuMulaiId)->hari];
            $posisiAwal = $slotDiHari->search(fn($w) => $w->id == $waktuMulaiId);

            // Pastikan ada cukup slot setelah waktu mulai
            if ($posisiAwal !== false && ($posisiAwal + $durasi) <= $slotDiHari->count()) {
                $slotTarget = $slotDiHari->slice($posisiAwal, $durasi);
                $semuaSlotTersedia = true;

                // Cek apakah semua slot yang dibutuhkan kosong
                foreach ($slotTarget as $slot) {
                    if (isset($jadwal[$slot->id][$ruanganTargetId])) {
                        $semuaSlotTersedia = false;
                        break;
                    }
                }

                // Jika semua slot tersedia, tempatkan bloknya
                if ($semuaSlotTersedia) {
                    foreach ($slotTarget as $slot) {
                        $jadwal[$slot->id][$ruanganTargetId] = $sesi;
                    }
                }
                // Jika tidak, biarkan saja. Ini berarti ada konflik pada jadwal yang dikunci.
            }
        }

        // FASE 3: Tempatkan sisa sesi yang BEBAS menggunakan fungsi bantu kita
        foreach ($sesiBebas as $sesi) {
            $this->tempatkanSesi($sesi, $jadwal);
        }

        return $jadwal;
    }

    private function buatSolusiTetangga($jadwal)
    {
        if (empty($jadwal)) return $jadwal;
    
        $konflik = $this->cariKonflik($jadwal);
    
        if (empty($konflik)) {
            // Jika tidak ada konflik, lakukan pertukaran acak untuk eksplorasi
            $kunciWaktu = array_keys($jadwal);
            if(count($kunciWaktu) < 2) return $jadwal;
            
            $waktu1 = $kunciWaktu[array_rand($kunciWaktu)];
            $ruang1 = array_rand($jadwal[$waktu1]);
            
            $waktu2 = $kunciWaktu[array_rand($kunciWaktu)];
            $ruang2 = array_rand($jadwal[$waktu2]);

            // Tukar posisi
            $sesi1 = $jadwal[$waktu1][$ruang1];
            $jadwal[$waktu1][$ruang1] = $jadwal[$waktu2][$ruang2];
            $jadwal[$waktu2][$ruang2] = $sesi1;
        } else {
            // Jika ADA KONFLIK, fokus untuk memperbaikinya
            $sesiYangBermasalah = $konflik[array_rand($konflik)];
            $this->hapusSesiDariJadwal($sesiYangBermasalah, $jadwal);
            $this->tempatkanSesi($sesiYangBermasalah, $jadwal);
        }
    
        return $jadwal;
    }

    private function tempatkanSesi($sesi, &$jadwal)
    {
        $durasi = $sesi['durasi'];
        $detailKelas = $this->kelases->get($sesi['id_kelas']);
        $ruanganUtamaId = $detailKelas ? $detailKelas->ruangan_id : null;

        // Coba tempatkan di ruangan utama dulu
        if ($ruanganUtamaId && $this->cobaTempatkanDiRuanganSpesifik($sesi, $ruanganUtamaId, $jadwal)) {
            return true;
        }

        // Jika gagal atau tidak punya ruangan utama, coba di ruangan acak lain
        foreach ($this->ruangans->shuffle() as $ruangan) {
            if ($ruangan->id_ruangan != $ruanganUtamaId) {
                if ($this->cobaTempatkanDiRuanganSpesifik($sesi, $ruangan->id_ruangan, $jadwal)) {
                    return true;
                }
            }
        }
        return false;
    }

    private function cobaTempatkanDiRuanganSpesifik($sesi, $idRuanganTarget, &$jadwal)
    {
        $durasi = $sesi['durasi'];
        $hariAcak = $this->waktuPerHari->keys()->shuffle();

        foreach ($hariAcak as $hari) {
            // Ambil semua slot KBM (Kegiatan Belajar Mengajar) di hari itu
            $slotKBMDiHari = $this->waktuPerHari[$hari]->where('tipe_slot', 'KBM');

            if ($slotKBMDiHari->count() >= $durasi) {
                // Cari N slot KBM kosong yang berurutan
                for ($i = 0; $i <= $slotKBMDiHari->count() - $durasi; $i++) {
                    $slotTarget = $slotKBMDiHari->slice($i, $durasi);
                    $semuaSlotTersedia = true;

                    foreach ($slotTarget as $slot) {
                        if (isset($jadwal[$slot->id][$idRuanganTarget])) {
                            $semuaSlotTersedia = false;
                            break;
                        }
                    }

                    if ($semuaSlotTersedia) {
                        foreach ($slotTarget as $slot) {
                            $jadwal[$slot->id][$idRuanganTarget] = $sesi;
                        }
                        return true; // Berhasil ditempatkan
                    }
                }
            }
        }
        return false; // Gagal menempatkan
    }
    
    private function hapusSesiDariJadwal($sesiUntukDihapus, &$jadwal)
    {$idUnikSesi=$sesiUntukDihapus['sesi_id_unik'];foreach($jadwal as $id_waktu=>&$ruangan){foreach($ruangan as $id_ruangan=>$sesi){if(isset($sesi['sesi_id_unik'])&&$sesi['sesi_id_unik']===$idUnikSesi){unset($ruangan[$id_ruangan]);}}}}
    private function cariKonflik($jadwal)
    {$sesiBermasalah=[];$konflik=[];foreach($jadwal as $id_waktu=>$sesiDiWaktu){foreach($sesiDiWaktu as $id_ruangan=>$sesi){if(!is_array($sesi)||!isset($sesi['id_guru'])||!isset($sesi['id_kelas']))continue;$id_guru=$sesi['id_guru'];$id_kelas=$sesi['id_kelas'];if(isset($konflik[$id_waktu]['guru'][$id_guru]))$sesiBermasalah[]=$sesi;$konflik[$id_waktu]['guru'][$id_guru]=true;if(isset($konflik[$id_waktu]['kelas'][$id_kelas]))$sesiBermasalah[]=$sesi;$konflik[$id_waktu]['kelas'][$id_kelas]=true;}}return $sesiBermasalah;}
    
    private function hitungBiaya($jadwal)
    {
        $biaya = 0;
        $konflik = [];
        $jadwalPerRuangan = [];

        // Susun ulang jadwal agar mudah dicek per ruangan dan waktu
        foreach ($jadwal as $id_waktu => $sesiDiWaktu) {
            foreach ($sesiDiWaktu as $id_ruangan => $sesi) {
                $jadwalPerRuangan[$id_ruangan][$id_waktu] = $sesi;
            }
        }

        foreach ($jadwalPerRuangan as $id_ruangan => $sesiDiRuangan) {
            // Urutkan jadwal berdasarkan ID waktu (penting untuk cek urutan)
            ksort($sesiDiRuangan);
            $kunciWaktu = array_keys($sesiDiRuangan);

            for ($i = 0; $i < count($kunciWaktu); $i++) {
                $id_waktu_sekarang = $kunciWaktu[$i];
                $sesi = $sesiDiRuangan[$id_waktu_sekarang];

                if (!is_array($sesi) || !isset($sesi['id_guru']) || !isset($sesi['id_kelas'])) continue;

                $id_guru = $sesi['id_guru'];
                $id_kelas = $sesi['id_kelas'];

                // Aturan #1 & #2: Guru dan Kelas bentrok
                if (isset($konflik[$id_waktu_sekarang]['guru'][$id_guru])) $biaya += 100;
                $konflik[$id_waktu_sekarang]['guru'][$id_guru] = true;
                if (isset($konflik[$id_waktu_sekarang]['kelas'][$id_kelas])) $biaya += 100;
                $konflik[$id_waktu_sekarang]['kelas'][$id_kelas] = true;

                // Aturan #3: Kelas tidak di ruangan utamanya
                $detailKelas = $this->kelases->get($id_kelas);
                if ($detailKelas && $detailKelas->ruangan_id != null && $detailKelas->ruangan_id != $id_ruangan) {
                    $biaya += 50; // Penalti sedang
                }

                // --- ATURAN BARU: PENALTI KERAPIAN ---
                // Cek apakah slot sebelum dan sesudahnya kosong (di hari yang sama)
                $adaSlotSebelum = isset($kunciWaktu[$i - 1]) && ($kunciWaktu[$i - 1] == $id_waktu_sekarang - 1);
                $adaSlotSesudah = isset($kunciWaktu[$i + 1]) && ($kunciWaktu[$i + 1] == $id_waktu_sekarang + 1);

                if (!$adaSlotSebelum && !$adaSlotSesudah && $sesi['durasi'] < 3) {
                    // Jika sesi ini sendirian (tidak ada tetangga sebelum & sesudah) dan durasinya pendek,
                    // beri penalti kecil agar ia cenderung berkelompok.
                    $biaya += 2;
                }
            }
        }
        return $biaya;
    }
    private function hitungProbabilitas($biayaLama,$biayaBaru,$temperatur)
    {if($biayaBaru<$biayaLama)return 1.0;if($temperatur<=0)return 0;return exp(($biayaLama-$biayaBaru)/$temperatur);}
}