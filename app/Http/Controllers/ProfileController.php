<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([

            'name' => 'required|string|min:2|max:255',

            'lastname1' => 'required|string|min:2|max:255',

            'lastname2' => 'nullable|string|min:2|max:255',

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],

            'password' => 'nullable|string|min:6|confirmed',
        ]);

        // Datos básicos
        $user->name = $validated['name'];
        $user->lastname1 = $validated['lastname1'] ?? null;
        $user->lastname2 = $validated['lastname2'] ?? null;
        $user->email = $validated['email'];

        // Password opcional
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return response()->json([
            'message' => 'Perfil actualizado correctamente',
            'user' => $user->load('role')
        ]);
    }
}
