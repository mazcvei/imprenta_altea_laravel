<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\RolEnum;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
     // LOGIN
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = auth()->attempt($credentials)) {
            return response()->json([
                'error' => 'Credenciales incorrectas'
            ], 401);
        }

        return $this->respondWithToken($token);
    }

    // REGISTER
    public function register(Request $request)
    {
   
        $request->validate([
            'name' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    

        $user = User::create([
            'name' => $request->name,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rol_id' => RolEnum::CLIENTE->value, // client por defecto
        ]);

        return response()->json([
            'message' => 'Usuario creado correctamente'
        ], 201);
    }

    // PERFIL
    public function me()
    {
        return response()->json([
            'user' => auth()->user()->load('role')
        ]);
    }

    // LOGOUT
    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Logout correcto']);
    }

    // REFRESH TOKEN
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    // RESPUESTA TOKEN
    protected function respondWithToken($token)
    {
        $user = auth()->user()->load('role'); // ya autenticado correctamente
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => $user // cargar relación de rol para incluirla en la respuesta
        ]);
    }
}
