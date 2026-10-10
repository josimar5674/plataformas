<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMobileUserIsActive
{
  public function handle(Request $request, Closure $next): Response
{
    $user = $request->user();

    if (!$user || (int) $user->estado !== 1) {
        if ($user) {
            $user->currentAccessToken()?->delete();
        }

        return response()->json([
            'message' => 'Tu cuenta está desactivada. Se ha cerrado tu sesión.',
            'code' => 'ACCOUNT_INACTIVE',
        ], 403);
    }

    $token = $user->currentAccessToken();

    if (!$token) {
        return response()->json([
            'message' => 'No existe una sesión válida.',
        ], 401);
    }

    $device = $user->trustedDevices()
        ->where('device_uuid', $token->name)
        ->where('is_active', true)
        ->first();

    if (!$device) {
        return response()->json([
            'message' => 'Este dispositivo todavía no está autorizado.',
            'code' => 'DEVICE_NOT_TRUSTED',
        ], 403);
    }

    $device->update([
        'last_used_at' => now(),
    ]);

    return $next($request);
}
}