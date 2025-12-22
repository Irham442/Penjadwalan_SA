<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Persetujuan - Sistem Penjadwalan</title>
    
    <!-- CSS Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .table thead { background-color: #343a40; color: white; }
        .btn-sm { border-radius: 6px; padding: 5px 12px; }
        
        /* Navbar khusus Approval (Warna Gelap/Ungu biar beda) */
        .navbar-custom { background-color: #4a148c; } 
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom mb-4 shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">
                <i class="bi bi-shield-check me-2"></i> Panel Persetujuan
            </a>
            <div class="d-flex align-items-center gap-3">
                <span class="text-white d-none d-md-block">Halo, {{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm rounded-pill px-3">
                        Logout <i class="bi bi-box-arrow-right ms-1"></i>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- KONTEN UTAMA -->
    <div class="container pb-5">
        
        <!-- Welcome Banner -->
        <div class="card bg-white mb-4">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <h2 class="fw-bold text-gray-800 mb-1">Validasi Jadwal</h2>
                    <p class="text-muted mb-0">Tinjau dan setujui draft jadwal yang diajukan oleh Tim Kurikulum.</p>
                </div>
                <div class="d-none d-md-block text-primary opacity-25">
                    <i class="bi bi-calendar-check-fill" style="font-size: 3rem;"></i>
                </div>
            </div>
        </div>

        <!-- Tabel Daftar Draft -->
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 fw-bold text-primary">
                    <i class="bi bi-list-task me-2"></i> Daftar Pengajuan Menunggu Persetujuan
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="text-center">
                            <tr>
                                <th width="5%" class="py-3">No</th>
                                <th width="20%">Periode Akademik</th>
                                <th width="15%">Semester</th>
                                <th width="25%">Waktu Pengajuan</th>
                                <th width="20%">Status Saat Ini</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jadwalMenunggu as $jadwal)
                                <tr>
                                    <td class="text-center fw-bold">{{ $loop->iteration }}</td>
                                    
                                    <td class="text-center">
                                        <span class="fw-bold text-dark">{{ $jadwal->tahun_ajaran }}</span>
                                    </td>
                                    
                                    <td class="text-center">
                                        @if($jadwal->semester == 'Ganjil')
                                            <span class="badge bg-primary rounded-pill px-3">Ganjil</span>
                                        @else
                                            <span class="badge bg-info text-dark rounded-pill px-3">Genap</span>
                                        @endif
                                    </td>
                                    
                                    <td class="text-center text-muted">
                                        <i class="bi bi-clock me-1"></i> {{ $jadwal->created_at->format('d M Y, H:i') }}
                                        <br>
                                        <small>{{ $jadwal->created_at->diffForHumans() }}</small>
                                    </td>

                                    <td class="text-center">
                                        <span class="badge bg-warning text-dark border border-warning">
                                            <i class="bi bi-hourglass-split me-1"></i> Menunggu Review
                                        </span>
                                    </td>
                                    
                                    <td class="text-center">
                                        <a href="{{ route('admin.jadwal.show', $jadwal->id) }}" class="btn btn-primary btn-sm shadow-sm">
                                            <i class="bi bi-eye-fill me-1"></i> Periksa
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <img src="https://cdn-icons-png.flaticon.com/512/11435/11435061.png" width="100" class="mb-3 opacity-50" alt="Empty">
                                        <h5 class="text-muted">Tidak ada pengajuan baru</h5>
                                        <p class="text-secondary small mb-0">Semua jadwal sudah diproses atau belum ada yang diajukan.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


            <!-- [BARU] PANEL MONITORING KETERSEDIAAN GURU -->
        <div class="card shadow-sm border-0 mb-4 border-start border-4 border-info">
            <div class="card-header bg-white fw-bold py-3 d-flex justify-content-between align-items-center">
                <span><i class="bi bi-eye-fill text-info me-2"></i> Monitoring Ketersediaan Guru</span>
                <span class="badge bg-info text-dark">{{ count($rekapKetersediaan ?? []) }} Guru Menginput</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th class="ps-4">Nama Guru</th>
                                <th>Jadwal Tidak Bisa (Blocked)</th>
                                <th>Total Jam</th>
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
                                        {{-- Hitung kasar total jam yang di-block --}}
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
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <i class="bi bi-check-circle-fill text-success fs-4 d-block mb-1"></i>
                                        Belum ada guru yang mengajukan jam berhalangan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- JS Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>