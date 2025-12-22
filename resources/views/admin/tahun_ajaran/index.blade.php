<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Tahun Ajaran - Sistem Penjadwalan</title>
    
    <!-- CSS Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body { background-color: #f8f9fa; }
        .card { border-radius: 12px; border: none; }
        .table-hover tbody tr:hover { background-color: #f1f3f5; }
        
        /* Animasi kecil untuk badge */
        .badge { transition: all 0.2s; }
        .badge:hover { transform: scale(1.05); }

        /* Style Tambahan untuk Navbar */
        .navbar-nav .nav-link {
            transition: all 0.3s ease;
            border-bottom: 2px solid transparent;
        }
        .navbar-nav .nav-link:hover {
            color: #ffc107 !important; /* Kuning Warning */
            transform: translateY(-2px);
        }
        .navbar-nav .nav-link.active {
            color: #ffc107 !important;
            border-bottom: 2px solid #ffc107;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <!-- NAVBAR START -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow sticky-top mb-4">
        <div class="container">
            <!-- BRAND LOGO -->
            <a class="navbar-brand fw-bold" href="{{ route('admin.dashboard') }}">
                <i class="bi bi-calendar-week me-2"></i> SIP JADWAL
            </a>
    
            <!-- TOGGLER (HAMBURGER MENU UNTUK HP) -->
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
    
            <!-- MENU ITEMS -->
            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    
                    <!-- Dashboard -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                            <i class="bi bi-speedometer2 me-1"></i> Dashboard
                        </a>
                    </li>
    
                    <!-- Data Master: Tahun Ajar (Current Page) -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.tahun_ajaran.*') ? 'active' : '' }}" href="{{ route('admin.tahun_ajaran.index') }}">
                            <i class="bi bi-calendar-event me-1"></i> Tahun Ajar
                        </a>
                    </li>
    
                    <!-- Mapel -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.mapel.*') ? 'active' : '' }}" href="{{ route('admin.mapel.index') }}">
                            <i class="bi bi-journal-bookmark me-1"></i> Mapel
                        </a>
                    </li>

                    <!-- Ruangan (Ditambahkan sesuai route resource 'ruangan') -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.ruangan.*') ? 'active' : '' }}" href="{{ route('admin.ruangan.index') }}">
                            <i class="bi bi-building me-1"></i> Ruangan
                        </a>
                    </li>
    
                    <!-- Guru -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.guru.*') ? 'active' : '' }}" href="{{ route('admin.guru.index') }}">
                            <i class="bi bi-person-badge me-1"></i> Guru
                        </a>
                    </li>
    
                    <!-- Kelas -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.kelas.*') ? 'active' : '' }}" href="{{ route('admin.kelas.index') }}">
                            <i class="bi bi-people me-1"></i> Kelas
                        </a>
                    </li>
    
                    <!-- Beban Ajar (Diupdate menggunakan hyphen '-' sesuai default resource route) -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.beban-ajar.*') ? 'active' : '' }}" href="{{ route('admin.beban-ajar.index') }}">
                            <i class="bi bi-briefcase me-1"></i> Beban Ajar
                        </a>
                    </li>
    
                </ul>
    
                <!-- BAGIAN KANAN (USER & LOGOUT) -->
                <div class="d-flex align-items-center mt-3 mt-lg-0">
                    <!-- Menampilkan Nama User Login -->
                    <span class="text-white-50 me-3 d-none d-lg-block small">
                        Halo, {{ Auth::user()->name ?? 'Admin' }}
                    </span>
    
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3 w-100 w-lg-auto">
                            <i class="bi bi-box-arrow-right me-1"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
    <!-- NAVBAR END -->

    <!-- KONTEN UTAMA -->
    <div class="container">
        
        <!-- Header Halaman -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 fw-bold text-gray-800">
                    <i class="bi bi-calendar-range text-primary me-2"></i>Data Tahun Ajaran
                </h1>
                <p class="text-muted small mb-0 mt-1">Kelola periode akademik dan atur semester yang aktif.</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
            </a>
        </div>
        
        <!-- Pesan Sukses -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 border-start border-success border-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                    <div>
                        <strong>Berhasil!</strong> {{ session('success') }}
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        <!-- Pesan Error -->
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 border-start border-danger border-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                    <div>
                        <strong>Terjadi Kesalahan!</strong> Silakan periksa inputan Anda.
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    
        <!-- Card Tabel -->
        <div class="card shadow-sm">
            <!-- Card Header -->
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <div class="text-primary fw-bold">
                    <i class="bi bi-table me-1"></i> Daftar Periode Akademik
                </div>
                <button type="button" class="btn btn-primary btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Periode
                </button>
            </div>
    
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle mb-0">
                        <thead class="table-light text-center">
                            <tr>
                                <th width="5%" class="text-secondary text-uppercase small">No</th>
                                <th width="25%" class="text-secondary text-uppercase small">Tahun Ajaran</th>
                                <th width="15%" class="text-secondary text-uppercase small">Semester</th>
                                <th width="15%" class="text-secondary text-uppercase small">Status</th>
                                <th width="30%" class="text-secondary text-uppercase small">Aksi & Pengaturan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tahunAjarans as $index => $item)
                            <!-- Baris hijau lembut jika aktif -->
                            <tr class="{{ $item->is_active ? 'table-success bg-opacity-10' : '' }}">
                                <td class="text-center fw-bold text-secondary">{{ $index + 1 }}</td>
                                
                                <td class="fw-bold text-center text-dark">
                                    {{ $item->tahun }}
                                </td>
                                
                                <td class="text-center">
                                    @if($item->semester == 'Ganjil')
                                        <span class="badge bg-info text-dark bg-opacity-25 border border-info">Ganjil</span>
                                    @else
                                        <span class="badge bg-warning text-dark bg-opacity-25 border border-warning">Genap</span>
                                    @endif
                                </td>
                                
                                <td class="text-center">
                                    @if($item->is_active)
                                        <span class="badge rounded-pill bg-success shadow-sm px-3 py-2">
                                            <i class="bi bi-check-circle-fill me-1"></i> SEDANG AKTIF
                                        </span>
                                    @else
                                        <span class="badge rounded-pill bg-secondary bg-opacity-50 text-dark px-3 py-2">
                                            Non-Aktif
                                        </span>
                                    @endif
                                </td>
                                
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        
                                        {{-- Tombol Set Aktif --}}
                                        @if(!$item->is_active)
                                        <form action="{{ route('admin.tahun_ajaran.activate', $item->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-success btn-sm" title="Jadikan Periode Aktif">
                                                <i class="bi bi-power"></i> Aktifkan
                                            </button>
                                        </form>
                                        @endif
    
                                        {{-- Tombol Kelola Guru (Diaktifkan karena route tersedia) --}}
                                        <a href="{{ route('admin.tahun_ajaran.manage_teachers', $item->id) }}" 
                                            class="btn btn-info btn-sm text-white" 
                                            title="Pilih Guru yang Mengajar">
                                                <i class="bi bi-person-lines-fill"></i> Guru
                                        </a> 
    
                                        {{-- Tombol Hapus --}}
                                        <form action="{{ route('admin.tahun_ajaran.destroy', $item->id) }}" method="POST" onsubmit="return confirm('PERINGATAN: Menghapus tahun ajaran ini akan menghapus semua data jadwal dan beban ajar yang terkait. Lanjutkan?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus Data">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <div class="mb-2">
                                        <i class="bi bi-calendar-x fs-1 text-secondary opacity-50"></i>
                                    </div>
                                    <h6 class="fw-bold">Belum ada data Tahun Ajaran</h6>
                                    <p class="small mb-0">Silakan tambahkan periode akademik baru.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3 p-3 bg-light rounded border border-secondary border-opacity-10 text-muted small">
                    <div class="d-flex align-items-start">
                        <i class="bi bi-info-circle-fill text-primary me-2 mt-1"></i> 
                        <div>
                            <strong>Catatan Sistem:</strong>
                            <ul class="mb-0 ps-3 mt-1">
                                <li>Hanya satu Tahun Ajaran yang bisa berstatus <strong>AKTIF</strong> dalam satu waktu.</li>
                                <li>Mengaktifkan tahun ajaran baru akan otomatis menonaktifkan tahun ajaran sebelumnya.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- MODAL TAMBAH -->
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="modalTambahLabel">
                        <i class="bi bi-calendar-plus me-2"></i>Tambah Tahun Ajaran
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <form action="{{ route('admin.tahun_ajaran.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary">Tahun Ajaran</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-calendar3"></i></span>
                                <input type="text" class="form-control" name="tahun" placeholder="Contoh: 2025/2026" required>
                            </div>
                            <div class="form-text small">Format: TAHUN/TAHUN (Contoh: 2024/2025)</div>
                        </div>
    
                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary">Semester</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-bar-chart-steps"></i></span>
                                <select class="form-select" name="semester" required>
                                    <option value="" disabled selected>-- Pilih Semester --</option>
                                    <option value="Ganjil">Ganjil</option>
                                    <option value="Genap">Genap</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer bg-light px-4">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JS Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>