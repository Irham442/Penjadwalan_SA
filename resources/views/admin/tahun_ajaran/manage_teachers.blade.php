<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Guru Aktif - Sistem Penjadwalan</title>
    
    <!-- CSS Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body { background-color: #f8f9fa; }
        .card { border-radius: 12px; }
        .guru-card { transition: all 0.2s; cursor: pointer; }
        .guru-card:hover { transform: translateY(-3px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
        .form-check-input { cursor: pointer; }
    </style>
</head>
<body>

    <!-- NAVBAR (Sama seperti Index) -->
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
        
        <!-- Header Halaman -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 fw-bold text-gray-800">Plotting Guru Aktif</h1>
                <p class="text-muted small mb-0">
                    Periode: <span class="badge bg-primary">{{ $tahun->tahun }}</span> 
                    Semester: <span class="badge bg-info text-dark">{{ $tahun->semester }}</span>
                </p>
            </div>
            <a href="{{ route('admin.tahun_ajaran.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <!-- Card Utama -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white py-3">
                <h6 class="m-0 fw-bold">
                    <i class="bi bi-person-check-fill me-2"></i> Pilih Guru yang Mengajar
                </h6>
            </div>
            
            <div class="card-body">
                <!-- Info Alert -->
                <div class="alert alert-info border-0 shadow-sm d-flex align-items-center mb-4">
                    <i class="bi bi-info-circle-fill fs-4 me-3"></i>
                    <div>
                        <strong>Petunjuk:</strong> Centang guru yang <b>AKTIF</b> mengajar pada tahun ajaran ini.<br>
                        Hanya guru yang dicentang di sini yang namanya akan muncul saat Anda mengisi <b>Beban Ajar</b> nanti.
                    </div>
                </div>

                <form action="{{ route('admin.tahun_ajaran.update_teachers', $tahun->id) }}" method="POST">
                    @csrf
                    
                    <!-- Tombol Select All -->
                    <div class="card bg-light border-0 mb-4">
                        <div class="card-body py-2">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="checkAll" style="transform: scale(1.2);">
                                <label class="form-check-label fw-bold ms-2 pt-1" for="checkAll" style="cursor: pointer;">
                                    Pilih Semua Guru (Check All)
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Grid Daftar Guru -->
                    <div class="row">
                        @foreach($gurus as $guru)
                        <div class="col-md-3 col-sm-6 mb-3">
                            <!-- Card Guru Kecil -->
                            <div class="card h-100 shadow-sm border-0 border-start border-4 border-primary guru-card">
                                <div class="card-body position-relative">
                                    <div class="d-flex align-items-start">
                                        <div class="form-check">
                                            <input class="form-check-input guru-checkbox" 
                                                   type="checkbox" 
                                                   name="guru_ids[]" 
                                                   value="{{ $guru->id_guru }}" 
                                                   id="guru_{{ $guru->id_guru }}"
                                                   style="transform: scale(1.3); margin-top: 5px;"
                                                   {{ in_array($guru->id_guru, $activeGuruIds) ? 'checked' : '' }}>
                                        </div>
                                        <label class="ms-3 w-100" for="guru_{{ $guru->id_guru }}" style="cursor: pointer;">
                                            <span class="d-block fw-bold text-dark">{{ $guru->nama }}</span>
                                            <small class="text-muted d-block mt-1">
                                                <i class="bi bi-card-heading me-1"></i>
                                                {{ $guru->nip ? $guru->nip : 'NIP: -' }}
                                            </small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Tombol Simpan (Sticky Bottom Style) -->
                    <div class="d-grid gap-2 mt-4 pt-3 border-top">
                        <button type="submit" class="btn btn-success btn-lg shadow">
                            <i class="bi bi-save me-2"></i> Simpan Data Guru Aktif
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JS Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Script Check All -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkAll = document.getElementById('checkAll');
            if(checkAll){
                checkAll.addEventListener('change', function() {
                    const checkboxes = document.querySelectorAll('.guru-checkbox');
                    checkboxes.forEach(cb => cb.checked = this.checked);
                });
            }
        });
    </script>
</body>
</html>