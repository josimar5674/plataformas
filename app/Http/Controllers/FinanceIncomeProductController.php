<?php

namespace App\Http\Controllers;

use App\Models\BusinessCustomer;
use App\Models\Comercial;
use App\Models\FinanceExchangeRate;
use App\Models\FinanceIncome;
use App\Models\FinanceIncomeItem;
use App\Models\FinanceTaxRate;
use App\Models\Inversion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FinanceIncomeProductController extends Controller
{
    public function create()
    {
        $customers = BusinessCustomer::orderBy('nombre')->get();
        $investments = Inversion::orderBy('clave')->orderBy('nombre')->get();
        $taxRates = FinanceTaxRate::where('active', true)->orderBy('sort_order')->orderBy('percentage')->get();
        $exchangeRate = FinanceExchangeRate::where('year', now()->year)->where('month', now()->month)->first();

        return view('finances.incomes.form-products', compact('customers', 'investments', 'taxRates', 'exchangeRate'));
    }

    public function products(Inversion $inversion): JsonResponse
    {
        $products = Comercial::where('inversion_id', $inversion->id)
            ->orderBy('producto')
            ->get(['id', 'inversion_id', 'producto', 'cantidad', 'unidad', 'precio_unitario', 'subtotal'])
            ->map(fn (Comercial $item) => [
                'id' => $item->id,
                'name' => $item->producto ?: ('Producto comercial #' . $item->id),
                'price' => (float) ($item->precio_unitario ?? 0),
                'available_quantity' => (float) ($item->cantidad ?? 0),
                'unit' => $item->unidad,
            ]);

        return response()->json($products);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'business_customer_id' => ['required', 'exists:business_customers,id'],
            'inversion_id' => ['required', 'exists:inversiones,id'],
            'income_date' => ['required', 'date'],
            'invoice_number' => ['nullable', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.comercial_id' => ['required', 'integer', 'distinct', 'exists:comercial,id'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0', 'max:999999999'],
            'items.*.unit_price_usd' => ['required', 'numeric', 'gte:0', 'max:999999999999'],
            'items.*.finance_tax_rate_id' => ['required', 'exists:finance_tax_rates,id'],
        ]);

        $date = Carbon::parse($data['income_date']);
        $exchangeRate = FinanceExchangeRate::where('year', $date->year)->where('month', $date->month)->first();
        if (!$exchangeRate) {
            throw ValidationException::withMessages(['income_date' => "No hay una tasa de cambio configurada para {$date->format('m/Y')}. Configúrala antes de registrar ingresos."]);
        }

        $items = $this->prepareItems($data['items'], (int) $data['inversion_id'], (float) $exchangeRate->rate);
        $subtotalUsd = round(collect($items)->sum('subtotal_usd'), 2);
        $taxUsd = round(collect($items)->sum('tax_amount_usd'), 2);
        $totalUsd = round($subtotalUsd + $taxUsd, 2);
        $rate = (float) $exchangeRate->rate;

        $income = DB::transaction(function () use ($request, $data, $items, $subtotalUsd, $taxUsd, $totalUsd, $rate) {
            $income = FinanceIncome::create([
                'business_customer_id' => $data['business_customer_id'],
                'inversion_id' => $data['inversion_id'],
                'description' => $data['description'],
                'income_date' => $data['income_date'],
                'invoice_number' => $data['invoice_number'] ?? null,
                'subtotal_usd' => $subtotalUsd,
                'finance_tax_rate_id' => null,
                'tax_name_snapshot' => 'Impuestos por producto',
                'tax_percentage_snapshot' => 0,
                'tax_amount_usd' => $taxUsd,
                'total_usd' => $totalUsd,
                'exchange_rate' => $rate,
                'subtotal_hnl' => round($subtotalUsd * $rate, 2),
                'tax_amount_hnl' => round($taxUsd * $rate, 2),
                'total_hnl' => round($totalUsd * $rate, 2),
                'status' => 'pending',
                'notes' => $data['notes'] ?? null,
                'created_by' => $request->user()->id,
            ]);
            $income->items()->createMany($items);
            return $income;
        });

        return redirect()->route('finances.incomes.show', $income)->with('success', 'Ingreso y productos registrados correctamente.');
    }

    public function edit(FinanceIncome $income)
    {
        $income->load('items');
        $customers = BusinessCustomer::orderBy('nombre')->get();
        $investments = Inversion::orderBy('clave')->orderBy('nombre')->get();
        $taxRates = FinanceTaxRate::where('active', true)->orderBy('sort_order')->orderBy('percentage')->get();
        $exchangeRate = FinanceExchangeRate::where('year', $income->income_date->year)->where('month', $income->income_date->month)->first();

        return view('finances.incomes.form-products', compact('income', 'customers', 'investments', 'taxRates', 'exchangeRate'));
    }

    public function update(Request $request, FinanceIncome $income)
    {
        $data = $request->validate([
            'business_customer_id' => ['required', 'exists:business_customers,id'],
            'inversion_id' => ['required', 'exists:inversiones,id'],
            'income_date' => ['required', 'date'],
            'invoice_number' => ['nullable', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.comercial_id' => ['required', 'integer', 'distinct', 'exists:comercial,id'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0', 'max:999999999'],
            'items.*.unit_price_usd' => ['required', 'numeric', 'gte:0', 'max:999999999999'],
            'items.*.finance_tax_rate_id' => ['required', 'exists:finance_tax_rates,id'],
        ]);

        $date = Carbon::parse($data['income_date']);
        $exchangeRate = FinanceExchangeRate::where('year', $date->year)->where('month', $date->month)->first();
        if (!$exchangeRate) {
            throw ValidationException::withMessages(['income_date' => "No hay tasa de cambio configurada para {$date->format('m/Y')}."]);
        }

        $items = $this->prepareItems($data['items'], (int) $data['inversion_id'], (float) $exchangeRate->rate);
        $subtotalUsd = round(collect($items)->sum('subtotal_usd'), 2);
        $taxUsd = round(collect($items)->sum('tax_amount_usd'), 2);
        $totalUsd = round($subtotalUsd + $taxUsd, 2);
        $rate = (float) $exchangeRate->rate;
        $totalHnl = round($totalUsd * $rate, 2);
        $paid = (float) $income->payments()->sum('amount_hnl');
        if ($totalHnl < $paid) {
            throw ValidationException::withMessages(['items' => 'El total nuevo no puede ser menor que los abonos ya recibidos (L ' . number_format($paid, 2) . ').']);
        }

        DB::transaction(function () use ($income, $data, $items, $subtotalUsd, $taxUsd, $totalUsd, $rate, $paid, $totalHnl) {
            $income->update([
                'business_customer_id' => $data['business_customer_id'],
                'inversion_id' => $data['inversion_id'],
                'description' => $data['description'],
                'income_date' => $data['income_date'],
                'invoice_number' => $data['invoice_number'] ?? null,
                'subtotal_usd' => $subtotalUsd,
                'finance_tax_rate_id' => null,
                'tax_name_snapshot' => 'Impuestos por producto',
                'tax_percentage_snapshot' => 0,
                'tax_amount_usd' => $taxUsd,
                'total_usd' => $totalUsd,
                'exchange_rate' => $rate,
                'subtotal_hnl' => round($subtotalUsd * $rate, 2),
                'tax_amount_hnl' => round($taxUsd * $rate, 2),
                'total_hnl' => $totalHnl,
                'status' => $paid <= 0 ? 'pending' : ($paid >= $totalHnl ? 'paid' : 'partial'),
                'notes' => $data['notes'] ?? null,
            ]);
            $income->items()->delete();
            $income->items()->createMany($items);
        });

        return redirect()->route('finances.incomes.show', $income)->with('success', 'Ingreso actualizado correctamente.');
    }

    private function prepareItems(array $submittedItems, int $investmentId, float $exchangeRate): array
    {
        $prepared = [];
        foreach ($submittedItems as $submitted) {
            $product = Comercial::where('inversion_id', $investmentId)->find($submitted['comercial_id']);
            if (!$product) {
                throw ValidationException::withMessages(['items' => 'Uno de los productos seleccionados no pertenece a la inversión elegida.']);
            }

            $tax = FinanceTaxRate::where('active', true)->find($submitted['finance_tax_rate_id']);
            if (!$tax) {
                throw ValidationException::withMessages(['items' => 'Uno de los impuestos seleccionados no está activo.']);
            }

            // Se precarga el precio desde Comercial en el navegador; el servidor acepta ajustes manuales y los valida.
            $quantity = round((float) $submitted['quantity'], 2);
            $unitPrice = round((float) $submitted['unit_price_usd'], 2);
            $subtotal = round($quantity * $unitPrice, 2);
            $taxAmount = round($subtotal * ((float) $tax->percentage / 100), 2);
            $total = round($subtotal + $taxAmount, 2);

            $prepared[] = [
                'comercial_id' => $product->id,
                'product_name_snapshot' => $product->producto ?: ('Producto comercial #' . $product->id),
                'quantity' => $quantity,
                'unit_price_usd' => $unitPrice,
                'subtotal_usd' => $subtotal,
                'finance_tax_rate_id' => $tax->id,
                'tax_name_snapshot' => $tax->name,
                'tax_percentage_snapshot' => $tax->percentage,
                'tax_amount_usd' => $taxAmount,
                'total_usd' => $total,
                'subtotal_hnl' => round($subtotal * $exchangeRate, 2),
                'tax_amount_hnl' => round($taxAmount * $exchangeRate, 2),
                'total_hnl' => round($total * $exchangeRate, 2),
            ];
        }
        return $prepared;
    }
}
