<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return view('profile.index', compact('user'));
    }

    public function update(UpdateUserRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $data = $request->validated();

        $payload = [
            'name' => $data['name'],
            'email' => $data['email'],
        ];

        if (!empty($data['password'])) {
            $payload['password'] = $data['password'];
        }

        $user->update($payload);

        return redirect()
            ->route('profile.index')
            ->with('success', 'Perfil actualizado correctamente');
    }
}