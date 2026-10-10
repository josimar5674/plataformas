@extends('layouts.app')

@section('content')

<div class="configuration-header">
    <div>
        <h1>🧾 Facturas</h1>
        <small>Selecciona una categoría para consultar sus facturas.</small>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div style="
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(240px,1fr));
    gap:18px;
    margin-top:25px;
">

    @forelse($tipos as $tipo)

        <a href="{{ route('invoices.show', \Illuminate\Support\Str::slug($tipo->name)) }}"
           style="
                display:block;
                padding:24px;
                border:1px solid var(--border);
                border-radius:12px;
                background:var(--surface);
                color:var(--text);
                text-decoration:none;
           ">

            <div style="font-size:30px;margin-bottom:12px;">
                🧾
            </div>

            <h3 style="margin:0 0 8px;">
                {{ $tipo->name }}
            </h3>

            <span style="color:var(--text-secondary);">
                Ver facturas →
            </span>

        </a>

    @empty

        <div class="configuration-item">
            No tienes categorías de facturas autorizadas.
        </div>

    @endforelse

</div>

@endsection