<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Ruangan - Sistem Penjadwalan</title>
    
    <!-- CSS Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body { background-color: #f8f9fa; }
        .card { border-radius: 12px; border: none; }
        .table thead { background-color: #343a40; color: white; }
        .btn-sm { border-radius: 6px; }
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
                <h1 class="h3 mb-0 fw-bold text-gray-800">Manajemen Ruangan</h1>
                <p class="text-muted small mb-0">Daftar lokasi fisik (kelas/lab) untuk kegiatan belajar mengajar.</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Menu Utama
            </a>
        </div>

        <!-- PESAN SUKSES -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- CARD TABEL -->
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 fw-bold text-primary">
                    <i class="bi bi-door-open me-2"></i> Daftar Ruangan Tersedia
                </h6>
                <a href="{{ route('admin.ruangan.create') }}" class="btn btn-primary btn-sm shadow-sm">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Ruangan
                </a>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle">
                        <thead class="text-center table-dark">
                            <tr>
                                <th width="10%">No</th>
                                <th width="45%">Nama Ruangan</th>
                                <th width="20%">Kapasitas</th>
                                <th width="25%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($semuaRuangan as $ruangan)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    
                                    <td class="fw-bold text-dark">
                                        {{ $ruangan->nama_ruangan }}
                                    </td>
                                    
                                    <td class="text-center">
                                        <span class="badge bg-info text-dark">
                                            <i class="bi bi-people-fill me-1"></i> {{ $ruangan->kapasitas }} Siswa
                                        </span>
                                    </td>
                                    
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <!-- Tombol Edit -->
                                            <a href="{{ route('admin.ruangan.edit', ['ruangan' => $ruangan->id_ruangan]) }}" class="btn btn-sm btn-warning text-white" title="Edit Data">
                                                <i class="bi bi-pencil-square"></i> Edit
                                            </a>

                                            <!-- Tombol Hapus -->
                                            <form action="{{ route('admin.ruangan.destroy', ['ruangan' => $ruangan->id_ruangan]) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus ruangan ini? Data jadwal yang menggunakan ruangan ini mungkin akan terganggu.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus Data">
                                                    <i class="bi bi-trash"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                        Belum ada data ruangan yang terdaftar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                {{-- Jika menggunakan pagination, bisa tambahkan di sini --}}
                {{-- <div class="mt-3"> {{ $semuaRuangan->links() }} </div> --}}
            </div>
        </div>
    </div>

    <!-- JS Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>