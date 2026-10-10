<?php

namespace App\Http\Controllers;

use App\Models\ConfigurationCatalog;
use App\Models\ConfigurationOption;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class InvoiceController extends Controller
{
    /**
     * Mostrar las categorías de facturas autorizadas.
     */
    public function index()
    {
        $user = auth()->user();

        $catalogo = ConfigurationCatalog::where(
            'name',
            'Tipos de facturas'
        )->firstOrFail();

        $tipos = $catalogo->options()
            ->where('active', true)
            ->whereIn('id', function ($query) use ($user) {
                $query->select('configuration_option_id')
                    ->from('user_invoice_type')
                    ->where('user_id', $user->id);
            })
            ->orderBy('name')
            ->get();

        return view('facturas.index', compact('tipos'));
    }

    /**
     * Mostrar las facturas de una categoría autorizada.
     */
public function show(string $tipo)
{
    $user = auth()->user();

    $catalogo = ConfigurationCatalog::where(
        'name',
        'Tipos de facturas'
    )->firstOrFail();

    // Buscar la categoría por su slug
    $tipoFactura = $catalogo->options()
        ->where('active', true)
        ->get()
        ->first(fn ($option) =>
            Str::slug($option->name) === $tipo
        );

    abort_if(!$tipoFactura, 404);

    // Verificar autorización
    abort_unless(
        $user->role === 'admin' ||
        $user->tiposFacturas()
            ->where('configuration_options.id', $tipoFactura->id)
            ->exists(),
        403,
        'No tienes permiso para consultar esta categoría de facturas.'
    );

    // Obtener facturas de la categoría
  $facturas = Invoice::with('user')
    ->where('configuration_option_id', $tipoFactura->id)
    ->orderByDesc('created_at')
    ->paginate(15);

    return view(
        'facturas.show',
        compact('tipoFactura', 'facturas')
    );
}

    /**
     * Marcar una factura como revisada o pendiente.
     */
    public function toggleReviewed(Invoice $invoice)
    {
        $user = auth()->user();

        abort_unless(
            $user->role === 'admin' ||
            (
                $invoice->user_id === $user->id &&
                $user->tiposFacturas()
                    ->where(
                        'configuration_options.id',
                        $invoice->configuration_option_id
                    )
                    ->exists()
            ),
            403,
            'No tienes permiso para modificar esta factura.'
        );

        $invoice->reviewed = !$invoice->reviewed;
        $invoice->save();

        return back()->with(
            'success',
            'Estado de revisión actualizado.'
        );
    }

    public function edit(Invoice $invoice)
{
    $user = auth()->user();

    $this->authorizeInvoiceAccess($invoice, $user);

    $invoice->load('user', 'type');

    return view('facturas.edit', compact('invoice'));
}

public function update(Request $request, Invoice $invoice)
{
    $user = auth()->user();

    $this->authorizeInvoiceAccess($invoice, $user);

    $validated = $request->validate([
        'invoice_date' => ['required', 'date'],
        'provider_name' => ['required', 'string', 'max:255'],
        'invoice_number' => ['nullable', 'string', 'max:100'],
        'amount' => ['required', 'numeric', 'min:0.01'],
        'description' => ['nullable', 'string', 'max:2000'],
    ]);

    $validated['processed_at'] = now();

    $invoice->update($validated);

    return redirect()
        ->route('invoices.show', \Illuminate\Support\Str::slug($invoice->type->name))
        ->with('success', 'Factura procesada correctamente.');
}

private function authorizeInvoiceAccess(Invoice $invoice, $user): void
{
    abort_unless(
        $user->role === 'admin' ||
        $user->tiposFacturas()
            ->where('configuration_options.id', $invoice->configuration_option_id)
            ->exists(),
        403,
        'No tienes permiso para acceder a esta factura.'
    );
}
}