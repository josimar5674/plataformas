<?php

namespace App\Http\Controllers;

use App\Models\BusinessCustomer;
use App\Models\FinanceExchangeRate;
use App\Models\FinanceBankAccount;
use App\Models\FinanceIncome;
use App\Models\FinancePayment;
use App\Models\FinanceTaxRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FinanceIncomeController extends Controller
{
    public function index(Request $request)
    {
        $incomes = FinanceIncome::with(['customer', 'creator'])
            ->withSum('payments', 'amount_hnl')
            ->when(!$request->hasAny(['year', 'month']), fn ($q) => $q->whereYear('income_date', now()->year)->whereMonth('income_date', now()->month))
            ->when($request->filled('year'), fn ($q) => $q->whereYear('income_date', (int) $request->input('year')))
            ->when($request->filled('month'), fn ($q) => $q->whereMonth('income_date', (int) $request->input('month')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = trim($request->string('search')->toString());
                $q->where(function ($sub) use ($search) {
                    $sub->where('description', 'like', "%{$search}%")
                        ->orWhereHas('customer', fn ($customer) => $customer->where('nombre', 'like', "%{$search}%"));
                });
            })
            ->orderByDesc('income_date')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('finances.incomes.index', compact('incomes'));
    }

    public function create()
    {
        $exchangeRate = FinanceExchangeRate::where('year', now()->year)
            ->where('month', now()->month)
            ->first();

        $customers = BusinessCustomer::orderBy('nombre')->get();
        $taxRates = FinanceTaxRate::where('active', true)
            ->orderBy('sort_order')->orderBy('percentage')->get();

        return view('finances.incomes.create', compact('exchangeRate', 'customers', 'taxRates'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'business_customer_id' => ['required', 'exists:business_customers,id'],
            'description' => ['required', 'string', 'max:255'],
            'income_date' => ['required', 'date'],
            'subtotal_usd' => ['required', 'numeric', 'gt:0', 'max:999999999999'],
            'finance_tax_rate_id' => ['required', 'exists:finance_tax_rates,id'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $taxRate = FinanceTaxRate::where('active', true)->findOrFail($data['finance_tax_rate_id']);
        $date = \Illuminate\Support\Carbon::parse($data['income_date']);
        $exchangeRate = FinanceExchangeRate::where('year', $date->year)
            ->where('month', $date->month)->first();

        if (!$exchangeRate) {
            throw ValidationException::withMessages([
                'income_date' => "No hay una tasa de cambio configurada para {$date->format('m/Y')}. Configúrala antes de registrar el ingreso.",
            ]);
        }

        $subtotalUsd = round((float) $data['subtotal_usd'], 2);
        $taxUsd = round($subtotalUsd * ((float) $taxRate->percentage / 100), 2);
        $totalUsd = round($subtotalUsd + $taxUsd, 2);
        $rate = (float) $exchangeRate->rate;

        FinanceIncome::create([
            'business_customer_id' => $data['business_customer_id'],
            'description' => $data['description'],
            'income_date' => $data['income_date'],
            'subtotal_usd' => $subtotalUsd,
            'finance_tax_rate_id' => $taxRate->id,
            'tax_name_snapshot' => $taxRate->name,
            'tax_percentage_snapshot' => $taxRate->percentage,
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

        return redirect()->route('finances.incomes.index')->with('success', 'Ingreso registrado correctamente.');
    }


    public function edit(FinanceIncome $income)
    {
        $income->load('payments');
        $customers = BusinessCustomer::orderBy('nombre')->get();
        $taxRates = FinanceTaxRate::where('active', true)->orderBy('sort_order')->orderBy('percentage')->get();
        $exchangeRate = FinanceExchangeRate::where('year', $income->income_date->year)
            ->where('month', $income->income_date->month)->first();

        return view('finances.incomes.edit', compact('income', 'customers', 'taxRates', 'exchangeRate'));
    }

    public function update(Request $request, FinanceIncome $income)
    {
        $data = $request->validate([
            'business_customer_id' => ['required', 'exists:business_customers,id'],
            'description' => ['required', 'string', 'max:255'],
            'income_date' => ['required', 'date'],
            'subtotal_usd' => ['required', 'numeric', 'gt:0', 'max:999999999999'],
            'finance_tax_rate_id' => ['required', 'exists:finance_tax_rates,id'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $taxRate = FinanceTaxRate::where('active', true)->findOrFail($data['finance_tax_rate_id']);
        $date = \\Illuminate\\Support\\Carbon::parse($data['income_date']);
        $exchangeRate = FinanceExchangeRate::where('year', $date->year)->where('month', $date->month)->first();
        if (!$exchangeRate) {
            throw ValidationException::withMessages(['income_date' => "No hay tasa de cambio configurada para {$date->format('m/Y')}."]);
        }

        $subtotalUsd = round((float) $data['subtotal_usd'], 2);
        $taxUsd = round($subtotalUsd * ((float) $taxRate->percentage / 100), 2);
        $totalUsd = round($subtotalUsd + $taxUsd, 2);
        $rate = (float) $exchangeRate->rate;
        $totalHnl = round($totalUsd * $rate, 2);
        $paid = (float) $income->payments()->sum('amount_hnl');

        if ($totalHnl < $paid) {
            throw ValidationException::withMessages(['subtotal_usd' => 'El total nuevo no puede ser menor que los abonos ya recibidos (L '.number_format($paid, 2).').']);
        }

        $income->update([
            'business_customer_id' => $data['business_customer_id'],
            'description' => $data['description'],
            'income_date' => $data['income_date'],
            'subtotal_usd' => $subtotalUsd,
            'finance_tax_rate_id' => $taxRate->id,
            'tax_name_snapshot' => $taxRate->name,
            'tax_percentage_snapshot' => $taxRate->percentage,
            'tax_amount_usd' => $taxUsd,
            'total_usd' => $totalUsd,
            'exchange_rate' => $rate,
            'subtotal_hnl' => round($subtotalUsd * $rate, 2),
            'tax_amount_hnl' => round($taxUsd * $rate, 2),
            'total_hnl' => $totalHnl,
            'status' => $paid <= 0 ? 'pending' : ($paid >= $totalHnl ? 'paid' : 'partial'),
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()->route('finances.incomes.show', $income)->with('success', 'Ingreso actualizado correctamente.');
    }

    public function destroy(FinanceIncome $income)
    {
        if ($income->payments()->exists()) {
            return redirect()->route('finances.incomes.show', $income)
                ->with('error', 'No se puede eliminar un ingreso que ya tiene abonos registrados. Corrige o elimina primero sus pagos.');
        }

        $income->delete();

        return redirect()->route('finances.incomes.index')->with('success', 'Ingreso eliminado correctamente.');
    }

    public function generateFromPreviousMonth(Request $request)
    {
        $target = now()->startOfMonth();
        $source = $target->copy()->subMonth();
        $sourceIncomes = FinanceIncome::whereYear('income_date', $source->year)
            ->whereMonth('income_date', $source->month)->with('taxRate')->get();

        if ($sourceIncomes->isEmpty()) {
            return redirect()->route('finances.incomes.index')->with('error', 'No hay ingresos en el mes anterior para copiar.');
        }

        $exchangeRate = FinanceExchangeRate::where('year', $target->year)->where('month', $target->month)->first();
        if (!$exchangeRate) {
            return redirect()->route('finances.incomes.index')->with('error', 'Configura primero la tasa de cambio del mes actual.');
        }

        $created = 0;
        $skipped = 0;
        DB::transaction(function () use ($sourceIncomes, $target, $exchangeRate, &$created, &$skipped, $request) {
            foreach ($sourceIncomes as $sourceIncome) {
                $exists = FinanceIncome::where('copied_from_income_id', $sourceIncome->id)
                    ->whereYear('income_date', $target->year)
                    ->whereMonth('income_date', $target->month)->exists();
                if ($exists) {
                    $skipped++;
                    continue;
                }

                $tax = $sourceIncome->taxRate;
                $percentage = $tax && $tax->active ? (float) $tax->percentage : (float) $sourceIncome->tax_percentage_snapshot;
                $taxName = $tax && $tax->active ? $tax->name : $sourceIncome->tax_name_snapshot;
                $subtotalUsd = (float) $sourceIncome->subtotal_usd;
                $taxUsd = round($subtotalUsd * $percentage / 100, 2);
                $totalUsd = round($subtotalUsd + $taxUsd, 2);
                $rate = (float) $exchangeRate->rate;
                $day = min($sourceIncome->income_date->day, $target->daysInMonth);
                FinanceIncome::create([
                    'business_customer_id' => $sourceIncome->business_customer_id,
                    'description' => $sourceIncome->description,
                    'income_date' => $target->copy()->day($day)->toDateString(),
                    'subtotal_usd' => $subtotalUsd,
                    'finance_tax_rate_id' => $tax && $tax->active ? $tax->id : null,
                    'tax_name_snapshot' => $taxName,
                    'tax_percentage_snapshot' => $percentage,
                    'tax_amount_usd' => $taxUsd,
                    'total_usd' => $totalUsd,
                    'exchange_rate' => $rate,
                    'subtotal_hnl' => round($subtotalUsd * $rate, 2),
                    'tax_amount_hnl' => round($taxUsd * $rate, 2),
                    'total_hnl' => round($totalUsd * $rate, 2),
                    'status' => 'pending',
                    'notes' => $sourceIncome->notes,
                    'created_by' => $request->user()->id,
                    'copied_from_income_id' => $sourceIncome->id,
                ]);
                $created++;
            }
        });

        return redirect()->route('finances.incomes.index')
            ->with('success', "Se generaron {$created} ingresos pendientes para {$target->format('m/Y')}. Se omitieron {$skipped} duplicados.");
    }

    public function show(FinanceIncome $income)
    {
        $income->load(['customer', 'creator', 'payments.receiver', 'payments.bankAccount']);
        $bankAccounts = FinanceBankAccount::where('active', true)->orderBy('bank_name')->orderBy('account_name')->get();
        $paid = (float) $income->payments->sum('amount_hnl');
        $outstanding = max(round((float) $income->total_hnl - $paid, 2), 0);

        return view('finances.incomes.show', compact('income', 'paid', 'outstanding', 'bankAccounts'));
    }

    public function storePayment(Request $request, FinanceIncome $income)
    {
        $data = $request->validate([
            'payment_date' => ['required', 'date'],
            'amount_hnl' => ['required', 'numeric', 'gt:0', 'max:999999999999'],
            'payment_method' => ['required', 'string', 'max:80'],
            'finance_bank_account_id' => ['nullable', 'required_if:payment_method,Transferencia', 'exists:finance_bank_accounts,id'],
            'reference' => ['nullable', 'string', 'max:120'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        DB::transaction(function () use ($request, $income, $data) {
            $lockedIncome = FinanceIncome::whereKey($income->id)->lockForUpdate()->firstOrFail();
            $paid = (float) $lockedIncome->payments()->sum('amount_hnl');
            $balance = round((float) $lockedIncome->total_hnl - $paid, 2);
            $amount = round((float) $data['amount_hnl'], 2);

            if ($balance <= 0) {
                throw ValidationException::withMessages(['amount_hnl' => 'Este ingreso ya está pagado por completo.']);
            }

            if ($amount > $balance) {
                throw ValidationException::withMessages(['amount_hnl' => 'El pago no puede superar el saldo pendiente de L ' . number_format($balance, 2) . '.']);
            }

            if (!empty($data['finance_bank_account_id']) && !FinanceBankAccount::where('active', true)->whereKey($data['finance_bank_account_id'])->exists()) {
                throw ValidationException::withMessages(['finance_bank_account_id' => 'La cuenta bancaria seleccionada no está activa.']);
            }

            FinancePayment::create([
                'finance_income_id' => $lockedIncome->id,
                'payment_date' => $data['payment_date'],
                'amount_hnl' => $amount,
                'payment_method' => $data['payment_method'],
                'finance_bank_account_id' => $data['finance_bank_account_id'] ?? null,
                'reference' => $data['reference'] ?? null,
                'notes' => $data['notes'] ?? null,
                'received_by' => $request->user()->id,
            ]);

            $newPaid = $paid + $amount;
            $lockedIncome->update(['status' => $newPaid >= (float) $lockedIncome->total_hnl ? 'paid' : 'partial']);
        });

        return redirect()->route('finances.incomes.show', $income)->with('success', 'Pago registrado correctamente.');
    }
}
