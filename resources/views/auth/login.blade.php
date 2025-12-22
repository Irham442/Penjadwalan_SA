<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIP JADWAL</title>
    <!-- CSS Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body {
            background-color: #f1f5f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            overflow: hidden;
            background: white;
            width: 100%;
            max-width: 400px;
        }
        .login-header {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            padding: 30px 20px;
            text-align: center;
            color: white;
        }
        .login-body {
            padding: 40px 30px;
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #0d6efd;
        }
        .btn-primary {
            padding: 10px;
            font-weight: 600;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="login-header">
            <div class="mb-2">
                <i class="bi bi-calendar-week fs-1"></i>
            </div>
            <h4 class="fw-bold mb-0">SIP JADWAL</h4>
            <small class="opacity-75">Sistem Informasi Penjadwalan Sekolah</small>
        </div>
        
        <div class="login-body">
            <!-- Menampilkan Pesan Error Validasi Umum (misal kredensial salah) -->
            @if ($errors->any())
                <div class="alert alert-danger py-2 small mb-3">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Input Email -->
                <div class="mb-3">
                    <label for="email" class="form-label text-muted small fw-bold">ALAMAT EMAIL</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                        <input type="email" class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="nama@sekolah.sch.id">
                    </div>
                </div>

                <!-- Input Password -->
                <div class="mb-4">
                    <label for="password" class="form-label text-muted small fw-bold">PASSWORD</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                        <input type="password" class="form-control border-start-0 ps-0 @error('password') is-invalid @enderror" 
                            id="password" name="password" required placeholder="••••••••">
                    </div>
                </div>

                {{-- <!-- Remember Me -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remember_me" name="remember">
                        <label class="form-check-label small text-muted" for="remember_me">
                            Ingat Saya
                        </label>
                    </div>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="small text-decoration-none text-primary fw-bold">Lupa Password?</a>
                    @endif
                </div> --}}

                <!-- Tombol Login -->
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary shadow-sm">
                        MASUK <i class="bi bi-box-arrow-in-right ms-2"></i>
                    </button>
                </div>
            </form>
        </div>
        
        <div class="bg-light p-3 text-center border-top">
            <small class="text-muted" style="font-size: 0.75rem;">
                &copy; {{ date('Y') }} Sistem Penjadwalan Sekolah
            </small>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>