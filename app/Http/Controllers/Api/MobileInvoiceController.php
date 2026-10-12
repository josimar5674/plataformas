<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ConfigurationCatalog;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MobileInvoiceController extends Controller
{
    /**
     * Registrar una nueva factura de combustible.
     */
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

       $imagePath = $request->file('image')->store('facturas', 'local');

        try {
            $factura = Invoice::create([
                'user_id' => $user->id,
                'configuration_option_id' => $tipo->id,
                'image_path' => $imagePath,
                'received_at' => now(),
                'reviewed' => false,
            ]);
        } catch (\Throwable $e) {
            Storage::disk('local')->delete($imagePath);

            throw $e;
        }

        return response()->json([
            'message' => 'Factura recibida correctamente.',
            'invoice' => $this->formatInvoice($factura->load('type')),
        ], 201);
    }

    /**
     * Listar las facturas del usuario autenticado.
     */
    public function index(Request $request)
    {
        $facturas = Invoice::with('type')
            ->where('user_id', $request->user()->id)
            ->whereHas('type', function ($query) {
                $query->where('name', 'Combustible');
            })
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json([
            'data' => $facturas->getCollection()
                ->map(fn (Invoice $factura) => $this->formatInvoice($factura))
                ->values(),
            'current_page' => $facturas->currentPage(),
            'last_page' => $facturas->lastPage(),
            'per_page' => $facturas->perPage(),
            'total' => $facturas->total(),
        ]);
    }

    /**
     * Cambiar la imagen de una factura todavía pendiente.
     */
    public function updateImage(Request $request, Invoice $invoice)
    {
        $user = $request->user();

        // Solo el propietario puede cambiar su factura.
        abort_unless(
            (int) $invoice->user_id === (int) $user->id,
            403,
            'No tienes permiso para modificar esta factura.'
        );

        // No permitir cambios después del procesamiento o revisión.
        abort_if(
            $invoice->processed_at !== null || $invoice->reviewed,
            422,
            'Esta factura ya no permite cambiar su imagen.'
        );

        // Limitar la edición a facturas de combustible.
        $invoice->loadMissing('type');

        abort_unless(
            $invoice->type &&
            $invoice->type->name === 'Combustible',
            422,
            'Esta operación solo está disponible para facturas de combustible.'
        );

        $request->validate([
            'image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:10240',
            ],
        ]);

        $newImagePath = $request->file('image')
            ->store('facturas', 'local');

        $oldImagePath = $invoice->image_path;

        try {
            $invoice->image_path = $newImagePath;
            $invoice->save();
        } catch (\Throwable $e) {
            Storage::disk('local')->delete($newImagePath);

            throw $e;
        }

        // Eliminar la imagen anterior únicamente después de guardar
        // correctamente la nueva.
        if (
            $oldImagePath &&
            $oldImagePath !== $newImagePath
        ) {
            Storage::disk('local')->delete($oldImagePath);
        }

        return response()->json([
            'message' => 'Imagen de la factura actualizada correctamente.',
            'invoice' => $this->formatInvoice($invoice->fresh()->load('type')),
        ]);
    }

    /**
     * Formatear los datos que necesita la aplicación móvil.
     */
    private function formatInvoice(Invoice $factura): array
    {
        $canEditImage = $factura->processed_at === null
            && !$factura->reviewed;

        return [
            'id' => $factura->id,
            'tipo' => $factura->type?->name,
            'proveedor' => $factura->provider_name,
            'numero' => $factura->invoice_number,
            'fecha' => $factura->invoice_date?->format('Y-m-d'),
            'importe' => $factura->amount,
            'descripcion' => $factura->description,
            'imagen' => $factura->image_path
                ? url("/api/mobile/invoices/{$factura->id}/image")
                : null,
            'estado' => $this->invoiceStatus($factura),
            'revisada' => $factura->reviewed,
            'recibida' => $factura->received_at?->toIso8601String(),
            'procesada' => $factura->processed_at?->toIso8601String(),
            'can_edit_image' => $canEditImage,
        ];
    }

    /**
     * Obtener el estado visible de la factura.
     */
    private function invoiceStatus(Invoice $factura): string
    {
        if ($factura->reviewed) {
            return 'Revisada';
        }

        if ($factura->processed_at !== null) {
            return 'Procesada';
        }

        return 'Pendiente de procesar';
    }
}