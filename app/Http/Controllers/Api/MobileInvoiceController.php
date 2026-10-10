<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ConfigurationCatalog;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MobileInvoiceController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:10240',
            ],
          
        ]);

        $user = $request->user();

        $catalogo = ConfigurationCatalog::where(
            'name',
            'Tipos de facturas'
        )->firstOrFail();

        $tipo = $catalogo->options()
    ->where('active', true)
    ->where('name', 'Combustible')
    ->firstOrFail();

        abort_unless(
            $user->role === 'admin' ||
            $user->tiposFacturas()
                ->where('configuration_options.id', $tipo->id)
                ->exists(),
            403,
            'No tienes permiso para enviar facturas de esta categoría.'
        );

        $imagePath = $request->file('image')
            ->store('facturas', 'public');

        try {
            $factura = Invoice::create([
                'user_id' => $user->id,
                'configuration_option_id' => $tipo->id,
                'image_path' => $imagePath,
                'received_at' => now(),
                'reviewed' => false,
            ]);
        } catch (\Throwable $e) {
            Storage::disk('public')->delete($imagePath);

            throw $e;
        }

        return response()->json([
            'message' => 'Factura recibida correctamente.',
            'invoice' => [
                'id' => $factura->id,
                'tipo' => $tipo->name,
                'estado' => 'Pendiente de procesar',
                'recibida' => $factura->received_at,
                'imagen' => Storage::disk('public')->url($imagePath),
            ],
        ], 201);
    }
}