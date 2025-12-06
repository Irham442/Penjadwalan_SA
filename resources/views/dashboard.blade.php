<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Jadwal Mengajar Saya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="bi bi-calendar-check"></i> Jadwal Mengajar Anda
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-light">Logout</button>
            </form>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="p-5 mb-4 bg-light rounded-3">
            <h3>Halo, {{ Auth::user()->name }}!</h3>
            @if($jadwalAktif)
                <p>Berikut adalah jadwal mengajar Anda untuk Semester {{ $jadwalAktif->semester }} Tahun Ajaran {{ $jadwalAktif->tahun_ajaran }}.</p>
            @else
                <p>Saat ini belum ada jadwal yang dipublikasikan.</p>
            @endif
        </div>
        
        @if($jadwalAktif && !empty($jadwalGuru))
            @foreach($jadwalGuru as $hari => $sesiHarian)
                <h4 class="mt-4">{{ $hari }}</h4>
                <div class="list-group">
                    @foreach($sesiHarian as $jam => $item)
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">{{ $item->mapel->nama_mapel ?? 'N/A' }}</h5>
                                <small>{{ date('H:i', strtotime($item->waktu->jam_mulai)) }} - {{ date('H:i', strtotime($item->waktu->jam_selesai)) }}</small>
                            </div>
                            <p class="mb-1">
                                Mengajar di kelas: <strong>{{ $item->kelas->nama_kelas ?? 'N/A' }}</strong>
                            </p>
                        </div>
                    @endforeach
                </div>
            @endforeach
        @elseif($jadwalAktif)
             <div class="alert alert-info">Anda tidak memiliki jadwal mengajar pada periode ini.</div>
        @endif
    </div>
</body>
</html>