<?php

namespace App\Services;

use App\Models\DeviceActivationPin;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class DeviceActivationService
{
    /**
     * Genera un PIN de activación de un solo uso.
     */
  /**
 * Genera un único PIN global de activación.
 */
public function generatePin(User $admin): string
{
    return DB::transaction(function () use ($admin) {

        // Invalidar todos los PIN anteriores que aún no se han utilizado.
        DeviceActivationPin::query()
            ->whereNull('used_at')
            ->update([
                'used_at' => now(),
            ]);

        // Generar un PIN aleatorio de seis dígitos.
        $pin = (string) random_int(100000, 999999);

        DeviceActivationPin::create([
            'generated_by' => $admin->id,
            'pin_hash' => Hash::make($pin),
            'expires_at' => now()->addMinute(),
            'used_at' => null,
        ]);

        return $pin;
    });

    
}

/**
 * Registra un dispositivo confiable para un usuario.
 */
public function registerTrustedDevice(
    User $user,
    string $deviceUuid,
    ?string $deviceName = null,
    ?string $platform = null
): \App\Models\TrustedDevice {
    return \App\Models\TrustedDevice::updateOrCreate(
        ['device_uuid' => $deviceUuid],
        [
            'user_id' => $user->id,
            'device_name' => $deviceName,
            'platform' => $platform,
            'is_active' => true,
            'registered_at' => now(),
            'last_used_at' => now(),
        ]
    );
}

    /**
     * Valida un PIN y lo marca como utilizado.
     */
    public function consumePin(string $pin): DeviceActivationPin
    {
        return DB::transaction(function () use ($pin) {
            $records = DeviceActivationPin::query()
                ->whereNull('used_at')
                ->where('expires_at', '>', now())
                ->lockForUpdate()
                ->get();

            $activationPin = $records->first(
                fn (DeviceActivationPin $record) =>
                    Hash::check($pin, $record->pin_hash)
            );

            if (!$activationPin) {
                throw ValidationException::withMessages([
                    'pin' => ['El PIN es inválido, ha expirado o ya fue utilizado.'],
                ]);
            }

            $activationPin->update([
                'used_at' => now(),
            ]);

            return $activationPin;
        });
    }

    /**
 * Valida el PIN y registra el dispositivo en una sola transacción.
 */
public function activateDevice(
    User $user,
    string $pin,
    string $deviceUuid,
    ?string $deviceName = null,
    ?string $platform = null
): \App\Models\TrustedDevice {
    return DB::transaction(function () use (
        $user,
        $pin,
        $deviceUuid,
        $deviceName,
        $platform
    ) {
        $activationPin = DeviceActivationPin::query()
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->lockForUpdate()
            ->get()
            ->first(
                fn (DeviceActivationPin $record) =>
                    Hash::check($pin, $record->pin_hash)
            );

        if (!$activationPin) {
            throw ValidationException::withMessages([
                'pin' => [
                    'El PIN es incorrecto, ha expirado o ya fue utilizado.',
                ],
            ]);
        }

        $device = \App\Models\TrustedDevice::updateOrCreate(
            ['device_uuid' => $deviceUuid],
            [
                'user_id' => $user->id,
                'device_name' => $deviceName,
                'platform' => $platform,
                'is_active' => true,
                'registered_at' => now(),
                'last_used_at' => now(),
            ]
        );

        $activationPin->update([
            'used_at' => now(),
        ]);

        return $device;
    });
}
}