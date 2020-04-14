<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ResetController extends Controller
{
    public function index(Request $request)
    { 
        return view('reset_options');
    }
}
