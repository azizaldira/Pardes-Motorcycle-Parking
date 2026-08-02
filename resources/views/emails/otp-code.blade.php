<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kode Verifikasi Reset Password</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f3f4f6; padding: 20px; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <h2 style="text-align: center; color: #1e3a8a; margin-top: 0;">Reset Password</h2>
        <p>Halo,</p>
        <p>Anda menerima email ini karena ada permintaan untuk mengatur ulang kata sandi (reset password) pada akun Anda.</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <p style="font-size: 14px; color: #6b7280; margin-bottom: 5px;">Kode Verifikasi (OTP) Anda:</p>
            <div style="font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #2563eb; background-color: #eff6ff; padding: 15px; border-radius: 8px; display: inline-block;">
                {{ $otp }}
            </div>
        </div>
        
        <p style="color: #dc2626; font-size: 14px; text-align: center;">
            <strong>Peringatan:</strong> Kode ini hanya berlaku selama <strong>5 menit</strong>. Jangan berikan kode ini kepada siapapun!
        </p>
        
        <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 20px 0;">
        <p style="font-size: 12px; color: #9ca3af; text-align: center;">
            Jika Anda tidak meminta reset password, Anda dapat mengabaikan email ini. Akun Anda akan tetap aman.
        </p>
    </div>
</body>
</html>
