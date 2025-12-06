<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Ruangan - Sistem Penjadwalan</title>
    
    <!-- CSS Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body { background-color: #f8f9fa; }
        .card { border-radius: 12px; border: none; }
        .form-label { font-weight: 600; color: #343a40; }
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
                <h1 class="h3 mb-0 fw-bold text-gray-800">Edit Ruangan</h1>
                <p class="text-muted small mb-0">Perbarui data ruangan dan kapasitasnya.</p>
            </div>
            <a href="{{ route('admin.ruangan.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <!-- CARD FORM -->
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 fw-bold text-primary">
                    <i class="bi bi-pencil-square me-2"></i> Form Edit Ruangan
                </h6>
            </div>
            
            <div class="card-body p-4">
                {{-- Form Update --}}
                <form action="{{ route('admin.ruangan.update', ['ruangan' => $ruangan->id_ruangan]) }}" method="POST">
                    @csrf
                    @method('PUT') <!-- Method Spoofing untuk Update -->

                    <div class="mb-3">
                        <label for="nama_ruangan" class="form-label">Nama Ruangan <span class="text-danger">*</span></label>
                        <input type="text" name="nama_ruangan" id="nama_ruangan" class="form-control" required value="{{ $ruangan->nama_ruangan }}" placeholder="Contoh: Lab Komputer 1">
                    </div>

                    <div class="mb-4">
                        <label for="kapasitas" class="form-label">Kapasitas (Kursi) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="kapasitas" id="kapasitas" class="form-control" required min="1" value="{{ $ruangan->kapasitas }}">
                            <span class="input-group-text">Siswa</span>
                        </div>
                        <div class="form-text">Masukkan jumlah maksimal siswa yang dapat ditampung.</div>
                    </div>
                    
                    <hr>

                    <!-- Tombol Aksi -->
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.ruangan.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-lg me-1"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> Update Data
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