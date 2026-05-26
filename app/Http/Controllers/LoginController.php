<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            return redirect()->route('panel');
        }

        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password'], 'estado' => 1], $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Credenciales incorrectas o usuario inactivo.']);
        }

        $request->session()->regenerate();

        return redirect()->route('panel')->with('success', 'Bienvenido ' . Auth::user()->name);
    }
}