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

    public function show(FinanceIncome $income)
    {
        $income->load(['customer', 'creator', 'payments.receiver']);
        $paid = (float) $income->payments->sum('amount_hnl');
        $outstanding = max(round((float) $income->total_hnl - $paid, 2), 0);

        return view('finances.incomes.show', compact('income', 'paid', 'outstanding'));
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
