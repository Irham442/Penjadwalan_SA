<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Mengajar - Portal Guru</title>
    
    <!-- CSS Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body { background-color: #f0f2f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        
        /* Kartu Jadwal */
        .schedule-card {
            border-left: 5px solid #198754; /* Garis hijau di kiri */
            border-radius: 8px;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: transform 0.2s;
        }
        .schedule-card:hover { transform: translateY(-3px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        
        /* Badge Waktu */
        .time-badge {
            background-color: #e9ecef;
            color: #495057;
            font-weight: 600;
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

    <!-- NAVBAR GURU -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">
                <i class="bi bi-mortarboard-fill me-2"></i> Portal Guru
            </a>
            <div class="d-flex align-items-center gap-3">
                <span class="text-white d-none d-md-block">Halo, {{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-light btn-sm text-success fw-bold rounded-pill px-3">
                        Logout <i class="bi bi-box-arrow-right ms-1"></i>
                    </button>
                </form>
            </div>
        </div>
    </nav>
    <div class="container mt-3">
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
</div>

    <!-- KONTEN UTAMA -->
    <div class="container pb-5">

        <!-- HEADER & FILTER -->
        <div class="row align-items-end mb-4 g-3">
            <div class="col-lg-6">
                <h2 class="fw-bold text-gray-800 mb-1">Jadwal Mengajar</h2>
                @if($jadwalAktif)
                    <p class="text-muted mb-0">
                        Periode Aktif: <span class="badge bg-success">{{ $jadwalAktif->tahun_ajaran }} ({{ $jadwalAktif->semester }})</span>
                    </p>
                @else
                    <p class="text-muted mb-0">Belum ada jadwal aktif.</p>
                @endif
            </div>

            <!-- FORM FILTER -->
            <div class="col-lg-6">
                @if($daftarPeriode->isNotEmpty())
                <form action="{{ route('teacher.dashboard') }}" method="GET">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-2">
                            <div class="row g-2 align-items-center">
                                <div class="col-5">
                                    <select name="tahun_ajaran" class="form-select form-select-sm bg-light border-0">
                                        @foreach($daftarPeriode->pluck('tahun_ajaran')->unique() as $tahun)
                                            <option value="{{ $tahun }}" {{ $selectedTahun == $tahun ? 'selected' : '' }}>
                                                {{ $tahun }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-4">
                                    <select name="semester" class="form-select form-select-sm bg-light border-0">
                                        @foreach(['Ganjil', 'Genap'] as $sem)
                                            <option value="{{ $sem }}" {{ $selectedSemester == $sem ? 'selected' : '' }}>
                                                {{ $sem }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-3">
                                    <button type="submit" class="btn btn-success btn-sm w-100 fw-bold">
                                        <i class="bi bi-search"></i> Cari
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                @endif
            </div>
        </div>

        <!-- AREA JADWAL -->
        @if($jadwalAktif && !empty($jadwalGabungan)) 
            <div class="row">
                @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari)
                    @if(isset($jadwalGabungan[$hari]))
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="d-flex align-items-center mb-3">
                                <span class="badge bg-dark rounded-pill px-3 py-2">{{ strtoupper($hari) }}</span>
                                <div class="ms-2 border-bottom flex-grow-1"></div>
                            </div>

                            @foreach($jadwalGabungan[$hari] as $blok)
                                <div class="schedule-card p-3 mb-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="fw-bold text-success mb-0">
                                            {{ $blok['mapel']->nama_mapel ?? 'Mapel Dihapus' }}
                                        </h5>
                                        <div class="time-badge">
                                            {{ date('H:i', strtotime($blok['jam_mulai'])) }} - {{ date('H:i', strtotime($blok['jam_selesai'])) }}
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center text-secondary">
                                        <i class="bi bi-geo-alt-fill me-2 text-danger"></i>
                                        <span class="fw-bold text-dark">{{ $blok['kelas']->nama_kelas ?? 'Kelas ?' }}</span>
                                    </div>
                                    <div class="mt-2 small text-muted border-top pt-2">
                                        Kode Mapel: {{ $blok['mapel']->kode_mapel ?? '-' }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @endforeach
            </div>
        @elseif($jadwalAktif)
            <!-- State Kosong (Ada jadwal periode ini, tapi guru ini tidak mengajar) -->
            <div class="text-center py-5">
                <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="150" class="mb-3 opacity-50" alt="Relax">
                <h4 class="text-muted">Tidak ada jadwal mengajar.</h4>
                <p class="text-secondary">Anda tidak memiliki jam mengajar pada periode {{ $jadwalAktif->tahun_ajaran }} ({{ $jadwalAktif->semester }}).</p>
            </div>
        @else
            <!-- State Kosong Total (Belum ada jadwal dipublish sama sekali) -->
            <div class="alert alert-warning text-center shadow-sm border-0 py-5">
                <i class="bi bi-cone-striped fs-1 d-block mb-3"></i>
                <h4>Belum Ada Jadwal Dirilis</h4>
                <p>Admin kurikulum belum mempublikasikan jadwal untuk periode ini.</p>
            </div>
        @endif

        <!-- Menu Tambahan (Input Ketersediaan) -->
        <div class="text-center mt-5">
            <p class="text-muted mb-2">Punya halangan mengajar di hari tertentu?</p>
            <a href="{{ route('guru.availability.index') }}" class="btn btn-outline-success">
                <i class="bi bi-calendar-x me-1"></i> Input Ketersediaan Waktu
            </a>
        </div>

    </div>

    <!-- JS Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>