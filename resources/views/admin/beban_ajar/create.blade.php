<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tambah Beban Ajar - Sistem Penjadwalan</title>
    
    <!-- CSS Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body { background-color: #f8f9fa; }
        .card { border-radius: 12px; border: none; }
        .form-label { font-weight: 600; color: #343a40; }
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
                <h1 class="h3 mb-0 fw-bold text-gray-800">Tambah Beban Ajar</h1>
                <p class="text-muted small mb-0">Input data penugasan guru untuk periode ini.</p>
            </div>
            <a href="{{ route('admin.beban-ajar.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <!-- FITUR PENGAMAN HUMAN ERROR (INFO PERIODE) -->
        @if($tahunAktif)
            <div class="alert alert-primary shadow-sm d-flex align-items-center info-banner mb-4" role="alert" style="border-color: #0d6efd;">
                <i class="bi bi-info-circle-fill fs-3 me-3"></i>
                <div>
                    <strong>PERHATIAN:</strong> Data ini akan disimpan untuk Periode Akademik:
                    <br>
                    <span class="fs-5 fw-bold">{{ $tahunAktif->tahun }} - Semester {{ $tahunAktif->semester }}</span>
                </div>
            </div>
        @else
            <div class="alert alert-danger shadow-sm d-flex align-items-center info-banner mb-4" role="alert" style="border-color: #dc3545;">
                <i class="bi bi-exclamation-triangle-fill fs-3 me-3"></i>
                <div>
                    <strong>PERINGATAN:</strong> Tidak ada Tahun Ajaran yang Aktif!
                    <br>
                    Mohon aktifkan tahun ajaran terlebih dahulu sebelum mengisi data ini.
                </div>
            </div>
        @endif

        <!-- CARD FORM -->
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 fw-bold text-primary">
                    <i class="bi bi-plus-circle me-2"></i> Form Input Data
                </h6>
            </div>
            
            <div class="card-body p-4">
                <form action="{{ route('admin.beban-ajar.store') }}" method="POST">
                    @csrf

                    <!-- 1. Guru Pengajar -->
                    <div class="mb-4">
                        <label for="id_guru" class="form-label">Guru Pengajar <span class="text-danger">*</span></label>
                        <select class="form-select form-select-lg" id="id_guru" name="id_guru" required>
                            <option value="">-- Pilih Guru --</option>
                            @foreach($gurus as $guru)
                                <option value="{{ $guru->id_guru }}">
                                    {{ $guru->nama }} {{ $guru->kode_guru ? '('.$guru->kode_guru.')' : '' }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">
                            Hanya guru yang dicentang di menu "Tahun Ajaran" yang muncul di sini.
                        </div>
                    </div>

                    <div class="row">
                        <!-- 2. Filter Kejuruan -->
                        <div class="col-md-4 mb-3">
                            <label for="kejuruan_filter" class="form-label">Filter Kejuruan / Tipe</label>
                            <select class="form-select" id="kejuruan_filter">
                                <option value="">-- Pilih Tipe Mapel --</option>
                                <option value="UMUM">UMUM (Non-Produktif)</option>
                                @foreach($kejuruanList as $jurusan)
                                    <option value="{{ $jurusan }}">{{ $jurusan }}</option>
                                @endforeach
                            </select>
                            <div class="form-text text-primary">Pilih ini dulu untuk memfilter Mapel & Kelas.</div>
                        </div>

                        <!-- 3. Mata Pelajaran -->
                        <div class="col-md-4 mb-3">
                            <label for="id_mapel" class="form-label">Mata Pelajaran <span class="text-danger">*</span></label>
                            <select class="form-select bg-light" id="id_mapel" name="id_mapel" required disabled>
                                <option value="">-- Pilih Filter Dulu --</option>
                            </select>
                        </div>

                        <!-- 4. Kelas -->
                        <div class="col-md-4 mb-3">
                            <label for="id_kelas" class="form-label">Kelas <span class="text-danger">*</span></label>
                            <select class="form-select bg-light" id="id_kelas" name="id_kelas" required disabled>
                                <option value="">-- Pilih Filter Dulu --</option>
                            </select>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="row">
                        <!-- 5. Jumlah Jam -->
                        <div class="col-md-6 mb-3">
                            <label for="jumlah_jam_seminggu" class="form-label">Total Jam per Minggu <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="jumlah_jam_seminggu" name="jumlah_jam_seminggu" required placeholder="Cth: 4" min="1">
                                <span class="input-group-text">Jam Pelajaran (JP)</span>
                            </div>
                        </div>

                        <!-- 6. Jam per Blok -->
                        <div class="col-md-6 mb-3">
                            <label for="jam_per_blok" class="form-label">Durasi per Pertemuan (Blok) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="jam_per_blok" name="jam_per_blok" required placeholder="Cth: 2" min="1" value="">
                                <span class="input-group-text">JP</span>
                            </div>
                            <div class="form-text">Contoh: Isi 2 jika sekali masuk kelas langsung 2 jam pelajaran.</div>
                        </div>
                    </div>

                    <!-- 7. Lock Waktu (Opsional) -->
                    <div class="mb-4">
                        <label for="id_hari_waktu" class="form-label text-muted">Kunci Waktu Spesifik (Opsional)</label>
                        <select class="form-select" id="id_hari_waktu" name="id_hari_waktu">
                            <option value="">-- Biarkan Sistem Mengacak (Rekomendasi) --</option>
                            @foreach($waktus->groupBy('hari') as $hari => $slots)
                                <optgroup label="{{ $hari }}">
                                    @foreach($slots as $waktu)
                                        @if($waktu->bisa_dijadwalkan == 1)
                                            <option value="{{ $waktu->id }}">
                                                {{ $hari }} | {{ date('H:i', strtotime($waktu->jam_mulai)) }} - {{ date('H:i', strtotime($waktu->jam_selesai)) }}
                                            </option>
                                        @endif
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        <div class="form-text">
                            <i class="bi bi-info-circle"></i> Gunakan ini HANYA jika guru harus mengajar di jam tertentu (misal: Guru luar biasa).
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="d-flex gap-2 justify-content-end border-top pt-3">
                        <a href="{{ route('admin.beban-ajar.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-lg me-1"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> Simpan Data
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <!-- JS: Bootstrap & jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- SCRIPT AJAX FILTER -->
    <script>
        $(document).ready(function() {
            // Setup CSRF Token untuk semua request AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Ketika Filter Kejuruan Berubah
            $('#kejuruan_filter').on('change', function() {
                var kejuruan = $(this).val();
                
                var mapelDropdown = $('#id_mapel');
                var kelasDropdown = $('#id_kelas');

                // Reset dan disable dulu
                mapelDropdown.empty().append('<option value="">Memuat...</option>').prop('disabled', true);
                kelasDropdown.empty().append('<option value="">Memuat...</option>').prop('disabled', true);

                if (!kejuruan) {
                    mapelDropdown.empty().append('<option value="">-- Pilih Filter Dulu --</option>');
                    kelasDropdown.empty().append('<option value="">-- Pilih Filter Dulu --</option>');
                    return;
                }

                // Panggil AJAX
                $.ajax({
                    type: 'GET',
                    url: '{{ route("admin.beban-ajar.getData") }}', 
                    data: { 'kejuruan': kejuruan },
                    success: function(data) {
                        // 1. Isi Dropdown Mapel
                        mapelDropdown.empty().append('<option value="">-- Pilih Mata Pelajaran --</option>');
                        if(data.mapels.length > 0){
                            $.each(data.mapels, function(index, mapel) {
                                mapelDropdown.append('<option value="' + mapel.id_mapel + '">' + mapel.kode_mapel + ' - ' + mapel.nama_mapel + '</option>');
                            });
                            mapelDropdown.prop('disabled', false).removeClass('bg-light');
                        } else {
                            mapelDropdown.append('<option value="">Tidak ada mapel ditemukan</option>');
                        }

                        // 2. Isi Dropdown Kelas
                        kelasDropdown.empty().append('<option value="">-- Pilih Kelas --</option>');
                        if(data.kelases.length > 0){
                            $.each(data.kelases, function(index, kelas) {
                                kelasDropdown.append('<option value="' + kelas.id_kelas + '">' + kelas.nama_kelas + '</option>');
                            });
                            kelasDropdown.prop('disabled', false).removeClass('bg-light');
                        } else {
                            kelasDropdown.append('<option value="">Tidak ada kelas ditemukan</option>');
                        }
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText); 
                        alert('Terjadi kesalahan saat mengambil data. Silakan coba lagi.');
                        mapelDropdown.empty().append('<option value="">-- Error --</option>');
                        kelasDropdown.empty().append('<option value="">-- Error --</option>');
                    }
                });
            });
        });
    </script>
</body>
</html>