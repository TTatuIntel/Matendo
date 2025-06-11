<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HealthworkerController extends Controller
{
    public function index()
    {
        return view('healthworker.dashboard');
    }
}
