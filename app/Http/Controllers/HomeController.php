<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(){
        if(!Auth::check()){
            return view('welcome');
        }
        return view('panel.index');
    }
}
