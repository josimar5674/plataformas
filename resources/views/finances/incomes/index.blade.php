@extends('layouts.app')

@section('content')
<div style="max-width:1200px;margin:24px auto;padding:0 16px;">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;margin-bottom:22px;">
        <div><h1 style="margin:0;">💰 Ingresos</h1><p style="color:var(--text-secondary);margin:6px 0 0;">Ingresos en USD, equivalencia en lempiras y abonos recibidos.</p></div>
        <a href="{{ route('finances.incomes.create') }}" class="btn-primary-custom" style="text-decoration:none;">＋ Registrar ingreso</a>
    </div>
    <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:18px;">
        <input class="form-control" style="max-width:320px;" type="search" name="search" value="{{ request('search') }}" placeholder="Buscar por cliente o descripción">
        <select class="form-control" name="status" style="max-width:200px;">
            <option value="">Todos los estados</option>
            @foreach(['pending'=>'Pendiente','partial'=>'Parcial','paid'=>'Pagado'] as $key=>$label)
                <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
            @endforeach
        </select>
        <button class="btn-secondary" type="submit">Filtrar</button>
        <a class="btn-secondary" href="{{ route('finances.incomes.index') }}" style="text-decoration:none;">Limpiar</a>
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
