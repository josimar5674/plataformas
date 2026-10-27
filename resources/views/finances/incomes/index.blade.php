@extends('layouts.app')

@section('content')
@if(session('success'))<div class="alert alert-success" style="margin:16px 0;">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-danger" style="margin:16px 0;">{{ session('error') }}</div>@endif
@if($errors->any())<div class="alert alert-danger" style="margin:16px 0;">{{ $errors->first() }}</div>@endif
<div style="max-width:1200px;margin:24px auto;padding:0 16px;">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;margin-bottom:22px;">
        <div><h1 style="margin:0;">💰 Ingresos</h1><p style="color:var(--text-secondary);margin:6px 0 0;">Ingresos en USD, equivalencia en lempiras y abonos recibidos.</p></div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <form method="POST" action="{{ route('finances.incomes.copy-previous-month') }}" onsubmit="return confirm('Se copiarán los ingresos del mes anterior que no existan todavía en el mes actual. Quedarán pendientes y sin abonos copiados. ¿Continuar?');">@csrf<button type="submit" class="btn-secondary">⟳ Copiar mes anterior</button></form>
            <a href="{{ route('finances.incomes.create') }}" class="btn-primary-custom" style="text-decoration:none;">＋ Registrar ingreso</a>
        </div>
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
        <table style="width:100%;border-collapse:collapse;min-width:850px;">
            <thead><tr style="text-align:left;border-bottom:1px solid var(--border);">
                <th style="padding:13px;">Fecha</th><th style="padding:13px;">Cliente</th><th style="padding:13px;">Descripción</th><th style="padding:13px;">Total USD</th><th style="padding:13px;">Total HNL</th><th style="padding:13px;">Abonado HNL</th><th style="padding:13px;">Saldo HNL</th><th style="padding:13px;">Estado</th><th style="padding:13px;"></th>
            </tr></thead>
            <tbody>
            @forelse($incomes as $income)
                @php($paid = (float)($income->payments_sum_amount_hnl ?? 0))
                <tr style="border-bottom:1px solid var(--border);">
                    <td style="padding:13px;">{{ $income->income_date->format('d/m/Y') }}</td>
                    <td style="padding:13px;">{{ $income->customer->nombre ?? '—' }}</td>
                    <td style="padding:13px;">{{ $income->description }}</td>
                    <td style="padding:13px;">$ {{ number_format((float)$income->total_usd, 2) }}</td>
                    <td style="padding:13px;">L {{ number_format((float)$income->total_hnl, 2) }}</td>
                    <td style="padding:13px;">L {{ number_format($paid, 2) }}</td>
                    <td style="padding:13px;">L {{ number_format(max((float)$income->total_hnl-$paid, 0), 2) }}</td>
                    <td style="padding:13px;">{{ ['pending'=>'Pendiente','partial'=>'Parcial','paid'=>'Pagado'][$income->status] ?? $income->status }}</td>
                    <td style="padding:13px;"><a href="{{ route('finances.incomes.show', $income) }}">Ver</a></td>
                </tr>
            @empty
                <tr><td colspan="9" style="padding:28px;text-align:center;color:var(--text-secondary);">Todavía no hay ingresos registrados.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:18px;">{{ $incomes->links() }}</div>
</div>
@endsection
