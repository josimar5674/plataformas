<?php

namespace App\Http\Controllers;

use App\Models\InvoiceType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class InvoiceTypeController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:invoice_types,name',
        ]);

        $slug = Str::slug($request->name);

        if (InvoiceType::where('slug', $slug)->exists()) {
            return back()
                ->withInput()
                ->with('error', 'Ya existe un tipo de factura con ese código.');
        }

        InvoiceType::create([
            'name' => $request->name,
            'slug' => $slug,
            'active' => true,
        ]);

        return redirect('/configuraciones?section=invoice-types')
            ->with('success', 'Tipo de factura creado correctamente.');
    }

    public function toggle(InvoiceType $invoiceType)
    {
        $invoiceType->update([
            'active' => !$invoiceType->active,
        ]);

        return redirect('/configuraciones?section=invoice-types')
            ->with('success', 'Estado actualizado correctamente.');
    }
}