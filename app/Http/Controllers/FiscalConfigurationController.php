<?php

namespace App\Http\Controllers;

use App\Models\FinanceExchangeRate;
use App\Models\FinanceTaxRate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FiscalConfigurationController extends Controller
{
    public function storeTax(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120', 'unique:finance_tax_rates,name'],
            'percentage' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        FinanceTaxRate::create([
            'name' => trim($validated['name']),
            'percentage' => $validated['percentage'],
            'active' => true,
            'sort_order' => 0,
        ]);

        return redirect('/configuraciones?section=fiscal')
            ->with('success', 'Impuesto creado correctamente.');
    }

    public function updateTax(Request $request, FinanceTaxRate $taxRate): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:120',
                Rule::unique('finance_tax_rates', 'name')->ignore($taxRate->id),
            ],
            'percentage' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        $taxRate->update([
            'name' => trim($validated['name']),
            'percentage' => $validated['percentage'],
        ]);

        return redirect('/configuraciones?section=fiscal')
            ->with('success', 'Impuesto actualizado correctamente.');
    }

    public function toggleTax(FinanceTaxRate $taxRate): RedirectResponse
    {
        $taxRate->update(['active' => ! $taxRate->active]);

        return redirect('/configuraciones?section=fiscal')
            ->with('success', 'Estado del impuesto actualizado.');
    }

    public function storeExchangeRate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'year' => ['required', 'integer', 'min:2020', 'max:2200'],
            'month' => ['required', 'integer', 'between:1,12'],
            'rate' => ['required', 'numeric', 'gt:0', 'max:99999999.9999'],
        ]);

        FinanceExchangeRate::updateOrCreate(
            [
                'year' => $validated['year'],
                'month' => $validated['month'],
            ],
            [
                'rate' => $validated['rate'],
                'updated_by' => $request->user()->id,
            ]
        );

        return redirect('/configuraciones?section=fiscal&year='
            . $validated['year'] . '&month=' . $validated['month'])
            ->with('success', 'Tasa de cambio mensual guardada correctamente.');
    }
}
