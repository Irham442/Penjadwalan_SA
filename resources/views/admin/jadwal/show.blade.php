<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Detail Draft Jadwal #{{ $jadwal->id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .schedule-table th, .schedule-table td {
            min-width: 160px;
            vertical-align: middle; /* Posisi teks di tengah */
            height: 100px;
            padding: 8px;
            font-size: 0.9rem;
        }
        .schedule-table .time-header {
            min-width: 120px;
            font-weight: bold;
        }
        .schedule-item {
            font-size: 0.8rem;
            line-height: 1.2;
            padding: 5px;
            border-radius: 5px;
            background-color: #e9ecef;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
    </style>
</head>
<body>
    <div class="container-fluid mt-4">
        <h3>Detail Draft Jadwal #{{ $jadwal->id }}</h3>
        <p>Status: <span class="badge bg-secondary">{{ $jadwal->status }}</span></p>
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary mb-3">Kembali ke Dashboard</a>
        
        {{-- Tempat untuk Tombol Aksi Persetujuan --}}
        @if(Auth::user()->role === 'Super Admin' && $jadwal->status == 'MENUNGGU_PERSETUJUAN')
            {{-- ... Kode untuk modal dan tombol persetujuan Anda diletakkan di sini ... --}}
        @endif

        <div class="table-responsive">
            <table class="table table-bordered text-center schedule-table">
                <thead>
                    <tr>
                        <th class="time-header">Waktu \ Ruangan</th>
                        @foreach($ruangans as $ruangan)
                            <th>{{ $ruangan->nama_ruangan }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($waktus->groupBy('hari') as $hari => $waktuDiHari)
                        @foreach($waktuDiHari as $waktu)
                        <tr>
                            <td class="time-header">{{ $waktu->hari }} <br> {{ date('H:i', strtotime($waktu->jam_mulai)) }} - {{ date('H:i', strtotime($waktu->jam_selesai)) }}</td>
                            @foreach($ruangans as $ruangan)
                                <td>
                                    {{-- INI BAGIAN PALING PENTING --}}
                                    @if(isset($jadwalGrid[$waktu->id][$ruangan->id_ruangan]))
                                        @php
                                            $item = $jadwalGrid[$waktu->id][$ruangan->id_ruangan];
                                            
                                            // Asumsi nama kolomnya adalah 'kategori'. Sesuaikan jika berbeda.
                                            $kategori = $item->mapel->kategori ?? 'Umum';

                                            // Tentukan warna berdasarkan kategori
                                            $bgColor = match ($kategori) {
                                                'Produktif' => '#d1e7dd', // Hijau muda
                                                'Adaptif'   => '#cff4fc',   // Biru muda
                                                default     => '#f8f9fa',     // Abu-abu muda (untuk 'Normal')
                                            };
                                        @endphp

                                        <div class="schedule-item" style="background-color: {{ $bgColor }}; border-left: 5px solid #6c757d;">
                                                <strong>{{ $item->mapel->nama_mapel ?? 'N/A' }}</strong><br>
                                            <span class="text-muted small">({{ $item->mapel->kode_mapel ?? '' }})</span><br>
                                            <hr class="my-1">
                                            <small>
                                                {{ $item->guru->nama ?? 'N/A' }}<br>
                                                <i>({{ $item->kelas->nama_kelas ?? 'N/A' }})</i>
                                            </small>
                                        </div>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>