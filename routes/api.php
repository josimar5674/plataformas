<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MobileAuthController;
use App\Http\Controllers\Api\DeviceActivationController;
use App\Http\Middleware\EnsureMobileUserIsActive;
use App\Http\Controllers\Api\MobileInvoiceController;


// Inicio de sesión: no requiere token previo.
Route::prefix('mobile')->group(function () {
    Route::post('/login', [MobileAuthController::class, 'login'])
        ->middleware('throttle:5,1');

 // Requiere autenticación, pero todavía no exige un dispositivo confiable.
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [MobileAuthController::class, 'logout']);

    // Generación de PIN para administradores.
    Route::post(
        '/admin/device-activation/pin',
        [DeviceActivationController::class, 'generate']
    );

    // Activación de un dispositivo nuevo.
    Route::post(
        '/activate-device',
        [DeviceActivationController::class, 'activate']
    );
});

    // Las funcionalidades normales requieren usuario activo
    // y dispositivo confiable.
    Route::middleware([
        'auth:sanctum',
        EnsureMobileUserIsActive::class,
    ])->group(function () {
        Route::get('/user', function (Request $request) {
            return response()->json([
                'user' => $request->user(),
            ]);
        });

        /*
         * Las demás API protegidas de ZHX irán aquí.
         */
Route::post('/invoices', [
    MobileInvoiceController::class,
    'store'
]);
        
    });
});

// Conservamos la ruta existente de Sanctum.
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');