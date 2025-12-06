<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Ketersediaan Waktu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f0f2f5; font-family: 'Segoe UI', sans-serif; }
    </style>
</head>
<body>

    <nav class="navbar navbar-dark bg-success mb-4 shadow-sm">
        <div class="container">
            <span class="navbar-brand mb-0 h1"><i class="bi bi-clock-history me-2"></i> Ketersediaan Guru</span>
            {{-- Kembali ke Dashboard Guru (menggunakan route 'teacher.dashboard') --}}
            <a href="{{ route('teacher.dashboard') }}" class="btn btn-outline-light btn-sm">Kembali ke Dashboard</a>
        </div>
    </nav>

    <div class="container">
        
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-3">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-3">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(!$tahunAktif)
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle me-2"></i> Tidak ada Tahun Ajaran aktif. Anda tidak dapat menginput ketersediaan.
            </div>
        @else

            <div class="row">
                <!-- FORM INPUT (KIRI) -->
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white fw-bold text-success py-3">
                            <i class="bi bi-plus-circle me-2"></i> Tambah Waktu Berhalangan
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-3">
                                Masukkan hari dan jam dimana Anda <strong>TIDAK BISA</strong> mengajar.
                            </p>

                            {{-- Route menggunakan 'guru.availability.store' sesuai web.php --}}
                            <form action="{{ route('guru.availability.store') }}" method="POST">
                                @csrf
                                
                                {{-- Controller Anda sudah menangani ID Guru & ID Tahun Ajaran dari Backend --}}
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold small">Hari</label>
                                    <select name="day" class="form-select" required>
                                        <option value="">Pilih Hari...</option>
                                        @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $h)
                                            <option value="{{ $h }}">{{ $h }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label fw-bold small">Jam Mulai</label>
                                        <input type="time" name="start_time" class="form-control" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label fw-bold small">Jam Selesai</label>
                                        <input type="time" name="end_time" class="form-control" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold small">Alasan (Opsional)</label>
                                    <textarea name="reason" class="form-control" rows="2" placeholder="Contoh: Rapat MGMP, Kuliah, dll"></textarea>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-success fw-bold">
                                        Simpan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- LIST DATA (KANAN) -->
                <div class="col-md-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white fw-bold py-3">
                            <i class="bi bi-list-check me-2"></i> Daftar Waktu Berhalangan Saya
                        </div>
                        <div class="card-body p-0">
                            {{-- Menggunakan variabel $availabilities sesuai Controller --}}
                            @if($availabilities->isEmpty())
                                <div class="text-center py-5 text-muted">
                                    <i class="bi bi-calendar-check fs-1 d-block mb-2"></i>
                                    <p>Anda belum menginput waktu berhalangan.<br>Sistem menganggap Anda <strong>BISA</strong> mengajar di semua jam.</p>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="ps-4">Hari</th>
                                                <th>Jam</th>
                                                <th>Alasan</th>
                                                <th class="text-end pe-4">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($availabilities as $item)
                                            <tr>
                                                <td class="ps-4 fw-bold">{{ $item->day }}</td>
                                                <td>
                                                    <span class="badge bg-light text-dark border">
                                                        {{ date('H:i', strtotime($item->start_time)) }} - {{ date('H:i', strtotime($item->end_time)) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <small class="text-muted">{{ $item->reason ?? '-' }}</small>
                                                </td>
                                                <td class="text-end pe-4">
                                                    {{-- Route delete menggunakan 'guru.availability.destroy' --}}
                                                    <form action="{{ route('guru.availability.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus data ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-sm btn-outline-danger border-0">
                                                            <i class="bi bi-trash-fill"></i> Hapus
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>