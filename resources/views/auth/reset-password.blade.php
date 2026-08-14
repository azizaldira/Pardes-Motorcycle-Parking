<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - {{ $business->business_name ?? "Penitipan Parde's" }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-logo">
                <i class="fas fa-motorcycle"></i>
            </div>
            <h1 class="login-title">Reset Password</h1>
            <p class="login-subtitle">Masukkan kode OTP dari email dan password baru Anda</p>

            @if(session('success'))
            <div style="background-color: #dcfce7; color: #166534; padding: 0.75rem; border-radius: 6px; margin-bottom: 1rem; font-size: 0.875rem; text-align: center;">
                {{ session('success') }}
            </div>
            @endif

            <form action="{{ route('password.update') }}" method="POST" class="login-form">
                @csrf
                
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <div class="input-wrapper">
                        <i class="far fa-envelope"></i>
                        <input type="email" id="email" name="email" class="form-control" value="{{ old('email', $email ?? '') }}" required readonly style="background-color: #f3f4f6;">
                    </div>
                </div>

                <div class="form-group">
                    <label for="otp" class="form-label" style="display: flex; justify-content: space-between;">
                        <span>Kode OTP (6 Digit)</span>
                        <span id="countdownText" style="color: #3b82f6; font-size: 0.75rem; font-weight: 600;">05:00</span>
                    </label>
                    <div class="input-wrapper">
                        <i class="fas fa-key"></i>
                        <input type="text" id="otp" name="otp" class="form-control" placeholder="123456" maxlength="6" required autofocus>
                    </div>
                    @error('otp')
                        <span style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password Baru</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required minlength="6">
                    </div>
                    @error('password')
                        <span style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group mb-6">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Ketik ulang password" required minlength="6">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-full mb-4">Ubah Password</button>
                
                <div style="text-align: center;">
                    <a href="{{ route('password.request') }}" style="color: #6b7280; font-size: 0.875rem; text-decoration: none;">Tidak menerima kode? Minta ulang</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Countdown 5 Minutes
        let timeLeft = 300; // 5 minutes in seconds
        const countdownElement = document.getElementById('countdownText');
        
        const countdownTimer = setInterval(function() {
            if (timeLeft <= 0) {
                clearInterval(countdownTimer);
                countdownElement.innerHTML = "Kedaluwarsa";
                countdownElement.style.color = "#dc2626";
            } else {
                let minutes = Math.floor(timeLeft / 60);
                let seconds = timeLeft % 60;
                
                minutes = minutes < 10 ? "0" + minutes : minutes;
                seconds = seconds < 10 ? "0" + seconds : seconds;
                
                countdownElement.innerHTML = minutes + ":" + seconds;
                
                if(timeLeft <= 60) {
                    countdownElement.style.color = "#ea580c"; // orange when < 1 minute
                }
            }
            timeLeft -= 1;
        }, 1000);
    </script>
</body>
</html>
