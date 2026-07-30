<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function index()
    {
        $user = request()->user();
        return view('profile.index', compact('user'));
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = $request->user();
        $data = $request->validated();

        try {
            DB::transaction(function () use ($data, $user) {
                $payload = [
                    'name' => $data['name'],
                    'email' => $data['email'],
                ];

                if (!empty($data['password'])) {
                    $payload['password'] = bcrypt($data['password']);
                }

                $user->update($payload);
            });

            return redirect()
                ->route('profile.index')
                ->with('success', 'Tu perfil y credenciales han sido actualizados con éxito.');
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Error al actualizar el perfil: ' . $e->getMessage()])
                ->withInput();
        }
    }
}