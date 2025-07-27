<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Penjadwalan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body x-data="{ tab_aktif: '' }">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
                <i class="bi bi-calendar-week"></i>
                Dashboard Penjadwalan
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
        <div class="p-5 mb-4 bg-light rounded-3">
            <div class="container-fluid py-5">
                <h1 class="display-5 fw-bold">Selamat Datang, {{ Auth::user()->name }}!</h1>
                <p class="col-md-8 fs-4">Anda login sebagai <b>{{ Auth::user()->role }}</b>. Gunakan pusat kontrol ini untuk mengelola dan membuat jadwal pelajaran secara otomatis.</p>
                <hr class="my-4">
                
                <div class="btn-group">
                    <button type="button" class="btn btn-primary btn-lg dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-cpu-fill"></i>
                        Menu Generate Jadwal
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <form action="{{ route('admin.jadwal.generate') }}" method="POST" class="dropdown-item p-0">
                                @csrf
                                <button type="submit" class="btn btn-link text-decoration-none text-dark w-100 text-start">
                                    Generate Jadwal Baru
                                </button>
                            </form>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="#draft-section" onclick="event.preventDefault(); document.getElementById('draft-section').scrollIntoView({ behavior: 'smooth' });">
                                Lihat Draft Tersimpan
                            </a>
                        </li>
                    </ul>
                </div>
                <a href="{{ route('admin.beban-ajar.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-person-workspace"></i> Kelola Beban Ajar
                </a>
                <a href="{{ route('admin.ruangan.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-door-open"></i> Kelola Ruangan
                </a>
            </div>
        </div>

        <div class="row align-items-md-stretch mb-4">
            <div class="col-md-3" @click="tab_aktif = 'guru'" style="cursor: pointer;">
                <div class="h-100 p-4 text-white bg-primary rounded-3">
                    <h2><i class="bi bi-person-video3"></i> Guru</h2>
                    <p>Total guru terdaftar.</p>
                    <h2>{{ $jumlahGuru }}</h2>
                </div>
            </div>
            <div class="col-md-3" @click="tab_aktif = 'kelas'" style="cursor: pointer;">
                <div class="h-100 p-4 bg-secondary text-white rounded-3">
                    <h2><i class="bi bi-easel2"></i> Kelas</h2>
                    <p>Total rombongan belajar.</p>
                    <h2>{{ $jumlahKelas }}</h2>
                </div>
            </div>
            <div class="col-md-3" @click="tab_aktif = 'mapel'" style="cursor: pointer;">
                <div class="h-100 p-4 text-white bg-success rounded-3">
                    <h2><i class="bi bi-book"></i> Mata Pelajaran</h2>
                    <p>Total mata pelajaran.</p>
                    <h2>{{ $jumlahMapel }}</h2>
                </div>
            </div>
            <div class="col-md-3" @click="tab_aktif = 'ruangan'" style="cursor: pointer;">
                <div class="h-100 p-4 bg-warning text-dark rounded-3">
                    <h2><i class="bi bi-door-open"></i> Ruangan</h2>
                    <p>Total ruangan tersedia.</p>
                    <h2>{{ $jumlahRuangan }}</h2>
                </div>
            </div>
        </div>

        <div class="mt-5">
            <div x-show="tab_aktif === 'guru'" x-transition>
                <h3>Data Guru</h3>
                <table class="table table-striped table-hover">
                    <thead>
                        <tr><th>ID</th><th>Nama</th><th>NIP</th><th>NUPTK</th></tr>
                    </thead>
                    <tbody>
                        @foreach($semuaGuru as $item)
                        <tr><td>{{ $item->id_guru }}</td><td>{{ $item->nama }}</td><td>{{ $item->nip }}</td><td>{{ $item->nuptk }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div x-show="tab_aktif === 'kelas'" x-transition>
                <h3>Data Kelas</h3>
                <table class="table table-striped table-hover">
                    <thead>
                        <tr><th>ID</th><th>Nama Kelas</th><th>Tingkat</th><th>Kapasitas</th></tr>
                    </thead>
                    <tbody>
                        @foreach($semuaKelas as $item)
                        <tr><td>{{ $item->id_kelas }}</td><td>{{ $item->nama_kelas }}</td><td>{{ $item->tingkat }}</td><td>{{ $item->kapasitas }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div x-show="tab_aktif === 'mapel'" x-transition>
                <h3>Data Mata Pelajaran</h3>
                <table class="table table-striped table-hover">
                    <thead>
                        <tr><th>ID Mapel</th><th>Kode</th><th>Nama Mata Pelajaran</th></tr>
                    </thead>
                    <tbody>
                        @foreach($semuaMapel as $mapel)
                        <tr><td>{{ $mapel->id_mapel }}</td><td>{{ $mapel->kode_mapel }}</td><td>{{ $mapel->nama_mapel }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div x-show="tab_aktif === 'ruangan'" x-transition>
                <h3>Data Ruangan</h3>
                <table class="table table-striped table-hover">
                    <thead>
                        <tr><th>ID</th><th>Nama Ruangan</th><th>Kapasitas</th></tr>
                    </thead>
                    <tbody>
                        @foreach($semuaRuangan as $ruangan)
                        <tr><td>{{ $ruangan->id }}</td><td>{{ $ruangan->nama_ruangan }}</td><td>{{ $ruangan->kapasitas }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-5" id="draft-section">
            <h4><i class="bi bi-pencil-square"></i> Draft yang Membutuhkan Aksi</h4>
            <p>Daftar jadwal yang perlu Anda kirim untuk persetujuan atau perbaiki.</p>
            <div class="card">
                <div class="card-body">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID Draft</th>
                                <th>Tahun Ajaran</th>
                                <th>Semester</th>
                                <th>Versi</th>
                                <th>Status</th>
                                <th>Catatan Revisi</th>
                                <th>Dibuat Pada</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($drafts as $draft)
                                <tr>
                                    <td>#{{ $draft->id }}</td>
                                    <td>{{ $draft->tahun_ajaran }}</td>
                                    <td>{{ $draft->semester }}</td>
                                    <td>{{ $draft->versi }}</td>
                                    <td>
                                        @if($draft->status == 'DRAFT')
                                            <span class="badge bg-secondary">Draft</span>
                                        @elseif($draft->status == 'REVISI')
                                            <span class="badge bg-warning text-dark">Perlu Revisi</span>
                                        @endif
                                    </td>
                                    <td>{{ $draft->catatan_revisi }}</td>
                                    <td>{{ $draft->created_at->format('d M Y H:i') }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.jadwal.show', $draft->id) }}" class="btn btn-info btn-sm">Lihat Detail</a>
                                        
                                        {{-- Hanya tampilkan tombol Kirim jika statusnya DRAFT --}}
                                        @if($draft->status == 'DRAFT')
                                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#konfirmasiKirimModal-{{ $draft->id }}">
                                                Kirim untuk Persetujuan
                                            </button>

                                            <div class="modal fade" id="konfirmasiKirimModal-{{ $draft->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header"><h1 class="modal-title fs-5">Konfirmasi Pengiriman</h1><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                                        <div class="modal-body text-start">Apakah Anda yakin ingin mengirim <strong>Draft Jadwal #{{ $draft->id }}</strong>?</div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                            <form action="{{ route('admin.jadwal.submit', $draft->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="btn btn-success">Ya, Kirim Sekarang</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif($draft->status == 'REVISI')
                                            <form action="{{ route('admin.jadwal.regenerate', $draft->id) }}" method="POST" onsubmit="return confirm('Anda yakin ingin membuat ulang jadwal ini berdasarkan catatan revisi? Draft lama akan dihapus.');">
                                                @csrf
                                                <button type="submit" class="btn btn-warning btn-sm">
                                                    Generate Ulang Revisi
                                                </button>
                                            </form>

                                            <div class="modal fade" id="konfirmasiRevisiModal-{{ $draft->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h1 class="modal-title fs-5">Konfirmasi Revisi</h1>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body text-start">
                                                            <p>Anda akan menghapus draft ini untuk membuat revisi baru. Catatan dari penyetuju:</p>
                                                            <blockquote class="blockquote bg-light p-2 rounded">
                                                                <em>"{{ $draft->catatan_revisi }}"</em>
                                                            </blockquote>
                                                            <p>Apakah Anda yakin ingin melanjutkan?</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                            <form action="{{ route('admin.jadwal.destroy', $draft->id) }}" method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-warning">Ya, Hapus & Siap Revisi</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Tidak ada pekerjaan yang membutuhkan aksi Anda saat ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-5">
            <h4><i class="bi bi-hourglass-split"></i> Riwayat Pengajuan Jadwal</h4>
            <p>Daftar jadwal yang sudah Anda kirim dan sedang dalam proses atau sudah selesai.</p>
            <div class="card">
                <div class="card-body">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID Draft</th>
                                <th>Tahun Ajaran</th>
                                <th>Semester</th>
                                <th>Status</th>
                                <th>Versi</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($riwayat as $item)
                                <tr>
                                    <td>#{{ $item->id }}</td>
                                    <td>{{ $item->tahun_ajaran }}</td>
                                    <td>{{ $item->semester }}</td>
                                    <td>
                                        @if($item->status == 'MENUNGGU_PERSETUJUAN')
                                            <span class="badge bg-info text-dark">Menunggu Persetujuan</span>
                                        @elseif($item->status == 'DIPUBLIKASIKAN')
                                            <span class="badge bg-success">Sudah Dipublikasikan</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->versi }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.jadwal.show', $item->id) }}" class="btn btn-secondary btn-sm">Lihat Detail</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Belum ada riwayat pengajuan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <footer class="pt-3 mt-4 text-muted border-top">
            &copy; 2025 - Aplikasi Penjadwalan Otomatis
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>