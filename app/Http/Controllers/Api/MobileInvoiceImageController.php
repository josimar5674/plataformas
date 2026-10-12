<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MobileInvoiceImageController extends Controller
{
    /**
     * Mostrar la imagen privada de una factura.
     */
    public function show(Request $request, Invoice $invoice)
    {
        $user = $request->user();

        // Verificar que el usuario esté autenticado.
        abort_unless($user, 401);

        // Solo el propietario o un administrador pueden acceder.
        abort_unless(
            (int) $invoice->user_id === (int) $user->id
                || $user->role === 'admin',
            403,
            'No tienes permiso para consultar esta factura.'
        );

        // Verificar que corresponda a una factura de combustible.
        $invoice->loadMissing('type');

        abort_unless(
            $invoice->type
                && $invoice->type->name === 'Combustible',
            404
        );

        // Comprobar que exista la imagen privada.
        abort_unless(
            $invoice->image_path
                && Storage::disk('local')->exists($invoice->image_path),
            404,
            'La imagen de la factura no existe.'
        );

        // Entregar la imagen sin exponer su ruta física.
        return Storage::disk('local')->response(
            $invoice->image_path,
            null,
            [
                'Cache-Control' => 'private, no-store, max-age=0',
                'X-Content-Type-Options' => 'nosniff',
            ]
        );
    }
}