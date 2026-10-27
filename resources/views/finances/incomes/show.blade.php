@extends('layouts.app')

@section('content')
<div style="max-width:1100px;margin:24px auto;padding:0 16px;">
    <a href="{{ route('finances.incomes.index') }}">← Volver a ingresos</a>
    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:wrap;margin:16px 0 22px;">
        <div><h1 style="margin:0;">{{ $income->description }}</h1><p style="color:var(--text-secondary);margin:6px 0;">{{ $income->customer->nombre ?? 'Cliente no disponible' }} · {{ $income->income_date->format('d/m/Y') }}</p></div>
        <span style="padding:8px 12px;border-radius:20px;background:var(--surface);border:1px solid var(--border);">{{ ['pending'=>'Pendiente','partial'=>'Pago parcial','paid'=>'Pagado'][$income->status] ?? $income->status }}</span>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:14px;margin-bottom:22px;">
        @foreach([['Subtotal USD','$ '.number_format((float)$income->subtotal_usd,2)],['Impuesto '.$income->tax_name_snapshot.' ('.$income->tax_percentage_snapshot.'%)','$ '.number_format((float)$income->tax_amount_usd,2)],['Total USD','$ '.number_format((float)$income->total_usd,2)],['Total en lempiras','L '.number_format((float)$income->total_hnl,2)],['Abonado','L '.number_format($paid,2)],['Saldo pendiente','L '.number_format($outstanding,2)]] as [$label,$value])
            <div style="padding:16px;background:var(--surface);border:1px solid var(--border);border-radius:10px;"><div style="font-size:13px;color:var(--text-secondary);">{{ $label }}</div><strong style="display:block;font-size:20px;margin-top:8px;">{{ $value }}</strong></div>
        @endforeach
    </div>
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:20px;margin-bottom:22px;">
        <h2 style="margin-top:0;">Detalle del cálculo guardado</h2>
        <p>Tipo de cambio histórico: <strong>1 USD = L {{ number_format((float)$income->exchange_rate,4) }}</strong></p>
        <p>Impuesto guardado: <strong>{{ $income->tax_name_snapshot }} — {{ number_format((float)$income->tax_percentage_snapshot,4) }}%</strong></p>
        @if($income->notes)<p>Notas: {{ $income->notes }}</p>@endif
    </div>
    @if($outstanding > 0)
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:20px;margin-bottom:22px;">
        <h2 style="margin-top:0;">Registrar abono en lempiras</h2>
        <p style="color:var(--text-secondary);">Saldo actual: <strong>L {{ number_format($outstanding,2) }}</strong>. No se permiten pagos superiores al saldo.</p>
        <form method="POST" action="{{ route('finances.incomes.payments.store', $income) }}">
            @csrf
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px;">
                <div><label>Fecha de pago *</label><input class="form-control" type="date" name="payment_date" value="{{ old('payment_date',now()->toDateString()) }}" required></div>
                <div><label>Importe (HNL) *</label><input class="form-control" type="number" name="amount_hnl" min="0.01" max="{{ number_format($outstanding,2,'.','') }}" step="0.01" value="{{ old('amount_hnl') }}" required></div>
                <div><label>Forma de pago *</label><select class="form-control" name="payment_method" required><option value="">Selecciona</option>@foreach(['Efectivo','Transferencia','Tarjeta','Cheque','Otro'] as $method)<option value="{{ $method }}" @selected(old('payment_method')===$method)>{{ $method }}</option>@endforeach</select></div>
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
                <tr style="border-bottom:1px solid var(--border);"><td style="padding:10px;">{{ $payment->payment_date->format('d/m/Y') }}</td><td style="padding:10px;">{{ $payment->payment_method }}</td><td style="padding:10px;">{{ $payment->reference ?: '—' }}</td><td style="padding:10px;">{{ $payment->receiver->name ?? '—' }}</td><td style="padding:10px;text-align:right;">L {{ number_format((float)$payment->amount_hnl,2) }}</td></tr>
            @empty<tr><td colspan="5" style="padding:22px;text-align:center;color:var(--text-secondary);">Aún no hay pagos registrados.</td></tr>@endforelse</tbody>
        </table></div>
    </div>
</div>
@endsection
