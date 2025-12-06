<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Jadwal #{{ $jadwal->id }}</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', sans-serif; font-size: 0.85rem; }
        
        /* Layout Tabel Lebar */
        .table-container { 
            overflow-x: auto; 
            background: white; 
            border: 1px solid #dee2e6; 
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            max-height: 85vh; /* Agar bisa scroll vertikal juga */
        }
        
        .schedule-table { 
            width: 100%; 
            border-collapse: separate; 
            border-spacing: 0; 
            min-width: 2000px; /* Paksa lebar agar tidak gepeng */
        }
        
        /* Sticky Header (Jam) */
        .schedule-table thead th { 
            position: sticky; top: 0; z-index: 100;
            background-color: #2c3e50; color: white; 
            text-align: center; vertical-align: middle; 
            padding: 8px; border: 1px solid #34495e;
            height: 50px;
        }
        
        /* Sticky Kolom Hari (Kiri) */
        .day-column { 
            position: sticky; left: 0; z-index: 50;
            background-color: #ecf0f1; font-weight: 800; 
            text-align: center; vertical-align: middle; 
            width: 80px; border-right: 2px solid #bdc3c7; border-bottom: 1px solid #dee2e6;
            text-transform: uppercase; color: #2c3e50;
        }
        
        /* Corner Cell */
        .corner-cell { position: sticky; left: 0; top: 0; z-index: 150; background-color: #212529 !important; border: 1px solid #000; }

        /* Sel Jadwal */
        .schedule-table td { 
            vertical-align: top; padding: 4px; 
            border: 1px solid #dee2e6; background-color: white;
            min-width: 160px; /* Lebar minimal per jam */
        }

        /* Kolom Istirahat */
        .istirahat-col { background-color: #95a5a6; }
        .istirahat-content {
            writing-mode: vertical-rl; transform: rotate(180deg); 
            text-align: center; font-weight: bold; color: white; 
            height: 100px; margin: auto; letter-spacing: 3px;
        }

        /* Kartu Mapel Kecil */
        .mini-card {
            background-color: #f8f9fa;
            border-left: 3px solid #0d6efd;
            padding: 4px 6px;
            margin-bottom: 3px;
            border-radius: 4px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            font-size: 0.75rem;
            line-height: 1.2;
            transition: transform 0.1s;
        }
        .mini-card:hover { transform: scale(1.05); z-index: 10; position: relative; box-shadow: 0 4px 8px rgba(0,0,0,0.15); }
        
        .mini-card strong { color: #0d6efd; display: block; } /* Kelas */
        .mini-card span { color: #333; font-weight: 600; display: block;} /* Mapel */
        .mini-card small { color: #666; font-style: italic; display: block; font-size: 0.7rem; } /* Guru */

        /* Warna Kategori */
        .cat-produktif { background: #eafaf1; border-left-color: #27ae60; }
        .cat-produktif strong { color: #27ae60; }
        
        .cat-adaptif { background: #ebf5fb; border-left-color: #2980b9; }
        .cat-adaptif strong { color: #2980b9; }
        
        .cat-umum { background: #fef5e7; border-left-color: #e67e22; }
        .cat-umum strong { color: #e67e22; }

    </style>
</head>
<body>

    <!-- NAVBAR DENGAN LOGIKA ROLE -->
    <nav class="navbar navbar-dark bg-dark px-4 mb-3 sticky-top shadow-sm">
        <span class="navbar-brand mb-0 h1"><i class="fas fa-calendar-alt me-2"></i> Master Jadwal</span>
        <div class="d-flex gap-2 align-items-center">
            
            <!-- Info Badge -->
            <span class="badge bg-primary">{{ $jadwal->tahun_ajaran }}</span>
            <span class="badge bg-info text-dark">{{ $jadwal->semester }}</span>
            
            @if($jadwal->status == 'DRAFT')
                <span class="badge bg-secondary">DRAFT</span>
            @elseif($jadwal->status == 'MENUNGGU_PERSETUJUAN')
                <span class="badge bg-warning text-dark">MENUNGGU APPROVAL</span>
            @elseif($jadwal->status == 'DIPUBLIKASIKAN')
                <span class="badge bg-success">PUBLISHED</span>
            @elseif($jadwal->status == 'REVISI')
                <span class="badge bg-danger">REVISI</span>
            @endif
            
            <!-- Tombol Kembali (Umum) -->
             
            <!-- {{-- LOGIKA ADMIN KURIKULUM --}} -->
            @if(Auth::user()->role == 'Kurikulum')
                
                <!-- {{-- 1. Tombol Kembali (Selalu Muncul untuk Kurikulum, apapun statusnya) --}} -->
                <a href="{{ route('admin.dashboard') }}" class="btn btn-light btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Menu Utama
                </a>

                <!-- {{-- 2. Tombol Kirim Approval (Hanya Muncul jika status DRAFT) --}} -->
                @if($jadwal->status == 'DRAFT')
                    <form action="{{ route('admin.jadwal.submit', $jadwal->id) }}" method="POST" onsubmit="return confirm('Kirim jadwal untuk persetujuan?');">
                        @csrf
                        <button class="btn btn-primary btn-sm fw-bold">
                            <i class="fas fa-paper-plane me-1"></i> Kirim Approval
                        </button>
                    </form>
                @endif

            @endif

            <!-- ================= LOGIKA SUPER ADMIN / KEPSEK ================= -->
            <!-- Tombol muncul jika User = Super Admin DAN Status = Menunggu Persetujuan -->
            @if(Auth::user()->role == 'Super Admin' && $jadwal->status == 'MENUNGGU_PERSETUJUAN')
                <a href="{{ route('approval.dashboard') }}" class="btn btn-light btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Menu Utama
                </a>
                <div class="vr bg-white mx-2 opacity-50"></div>
                
                <!-- Tombol Minta Revisi (Trigger Modal) -->
                <button type="button" class="btn btn-warning btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#rejectModal">
                    <i class="fas fa-undo me-1"></i> Minta Revisi
                </button>

                <!-- Tombol Setujui (Trigger Modal) -->
                <button type="button" class="btn btn-success btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#approveModal">
                    <i class="fas fa-check-circle me-1"></i> Setujui
                </button>
            @endif

        </div>
    </nav>

    <div class="container-fluid px-4 pb-4">
        
        <!-- Alert Notifikasi -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Alert Status Revisi -->
        @if($jadwal->status == 'REVISI')
            <div class="alert alert-danger py-2 d-flex align-items-center">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <div>
                    <strong>Perlu Revisi:</strong> {{ $jadwal->catatan_revisi }}
                </div>
            </div>
        @endif

        <!-- TABEL JADWAL (CORE CONTENT) -->
        <div class="table-container">
            <table class="schedule-table">
                <thead>
                    <tr>
                        <th class="day-column corner-cell">HARI \ JAM</th>
                        
                        <!-- Header Jam -->
                        @foreach($jamHeaders as $index => $waktu)
                            
                            {{-- LOGIKA BARU: Cek jika ini adalah SLOT PERTAMA (Index 0) dan Non-KBM --}}
                            @if($index == 0 && $waktu->bisa_dijadwalkan == 0)
                                <th style="background-color: #34495e; min-width: 100px;">
                                    <div>PEMBIASAAN</div>
                                    <small style="font-weight: normal; opacity: 0.8;">
                                        06:30 - 07:10
                                    </small>
                                </th>
                            
                            {{-- Slot Istirahat Biasa (Selain yang pertama) --}}
                            @elseif($waktu->bisa_dijadwalkan == 0)
                                <th class="istirahat-col" style="min-width: 50px;">IST</th>
                            
                            {{-- Slot Jam Pelajaran (JP) --}}
                            @else
                                <th>
                                    <div>JP {{ $index + 1 }}</div>
                                    <small style="font-weight: normal; opacity: 0.8;">
                                        {{ date('H:i', strtotime($waktu->jam_mulai)) }} - {{ date('H:i', strtotime($waktu->jam_selesai)) }}
                                    </small>
                                </th>
                            @endif

                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @php $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat']; @endphp
                    
                    @foreach($days as $hari)
                        <tr>
                            <!-- Kolom Hari -->
                            <td class="day-column">{{ $hari }}</td>

                            <!-- Loop Kolom Jam -->
                            @foreach($jamHeaders as $index => $waktuHeader)
                                
                                {{-- LOGIKA BARU: Body untuk Slot Pertama (Kosongkan/Isi Teks Statis) --}}
                                @if($index == 0 && $waktuHeader->bisa_dijadwalkan == 0)
                                    <td class="text-center align-middle text-muted small bg-light" style="border: 1px solid #dee2e6;">
                                        @if($hari == 'Senin') Upacara
                                        @endif
                                    </td>

                                {{-- Body Istirahat Biasa --}}
                                @elseif($waktuHeader->bisa_dijadwalkan == 0)
                                    <td class="istirahat-col">
                                        <div class="istirahat-content">ISTIRAHAT</div>
                                    </td>

                                {{-- Body Jam Pelajaran --}}
                                @else
                                    <td>
                                        @if(isset($jadwalGrid[$hari][$index]))
                                            @foreach($jadwalGrid[$hari][$index] as $item)
                                                @php
                                                    $kategori = $item->mapel->kategori ?? 'Umum';
                                                    $cssClass = match($kategori) {
                                                        'Produktif' => 'cat-produktif',
                                                        'Adaptif' => 'cat-adaptif',
                                                        default => 'cat-umum'
                                                    };
                                                @endphp

                                                <div class="mini-card {{ $cssClass }}">
                                                    <strong>{{ $item->kelas->nama_kelas ?? '?' }}</strong>
                                                    <span>{{ $item->mapel->kode_mapel ?? Str::limit($item->mapel->nama_mapel, 15) }}</span>
                                                    <small>{{ Str::limit($item->guru->nama ?? '?', 15) }}</small>
                                                    @if($item->ruangan)
                                                        <div class="text-end mt-1" style="font-size: 0.6rem; color:#000;">
                                                            <i class="fas fa-door-open"></i> {{ $item->ruangan->nama_ruangan }}
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        @endif
                                    </td>
                                @endif

                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="mt-3 text-muted small">
            <i class="fas fa-info-circle me-1"></i> 
            <strong>Legenda:</strong> 
            <span class="badge text-dark border cat-produktif">Produktif</span> 
            <span class="badge text-dark border cat-adaptif">Adaptif</span> 
            <span class="badge text-dark border cat-umum">Umum/Normatif</span>
        </div>
    </div>

    <!-- ================= MODAL APPROVAL SUPER ADMIN ================= -->
    <!-- Hanya dirender jika User adalah Super Admin -->
    @if(Auth::user()->role == 'Super Admin' && $jadwal->status == 'MENUNGGU_PERSETUJUAN')
        
        <!-- Modal Setujui -->
        <div class="modal fade" id="approveModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title"><i class="fas fa-check-circle me-2"></i>Konfirmasi Publikasi</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('approval.approve', $jadwal->id) }}" method="POST">
                        @csrf
                        <div class="modal-body text-center py-4">
                            <p class="mb-2 lead fw-bold">Setujui jadwal ini?</p>
                            <p class="text-muted small">
                                Status akan berubah menjadi <strong>DIPUBLIKASIKAN</strong>.<br>
                                Jadwal akan langsung tampil di dashboard guru dan siswa.
                            </p>
                        </div>
                        <div class="modal-footer justify-content-center">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success fw-bold px-4">Ya, Setujui & Terbitkan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Revisi -->
        <div class="modal fade" id="rejectModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title fw-bold"><i class="fas fa-undo me-2"></i>Permintaan Revisi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('approval.reject', $jadwal->id) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="alert alert-light border small text-muted mb-3">
                                <i class="fas fa-info-circle me-1"></i>
                                Jadwal akan dikembalikan ke Admin Kurikulum dengan status <strong>REVISI</strong>.
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Catatan Perbaikan:</label>
                                <textarea name="catatan_revisi" class="form-control" rows="5" placeholder="Contoh: Jadwal Kelas X RPL hari Senin terlalu padat, tolong geser mapel produktif..." required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-warning fw-bold">
                                <i class="fas fa-paper-plane me-1"></i> Kirim Revisi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Script Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>