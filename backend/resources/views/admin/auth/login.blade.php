<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta tags untuk SEO dan responsivitas -->
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Travelo - Login</title>

    <!-- Bootstrap core CSS -->
    <link href="{{ asset('admin_assets/css/sb-admin-2.min.css') }}" rel="stylesheet">
    <!-- CSS kustom untuk halaman login -->
    <link rel="stylesheet" href="{{ asset('admin_assets/css/login_register.css') }}">
    <!-- Favicon aplikasi -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('admin_assets/img/logo_icon.png') }}">

    <!-- Font Awesome untuk ikon -->
    <link href="{{ asset('admin_assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">

    <!-- Google Font - Poppins (sesuai mobile app) -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <!-- Header Section (seperti mobile app) -->
    <div class="login-header">
        <div class="login-header-content">
            <h1 class="login-welcome">Selamat Datang!</h1>
            <p class="login-subtitle">Jelajahi dunia dengan TRAVELO</p>
        </div>
    </div>

    <!-- Container Utama -->
    <div class="login-container">
        <div class="login-card">
            <!-- Header Card -->
            <div class="login-card-header">
                <h2 class="login-title">Masuk ke Akun Admin</h2>
                <p class="login-description">Silakan masuk dengan kredensial Anda</p>
            </div>

            <!-- Form Login -->
            <form action="{{ route('loginAction') }}" method="POST" class="login-form">
                @csrf

                <!-- Pesan Error -->
                @if ($errors->any())
                    <div class="alert alert-danger login-alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Input Email -->
                <div class="form-group login-form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" class="form-control login-input @error('email') is-invalid @enderror"
                            id="email" name="email" value="{{ old('email') }}"
                            placeholder="Masukkan email Anda" required autofocus>
                    </div>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Input Password -->
                <div class="form-group login-form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" class="form-control login-input @error('password') is-invalid @enderror"
                            id="password" name="password" placeholder="Masukkan password Anda" required>
                    </div>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Tombol Login -->
                <button type="submit" class="btn btn-primary login-btn">
                    <span>Login</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- File JavaScript -->
    <!-- Bootstrap + jQuery Scripts -->
    <script src="{{ asset('admin_assets/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('admin_assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
</body>

</html>
