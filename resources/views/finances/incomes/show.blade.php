@extends('layouts.app')

@section('content')
@if(session('success'))<div class="alert alert-success" style="margin:16px auto;max-width:1100px;">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-danger" style="margin:16px auto;max-width:1100px;">{{ session('error') }}</div>@endif
@if($errors->any())<div class="alert alert-danger" style="margin:16px auto;max-width:1100px;">{{ $errors->first() }}</div>@endif
<div style="max-width:1100px;margin:24px auto;padding:0 16px;">
    <a href="{{ route('finances.incomes.index') }}" style="color:var(--text, #111827);">← Volver a ingresos</a>
    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:wrap;margin:16px 0 22px;">
        <div><h1 style="margin:0;">{{ $income->description }}</h1><p style="color:var(--text-secondary);margin:6px 0;">{{ $income->customer->nombre ?? 'Cliente no disponible' }} · {{ $income->income_date->format('d/m/Y') }}</p></div>
        <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
            <span style="display:inline-flex;align-items:center;min-height:40px;box-sizing:border-box;padding:0 14px;border-radius:20px;border:1px solid;{{ $income->status === 'paid' ? 'background:#dcfce7;color:#166534;border-color:#86efac;' : ($income->status === 'pending' ? 'background:#fef3c7;color:#92400e;border-color:#fcd34d;' : 'background:#dbeafe;color:#1d4ed8;border-color:#93c5fd;') }}">{{ ['pending'=>'Pendiente','partial'=>'Pago parcial','paid'=>'Pagado'][$income->status] ?? $income->status }}</span>
            <a class="btn-secondary" href="{{ route('finances.incomes.edit', $income) }}" style="display:inline-flex;align-items:center;justify-content:center;min-height:40px;box-sizing:border-box;padding:0 14px;border-radius:9px;text-decoration:none;">Editar ingreso</a>
            <form method="POST" action="{{ route('finances.incomes.destroy', $income) }}" onsubmit="return confirm('ATENCIÓN: se eliminará este ingreso junto con todos sus abonos e historial de pagos. Esta acción no se puede deshacer. ¿Deseas continuar?');">@csrf @method('DELETE')<button type="submit" class="btn-secondary" style="display:inline-flex;align-items:center;justify-content:center;min-height:40px;box-sizing:border-box;padding:0 14px;border-radius:9px;cursor:pointer;">Eliminar</button></form>
        </div>
    </div>
    @if($income->invoice_number || $income->inversion)
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:10px;padding:14px 18px;margin-bottom:18px;display:flex;gap:24px;flex-wrap:wrap;">
            @if($income->invoice_number)<div>Número de factura: <strong>{{ $income->invoice_number }}</strong></div>@endif
            @if($income->inversion)<div>Inversión: <strong>{{ $income->inversion->clave ? $income->inversion->clave.' — ' : '' }}{{ $income->inversion->nombre }}</strong></div>@endif
        </div>
    @endif
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:14px;margin-bottom:22px;">
        @foreach([['Subtotal USD','$ '.number_format((float)$income->subtotal_usd,2)],[$income->tax_name_snapshot === 'Impuestos por producto' ? 'Impuestos por producto' : 'Impuesto '.$income->tax_name_snapshot.' ('.$income->tax_percentage_snapshot.'%)','$ '.number_format((float)$income->tax_amount_usd,2)],['Total USD','$ '.number_format((float)$income->total_usd,2)],['Total en lempiras','L '.number_format((float)$income->total_hnl,2)],['Abonado','L '.number_format($paid,2)],['Saldo pendiente','L '.number_format($outstanding,2)]] as [$label,$value])
            <div style="padding:16px;background:var(--surface);border:1px solid var(--border);border-radius:10px;"><div style="font-size:13px;color:var(--text-secondary);">{{ $label }}</div><strong style="display:block;font-size:20px;margin-top:8px;">{{ $value }}</strong></div>
        @endforeach
    </div>
    <div style="display:flex;justify-content:flex-end;margin:-6px 0 12px;">
        <span title="Tipo de cambio histórico guardado para este ingreso" style="display:inline-flex;align-items:center;gap:7px;padding:7px 11px;border-radius:9px;background:#e0f2fe;border:1px solid #bae6fd;color:#075985;font-size:12px;font-weight:700;white-space:nowrap;">💱 USD = L {{ number_format((float)$income->exchange_rate,2) }}</span>
    </div>
    @if($income->items->isNotEmpty())
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:20px;margin-bottom:22px;">
            <h2 style="margin-top:0;">Productos del ingreso</h2>
            <div style="overflow-x:auto;"><table style="width:100%;border-collapse:collapse;min-width:760px;">
                <thead><tr style="text-align:left;border-bottom:1px solid var(--border);"><th style="padding:10px;">Producto inmobiliario</th><th style="padding:10px;text-align:right;">Cantidad</th><th style="padding:10px;text-align:right;">Precio unitario</th><th style="padding:10px;">Impuesto</th><th style="padding:10px;text-align:right;">Subtotal</th><th style="padding:10px;text-align:right;">Total</th></tr></thead>
                <tbody>
                @foreach($income->items as $item)
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:10px;">{{ $item->product_name_snapshot }}</td>
                        <td style="padding:10px;text-align:right;">{{ number_format((float)$item->quantity,2) }}</td>
                        <td style="padding:10px;text-align:right;">$ {{ number_format((float)$item->unit_price_usd,2) }}</td>
                        <td style="padding:10px;">{{ $item->tax_name_snapshot ?? 'Sin impuesto' }} ({{ number_format((float)$item->tax_percentage_snapshot,2) }}%)<br><small>$ {{ number_format((float)$item->tax_amount_usd,2) }}</small></td>
                        <td style="padding:10px;text-align:right;">$ {{ number_format((float)$item->subtotal_usd,2) }}</td>
                        <td style="padding:10px;text-align:right;font-weight:600;">$ {{ number_format((float)$item->total_usd,2) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table></div>
        </div>
    @endif
    @if($income->notes)
    <div style="display:flex;gap:8px;align-items:flex-start;background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:12px 15px;margin-bottom:22px;color:#475569;">
        <span aria-hidden="true">📝</span><div><strong style="font-size:13px;">Notas</strong><div style="margin-top:3px;">{{ $income->notes }}</div></div>
    </div>
    @endif
    @if($outstanding > 0)
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:20px;margin-bottom:22px;">
        <h2 style="margin-top:0;">Registrar abono en lempiras</h2>
        <p style="color:var(--text-secondary);">Saldo actual: <strong>L {{ number_format($outstanding,2) }}</strong>. No se permiten pagos superiores al saldo.</p>
        <form method="POST" action="{{ route('finances.incomes.payments.store', $income) }}">
            @csrf
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px;">
                <div><label>Fecha de pago *</label><input class="form-control" type="date" name="payment_date" value="{{ old('payment_date',now()->toDateString()) }}" required></div>
                <div><label>Importe (HNL) *</label><input class="form-control" type="number" name="amount_hnl" min="0.01" max="{{ number_format($outstanding,2,'.','') }}" step="0.01" value="{{ old('amount_hnl') }}" required></div>
                <div><label>Forma de pago *</label><select class="form-control" id="payment_method" name="payment_method" required><option value="">Selecciona</option>@foreach(['Efectivo','Transferencia','Tarjeta','Cheque','Otro'] as $method)<option value="{{ $method }}" @selected(old('payment_method')===$method)>{{ $method }}</option>@endforeach</select></div>
                <div id="bank-account-field"><label>Cuenta bancaria <span id="bank-required-mark">*</span></label><select class="form-control" id="finance_bank_account_id" name="finance_bank_account_id"><option value="">Selecciona una cuenta</option>@foreach($bankAccounts as $bankAccount)<option value="{{ $bankAccount->id }}" @selected(old('finance_bank_account_id') == $bankAccount->id)>{{ $bankAccount->bank_name }} — {{ $bankAccount->account_name }} · {{ $bankAccount->account_number }} ({{ $bankAccount->currency }})</option>@endforeach</select><small style="color:var(--text-secondary);">Obligatoria si la forma de pago es transferencia.</small></div>
                <div><label>Referencia</label><input class="form-control" name="reference" maxlength="120" value="{{ old('reference') }}"></div>
            </div>
            <div style="margin-top:14px;"><label>Notas</label><textarea class="form-control" name="notes" rows="2">{{ old('notes') }}</textarea></div>
            <div style="text-align:right;margin-top:16px;"><button class="btn-primary-custom" type="submit">Guardar abono</button></div>
        </form>
    </div>
    @endif
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:20px;">
        <h2 style="margin-top:0;">Historial de pagos</h2>
        <div style="overflow-x:auto;"><table style="width:100%;border-collapse:collapse;min-width:650px;">
            <thead><tr style="text-align:left;border-bottom:1px solid var(--border);"><th style="padding:10px;">Fecha</th><th style="padding:10px;">Forma de pago</th><th style="padding:10px;">Referencia</th><th style="padding:10px;">Recibido por</th><th style="padding:10px;text-align:right;">Importe HNL</th></tr></thead>
            <tbody>@forelse($income->payments->sortByDesc('payment_date') as $payment)
                <tr style="border-bottom:1px solid var(--border);"><td style="padding:10px;">{{ $payment->payment_date->format('d/m/Y') }}</td><td style="padding:10px;">{{ $payment->payment_method }}@if($payment->bankAccount)<br><small>{{ $payment->bankAccount->bank_name }} — {{ $payment->bankAccount->account_number }}</small>@endif</td><td style="padding:10px;">{{ $payment->reference ?: '—' }}</td><td style="padding:10px;">{{ $payment->receiver->name ?? '—' }}</td><td style="padding:10px;text-align:right;">L {{ number_format((float)$payment->amount_hnl,2) }}</td></tr>
            @empty<tr><td colspan="5" style="padding:22px;text-align:center;color:var(--text-secondary);">Aún no hay pagos registrados.</td></tr>@endforelse</tbody>
        </table></div>
    </div>
</div>
<script>
(function () {
    const method = document.getElementById('payment_method');
    const account = document.getElementById('finance_bank_account_id');
    const mark = document.getElementById('bank-required-mark');
    function syncBankAccount() {
        if (!method || !account) return;
        const required = method.value === 'Transferencia';
        account.required = required;
        if (mark) mark.style.visibility = required ? 'visible' : 'hidden';
    }
    method?.addEventListener('change', syncBankAccount);
    syncBankAccount();
})();
</script>
@endsection
