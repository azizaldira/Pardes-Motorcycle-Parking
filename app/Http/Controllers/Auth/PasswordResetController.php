<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Carbon\Carbon;
use App\Models\User;

class PasswordResetController extends Controller
{
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        // Cek apakah email terdaftar
        $userExists = User::where('email', $request->email)->exists();
        if (!$userExists) {
            return back()->withErrors(['email' => 'Email tidak terdaftar pada sistem.'])->withInput();
        }

        $key = 'send-otp:' . $request->email;

        // Rate Limiter: Maksimal 1 request setiap 60 detik
        if (RateLimiter::tooManyAttempts($key, 1)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors(['email' => "Tunggu {$seconds} detik sebelum meminta kode baru."])->withInput();
        }

        RateLimiter::hit($key, 60);

        // Generate OTP 6 Digit
        $otp = rand(100000, 999999);

        // Hapus token lama
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Simpan token baru (di-hash)
        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => Hash::make($otp),
            'created_at' => Carbon::now(),
        ]);

        // Kirim Email
        Mail::send('emails.otp-code', ['otp' => $otp], function($message) use ($request) {
            $message->to($request->email)
                    ->subject('Kode Verifikasi Reset Password');
        });

        return redirect()->route('password.reset.form', ['email' => $request->email])
                         ->with('success', 'Kode verifikasi telah dikirim ke email Anda.');
    }

    public function showResetForm(Request $request)
    {
        return view('auth.reset-password', ['email' => $request->query('email')]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
            'password' => 'required|string|min:6|confirmed'
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (!$record) {
            return back()->withErrors(['otp' => 'Sesi reset password tidak ditemukan atau sudah digunakan.'])->withInput($request->only('email'));
        }

        // Cek batas waktu (5 menit)
        $createdAt = Carbon::parse($record->created_at);
        if ($createdAt->diffInMinutes(Carbon::now()) >= 5) {
            return back()->withErrors(['otp' => 'Kode OTP sudah kedaluwarsa. Silakan minta kode baru.'])->withInput($request->only('email'));
        }

        // Verifikasi Kode
        if (!Hash::check($request->otp, $record->token)) {
            return back()->withErrors(['otp' => 'Kode OTP salah.'])->withInput($request->only('email', 'otp'));
        }

        // Update Password
        User::where('email', $request->email)->update([
            'password' => Hash::make($request->password)
        ]);

        // Hapus record token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Password berhasil diubah. Silakan login.');
    }
}
