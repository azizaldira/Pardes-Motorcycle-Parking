<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - {{ $business->business_name ?? "Penitipan Parde's" }}</title>
    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-logo">
                <i class="fas fa-motorcycle"></i>
            </div>
            <h1 class="login-title">{{ $business->business_name ?? "Penitipan Parde's" }}</h1>
            <p class="login-subtitle">Sistem Manajemen Parkir Motor</p>

            <form action="{{ route('login') }}" method="POST" class="login-form">
                @csrf
                
                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <div class="input-wrapper">
                        <i class="far fa-user"></i>
                        <input type="text" id="username" name="username" class="form-control" placeholder="Masukkan username" value="{{ old('username') }}" required autofocus>
                    </div>
                    @error('username')
                        <span style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group mb-6">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan password" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-full">Masuk</button>
            </form>
        </div>
    </div>
</body>
</html>
