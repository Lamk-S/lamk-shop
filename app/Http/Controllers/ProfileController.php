<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('profile.index', compact('user'));
    }

    public function update(Request $request, User $profile)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $profile->id,
            'password' => 'nullable|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Ingrese un correo válido.',
            'email.unique' => 'Este correo ya está registrado.',
            'password.min' => 'La contraseña debe tener mínimo 8 caracteres.',
        ]);

        $data = $request->only('name', 'email');

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        $profile->update($data);

        return redirect()->route('profile.index')->with('success', 'Perfil actualizado');
    }
}