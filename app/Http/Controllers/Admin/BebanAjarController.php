<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BebanAjar;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\HariWaktu;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class BebanAjarController extends Controller
{
    public function index()
    {
        // 1. Ambil Tahun Ajaran yang sedang AKTIF (Untuk info di atas tabel)
        $tahunAktif = TahunAjaran::where('is_active', 1)->first();

        // 2. Ambil data beban ajar
        $bebanAjars = BebanAjar::with(['guru', 'mapel', 'kelas'])
                        ->orderBy(function($query) {
                            $query->select('nama')
                                ->from('guru')
                                ->whereColumn('guru.id_guru', 'beban_ajar.id_guru');
                        })
                        ->paginate(20);

        // 3. Kirim $tahunAktif juga ke view
        return view('admin.beban_ajar.index', compact('bebanAjars', 'tahunAktif'));
    }
    
    public function create()
    {
        // ============================================================
        // [REVISI DOSEN] FILTERISASI GURU BERDASARKAN TAHUN AJARAN
        // ============================================================
        
        // 1. Cek Tahun Ajaran mana yang sedang AKTIF (is_active = 1)
        $tahunAktif = TahunAjaran::where('is_active', 1)->first();

        if ($tahunAktif) {
            // Jika ada tahun aktif, ambil guru yang terdaftar di tahun itu saja
            // Relasi 'gurus()' harus ada di Model TahunAjaran
            $gurus = $tahunAktif->gurus()->orderBy('nama')->get();
        } else {
            // Fallback: Jika belum ada tahun aktif diset, tampilkan semua guru
            $gurus = Guru::orderBy('nama')->get();
        }

        // 2. Ambil daftar Kejuruan dari MATA PELAJARAN
        $produktifMapels = MataPelajaran::where('kategori', 'Produktif')->pluck('kode_mapel');
        
        $kejuruanList = $produktifMapels->map(function($kode_mapel) {
            $parts = explode('-', $kode_mapel);
            if (count($parts) > 0) {
                return $parts[0];
            }
            return null;
        })->filter()->unique()->sort();
        
        // 3. Ambil daftar waktu untuk locking
        $waktus = HariWaktu::orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat')")
                          ->orderBy('jam_mulai')
                          ->get();
                          
        // 4. Kirim ke view (tambahkan variabel tahunAktif agar bisa ditampilkan di judul form jika mau)
        return view('admin.beban_ajar.create', compact('gurus', 'kejuruanList', 'waktus', 'tahunAktif'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_guru' => 'required|exists:guru,id_guru',
            'id_mapel' => 'required|exists:mata_pelajaran,id_mapel',
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'jumlah_jam_seminggu' => 'required|integer|min:1',
            'jam_per_blok' => 'required|integer|min:1',
            'id_hari_waktu' => 'nullable|exists:hari_waktu,id', 
        ]);

        BebanAjar::create([
            'id_guru' => $request->id_guru,
            'id_mapel' => $request->id_mapel,
            'id_kelas' => $request->id_kelas,
            'jumlah_jam_seminggu' => $request->jumlah_jam_seminggu,
            'jam_per_blok' => $request->jam_per_blok,
            'id_hari_waktu' => $request->id_hari_waktu,
        ]);

        return redirect()->route('admin.beban-ajar.index')->with('success', 'Beban ajar berhasil ditambahkan.'); 
    }


    public function edit(BebanAjar $bebanAjar)
    {
        $bebanAjar->load(['mapel', 'kelas']);

        // ============================================================
        // [REVISI DOSEN] FILTERISASI DI FORM EDIT JUGA
        // ============================================================
        $tahunAktif = TahunAjaran::where('is_active', 1)->first();

        if ($tahunAktif) {
            $gurus = $tahunAktif->gurus()->orderBy('nama')->get();
        } else {
            $gurus = Guru::orderBy('nama')->get();
        }

        $waktus = HariWaktu::orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat')")
                          ->orderBy('jam_mulai')
                          ->get();
                          
        $kejuruanList = MataPelajaran::where('kategori', 'Produktif')
            ->selectRaw('DISTINCT SUBSTRING_INDEX(kode_mapel, "-", 1) as kejuruan')
            ->orderBy('kejuruan')
            ->get()
            ->pluck('kejuruan');

        // Logika dropdown mapel & kelas (seperti sebelumnya)
        $selectedKejuruan = null;
        $mapels = collect();
        $kelases = collect();
        $mapelTersimpan = $bebanAjar->mapel;

        if ($mapelTersimpan) {
            if ($mapelTersimpan->kategori != 'Produktif') {
                $selectedKejuruan = 'UMUM';
                $mapels = MataPelajaran::where('kategori', '!=', 'Produktif')->orderBy('nama_mapel')->get();
                $kelases = Kelas::orderBy('nama_kelas')->get();
            } else {
                $selectedKejuruan = explode('-', $mapelTersimpan->kode_mapel)[0] ?? null;
                if ($selectedKejuruan) {
                    $mapels = MataPelajaran::where('kode_mapel', 'LIKE', $selectedKejuruan . '%')->orderBy('nama_mapel')->get();
                    $kelases = Kelas::where('nama_kelas', 'LIKE', '%' . $selectedKejuruan . '%')->orderBy('nama_kelas')->get();
                    if ($kelases->isEmpty()) {
                        $kelases = Kelas::orderBy('nama_kelas')->get();
                    }
                }
            }
        }

        return view('admin.beban_ajar.edit', compact(
            'bebanAjar', 
            'gurus', 
            'waktus', 
            'kejuruanList', 
            'selectedKejuruan', 
            'mapels', 
            'kelases',
            'tahunAktif'
        ));
    }

    public function update(Request $request, BebanAjar $bebanAjar)
    {
        $request->validate([
            'id_guru' => 'required|exists:guru,id_guru', 
            'id_mapel' => 'required|exists:mata_pelajaran,id_mapel',
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'jumlah_jam_seminggu' => 'required|integer|min:1',
            'jam_per_blok' => 'required|integer|min:1',
            'id_hari_waktu' => 'nullable|exists:hari_waktu,id',
        ]);

        $bebanAjar->update($request->all());

        return redirect()->route('admin.beban-ajar.index')
                         ->with('success', 'Beban ajar berhasil diperbarui.');
    }

    public function destroy(BebanAjar $bebanAjar)
    {
        $bebanAjar->delete();
        return redirect()->route('admin.beban-ajar.index')->with('success', 'Beban ajar berhasil dihapus.');
    }

    // Fungsi AJAX (Tidak perlu diubah karena hanya urus Mapel/Kelas)
    public function getDataBebanAjar(Request $request)
    {
        $kejuruan = $request->get('kejuruan');

        if (!$kejuruan) {
            return response()->json(['mapels' => [], 'kelases' => []]);
        }

        if ($kejuruan == 'UMUM') {
            $mapels = MataPelajaran::where('kategori', '!=', 'Produktif')
                                    ->orderBy('nama_mapel')
                                    ->get(['id_mapel', 'kode_mapel', 'nama_mapel']);
            $kelases = Kelas::orderBy('nama_kelas')->get(['id_kelas', 'nama_kelas']); 
        } else {
            $mapels = MataPelajaran::where('kode_mapel', 'LIKE', $kejuruan . '%')
                                    ->orderBy('nama_mapel')
                                    ->get(['id_mapel', 'kode_mapel', 'nama_mapel']);
            
            $kelases = Kelas::where('nama_kelas', 'LIKE', '%' . $kejuruan . '%')
                            ->orderBy('nama_kelas')
                            ->get(['id_kelas', 'nama_kelas']);

            if ($kelases->isEmpty()) {
                $kelases = Kelas::orderBy('nama_kelas')->get(['id_kelas', 'nama_kelas']);
            }
        }

        return response()->json([
            'mapels' => $mapels,
            'kelases' => $kelases
        ]);
    }
}