<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function dashboard(){
            //return Auth::user();
            return view('dashboard');
    }
}
