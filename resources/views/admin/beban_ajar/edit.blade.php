<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Edit Beban Ajar - Sistem Penjadwalan</title>
    
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
                <h1 class="h3 mb-0 fw-bold text-gray-800">Edit Beban Ajar</h1>
                <p class="text-muted small mb-0">Perbarui data penugasan guru.</p>
            </div>
            <a href="{{ route('admin.beban-ajar.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <!-- INFO PERIODE (Opsional, jika ada data tahun aktif) -->
        @if(isset($tahunAktif) && $tahunAktif)
            <div class="alert alert-warning shadow-sm d-flex align-items-center info-banner mb-4" role="alert" style="border-color: #ffc107;">
                <i class="bi bi-pencil-fill fs-3 me-3 text-warning"></i>
                <div>
                    <strong>MODE EDIT:</strong> Anda sedang mengubah data untuk Periode:
                    <br>
                    <span class="fs-5 fw-bold">{{ $tahunAktif->tahun }} - Semester {{ $tahunAktif->semester }}</span>
                </div>
            </div>
        @endif

        <!-- CARD FORM -->
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 fw-bold text-primary">
                    <i class="bi bi-pencil-square me-2"></i> Form Edit Data
                </h6>
            </div>
            
            <div class="card-body p-4">
                <form action="{{ route('admin.beban-ajar.update', $bebanAjar->id) }}" method="POST">
                    @csrf
                    @method('PUT') <!-- Wajib untuk Update -->

                    <!-- 1. Guru Pengajar -->
                    <div class="mb-4">
                        <label for="id_guru" class="form-label">Guru Pengajar <span class="text-danger">*</span></label>
                        <select class="form-select form-select-lg" id="id_guru" name="id_guru" required>
                            <option value="">-- Pilih Guru --</option>
                            @foreach($gurus as $guru)
                                <option value="{{ $guru->id_guru }}" {{ $bebanAjar->id_guru == $guru->id_guru ? 'selected' : '' }}>
                                    {{ $guru->nama }} {{ $guru->kode_guru ? '('.$guru->kode_guru.')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row">
                        <!-- 2. Filter Kejuruan -->
                        <div class="col-md-4 mb-3">
                            <label for="kejuruan_filter" class="form-label">Filter Kejuruan / Tipe</label>
                            <select class="form-select" id="kejuruan_filter">
                                <option value="">-- Pilih Tipe Mapel --</option>
                                <option value="UMUM" {{ $selectedKejuruan == 'UMUM' ? 'selected' : '' }}>UMUM (Non-Produktif)</option>
                                @foreach($kejuruanList as $jurusan)
                                    <option value="{{ $jurusan }}" {{ $selectedKejuruan == $jurusan ? 'selected' : '' }}>{{ $jurusan }}</option>
                                @endforeach
                            </select>
                            <div class="form-text text-primary">Ubah ini jika ingin mengganti Mapel/Kelas ke jurusan lain.</div>
                        </div>

                        <!-- 3. Mata Pelajaran -->
                        <div class="col-md-4 mb-3">
                            <label for="id_mapel" class="form-label">Mata Pelajaran <span class="text-danger">*</span></label>
                            <select class="form-select bg-light" id="id_mapel" name="id_mapel" required>
                                <option value="">-- Pilih Mata Pelajaran --</option>
                                {{-- Loop mapel yang dikirim dari controller --}}
                                @foreach($mapels as $mapel)
                                    <option value="{{ $mapel->id_mapel }}" {{ $bebanAjar->id_mapel == $mapel->id_mapel ? 'selected' : '' }}>
                                        {{ $mapel->kode_mapel }} - {{ $mapel->nama_mapel }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 4. Kelas -->
                        <div class="col-md-4 mb-3">
                            <label for="id_kelas" class="form-label">Kelas <span class="text-danger">*</span></label>
                            <select class="form-select bg-light" id="id_kelas" name="id_kelas" required>
                                <option value="">-- Pilih Kelas --</option>
                                {{-- Loop kelas yang dikirim dari controller --}}
                                @foreach($kelases as $kelas)
                                    <option value="{{ $kelas->id_kelas }}" {{ $bebanAjar->id_kelas == $kelas->id_kelas ? 'selected' : '' }}>
                                        {{ $kelas->nama_kelas }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="row">
                        <!-- 5. Jumlah Jam -->
                        <div class="col-md-6 mb-3">
                            <label for="jumlah_jam_seminggu" class="form-label">Total Jam per Minggu <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="jumlah_jam_seminggu" name="jumlah_jam_seminggu" required value="{{ $bebanAjar->jumlah_jam_seminggu }}" min="1">
                                <span class="input-group-text">Jam Pelajaran (JP)</span>
                            </div>
                        </div>

                        <!-- 6. Jam per Blok -->
                        <div class="col-md-6 mb-3">
                            <label for="jam_per_blok" class="form-label">Durasi per Pertemuan (Blok) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="jam_per_blok" name="jam_per_blok" required value="{{ $bebanAjar->jam_per_blok }}" min="1">
                                <span class="input-group-text">JP</span>
                            </div>
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
                                            <option value="{{ $waktu->id }}" {{ $bebanAjar->id_hari_waktu == $waktu->id ? 'selected' : '' }}>
                                                {{ $hari }} | {{ date('H:i', strtotime($waktu->jam_mulai)) }} - {{ date('H:i', strtotime($waktu->jam_selesai)) }}
                                            </option>
                                        @endif
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        <div class="form-text">
                            <i class="bi bi-info-circle"></i> Gunakan ini HANYA jika guru harus mengajar di jam tertentu.
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="d-flex gap-2 justify-content-end border-top pt-3">
                        <a href="{{ route('admin.beban-ajar.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-lg me-1"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> Simpan Perubahan
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
            // Setup CSRF
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });

            // Ketika Filter Kejuruan Berubah
            $('#kejuruan_filter').on('change', function() {
                var kejuruan = $(this).val();
                
                var mapelDropdown = $('#id_mapel');
                var kelasDropdown = $('#id_kelas');

                // Kosongkan dan beri efek loading
                mapelDropdown.empty().append('<option value="">Memuat...</option>').prop('disabled', true);
                kelasDropdown.empty().append('<option value="">Memuat...</option>').prop('disabled', true);

                if (!kejuruan) {
                    mapelDropdown.empty().append('<option value="">-- Pilih Filter Dulu --</option>');
                    kelasDropdown.empty().append('<option value="">-- Pilih Filter Dulu --</option>');
                    return;
                }

                // AJAX Request
                $.ajax({
                    type: 'GET',
                    url: '{{ route("admin.beban-ajar.getData") }}', 
                    data: { 'kejuruan': kejuruan },
                    success: function(data) {
                        // Isi Mapel
                        mapelDropdown.empty().append('<option value="">-- Pilih Mata Pelajaran --</option>');
                        if(data.mapels.length > 0){
                            $.each(data.mapels, function(index, mapel) {
                                mapelDropdown.append('<option value="' + mapel.id_mapel + '">' + mapel.kode_mapel + ' - ' + mapel.nama_mapel + '</option>');
                            });
                            mapelDropdown.prop('disabled', false).removeClass('bg-light');
                        } else {
                            mapelDropdown.append('<option value="">Tidak ada data</option>');
                        }

                        // Isi Kelas
                        kelasDropdown.empty().append('<option value="">-- Pilih Kelas --</option>');
                        if(data.kelases.length > 0){
                            $.each(data.kelases, function(index, kelas) {
                                kelasDropdown.append('<option value="' + kelas.id_kelas + '">' + kelas.nama_kelas + '</option>');
                            });
                            kelasDropdown.prop('disabled', false).removeClass('bg-light');
                        } else {
                            kelasDropdown.append('<option value="">Tidak ada data</option>');
                        }
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText); 
                        alert('Gagal memuat data filter. Cek koneksi.');
                        mapelDropdown.empty().append('<option value="">-- Error --</option>');
                        kelasDropdown.empty().append('<option value="">-- Error --</option>');
                    }
                });
            });
        });
    </script>
</body>
</html>