<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Kelas - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        body { background-color: #f8f9fa; }
        .card { border-radius: 12px; border: none; }
        .table thead { background-color: #343a40; color: white; }
        .btn-sm { border-radius: 6px; }
        .table-hover tbody tr:hover { background-color: #f1f3f5; }
        
        /* Animasi kecil untuk badge */
        .badge { transition: all 0.2s; }
        .badge:hover { transform: scale(1.05); }

        /* Style Tambahan untuk Navbar */
        .navbar-nav .nav-link {
            transition: all 0.3s ease;
            border-bottom: 2px solid transparent;
        }
        .navbar-nav .nav-link:hover {
            color: #ffc107 !important; /* Kuning Warning */
            transform: translateY(-2px);
        }
        .navbar-nav .nav-link.active {
            color: #ffc107 !important;
            border-bottom: 2px solid #ffc107;
            font-weight: bold;
        }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow sticky-top mb-4">
        <div class="container">
            <!-- BRAND LOGO -->
            <a class="navbar-brand fw-bold" href="{{ route('admin.dashboard') }}">
                <i class="bi bi-calendar-week me-2"></i> SIP JADWAL
            </a>
    
            <!-- TOGGLER (HAMBURGER MENU UNTUK HP) -->
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
    
            <!-- MENU ITEMS -->
            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    
                    <!-- Dashboard -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                            <i class="bi bi-speedometer2 me-1"></i> Dashboard
                        </a>
                    </li>
    
                    <!-- Data Master: Tahun Ajar (Current Page) -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.tahun_ajaran.*') ? 'active' : '' }}" href="{{ route('admin.tahun_ajaran.index') }}">
                            <i class="bi bi-calendar-event me-1"></i> Tahun Ajar
                        </a>
                    </li>
    
                    <!-- Mapel -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.mapel.*') ? 'active' : '' }}" href="{{ route('admin.mapel.index') }}">
                            <i class="bi bi-journal-bookmark me-1"></i> Mapel
                        </a>
                    </li>

                    <!-- Ruangan (Ditambahkan sesuai route resource 'ruangan') -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.ruangan.*') ? 'active' : '' }}" href="{{ route('admin.ruangan.index') }}">
                            <i class="bi bi-building me-1"></i> Ruangan
                        </a>
                    </li>
    
                    <!-- Guru -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.guru.*') ? 'active' : '' }}" href="{{ route('admin.guru.index') }}">
                            <i class="bi bi-person-badge me-1"></i> Guru
                        </a>
                    </li>
    
                    <!-- Kelas -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.kelas.*') ? 'active' : '' }}" href="{{ route('admin.kelas.index') }}">
                            <i class="bi bi-people me-1"></i> Kelas
                        </a>
                    </li>
    
                    <!-- Beban Ajar (Diupdate menggunakan hyphen '-' sesuai default resource route) -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.beban-ajar.*') ? 'active' : '' }}" href="{{ route('admin.beban-ajar.index') }}">
                            <i class="bi bi-briefcase me-1"></i> Beban Ajar
                        </a>
                    </li>
    
                </ul>
    
                <!-- BAGIAN KANAN (USER & LOGOUT) -->
                <div class="d-flex align-items-center mt-3 mt-lg-0">
                    <!-- Menampilkan Nama User Login -->
                    <span class="text-white-50 me-3 d-none d-lg-block small">
                        Halo, {{ Auth::user()->name ?? 'Admin' }}
                    </span>
    
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3 w-100 w-lg-auto">
                            <i class="bi bi-box-arrow-right me-1"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="bi bi-shop me-2"></i>Data Kelas</h5>
                <a href="{{ route('admin.kelas.create') }}" class="btn btn-success btn-sm">
                    <i class="bi bi-plus-lg"></i> Tambah Kelas
                </a>
            </div>
            <div class="card-body">
                
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Kelas</th>
                                <th>Tingkat</th>
                                <th>Kapasitas</th>
                                <th>Wali Kelas</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kelas as $k)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="fw-bold">{{ $k->nama_kelas }}</td>
                                <td><span class="badge bg-secondary">Kelas {{ $k->tingkat }}</span></td>
                                <td>{{ $k->kapasitas }} Siswa</td>
                                <td>{{ $k->waliKelas->nama ?? '-' }}</td>
                                <td class="text-end">
                                    <form onsubmit="return confirm('Hapus kelas ini?');" action="{{ route('admin.kelas.destroy', $k->id_kelas) }}" method="POST">
                                        <a href="{{ route('admin.kelas.edit', $k->id_kelas) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">Data Kelas belum tersedia.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="mt-3">
            <a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted"><i class="bi bi-arrow-left"></i> Kembali ke Dashboard</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>