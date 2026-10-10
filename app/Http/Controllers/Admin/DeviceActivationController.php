<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DeviceActivationService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\TrustedDevice;

class DeviceActivationController extends Controller
{
    /**
     * Mostrar la pantalla de activación de dispositivos.
     */
    public function index(): View
    {
        return view('admin.device-activation.index');
    }

    /**
     * Generar un PIN para registrar un dispositivo.
     */
    public function generate(
        Request $request,
        DeviceActivationService $activationService
    ) {
        $admin = $request->user();

        abort_unless(
            $admin && $admin->role === 'admin' && (int) $admin->estado === 1,
            403
        );

        $pin = $activationService->generatePin($admin);

        return back()->with('activation_pin', $pin);
    }
    public function destroy(TrustedDevice $device)
{
    $device->delete();

    return redirect()
        ->back()
        ->with('success', 'Dispositivo eliminado correctamente.');
}
}