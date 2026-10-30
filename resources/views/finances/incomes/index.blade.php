@extends('layouts.app')

@section('content')
<style>
.finance-income-page { max-width:1500px !important; }
.finance-income-table thead { background:#eef2ff; color:#27346b; }
.finance-income-table tbody tr { transition:background-color .18s ease; }
.finance-income-table tbody tr:hover { background:#74D4FF !important; }
.finance-income-table th { white-space:nowrap; font-size:13px; }
.finance-income-table td { vertical-align:middle; }
.finance-income-table .money { white-space:nowrap; font-variant-numeric:tabular-nums; }
.finance-income-table .status-pill { display:inline-flex; align-items:center; padding:5px 10px; border-radius:999px; font-size:12px; font-weight:700; white-space:nowrap; }
.finance-income-table .status-pending { background:#fff1c2; color:#8a5200; }
.finance-income-table .status-partial { background:#dbeafe; color:#1d4ed8; }
.finance-income-table .status-paid { background:#dcfce7; color:#166534; }
.finance-income-table .income-view-link { display:inline-flex; padding:6px 11px; border-radius:8px; background:#e0e7ff; color:#3730a3; font-weight:600; text-decoration:none; }
.finance-income-table .income-view-link:hover { background:#74D4FF; }

.finance-income-header-actions { display:flex; align-items:stretch; gap:10px; flex-wrap:wrap; }
.finance-income-header-actions form { display:flex; margin:0; }
.finance-income-header-actions .btn-primary-custom,
.finance-income-header-actions .btn-secondary,
.finance-income-header-actions button { min-height:44px; height:44px; box-sizing:border-box; display:inline-flex; align-items:center; justify-content:center; padding:0 18px; border-radius:10px; font:inherit; font-weight:600; line-height:1; white-space:nowrap; }
.finance-income-header-actions a { text-decoration:none; }
</style>

<div class="finance-income-page" style="width:calc(100% - 32px);margin:24px auto;padding:0;">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;margin-bottom:22px;">
        <div><h1 style="margin:0;">Ingresos</h1><p style="color:var(--text-secondary);margin:6px 0 0;">Ingresos en USD, equivalencia en lempiras y abonos recibidos.</p></div>
        <div class="finance-income-header-actions">
            <form method="POST" action="{{ route('finances.incomes.copy-previous-month') }}" onsubmit="return confirm('Se copiarán los ingresos del mes anterior que no existan todavía en el mes actual. Quedarán pendientes y sin abonos copiados. ¿Continuar?');">@csrf<button type="submit" class="btn-secondary">⟳ Copiar mes anterior</button></form>
            <a href="{{ route('finances.incomes.create') }}" class="btn-primary-custom" style="text-decoration:none;">＋ Registrar ingreso</a>
        </div>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:12px;margin:20px 0;">
        <div style="padding:16px;border:1px solid var(--border);border-radius:12px;background:var(--surface);"><div style="font-size:13px;color:var(--text-secondary);">Ingresos encontrados</div><strong style="display:block;font-size:24px;margin-top:6px;">{{ number_format((int)$summary->count) }}</strong></div>
        <div style="padding:16px;border:1px solid var(--border);border-radius:12px;background:var(--surface);"><div style="font-size:13px;color:var(--text-secondary);">Total USD</div><strong style="display:block;font-size:24px;margin-top:6px;">$ {{ number_format((float)$summary->total_usd, 2) }}</strong></div>
        <div style="padding:16px;border:1px solid var(--border);border-radius:12px;background:var(--surface);"><div style="font-size:13px;color:var(--text-secondary);">Total HNL</div><strong style="display:block;font-size:24px;margin-top:6px;">L {{ number_format((float)$summary->total_hnl, 2) }}</strong></div>
        <div style="padding:16px;border:1px solid var(--border);border-radius:12px;background:var(--surface);"><div style="font-size:13px;color:var(--text-secondary);">Abonado HNL</div><strong style="display:block;font-size:24px;margin-top:6px;">L {{ number_format((float)$summary->paid_hnl, 2) }}</strong></div>
        <div style="padding:16px;border:1px solid var(--border);border-radius:12px;background:var(--surface);"><div style="font-size:13px;color:var(--text-secondary);">Saldo HNL</div><strong style="display:block;font-size:24px;margin-top:6px;">L {{ number_format((float)$summary->balance_hnl, 2) }}</strong></div>
    </div>

    <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:18px;align-items:end;">
        <div><label for="filter_year" style="display:block;margin-bottom:5px;">Año</label><select class="form-control" id="filter_year" name="year" style="min-width:115px;">
            <option value="">Todos</option>
            @for($year = now()->year + 1; $year >= 2020; $year--)
                <option value="{{ $year }}" @selected((string)request('year', now()->year) === (string)$year)>{{ $year }}</option>
            @endfor
        </select></div>
        <div><label for="filter_month" style="display:block;margin-bottom:5px;">Mes</label><select class="form-control" id="filter_month" name="month" style="min-width:145px;">
            <option value="">Todos</option>
            @foreach([1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre'] as $monthNumber=>$monthName)
                <option value="{{ $monthNumber }}" @selected((string)request('month', now()->month) === (string)$monthNumber)>{{ $monthName }}</option>
            @endforeach
        </select></div>
        <input class="form-control" style="max-width:320px;" type="search" name="search" value="{{ request('search') }}" placeholder="Buscar por cliente o descripción">
        <select class="form-control" name="status" style="max-width:200px;">
            <option value="">Todos los estados</option>
            @foreach(['pending'=>'Pendiente','partial'=>'Parcial','paid'=>'Pagado'] as $key=>$label)
                <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
            @endforeach
        </select>
        <button class="btn-secondary" type="submit">Filtrar</button>
    </form>
    <div style="overflow-x:auto;background:var(--surface);border:1px solid var(--border);border-radius:12px;">
        <table class="finance-income-table" style="width:100%;border-collapse:collapse;min-width:1320px;">
            <thead><tr style="text-align:left;border-bottom:1px solid var(--border);">
                <th style="padding:13px;">Fecha</th><th style="padding:13px;">Cliente</th><th style="padding:13px;">Descripción</th><th style="padding:13px;">Factura</th><th style="padding:13px;">Productos</th><th style="padding:13px;">Total USD</th><th style="padding:13px;">Total HNL</th><th style="padding:13px;">Abonado HNL</th><th style="padding:13px;">Saldo HNL</th><th style="padding:13px;">Estado</th><th style="padding:13px;"></th>
            </tr></thead>
            <tbody>
            @forelse($incomes as $income)
                @php($paid = (float)($income->payments_sum_amount_hnl ?? 0))
                <tr style="border-bottom:1px solid var(--border);">
                    <td style="padding:13px;">{{ $income->income_date->format('d/m/Y') }}</td>
                    <td style="padding:13px;">{{ $income->customer->nombre ?? '—' }}</td>
                    <td style="padding:13px;">{{ $income->description }}</td>
                    <td style="padding:13px;">{{ $income->invoice_number ?: '—' }}</td>
                    <td style="padding:13px;">@if($income->items->isNotEmpty()){{ $income->items->pluck('product_name_snapshot')->implode(', ') }}@else — @endif</td>
                    <td class="money" style="padding:13px;">$ {{ number_format((float)$income->total_usd, 2) }}</td>
                    <td class="money" style="padding:13px;">L {{ number_format((float)$income->total_hnl, 2) }}</td>
                    <td class="money" style="padding:13px;">L {{ number_format($paid, 2) }}</td>
                    <td class="money" style="padding:13px;">L {{ number_format(max((float)$income->total_hnl-$paid, 0), 2) }}</td>
                    <td style="padding:13px;"><span class="status-pill {{ $income->status === 'paid' ? 'status-paid' : ($income->status === 'partial' ? 'status-partial' : 'status-pending') }}">{{ ['pending'=>'Pendiente','partial'=>'Parcial','paid'=>'Pagado'][$income->status] ?? $income->status }}</span></td>
                    <td style="padding:13px;"><a class="income-view-link" href="{{ route('finances.incomes.show', $income) }}">Ver →</a></td>
                </tr>
            @empty
                <tr><td colspan="11" style="padding:28px;text-align:center;color:var(--text-secondary);">Todavía no hay ingresos registrados.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:18px;">{{ $incomes->links() }}</div>
</div>
@endsection
