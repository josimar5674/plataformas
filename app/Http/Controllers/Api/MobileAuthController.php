<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class MobileAuthController extends Controller
{
    /**
     * Iniciar sesión desde la aplicación móvil.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['required', 'string', 'max:100'],
            'device_uuid' => ['required', 'uuid'],
        ]);

        $user = \App\Models\User::where(
            'email',
            $credentials['email']
        )->first();

        if (
            !$user ||
            !Hash::check($credentials['password'], $user->password)
        ) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        // Comprobar que el usuario esté activo, si existe ese atributo.
       if ((int) $user->estado !== 1) {
    return response()->json([
        'message' => 'Tu cuenta está desactivada.'
    ], 403);
}

        // Crear un token de acceso para el dispositivo.
                $token = $user->createToken(
                $credentials['device_uuid']
            );

        return response()->json([
            'message' => 'Inicio de sesión exitoso.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name ?? trim(
                    ($user->first_name ?? '') . ' ' .
                    ($user->last_name ?? '')
                ),
                'email' => $user->email,
            ],
            'token' => $token->plainTextToken,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Cerrar sesión y revocar el token utilizado.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada correctamente.',
        ]);
    }
}