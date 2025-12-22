<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang - SIP JADWAL</title>
    <!-- CSS Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .hero-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            color: white;
            position: relative;
            overflow: hidden;
        }
        /* Hiasan background abstrak */
        .shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }
        .shape-1 { width: 300px; height: 300px; top: -100px; left: -100px; }
        .shape-2 { width: 500px; height: 500px; bottom: -150px; right: -150px; }
        
        .card-welcome {
            background: white;
            color: #333;
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            max-width: 600px;
            width: 100%;
            text-align: center;
            position: relative;
            z-index: 10;
        }
        .icon-logo {
            font-size: 4rem;
            color: #0d6efd;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>

    <section class="hero-section">
        <!-- Dekorasi Background -->
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>

        <div class="container px-4">
            <div class="d-flex justify-content-center">
                <div class="card-welcome">
                    <div class="mb-3">
                        <i class="bi bi-calendar-check-fill icon-logo"></i>
                    </div>
                    <h1 class="fw-bold mb-2">SIP JADWAL</h1>
                    <p class="text-muted fs-5 mb-4">Sistem Informasi Penjadwalan Sekolah <br> Terintegrasi & Otomatis</p>
                    
                    <div class="d-grid gap-3 col-lg-8 mx-auto">
                        @if (Route::has('login'))
                            @auth
                                <div class="alert alert-success py-2 mb-3 small">
                                    <i class="bi bi-person-check me-1"></i> Anda sedang login sebagai <strong>{{ Auth::user()->name }}</strong>
                                </div>
                                <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg rounded-pill fw-bold shadow-sm">
                                    <i class="bi bi-speedometer2 me-2"></i> Ke Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-primary btn-lg rounded-pill fw-bold shadow-sm">
                                    <i class="bi bi-box-arrow-in-right me-2"></i> Masuk Aplikasi
                                </a>
                                
                                {{-- Opsional: Jika ingin membuka pendaftaran umum, uncomment bagian ini --}}
                                {{-- 
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="btn btn-outline-secondary btn-lg rounded-pill fw-bold">
                                        Daftar Akun
                                    </a>
                                @endif 
                                --}}
                            @endauth
                        @endif
                    </div>

                    <div class="mt-5 pt-4 border-top">
                        <p class="small text-muted mb-0">
                            &copy; {{ date('Y') }} Tim Kurikulum & Pengembang. <br>
                            <span class="fst-italic">Memudahakan pengaturan jadwal pelajaran sekolah.</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
