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

        return view('auth.login', [
            'lastEmail' => request()->cookie('pos_last_email'),
        ]);
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
            'estado' => 1,
        ], $request->boolean('remember'))) {

            return back()->withErrors([
                'email' => 'Las credenciales no coinciden o la cuenta se encuentra suspendida.',
            ])->withInput($request->only('email', 'remember'));
        }

        $request->session()->regenerate();

        $firstName = explode(' ', Auth::user()->name)[0];

        return redirect()->intended(route('panel'))
            ->with('success', 'Bienvenido de nuevo, ' . $firstName . '.');
    }
}
