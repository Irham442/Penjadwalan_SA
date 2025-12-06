<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Beban Ajar - Sistem Penjadwalan</title>
    
    <!-- CSS Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body { background-color: #f8f9fa; }
        .card { border-radius: 12px; border: none; }
        .table thead { background-color: #343a40; color: white; }
        .info-banner { border-left: 5px solid; }
    </style>
</head>
<body>

    <!-- NAVBAR -->
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
    <div class="container pb-5">

        <!-- HEADER & TOMBOL KEMBALI -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 fw-bold text-gray-800">Manajemen Beban Ajar</h1>
                <p class="text-muted small mb-0">Atur pembagian tugas mengajar guru, mapel, dan kelas.</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Menu Utama
            </a>
        </div>

        <!-- FITUR PENGAMAN HUMAN ERROR -->
        @if($tahunAktif)
            <div class="alert alert-primary shadow-sm d-flex align-items-center info-banner" role="alert" style="border-color: #0d6efd;">
                <i class="bi bi-info-circle-fill fs-3 me-3"></i>
                <div>
                    <strong>PERHATIAN:</strong> Anda sedang mengelola data untuk Periode Akademik:
                    <br>
                    <span class="fs-5 fw-bold">{{ $tahunAktif->tahun }} - Semester {{ $tahunAktif->semester }}</span>
                    <div class="small mt-1 text-primary-emphasis">
                        Pastikan data yang Anda input atau hapus memang ditujukan untuk periode ini.
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-danger shadow-sm d-flex align-items-center info-banner" role="alert" style="border-color: #dc3545;">
                <i class="bi bi-exclamation-triangle-fill fs-3 me-3"></i>
                <div>
                    <strong>PERINGATAN KERAS:</strong> Tidak ada Tahun Ajaran yang Aktif!
                    <br>
                    Silakan setel Tahun Ajaran aktif terlebih dahulu sebelum menginput Beban Ajar untuk menghindari kesalahan data.
                    <br>
                    <a href="{{ route('admin.tahun_ajaran.index') }}" class="btn btn-sm btn-light mt-2 fw-bold text-danger">Kelola Tahun Ajaran</a>
                </div>
            </div>
        @endif

        <!-- CARD TABEL -->
        <div class="card shadow-sm mt-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 fw-bold text-primary">
                    <i class="bi bi-table me-2"></i> Daftar Beban Mengajar
                </h6>
                
                {{-- Tombol Tambah hanya muncul jika ada tahun aktif --}}
                @if($tahunAktif)
                <a href="{{ route('admin.beban-ajar.create') }}" class="btn btn-primary btn-sm shadow-sm">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Beban Ajar Baru
                </a>
                @else
                <button class="btn btn-secondary btn-sm" disabled title="Setel tahun ajaran dulu">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Beban Ajar Baru
                </button>
                @endif
            </div>

            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle">
                        <thead class="text-center">
                            <tr>
                                <th width="5%">No</th>
                                <th width="25%">Nama Guru</th>
                                <th width="25%">Mata Pelajaran</th>
                                <th width="15%">Kelas</th>
                                <th width="10%">Jam/Minggu</th>
                                <th width="20%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($bebanAjars as $bebanAjar)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration + $bebanAjars->firstItem() - 1 }}</td>
                                    
                                    <td class="fw-bold text-dark">
                                        {{ $bebanAjar->guru->nama ?? 'Data Guru Terhapus' }}
                                    </td>
                                    
                                    <td>
                                        {{ $bebanAjar->mapel->nama_mapel ?? 'Data Mapel Terhapus' }}
                                        @if(isset($bebanAjar->mapel->kode_mapel))
                                            <br><small class="text-muted">{{ $bebanAjar->mapel->kode_mapel }}</small>
                                        @endif
                                    </td>
                                    
                                    <td class="text-center">
                                        <span class="badge bg-info text-dark">{{ $bebanAjar->kelas->nama_kelas ?? 'N/A' }}</span>
                                    </td>
                                    
                                    <td class="text-center fw-bold">
                                        {{ $bebanAjar->jumlah_jam_seminggu }} JP
                                    </td>
                                    
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <a href="{{ route('admin.beban-ajar.edit', $bebanAjar->id) }}" class="btn btn-sm btn-warning text-white" title="Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            
                                            <form action="{{ route('admin.beban-ajar.destroy', $bebanAjar->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus beban ajar ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="bi bi-clipboard-x fs-1 d-block mb-2"></i>
                                        Belum ada data beban ajar untuk periode ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $bebanAjars->links() }}
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>