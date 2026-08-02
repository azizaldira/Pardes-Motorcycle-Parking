<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BusinessSetting;

class PlaceholderController extends Controller
{
    public function index()
    {
        return view('placeholder');
    }
}
