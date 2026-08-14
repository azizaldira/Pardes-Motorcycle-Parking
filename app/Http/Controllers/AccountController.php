<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BusinessSetting;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $message = "Dalam Pengembangan. ";
        if ($user->role === 'staff') {
            $message .= "Anda hanya dapat melihat dan mengubah akun Anda sendiri.";
        }
        return view('placeholder', compact('message'));
    }
}
