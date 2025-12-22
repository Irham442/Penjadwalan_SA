<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Penjadwalan</title>
    
    <!-- CSS Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body { background-color: #f1f5f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        
        /* Kartu Statistik (Link) */
        .stat-card {
            border: none;
            border-radius: 12px;
            transition: all 0.3s ease;
            overflow: hidden;
            text-decoration: none; /* Hilangkan garis bawah link */
            display: block; /* Agar full area bisa diklik */
            color: inherit;
        }
        .stat-card:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        /* Efek hover khusus untuk border warna */
        .stat-card.hover-primary:hover { border-bottom: 4px solid #0d6efd; }
        .stat-card.hover-success:hover { border-bottom: 4px solid #198754; }
        .stat-card.hover-info:hover { border-bottom: 4px solid #0dcaf0; }
        .stat-card.hover-warning:hover { border-bottom: 4px solid #ffc107; }

        .stat-icon {
            width: 50px; height: 50px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 10px; font-size: 24px;
        }
        
        /* Layout Utils */
        .table-card { border-radius: 12px; overflow: hidden; }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm sticky-top">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold" href="{{ route('admin.dashboard') }}">
                <i class="bi bi-calendar-week me-2"></i> SIP JADWAL
            </a>
            <div class="d-flex align-items-center text-white">
                <span class="me-3 d-none d-md-block">Halo, <strong>{{ Auth::user()->name }}</strong></span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm rounded-pill px-3">
                        <i class="bi bi-box-arrow-right me-1"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-4 py-4">
        
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <strong>Gagal Membuat Jadwal!</strong> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- BARIS 1: WELCOME & QUICK ACTIONS (Tanpa Data Ruangan) -->
        <div class="row mb-4">
            <!-- Kolom Kiri: Sambutan & Tombol Generate -->
            <div class="col-lg-8 mb-3 mb-lg-0">
                <div class="card border-0 shadow-sm h-100 bg-white" style="border-radius: 12px;">
                    <div class="card-body p-4 d-flex flex-column justify-content-center">
                        <h2 class="fw-bold text-gray-800">Dashboard Kurikulum</h2>
                        <p class="text-muted">Kelola data master dan generate jadwal pelajaran otomatis dengan mudah.</p>
                        
                        <div class="d-flex gap-2 flex-wrap mt-2">
                            
                            {{-- KONDISI 3: REVISI (Prioritas Tertinggi) --}}
                            @if($jadwalRevisi)
                                <form action="{{ route('admin.jadwal.regenerate', $jadwalRevisi->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin men-generate ulang jadwal ini sesuai catatan revisi?');">
                                    @csrf
                                    <button type="submit" class="btn btn-warning btn-lg shadow-sm px-4 fw-bold text-dark">
                                        <i class="bi bi-arrow-repeat me-2"></i> Regenerate Jadwal (Revisi)
                                    </button>
                                </form>

                            {{-- KONDISI 1: BELUM ADA JADWAL (Generate Baru) --}}
                            @elseif($bisaGenerateBaru)
                                <button type="button" class="btn btn-primary btn-lg shadow-sm px-4" data-bs-toggle="modal" data-bs-target="#generateModal">
                                    <i class="bi bi-cpu-fill me-2"></i> Generate Jadwal Baru
                                </button>

                            {{-- KONDISI 2: SUDAH ADA JADWAL (Nonaktif) --}}
                            @else
                                <button type="button" class="btn btn-secondary btn-lg px-4" data-bs-toggle="modal" data-bs-target="#warningModal">
                                    <i class="bi bi-cpu-fill me-2"></i> Generate Jadwal (Nonaktif)
                                </button>
                            @endif

                        </div>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Menu Navigasi Cepat (Tanpa Ruangan) -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100 bg-white" style="border-radius: 12px;">
                    <div class="card-header bg-white border-0 fw-bold py-3">Menu Manajemen Data</div>
                    <div class="card-body pt-0">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.tahun_ajaran.index') }}" class="btn btn-outline-primary text-start p-3 border-2 d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 p-2 rounded me-3 text-primary"><i class="bi bi-calendar-check fs-5"></i></div>
                                <div>
                                    <div class="fw-bold">Tahun Ajaran & Guru</div>
                                    <small class="text-muted" style="font-size: 0.75rem;">Set Periode Aktif & Guru Pengajar</small>
                                </div>
                            </a>
                            
                            <a href="{{ route('admin.beban-ajar.index') }}" class="btn btn-outline-success text-start p-3 border-2 d-flex align-items-center">
                                <div class="bg-success bg-opacity-10 p-2 rounded me-3 text-success"><i class="bi bi-person-workspace fs-5"></i></div>
                                <div>
                                    <div class="fw-bold">Beban Ajar</div>
                                    <small class="text-muted" style="font-size: 0.75rem;">Pembagian Tugas Mengajar</small>
                                </div>
                            </a>
                            
                            {{-- Data Ruangan dihapus dari sini sesuai permintaan --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- BARIS 2: STATISTIK RINGKAS (SEKARANG BISA DIKLIK / LINK) -->
        <div class="row g-3 mb-4">
            <!-- Link ke Data Guru -->
            <div class="col-md-3 col-sm-6">
                <a href="{{ route('admin.guru.index') }}" class="card stat-card hover-primary shadow-sm h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
                            <i class="bi bi-person-video3"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Total Guru</h6>
                            <h3 class="fw-bold mb-0 text-dark">{{ $jumlahGuru }}</h3>
                            <small class="text-primary" style="font-size: 0.75rem;">Klik untuk kelola <i class="bi bi-arrow-right"></i></small>
                        </div>
                    </div>
                </a>
            </div>
            
            <!-- Link ke Data Kelas -->
            <div class="col-md-3 col-sm-6">
                <a href="{{ route('admin.kelas.index') }}" class="card stat-card hover-success shadow-sm h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-success bg-opacity-10 text-success me-3">
                            <i class="bi bi-easel2"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Total Kelas</h6>
                            <h3 class="fw-bold mb-0 text-dark">{{ $jumlahKelas }}</h3>
                            <small class="text-success" style="font-size: 0.75rem;">Klik untuk kelola <i class="bi bi-arrow-right"></i></small>
                        </div>
                    </div>
                </a>
            </div>
            
            <!-- Link ke Data Mapel -->
            <div class="col-md-3 col-sm-6">
                <a href="{{ route('admin.mapel.index') }}" class="card stat-card hover-info shadow-sm h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-info bg-opacity-10 text-info me-3">
                            <i class="bi bi-book"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Mata Pelajaran</h6>
                            <h3 class="fw-bold mb-0 text-dark">{{ $jumlahMapel }}</h3>
                            <small class="text-info" style="font-size: 0.75rem;">Klik untuk kelola <i class="bi bi-arrow-right"></i></small>
                        </div>
                    </div>
                </a>
            </div>
            
            <!-- Link ke Data Ruangan (Pindahan dari Menu Atas) -->
            <div class="col-md-3 col-sm-6">
                <a href="{{ route('admin.ruangan.index') }}" class="card stat-card hover-warning shadow-sm h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning me-3">
                            <i class="bi bi-door-open"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Ruangan</h6>
                            <h3 class="fw-bold mb-0 text-dark">{{ $jumlahRuangan }}</h3>
                            <small class="text-warning" style="font-size: 0.75rem;">Klik untuk kelola <i class="bi bi-arrow-right"></i></small>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- BARIS 3: KONTEN UTAMA -->
        <div class="row">
            
            <!-- Kolom Kiri: MONITORING DATA GURU (Pengganti Tabs Data Reference) -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 mb-4 border-start border-4 border-info">
                    <div class="card-header bg-white fw-bold py-3 d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-eye-fill text-info me-2"></i> Monitoring Ketersediaan Guru</span>
                        <span class="badge bg-info text-dark">{{ count($rekapKetersediaan ?? []) }} Guru Menginput</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 450px; overflow-y: auto;">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th class="ps-4">Nama Guru</th>
                                        <th>Jadwal Tidak Bisa (Blocked)</th>
                                        <th>Total Jam</th>
                                        <th class="text-end pe-4">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($rekapKetersediaan ?? [] as $guruId => $items)
                                        @php $namaGuru = $items->first()->guru->nama ?? 'Guru Dihapus'; @endphp
                                        <tr>
                                            <td class="ps-4 fw-bold text-dark">{{ $namaGuru }}</td>
                                            <td>
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach($items as $item)
                                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger">
                                                            {{ substr($item->day, 0, 3) }} 
                                                            {{ date('H:i', strtotime($item->start_time)) }}-{{ date('H:i', strtotime($item->end_time)) }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                                @if($items->first()->reason)
                                                    <small class="text-muted d-block mt-1 fst-italic">"{{ Str::limit($items->first()->reason, 50) }}"</small>
                                                @endif
                                            </td>
                                            <td>
                                                @php 
                                                    $totalMenit = 0;
                                                    foreach($items as $i) {
                                                        $start = \Carbon\Carbon::parse($i->start_time);
                                                        $end = \Carbon\Carbon::parse($i->end_time);
                                                        $totalMenit += $end->diffInMinutes($start);
                                                    }
                                                @endphp
                                                <span class="badge {{ $totalMenit > 300 ? 'bg-warning text-dark' : 'bg-secondary' }}">
                                                    {{ round($totalMenit / 60, 1) }} Jam
                                                </span>
                                            </td>
                                            <td class="text-end pe-4">
                                                <form action="{{ route('admin.availability.reset', $guruId) }}" method="POST" onsubmit="return confirm('Hapus semua request waktu guru ini? Guru harus menginput ulang.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-outline-danger" title="Tolak / Reset Request">
                                                        <i class="bi bi-trash"></i> Reset
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5 text-muted">
                                                <i class="bi bi-check-circle-fill text-success fs-1 d-block mb-2"></i>
                                                <span class="fw-bold">Semua Guru Available</span><br>
                                                <small>Belum ada guru yang mengajukan jam berhalangan.</small>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-light small text-muted">
                        <i class="bi bi-info-circle me-1"></i> 
                        <strong>Kontrol Admin:</strong> Gunakan tabel ini untuk memantau request guru sebelum men-generate jadwal. Jika terlalu banyak blokir, jadwal mungkin gagal dibuat.
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: STATUS JADWAL (Drafts & Riwayat) -->
            <div class="col-lg-4">
                
                <!-- DRAFT CARD -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white fw-bold py-3 d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-pencil-square text-warning me-2"></i> Draft Jadwal</span>
                        <span class="badge bg-warning text-dark">{{ $drafts->count() }}</span>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            @forelse($drafts as $draft)
                                <li class="list-group-item p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge bg-primary">{{ $draft->tahun_ajaran }} - {{ $draft->semester }}</span>
                                        <small class="text-muted">{{ $draft->created_at->diffForHumans() }}</small>
                                    </div>
                                    
                                    @if($draft->status == 'REVISI')
                                        <div class="alert alert-warning py-2 px-3 small mb-2">
                                            <strong>Revisi:</strong> {{ Str::limit($draft->catatan_revisi, 50) }}
                                        </div>
                                    @endif

                                    <div class="d-grid gap-2">
                                        <a href="{{ route('admin.jadwal.show', $draft->id) }}" class="btn btn-sm btn-outline-primary">Lihat Detail</a>
                                    </div>

                                    @if($draft->status == 'DRAFT')
                                    <div class="modal fade" id="konfirmasiKirimModal-{{ $draft->id }}" tabindex="-1">
                                            {{-- Modal Content (Jika diperlukan) --}}
                                    </div>
                                    @endif
                                </li>
                            @empty
                                <li class="list-group-item text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-4 d-block mb-1"></i> Tidak ada draft aktif
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                <!-- RIWAYAT CARD -->
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white fw-bold py-3">
                        <i class="bi bi-clock-history text-secondary me-2"></i> Riwayat Terakhir
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @forelse($riwayat->take(3) as $item)
                                <a href="{{ route('admin.jadwal.show', $item->id) }}" class="list-group-item list-group-item-action p-3">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1 fw-bold">{{ $item->tahun_ajaran }} ({{ $item->semester }})</h6>
                                        @if($item->status == 'MENUNGGU_PERSETUJUAN')
                                            <span class="badge bg-info text-dark" style="font-size: 0.6rem;">Menunggu</span>
                                        @elseif($item->status == 'DIPUBLIKASIKAN')
                                            <span class="badge bg-success" style="font-size: 0.6rem;">Rilis</span>
                                        @endif
                                    </div>
                                    <small class="text-muted">Versi {{ $item->versi }} - {{ $item->created_at->format('d M') }}</small>
                                </a>
                            @empty
                                <div class="text-center py-3 text-muted small">Belum ada riwayat</div>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <footer class="mt-5 text-center text-muted small">
            &copy; 2025 - Sistem Informasi Penjadwalan Sekolah
        </footer>
    </div>

    <!-- MODAL GENERATE -->
    <div class="modal fade" id="generateModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-cpu me-2"></i>Konfirmasi Generate Jadwal</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                
                <form action="{{ route('admin.jadwal.generate') }}" method="POST">
                    @csrf
                    <div class="modal-body text-center py-4">
                        
                        @if($tahunAktif)
                            <div class="mb-3">
                                <i class="bi bi-calendar-check text-success" style="font-size: 3rem;"></i>
                            </div>
                            <h5 class="fw-bold text-gray-800">Periode Aktif Terdeteksi</h5>
                            <p class="text-muted mb-4">
                                Sistem akan membuat jadwal pelajaran secara otomatis untuk:
                            </p>
                            
                            <div class="alert alert-primary d-inline-block px-4 py-2 mx-auto">
                                <span class="fw-bold fs-5">{{ $tahunAktif->tahun }}</span> <br>
                                Semester <span class="fw-bold">{{ $tahunAktif->semester }}</span>
                            </div>

                            <p class="small text-muted mt-3 mb-0">
                                Pastikan Data Guru, Beban Ajar, dan Ruangan sudah valid sebelum melanjutkan.
                            </p>
                        @else
                            <div class="mb-3">
                                <i class="bi bi-exclamation-circle text-danger" style="font-size: 3rem;"></i>
                            </div>
                            <h5 class="fw-bold text-danger">Tidak Ada Periode Aktif!</h5>
                            <p class="text-muted">
                                Silakan aktifkan Tahun Ajaran terlebih dahulu di menu "Kelola Tahun Ajaran".
                            </p>
                        @endif

                    </div>
                    
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                        
                        @if($tahunAktif)
                            <button type="submit" class="btn btn-primary px-4 fw-bold">
                                <i class="bi bi-play-circle me-2"></i> Mulai Proses
                            </button>
                        @else
                            <a href="{{ route('admin.tahun_ajaran.index') }}" class="btn btn-danger px-4">
                                Ke Menu Tahun Ajaran
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL WARNING -->
    <div class="modal fade" id="warningModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-warning border-0">
                    <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Peringatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="mb-0">Anda sudah memiliki jadwal yang sedang berjalan/diproses. Selesaikan atau hapus draft tersebut sebelum membuat jadwal baru dari nol.</p>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-dark px-4" data-bs-dismiss="modal">Mengerti</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>