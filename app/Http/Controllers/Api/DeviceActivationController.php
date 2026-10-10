<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\DeviceActivationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeviceActivationController extends Controller
{
    /**
     * Generar un PIN global de activación.
     */
    public function generate(
        Request $request,
        DeviceActivationService $activationService
    ): JsonResponse {
        /** @var User $user */
        $user = $request->user();

        if ($user->role !== 'admin') {
            return response()->json([
                'message' => 'No tienes permisos para generar PIN de activación.',
            ], 403);
        }

        if ((int) $user->estado !== 1) {
            return response()->json([
                'message' => 'Tu cuenta está desactivada.',
            ], 403);
        }

        $pin = $activationService->generatePin($user);

        return response()->json([
            'message' => 'PIN generado correctamente.',
            'pin' => $pin,
            'expires_at' => now()->addMinute()->toIso8601String(),
            'expires_in_seconds' => 60,
        ]);
    }

    /**
 * Activar un dispositivo mediante un PIN temporal.
 */
public function activate(
    Request $request,
    DeviceActivationService $activationService
): JsonResponse {
    $user = $request->user();

    if (!$user || (int) $user->estado !== 1) {
        return response()->json([
            'message' => 'Tu cuenta está desactivada.',
            'code' => 'ACCOUNT_INACTIVE',
        ], 403);
    }

    $data = $request->validate([
        'pin' => ['required', 'digits:6'],
        'device_uuid' => ['required', 'uuid'],
        'device_name' => ['nullable', 'string', 'max:100'],
        'platform' => ['nullable', 'string', 'max:30'],
    ]);

    $device = $activationService->activateDevice(
        $user,
        $data['pin'],
        $data['device_uuid'],
        $data['device_name'] ?? null,
        $data['platform'] ?? null
    );

    return response()->json([
        'message' => 'Dispositivo registrado correctamente.',
        'device' => [
            'uuid' => $device->device_uuid,
            'name' => $device->device_name,
            'registered_at' => $device->registered_at,
        ],
    ]);
}
}