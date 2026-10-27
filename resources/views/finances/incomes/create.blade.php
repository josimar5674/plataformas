@extends('layouts.app')

@section('content')
<div style="max-width:900px;margin:24px auto;padding:0 16px;">
    <a href="{{ route('finances.incomes.index') }}">← Volver a ingresos</a>
    <h1 style="margin:16px 0 6px;">Registrar ingreso</h1>
    <p style="color:var(--text-secondary);">El importe base se ingresa en dólares. El sistema guarda la tasa del mes y calcula el impuesto y su equivalente en lempiras.</p>
    @if(!$exchangeRate)
        <div style="padding:14px;border-radius:8px;background:#fef3c7;color:#92400e;margin:18px 0;">No hay tasa de cambio configurada para {{ now()->format('m/Y') }}. Configura la tasa mensual en Configuraciones → Fiscal antes de registrar ingresos de este mes.</div>
    @else
        <div style="padding:12px 16px;border-radius:8px;background:var(--surface);border:1px solid var(--border);margin:18px 0;">Tasa de cambio aplicada ({{ now()->format('m/Y') }}): <strong>1 USD = L {{ number_format((float)$exchangeRate->rate, 4) }}</strong></div>
    @endif
    <form method="POST" action="{{ route('finances.incomes.store') }}" style="background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:22px;">
        @csrf
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(230px,1fr));gap:18px;">
            <div><label for="business_customer_id">Cliente *</label><select class="form-control" id="business_customer_id" name="business_customer_id" required><option value="">Selecciona un cliente</option>@foreach($customers as $customer)<option value="{{ $customer->id }}" @selected(old('business_customer_id') == $customer->id)>{{ $customer->nombre }}</option>@endforeach</select></div>
            <div><label for="income_date">Fecha del ingreso *</label><input class="form-control" id="income_date" type="date" name="income_date" value="{{ old('income_date', now()->toDateString()) }}" required></div>
            <div><label for="subtotal_usd">Subtotal en USD ($) *</label><input class="form-control" id="subtotal_usd" type="number" step="0.01" min="0.01" name="subtotal_usd" value="{{ old('subtotal_usd') }}" required></div>
            <div><label for="finance_tax_rate_id">Impuesto (selecciona uno) *</label><select class="form-control" id="finance_tax_rate_id" name="finance_tax_rate_id" required><option value="">Selecciona un impuesto</option>@foreach($taxRates as $tax)<option value="{{ $tax->id }}" data-percentage="{{ $tax->percentage }}" @selected(old('finance_tax_rate_id') == $tax->id)>{{ $tax->name }} ({{ rtrim(rtrim(number_format((float)$tax->percentage, 4), '0'), '.') }}%)</option>@endforeach</select></div>
        </div>
        <div style="margin-top:18px;"><label for="description">Descripción *</label><input class="form-control" id="description" name="description" maxlength="255" value="{{ old('description') }}" required></div>
        <div style="margin-top:18px;"><label for="notes">Notas</label><textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea></div>
        <div style="margin-top:18px;padding:16px;border-radius:8px;background:var(--background, #f8fafc);border:1px solid var(--border);">
            <strong>Vista previa (referencial)</strong>
            <div style="display:flex;justify-content:space-between;gap:12px;margin-top:10px;"><span>Impuesto estimado</span><strong id="preview-tax">$ 0.00</strong></div>
            <div style="display:flex;justify-content:space-between;gap:12px;margin-top:8px;"><span>Total estimado</span><strong id="preview-total">$ 0.00</strong></div>
            <small style="display:block;margin-top:8px;color:var(--text-secondary);">El cálculo definitivo se realiza en el servidor y se guarda con la tasa de cambio del mes correspondiente a la fecha del ingreso.</small>
        </div>
        <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:22px;">
            <a class="btn-secondary" href="{{ route('finances.incomes.index') }}" style="text-decoration:none;">Cancelar</a>
            <button class="btn-primary-custom" type="submit" @disabled(!$exchangeRate || $customers->isEmpty() || $taxRates->isEmpty())>Guardar ingreso</button>
        </div>
        @if($customers->isEmpty())<p style="color:#b91c1c;margin-top:12px;">No hay clientes comerciales registrados.</p>@endif
        @if($taxRates->isEmpty())<p style="color:#b91c1c;margin-top:12px;">No hay impuestos activos configurados.</p>@endif
    </form>
</div>
<script>
(function () {
    const subtotal = document.getElementById('subtotal_usd');
    const taxSelect = document.getElementById('finance_tax_rate_id');
    function updatePreview() {
        const amount = Math.max(Number(subtotal.value) || 0, 0);
        const option = taxSelect.options[taxSelect.selectedIndex];
        const percentage = Number(option?.dataset?.percentage) || 0;
        const tax = Math.round((amount * percentage / 100 + Number.EPSILON) * 100) / 100;
        document.getElementById('preview-tax').textContent = '$ ' + tax.toFixed(2);
        document.getElementById('preview-total').textContent = '$ ' + (Math.round((amount + tax + Number.EPSILON) * 100) / 100).toFixed(2);
    }
    subtotal.addEventListener('input', updatePreview);
    taxSelect.addEventListener('change', updatePreview);
    updatePreview();
})();
</script>
@endsection
