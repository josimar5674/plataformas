@extends('layouts.app')

@section('content')

<div class="configuration-header">
    <div>
        <h1>Procesar factura</h1>
        <small>
            Enviada por: {{ $invoice->user->name }}
        </small>
    </div>

    <a
        href="{{ route('invoices.show', \Illuminate\Support\Str::slug($invoice->type->name)) }}"
        class="btn-secondary"
    >
        ← Volver
    </a>
</div>

<div style="
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(320px,1fr));
    gap:25px;
    margin-top:25px;
">

    {{-- IMAGEN DE LA FACTURA --}}
    <section style="
        background:var(--surface);
        border:1px solid var(--border);
        border-radius:12px;
        padding:20px;
    ">
        <h3>Imagen recibida</h3>

        @if($invoice->image_path)
            <a
                href="{{ asset('storage/'.$invoice->image_path) }}"
                target="_blank"
            >
                <img
                    src="{{ asset('storage/'.$invoice->image_path) }}"
                    alt="Factura enviada"
                    style="
                        width:100%;
                        max-height:600px;
                        object-fit:contain;
                        margin-top:15px;
                        border-radius:8px;
                    "
                >
            </a>
        @else
            <p>No hay una imagen asociada a esta factura.</p>
        @endif
    </section>

    {{-- DATOS DE LA FACTURA --}}
    <section style="
        background:var(--surface);
        border:1px solid var(--border);
        border-radius:12px;
        padding:20px;
    ">
        <h3>Datos de la factura</h3>

        <form
            method="POST"
            action="{{ route('invoices.update', $invoice) }}"
        >
            @csrf
            @method('PUT')

            <div class="form-group" style="margin-top:20px;">
                <label for="invoice_date">Fecha de emisión</label>

                <input
                    type="date"
                    id="invoice_date"
                    name="invoice_date"
                    value="{{ old('invoice_date', $invoice->invoice_date?->format('Y-m-d')) }}"
                    required
                    style="width:100%;"
                >
            </div>

            <div class="form-group" style="margin-top:15px;">
                <label for="provider_name">Proveedor o gasolinera</label>

                <input
                    type="text"
                    id="provider_name"
                    name="provider_name"
                    value="{{ old('provider_name', $invoice->provider_name) }}"
                    maxlength="255"
                    required
                    style="width:100%;"
                >
            </div>

            <div class="form-group" style="margin-top:15px;">
                <label for="invoice_number">Número de factura</label>

                <input
                    type="text"
                    id="invoice_number"
                    name="invoice_number"
                    value="{{ old('invoice_number', $invoice->invoice_number) }}"
                    maxlength="100"
                    style="width:100%;"
                >
            </div>

            <div class="form-group" style="margin-top:15px;">
                <label for="amount">Total de la factura (L)</label>

                <input
                    type="number"
                    id="amount"
                    name="amount"
                    value="{{ old('amount', $invoice->amount) }}"
                    min="0.01"
                    step="0.01"
                    required
                    style="width:100%;"
                >
            </div>

            <div class="form-group" style="margin-top:15px;">
                <label for="description">Observaciones</label>

                <textarea
                    id="description"
                    name="description"
                    rows="3"
                    maxlength="2000"
                    style="width:100%;"
                >{{ old('description', $invoice->description) }}</textarea>
            </div>

            <button
                type="submit"
                class="btn-primary-custom"
                style="margin-top:20px;"
            >
                Guardar datos
            </button>
        </form>
    </section>

</div>

@endsection