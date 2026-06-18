<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BusinessSetting;

class PlaceholderController extends Controller
{
    public function index()
    {
        $business = BusinessSetting::first();
        return view('placeholder', compact('business'));
    }
}
