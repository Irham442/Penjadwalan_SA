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
    // 1. Cek Tahun Aktif
    $tahunAktif = \App\Models\TahunAjaran::where('is_active', 1)->first();

    // 2. Query Data
    $query = \App\Models\BebanAjar::with(['guru', 'mapel', 'kelas']); // Load relasi

    if ($tahunAktif) {
        // HANYA tampilkan data yang punya ID tahun ajaran aktif ini
        $query->where('id_tahun_ajaran', $tahunAktif->id);
    } else {
        // Jika tidak ada tahun aktif, jangan tampilkan apa-apa (tabel kosong)
        // Atau tampilkan data dummy id -1 agar result kosong
        $query->where('id_tahun_ajaran', -1);
    }

    $bebanAjars = $query->paginate(20);

    return view('admin.beban_ajar.index', compact('bebanAjars', 'tahunAktif'));
}
    
public function create()
    {
        // [REVISI DOSEN] FILTERISASI GURU BERDASARKAN TAHUN AJARAN
        $tahunAktif = TahunAjaran::where('is_active', 1)->first();

        if ($tahunAktif) {
            // Pastikan relasi gurus() ada di Model TahunAjaran, atau gunakan logika lain
            // Jika error, kembalikan ke Guru::all()
            if(method_exists($tahunAktif, 'gurus')) {
                $gurus = $tahunAktif->gurus()->orderBy('nama')->get();
            } else {
                $gurus = Guru::orderBy('nama')->get();
            }
        } else {
            $gurus = Guru::orderBy('nama')->get();
        }

        // Ambil daftar Kejuruan
        $produktifMapels = MataPelajaran::where('kategori', 'Produktif')->pluck('kode_mapel');
        
        $kejuruanList = $produktifMapels->map(function($kode_mapel) {
            $parts = explode('-', $kode_mapel);
            return count($parts) > 0 ? $parts[0] : null;
        })->filter()->unique()->sort();
        
        $waktus = HariWaktu::orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat')")
                            ->orderBy('jam_mulai')
                            ->get();

        return view('admin.beban_ajar.create', compact('gurus', 'kejuruanList', 'waktus', 'tahunAktif'));
    }

    public function store(Request $request)
    {
        // 1. Ambil Tahun Ajaran yang sedang AKTIF
        $tahunAktif = \App\Models\TahunAjaran::where('is_active', 1)->first();

        // Validasi: Jangan biarkan input jika tidak ada tahun ajaran aktif
        if (!$tahunAktif) {
            return redirect()->back()->with('error', 'Gagal: Tidak ada Tahun Ajaran yang aktif. Silakan setel dulu di menu Tahun Ajaran.');
        }

        $request->validate([
            'id_guru' => 'required',
            'id_mapel' => 'required',
            'id_kelas' => 'required',
            'jumlah_jam_seminggu' => 'required|numeric',
            'jam_per_blok' => 'required|numeric',
            'id_hari_waktu' => 'nullable', // Sesuaikan jika wajib
        ]);

        // 2. Simpan data dengan menyisipkan 'id_tahun_ajaran'
        \App\Models\BebanAjar::create([
            'id_guru' => $request->id_guru,
            'id_mapel' => $request->id_mapel,
            'id_kelas' => $request->id_kelas,
            'jumlah_jam_seminggu' => $request->jumlah_jam_seminggu,
            'jam_per_blok' => $request->jam_per_blok,
            'id_hari_waktu' => $request->id_hari_waktu,
            
            // INI KUNCINYA: Masukkan ID tahun aktif secara otomatis
            'id_tahun_ajaran' => $tahunAktif->id 
        ]);

        return redirect()->route('admin.beban-ajar.index')->with('success', 'Beban ajar berhasil ditambahkan untuk Semester ini.');
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