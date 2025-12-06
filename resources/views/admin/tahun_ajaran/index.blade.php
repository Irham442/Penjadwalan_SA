<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Tahun Ajaran - Sistem Penjadwalan</title>
    
    <!-- CSS Bootstrap 5 & Icons (Sama seperti Dashboard) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body { background-color: #f8f9fa; }
        .card { border-radius: 12px; }
    </style>
</head>
<body>

    <!-- NAVBAR (Dicopy dari Dashboard agar seragam) -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
                <i class="bi bi-calendar-week me-2"></i> Dashboard Penjadwalan
            </a>
            <div class="d-flex">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="bi bi-box-arrow-right me-1"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- KONTEN UTAMA -->
    <div class="container">
        
        <!-- Header Halaman -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 fw-bold text-gray-800">Data Tahun Ajaran</h1>
                <p class="text-muted small mb-0">Kelola periode akademik dan guru yang aktif.</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
            </a>
        </div>
        
        <!-- Pesan Sukses -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    
        <!-- Card Tabel -->
        <div class="card shadow-sm border-0">
            <!-- Card Header -->
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <div class="text-primary fw-bold">
                    <i class="bi bi-table me-1"></i> Daftar Periode Akademik
                </div>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Periode
                </button>
            </div>
    
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle">
                        <thead class="table-light text-center">
                            <tr>
                                <th width="5%">No</th>
                                <th width="25%">Tahun Ajaran</th>
                                <th width="15%">Semester</th>
                                <th width="15%">Status</th>
                                <th width="30%">Aksi & Pengaturan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tahunAjarans as $index => $item)
                            <!-- Baris hijau jika aktif -->
                            <tr class="{{ $item->is_active ? 'table-success' : '' }}">
                                <td class="text-center">{{ $index + 1 }}</td>
                                
                                <td class="fw-bold text-center text-primary">
                                    {{ $item->tahun }}
                                </td>
                                
                                <td class="text-center">
                                    {{ $item->semester }}
                                </td>
                                
                                <td class="text-center">
                                    @if($item->is_active)
                                        <span class="badge rounded-pill bg-success">
                                            <i class="bi bi-check-circle me-1"></i> SEDANG AKTIF
                                        </span>
                                    @else
                                        <span class="badge rounded-pill bg-secondary">
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
    
                                        {{-- Tombol Kelola Guru --}}
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
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Belum ada data Tahun Ajaran.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3 text-muted small">
                    <i class="bi bi-info-circle me-1"></i> 
                    <strong>Catatan:</strong> Hanya satu Tahun Ajaran yang bisa <strong>AKTIF</strong> dalam satu waktu.
                </div>
            </div>
        </div>
    </div>
    
    <!-- MODAL TAMBAH -->
    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-calendar-plus me-2"></i>Tambah Tahun Ajaran
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                
                <form action="{{ route('admin.tahun_ajaran.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tahun Ajaran</label>
                            <input type="text" class="form-control" name="tahun" placeholder="Contoh: 2025/2026" required>
                        </div>
    
                        <div class="mb-3">
                            <label class="form-label fw-bold">Semester</label>
                            <select class="form-select" name="semester" required>
                                <option value="Ganjil">Ganjil</option>
                                <option value="Genap">Genap</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JS Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>