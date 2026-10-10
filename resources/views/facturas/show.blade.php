@extends('layouts.app')

@section('content')

<div class="configuration-header">
    <div>
        <h1>🧾 {{ $tipoFactura->name }}</h1>
        <small>Consulta y revisión de facturas.</small>
    </div>

    <a href="{{ route('invoices.index') }}" class="btn-secondary">
        ← Volver
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div style="overflow-x:auto;margin-top:25px;">

    <table class="table">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Proveedor</th>
                 <th>Factura</th>
                <th>Número</th>
                <th>Importe</th>
                <th>Estado</th>
                <th>Revisión</th>
                <th>Procesamiento</th>
                
            </tr>
        </thead>

        <tbody>
           @forelse($facturas as $factura)
    <tr
        onclick="window.location.href='{{ route('invoices.edit', $factura) }}'"
        style="cursor:pointer;"
        title="Ver y editar factura"
    >
                    <td>
                      {{ $factura->invoice_date?->format('d/m/Y') ?? 'Pendiente' }}
                    </td>

                    <td>{{ $factura->provider_name }}</td>

                    <td>
    @if($factura->image_path)
        <a
            href="{{ asset('storage/' . $factura->image_path) }}"
            target="_blank"
            rel="noopener noreferrer"
            title="Abrir factura en tamaño completo"
        >
            <img
                src="{{ asset('storage/' . $factura->image_path) }}"
                alt="Factura de {{ $factura->provider_name ?? 'proveedor pendiente' }}"
                style="
                    width:65px;
                    height:65px;
                    object-fit:cover;
                    border-radius:8px;
                    border:1px solid var(--border);
                    cursor:pointer;
                "
            >
        </a>
    @else
        <span>Sin imagen</span>
    @endif
</td>

                    <td>{{ $factura->invoice_number ?? '—' }}</td>

                    <td>
                        L {{ number_format((float) $factura->amount, 2) }}
                    </td>
                    <td>
                        @if($factura->processed_at)
                            <span>Procesada</span>
                        @else
                            <a
                                href="{{ route('invoices.edit', $factura) }}"
                                class="btn-primary-custom"
                            >
                                Completar datos
                            </a>
                        @endif
                    </td>

                    <td>
                        {{ $factura->reviewed ? 'Revisada' : 'Pendiente' }}
                    </td>

                    <td>
                        <form
                            method="POST"
                            action="{{ route('invoices.toggle-reviewed', $factura) }}"
                        >
                            @csrf
                            @method('PATCH')

                           <form
                                method="POST"
                                action="{{ route('invoices.toggle-reviewed', $factura) }}"
                                onclick="event.stopPropagation();"
                            >
                                @csrf
                                @method('PATCH')

                                <button
                                    type="submit"
                                    class="{{ $factura->reviewed ? 'btn-reviewed' : 'btn-pending' }}"
                                >
                                    {{ $factura->reviewed ? '✓ Revisada' : 'Marcar revisada' }}
                                </button>
                            </form>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:25px;">
                        Todavía no hay facturas registradas en esta categoría.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>

<div style="margin-top:20px;">
    {{ $facturas->links() }}
</div>

@endsection