<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - {{ $business->business_name ?? "Penitipan Parde's" }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-logo">
                <i class="fas fa-motorcycle"></i>
            </div>
            <h1 class="login-title">Lupa Password</h1>
            <p class="login-subtitle">Masukkan email Anda untuk menerima kode OTP</p>

            <form action="{{ route('password.email') }}" method="POST" class="login-form">
                @csrf
                
                <div class="form-group mb-6">
                    <label for="email" class="form-label">Email</label>
                    <div class="input-wrapper">
                        <i class="far fa-envelope"></i>
                        <input type="email" id="email" name="email" class="form-control" placeholder="Masukkan email terdaftar" value="{{ old('email') }}" required autofocus>
                    </div>
                    @error('email')
                        <span style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary w-full mb-4">Kirim Kode Verifikasi</button>
                
                <div style="text-align: center;">
                    <a href="{{ route('login') }}" style="color: #6b7280; font-size: 0.875rem; text-decoration: none;">Kembali ke Halaman Login</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
