<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Persetujuan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="bi bi-check2-square"></i>
                Dashboard Persetujuan
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-danger">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </button>
            </form>
        </div>
    </nav>

    <div class="container mt-4">
        <h3>Selamat Datang, {{ Auth::user()->name }}!</h3>
        <p>Berikut adalah daftar draft jadwal yang memerlukan persetujuan Anda.</p>

        <div class="card">
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID Draft</th>
                            <th>Tahun Ajaran</th>
                            <th>Semester</th>
                            <th>Dibuat Pada</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jadwalMenunggu as $jadwal)
                            <tr>
                                <td>#{{ $jadwal->id }}</td>
                                <td>{{ $jadwal->tahun_ajaran }}</td>
                                <td>{{ $jadwal->semester }}</td>
                                <td>{{ $jadwal->created_at->format('d M Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.jadwal.show', $jadwal->id) }}" class="btn btn-primary btn-sm">
                                        Lihat Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada jadwal yang memerlukan persetujuan saat ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>