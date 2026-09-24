@extends('layouts.app')

@section('content')

<style>

    /* =========================
       CONTENEDOR PRINCIPAL
       ========================= */

    .exp-main {
        display: flex;
        min-height: calc(100vh - 80px);
        margin: -15px;
    }

    /* =========================
       SIDEBAR
       ========================= */

    .exp-sidebar {
        width: 280px;
        flex-shrink: 0;

        background: #f8fafc;
        border-right: 1px solid #e5e7eb;

        display: flex;
        flex-direction: column;
    }

    [data-theme="dark"] .exp-sidebar,
    .dark .exp-sidebar {
        background: #111827;
        border-color: #374151;
    }

    .exp-sidebar-header {
        padding: 18px;
        border-bottom: 1px solid #e5e7eb;
    }

    [data-theme="dark"] .exp-sidebar-header,
    .dark .exp-sidebar-header {
        border-color: #374151;
    }

    .exp-sidebar-title {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 12px;
    }

    .exp-search {
        width: 100%;
        padding: 10px 12px;
        border-radius: 10px;

        border: 1px solid #d1d5db;
        background: white;
        color: #111827;

        outline: none;
    }

    [data-theme="dark"] .exp-search,
    .dark .exp-search {
        background: #1f2937;
        color: white;
        border-color: #374151;
    }

    .exp-list {
        flex: 1;
        overflow-y: auto;
        padding: 8px;
    }

    /* =========================
       TARJETAS EXPEDIENTES
       ========================= */

    .exp-card {
        display: block;

        margin-bottom: 8px;
        padding: 12px;

        border-radius: 12px;

        background: white;
        border: 1px solid #e5e7eb;

        color: inherit;
        text-decoration: none;

        transition: .2s;
    }

    .exp-card:hover {
        transform: translateY(-1px);

        box-shadow:
            0 4px 15px rgba(0, 0, 0, .08);
    }

    [data-theme="dark"] .exp-card,
    .dark .exp-card {
        background: #1f2937;
        border-color: #374151;
    }

    .exp-card.active {
        border-left: 4px solid #0A84FF;
        background: #eff6ff;
    }

    [data-theme="dark"] .exp-card.active,
    .dark .exp-card.active {
        background: #172554;
    }

    .exp-number {
        font-weight: 700;
        font-size: 14px;
    }

    .exp-type {
        font-size: 12px;
        opacity: .75;
        margin-top: 3px;
    }

    .exp-user {
        font-size: 11px;
        opacity: .65;
        margin-top: 3px;
    }

    /* =========================
       ESTADOS
       ========================= */

    .estado-badge {
        display: inline-block;

        font-size: 11px;
        padding: 4px 8px;

        border-radius: 999px;
        font-weight: 600;

        margin-top: 8px;
    }

    .estado-pendiente {
        background: #fef3c7;
        color: #92400e;
    }

    .estado-proceso {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .estado-audiencia {
        background: #ede9fe;
        color: #6d28d9;
    }

    .estado-resuelto {
        background: #dcfce7;
        color: #166534;
    }

    .estado-cerrado {
        background: #e5e7eb;
        color: #374151;
    }

    /* =========================
       CONTENIDO
       ========================= */

    .exp-content {
        flex: 1;
        overflow-y: auto;
        padding: 25px;
    }

    .exp-header {
        display: flex;
        justify-content: space-between;
        align-items: center;

        margin-bottom: 20px;
    }

    .exp-header h2 {
        font-size: 28px;
        font-weight: 700;
    }

    /* =========================
       BOTONES
       ========================= */

    .exp-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;

        padding: 9px 14px;

        border-radius: 8px;

        border: none;

        cursor: pointer;

        font-weight: 500;

        text-decoration: none;
    }

    .exp-btn-blue {
        background: #0A84FF;
        color: white;
    }

    .exp-btn-blue:hover {
        background: #0071e3;
    }

    .exp-btn-yellow {
        background: #f59e0b;
        color: white;
    }

    .exp-btn-green {
        background: #16a34a;
        color: white;
    }

    .exp-btn-red {
        background: #dc2626;
        color: white;
    }

    .exp-btn-gray {
        background: #6b7280;
        color: white;
    }

    /* =========================
       PANELES
       ========================= */

    .exp-panel {
        background: white;

        border: 1px solid #e5e7eb;

        border-radius: 14px;

        padding: 18px;

        margin-bottom: 18px;

        box-shadow:
            0 1px 3px rgba(0,0,0,.08),
            0 8px 24px rgba(0,0,0,.05);
    }

    [data-theme="dark"] .exp-panel,
    .dark .exp-panel {
        background: #1f2937;
        border-color: #374151;
    }

    .panel-info {
        border-top: 3px solid #0A84FF;
    }

    .panel-estado {
        border-top: 3px solid #30D158;
    }

    .panel-sujetos {
        border-top: 3px solid #0A84FF;
    }

    .panel-documentos {
        border-top: 3px solid #f59e0b;
    }

    .panel-movimientos {
         border-top: 3px solid #7c3aed;

    margin-top: 25px;

    height: 450px;

    overflow-y: auto;

    box-sizing: border-box;
    }

    .panel-notas {
        border-top: 3px solid #ec4899;
    }

    .exp-panel-title {
        display: flex;
        align-items: center;
        gap: 8px;

        margin-bottom: 15px;
        padding-bottom: 10px;

        border-bottom: 1px solid #e5e7eb;

        font-size: 18px;
        font-weight: 600;
    }

    [data-theme="dark"] .exp-panel-title,
    .dark .exp-panel-title {
        border-color: #374151;
    }

    /* =========================
       INFORMACIÓN
       ========================= */

    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .info-item {
        padding: 8px 0;
    }

    .info-label {
        font-size: 12px;
        opacity: .65;
    }

    .info-value {
        font-weight: 500;
    }

    .info-full {
        grid-column: 1 / -1;
    }

    /* =========================
       GRID
       ========================= */

.exp-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 18px;

    align-items: stretch;

    height: 520px;
}

.exp-grid > .exp-panel {
    min-height: 0;
    height: 520px;
    overflow-y: auto;
    box-sizing: border-box;
}

.exp-grid + .exp-panel {
    position: relative;
    z-index: 1;
    clear: both;
}
    /* =========================
       FORMULARIOS
       ========================= */

    .exp-form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }

 .exp-input,
.exp-select,
.exp-textarea {
    width: 100%;
    padding: 10px 12px;

    border: 1px solid #d1d5db;
    border-radius: 9px;

    background: white;
    color: #111827;

    font-size: 14px;
    outline: none;

    transition: border-color .2s, box-shadow .2s;
}

.exp-select {
    cursor: pointer;
    appearance: none;

    background-image: linear-gradient(45deg, transparent 50%, #6b7280 50%),
                      linear-gradient(135deg, #6b7280 50%, transparent 50%);

    background-position:
        calc(100% - 17px) 50%,
        calc(100% - 11px) 50%;

    background-size:
        6px 6px,
        6px 6px;

    background-repeat: no-repeat;

    padding-right: 40px;
}

.exp-input:focus,
.exp-select:focus,
.exp-textarea:focus {
    border-color: #0A84FF;
    box-shadow: 0 0 0 3px rgba(10, 132, 255, .12);
}

    [data-theme="dark"] .exp-input,
    [data-theme="dark"] .exp-select,
    [data-theme="dark"] .exp-textarea,
    .dark .exp-input,
    .dark .exp-select,
    .dark .exp-textarea {
        background: #374151;
        color: white;
        border-color: #4b5563;
    }

    .exp-form-full {
        grid-column: 1 / -1;
    }

    /* =========================
       LISTAS
       ========================= */

    .exp-list-item {
        padding: 12px;

        border-radius: 10px;

        background: #f8fafc;

        margin-bottom: 8px;
    }

    [data-theme="dark"] .exp-list-item,
    .dark .exp-list-item {
        background: #374151;
    }

    .exp-muted {
        font-size: 12px;
        opacity: .65;
    }

    .hidden {
        display: none !important;
    }

    /* =========================
       RESPONSIVE
       ========================= */

    @media (max-width: 1000px) {

        .exp-grid {
            grid-template-columns: 1fr;
        }

        .exp-sidebar {
            width: 230px;
        }

    }

    @media (max-width: 700px) {

        .exp-main {
            flex-direction: column;
        }

        .exp-sidebar {
            width: 100%;
            height: auto;
            max-height: 350px;
        }

        .info-grid,
        .exp-form-grid {
            grid-template-columns: 1fr;
        }

        .exp-content {
            padding: 15px;
        }

    }

    .exp-switch-container {
    display: flex;
    align-items: center;
    gap: 12px;

    min-height: 48px;
}

.exp-switch {
    position: relative;
    display: inline-block;

    width: 46px;
    height: 26px;

    flex-shrink: 0;
}

.exp-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.exp-switch-slider {
    position: absolute;
    inset: 0;

    cursor: pointer;

    background: #d1d5db;

    border-radius: 999px;

    transition: .2s;
}

.exp-switch-slider::before {
    content: "";

    position: absolute;

    width: 20px;
    height: 20px;

    left: 3px;
    top: 3px;

    background: white;

    border-radius: 50%;

    box-shadow: 0 1px 3px rgba(0,0,0,.25);

    transition: .2s;
}

.exp-switch input:checked + .exp-switch-slider {
    background: #0A84FF;
}

.exp-switch input:checked + .exp-switch-slider::before {
    transform: translateX(20px);
}

.exp-switch-title {
    font-weight: 600;
    font-size: 14px;
}

.exp-switch-description {
    font-size: 12px;
    opacity: .6;
    margin-top: 2px;
}

</style>


<script>

    function activarEdicion() {

        document.getElementById('modo-vista').classList.add('hidden');

        document.getElementById('modo-edicion').classList.remove('hidden');

    }


    function cancelarEdicion() {

        document.getElementById('modo-edicion').classList.add('hidden');

        document.getElementById('modo-vista').classList.remove('hidden');

    }


    function toggleForm(id) {

        const form = document.getElementById(id);

        if (form) {

            form.classList.toggle('hidden');

        }

    }


    document.addEventListener('DOMContentLoaded', function () {

        const buscador =
            document.getElementById('buscarExpediente');

        if (!buscador) return;

        buscador.addEventListener('keyup', function () {

            const texto =
                this.value.toLowerCase();

            document
                .querySelectorAll('.exp-card')
                .forEach(card => {

                    const contenido =
                        card.innerText.toLowerCase();

                    card.style.display =
                        contenido.includes(texto)
                            ? 'block'
                            : 'none';

                });

        });

    });

</script>


@php

$puedeEditar = false;

if ($expedienteSeleccionado) {

    $puedeEditar =
        auth()->user()->role === 'admin'
        ||
        (
            $expedienteSeleccionado->user_id === auth()->id()
            &&
            $expedienteSeleccionado->permite_edicion
        );

}

@endphp


<div class="exp-main">

    {{-- =====================================================
         SIDEBAR
    ====================================================== --}}

    <aside class="exp-sidebar">

        <div class="exp-sidebar-header">

            <div class="exp-sidebar-title">
                📂 Expedientes
            </div>

            <input
                type="text"
                id="buscarExpediente"
                placeholder="Buscar expediente..."
                class="exp-search">

        </div>


        <div class="exp-list">

            @forelse($expedientes as $exp)

                @php

                    $estadoClass = [
                        'pendiente' => 'estado-pendiente',
                        'en_proceso' => 'estado-proceso',
                        'audiencia' => 'estado-audiencia',
                        'resuelto' => 'estado-resuelto',
                        'cerrado' => 'estado-cerrado',
                    ][$exp->estado] ?? 'estado-pendiente';

                @endphp

                <a
                    href="{{ route('expedientes.show', $exp->id) }}"
                    class="exp-card
                    {{ $expedienteSeleccionado &&
                       $expedienteSeleccionado->id === $exp->id
                       ? 'active'
                       : '' }}">

                    <div class="exp-number">
                        #{{ $exp->numero_expediente }}
                    </div>

                    <div class="exp-type">
                        {{ $exp->tipo_tramite }}
                    </div>

                    <div class="exp-user">
                        👤 {{ $exp->user->name ?? 'N/A' }}
                    </div>

                    <span class="estado-badge {{ $estadoClass }}">
                        {{ strtoupper(str_replace('_', ' ', $exp->estado)) }}
                    </span>

                </a>

            @empty

                <div style="padding:20px; text-align:center; opacity:.6;">
                    No hay expedientes.
                </div>

            @endforelse

        </div>

    </aside>


    {{-- =====================================================
         CONTENIDO
    ====================================================== --}}

    <section class="exp-content">


        {{-- HEADER --}}

        <div class="exp-header">

            <div>

                <h2>
                    Gestión de Expedientes
                </h2>

            </div>

            <a
                href="{{ route('expedientes.create') }}"
                class="exp-btn exp-btn-blue">

                + Nuevo Expediente

            </a>

        </div>


        {{-- =================================================
             SIN SELECCIÓN
        ================================================== --}}

        @if(!$expedienteSeleccionado)

            <div class="exp-panel panel-info">

                <div class="exp-panel-title">
                    📂 Expedientes
                </div>

                <div style="padding:30px; text-align:center; opacity:.65;">

                    Selecciona un expediente del panel izquierdo.

                </div>

            </div>

        @else


        {{-- =================================================
             INFORMACIÓN GENERAL
        ================================================== --}}

        <div class="exp-panel panel-info">

            <div class="exp-panel-title">
                📋 Información General
            </div>

@if($puedeEditar)

    <button
        type="button"
        onclick="activarEdicion()"
        class="exp-btn exp-btn-yellow">

        ✏️ Editar

    </button>

@endif


            {{-- VISTA --}}

            <div
                id="modo-vista"
                class="info-grid"
                style="margin-top:15px;">

                <div class="info-item">

                    <div class="info-label">
                        Expediente
                    </div>

                    <div class="info-value">
                        {{ $expedienteSeleccionado->numero_expediente }}
                    </div>

                </div>


                <div class="info-item">

                    <div class="info-label">
                        Tipo
                    </div>

                    <div class="info-value">
                        {{ $expedienteSeleccionado->tipo_tramite }}
                    </div>

                </div>


                <div class="info-item">

                    <div class="info-label">
                        Matrícula
                    </div>

                    <div class="info-value">
                        {{ $expedienteSeleccionado->matricula ?: '—' }}
                    </div>

                </div>


                <div class="info-item">

                    <div class="info-label">
                        Sede
                    </div>

                    <div class="info-value">
                        {{ $expedienteSeleccionado->sede ?: '—' }}
                    </div>

                </div>


                <div class="info-item">

                    <div class="info-label">
                        Cuantía
                    </div>

                    <div class="info-value">
                        L {{ number_format($expedienteSeleccionado->cuantia ?? 0, 2) }}
                    </div>

                </div>


                <div class="info-item">

                    <div class="info-label">
                        Asignado
                    </div>

                    <div class="info-value">
                        {{ $expedienteSeleccionado->asignado ?: '—' }}
                    </div>

                </div>


                <div class="info-item">

                    <div class="info-label">
                        Fecha de presentación
                    </div>

                    <div class="info-value">
                        {{ $expedienteSeleccionado->fecha_presentacion ?: '—' }}
                    </div>

                </div>


                <div class="info-item">

                    <div class="info-label">
                        Usuario
                    </div>

                    <div class="info-value">
                        {{ $expedienteSeleccionado->user->name ?? 'N/A' }}
                    </div>

                </div>


                <div class="info-item info-full">

                    <div class="info-label">
                        Pretensión principal
                    </div>

                    <div class="info-value">
                        {{ $expedienteSeleccionado->pretension_principal ?: '—' }}
                    </div>

                </div>


                <div class="info-item info-full">

                    <div class="info-label">
                        Descripción del proceso
                    </div>

                    <div class="info-value">
                        {{ $expedienteSeleccionado->descripcion_proceso ?: '—' }}
                    </div>

                </div>

            </div>


            {{-- EDICIÓN --}}

            <form
                id="modo-edicion"
                method="POST"
                action="{{ route('expedientes.update', $expedienteSeleccionado->id) }}"
                class="hidden"
                style="margin-top:15px;">

                @csrf
                @method('PUT')


                <div class="exp-form-grid">


                    <div>

                        <label>Tipo</label>

                        <select
                            name="tipo_tramite"
                            class="exp-select">

                            <option
                                value="Judicial"
                                {{ $expedienteSeleccionado->tipo_tramite === 'Judicial' ? 'selected' : '' }}>

                                Judicial

                            </option>

                            <option
                                value="Administrativo"
                                {{ $expedienteSeleccionado->tipo_tramite === 'Administrativo' ? 'selected' : '' }}>

                                Administrativo

                            </option>

                        </select>

                    </div>


                    <div>

                        <label>Matrícula</label>

                        <input
                            type="text"
                            name="matricula"
                            value="{{ $expedienteSeleccionado->matricula }}"
                            class="exp-input">

                    </div>


                    <div>

                        <label>Sede</label>

                        <input
                            type="text"
                            name="sede"
                            value="{{ $expedienteSeleccionado->sede }}"
                            class="exp-input">

                    </div>


                    <div>

                        <label>Asignado</label>

                        <input
                            type="text"
                            name="asignado"
                            value="{{ $expedienteSeleccionado->asignado }}"
                            class="exp-input">

                    </div>


                    <div>

                        <label>Cuantía</label>

                        <input
                            type="number"
                            step="0.01"
                            name="cuantia"
                            value="{{ $expedienteSeleccionado->cuantia }}"
                            class="exp-input">

                    </div>


                    @if(auth()->user()->role === 'admin')

                        <div>

                            <label>Asignar usuario</label>

                            <select
                                name="user_id"
                                class="exp-select">

                                @foreach($usuarios as $usuario)

                                    <option
                                        value="{{ $usuario->id }}"
                                        {{ $expedienteSeleccionado->user_id == $usuario->id ? 'selected' : '' }}>

                                        {{ $usuario->name }}

                                    </option>

                                @endforeach

                            </select>

                        </div>

<div class="exp-switch-container">

    <label class="exp-switch">

        <input
            type="checkbox"
            name="permite_edicion"
            value="1"
            {{ $expedienteSeleccionado->permite_edicion ? 'checked' : '' }}>

        <span class="exp-switch-slider"></span>

    </label>

    <div>
        <div class="exp-switch-title">
            Permitir modificación
        </div>

        <div class="exp-switch-description">
            El usuario asignado podrá modificar este expediente.
        </div>
    </div>

</div>

                    @endif


                    <div class="exp-form-full">

                        <label>Pretensión principal</label>

                        <textarea
                            name="pretension_principal"
                            rows="3"
                            class="exp-textarea">{{ $expedienteSeleccionado->pretension_principal }}</textarea>

                    </div>


                    <div class="exp-form-full">

                        <label>Descripción</label>

                        <textarea
                            name="descripcion_proceso"
                            rows="4"
                            class="exp-textarea">{{ $expedienteSeleccionado->descripcion_proceso }}</textarea>

                    </div>

                </div>


                <div style="display:flex; gap:8px; margin-top:15px;">

                    <button
                        type="button"
                        onclick="cancelarEdicion()"
                        class="exp-btn exp-btn-gray">

                        ❌ Cancelar

                    </button>


                    <button
                        type="submit"
                        class="exp-btn exp-btn-blue">

                        💾 Guardar

                    </button>

                </div>

            </form>

        </div>


        {{-- =================================================
             ESTADO
        ================================================== --}}

        <div class="exp-panel panel-estado">

            <div class="exp-panel-title">
                🔄 Estado del Expediente
            </div>

            <form
                method="POST"
                action="{{ route('expedientes.estado.update', $expedienteSeleccionado->id) }}">

                @csrf
                @method('PATCH')

                <div style="display:flex; gap:10px;">

                    <select
                        name="estado"
                        class="exp-select">

                        <option value="pendiente"
                            {{ $expedienteSeleccionado->estado === 'pendiente' ? 'selected' : '' }}>
                            Pendiente
                        </option>

                        <option value="en_proceso"
                            {{ $expedienteSeleccionado->estado === 'en_proceso' ? 'selected' : '' }}>
                            En proceso
                        </option>

                        <option value="audiencia"
                            {{ $expedienteSeleccionado->estado === 'audiencia' ? 'selected' : '' }}>
                            Audiencia
                        </option>

                        <option value="resuelto"
                            {{ $expedienteSeleccionado->estado === 'resuelto' ? 'selected' : '' }}>
                            Resuelto
                        </option>

                        <option value="cerrado"
                            {{ $expedienteSeleccionado->estado === 'cerrado' ? 'selected' : '' }}>
                            Cerrado
                        </option>

                    </select>

                    <button class="exp-btn exp-btn-blue">
                        Actualizar
                    </button>

                </div>

            </form>

        </div>


        {{-- =================================================
             SUJETOS + DOCUMENTOS
        ================================================== --}}

        <div class="exp-grid">


            {{-- ================= SUJETOS ================= --}}

            <div class="exp-panel panel-sujetos">

                <div class="exp-panel-title">
                    👥 Sujetos
                </div>


              <form
                method="POST"
                action="{{ route('expedientes.sujetos.store', $expedienteSeleccionado->id) }}"
                class="subject-create-form">

                    @csrf

                    <div class="exp-form-grid">

                        <select
                            name="tipo"
                            class="exp-select">

                            <option value="sujeto activo">
                                Sujeto Activo
                            </option>

                            <option value="sujeto pasivo">
                                Sujeto Pasivo
                            </option>

                            <option value="apoderado activo">
                                Apoderado Activo
                            </option>

                            <option value="apoderado pasivo">
                                Apoderado Pasivo
                            </option>

                        </select>


                        <input
                            type="text"
                            name="nombre"
                            placeholder="Nombre"
                            required
                            class="exp-input">


                        <input
                            type="text"
                            name="identificacion"
                            placeholder="Identificación"
                            class="exp-input">


                        <input
                            type="text"
                            name="cah"
                            placeholder="CAH"
                            class="exp-input">

                    </div>


                    <button
                        type="submit"
                        class="exp-btn exp-btn-blue"
                        style="margin-top:10px;">

                        Guardar Sujeto

                    </button>

                </form>


              <div
    id="lista-sujetos"
    style="margin-top:18px;">

    @forelse($expedienteSeleccionado->sujetos->sortByDesc('id') as $sujeto)
                     <div class="exp-list-item">

 <strong class="sujeto-tipo">
    {{ strtoupper($sujeto->tipo) }}
</strong>

<div class="sujeto-nombre">
    {{ $sujeto->nombre }}
</div>

<div class="exp-muted">

    <span class="sujeto-identificacion">
        ID: {{ $sujeto->identificacion ?: '—' }}
    </span>

    |

    <span class="sujeto-cah">
        CAH: {{ $sujeto->cah ?: '—' }}
    </span>

</div>


    @if($puedeEditar)

        <div style="display:flex; gap:8px; margin-top:10px;">

            <button
                type="button"
                onclick="toggleForm('editar-sujeto-{{ $sujeto->id }}')"
                class="exp-btn exp-btn-yellow">

                ✏️ Editar

            </button>


            <form
                method="POST"
                action="{{ route('sujetos.destroy', $sujeto->id) }}"
                onsubmit="return confirm('¿Está seguro que desea eliminar este sujeto?');">

                @csrf
                @method('DELETE')

                <button
                    type="submit"
                    class="exp-btn exp-btn-red">

                    🗑️ Eliminar

                </button>

            </form>

        </div>


        {{-- FORMULARIO DE EDICIÓN --}}

        <div
            id="editar-sujeto-{{ $sujeto->id }}"
            class="hidden"
            style="margin-top:12px;">

<form
    method="POST"
    action="{{ route('sujetos.update', $sujeto->id) }}"
    class="subject-edit-form"
    data-sujeto-id="{{ $sujeto->id }}">

                @csrf
                @method('PUT')

                <div class="exp-form-grid">

                    <select
                        name="tipo"
                        class="exp-select">

                        <option value="sujeto activo"
                            {{ $sujeto->tipo === 'sujeto activo' ? 'selected' : '' }}>
                            Sujeto Activo
                        </option>

                        <option value="sujeto pasivo"
                            {{ $sujeto->tipo === 'sujeto pasivo' ? 'selected' : '' }}>
                            Sujeto Pasivo
                        </option>

                        <option value="apoderado activo"
                            {{ $sujeto->tipo === 'apoderado activo' ? 'selected' : '' }}>
                            Apoderado Activo
                        </option>

                        <option value="apoderado pasivo"
                            {{ $sujeto->tipo === 'apoderado pasivo' ? 'selected' : '' }}>
                            Apoderado Pasivo
                        </option>

                    </select>


                    <input
                        type="text"
                        name="nombre"
                        value="{{ $sujeto->nombre }}"
                        placeholder="Nombre"
                        required
                        class="exp-input">


                    <input
                        type="text"
                        name="identificacion"
                        value="{{ $sujeto->identificacion }}"
                        placeholder="Identificación"
                        class="exp-input">


                    <input
                        type="text"
                        name="cah"
                        value="{{ $sujeto->cah }}"
                        placeholder="CAH"
                        class="exp-input">

                </div>


                <div style="display:flex; gap:8px; margin-top:10px;">

                    <button
                        type="submit"
                        class="exp-btn exp-btn-blue">

                        💾 Guardar cambios

                    </button>

                    <button
                        type="button"
                        onclick="toggleForm('editar-sujeto-{{ $sujeto->id }}')"
                        class="exp-btn exp-btn-gray">

                        Cancelar

                    </button>

                </div>

            </form>

        </div>

    @endif

</div>

                    @empty

                       <div class="sujetos-empty" style="opacity:.6;">
    No hay sujetos registrados.
</div>

                    @endforelse

                </div>

            </div>


            {{-- ================= DOCUMENTOS ================= --}}

            <div class="exp-panel panel-documentos">

                <div class="exp-panel-title">
                    📄 Bitacora
                </div>

<div style="margin-bottom:15px;">
                 @include('components.notes',[
    'modelo' => $expedienteSeleccionado,
    'modelClass' => 'App\Models\expediente'
])  

<hr>

@include('components.documents',[
    'modelo' => $expedienteSeleccionado,
    'modelClass' => 'App\Models\expediente'
])


@include('components.alerts',[
    'modelo' => $expedienteSeleccionado,
    'referencia' => 'numero_expediente',
    'modelClass' => 'App\Models\Expediente'
])

                </div>


                @forelse($expedienteSeleccionado->documentos as $documento)

                    

                @empty

                    <div style="opacity:.6;">
                        No hay documentos registrados.
                    </div>

                @endforelse

            </div>

        </div>


        {{-- =================================================
             MOVIMIENTOS
        ================================================== --}}

    <div class="exp-panel panel-movimientos">

    <div class="exp-panel-title">
        📜 Movimientos
    </div>


    {{-- =========================
         NUEVO MOVIMIENTO
    ========================== --}}



        <form
            method="POST"
            action="{{ route('expedientes.movimientos.store', $expedienteSeleccionado->id) }}">

            @csrf

            <div class="exp-form-grid">

                <input
                    type="date"
                    name="fecha"
                    required
                    class="exp-input">

            </div>


            <textarea
                name="descripcion"
                required
                placeholder="Descripción del movimiento..."
                class="exp-textarea"
                style="margin-top:10px;"
                rows="3"></textarea>


            <button
                type="submit"
                class="exp-btn exp-btn-blue"
                style="margin-top:10px;">

                Guardar Movimiento

            </button>

        </form>




    {{-- =========================
         LISTA DE MOVIMIENTOS
    ========================== --}}

    <div style="margin-top:18px;">

        @forelse($expedienteSeleccionado->movimientos->sortByDesc('fecha') as $movimiento)

            <div class="exp-list-item">

                <strong>
                    {{ $movimiento->fecha }}
                </strong>


                <div style="margin-top:5px;">
                    {{ $movimiento->descripcion }}
                </div>


                {{-- =========================
                     ACCIONES
                ========================== --}}

                @if($puedeEditar)

                    <div style="display:flex; gap:8px; margin-top:10px;">

                        {{-- EDITAR --}}

                        <button
                            type="button"
                            onclick="toggleForm('editar-movimiento-{{ $movimiento->id }}')"
                            class="exp-btn exp-btn-yellow">

                            ✏️ Editar

                        </button>


                        {{-- ELIMINAR --}}

                        <form
                            method="POST"
                            action="{{ route('movimientos.destroy', $movimiento->id) }}"
                            onsubmit="return confirm('¿Está seguro de eliminar este movimiento?');">

                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="exp-btn exp-btn-red">

                                🗑️ Eliminar

                            </button>

                        </form>

                    </div>


                    {{-- =========================
                         FORMULARIO EDITAR
                    ========================== --}}

                    <div
                        id="editar-movimiento-{{ $movimiento->id }}"
                        class="hidden"
                        style="margin-top:12px;">

                        <form
                            method="POST"
                            action="{{ route('movimientos.update', $movimiento->id) }}">

                            @csrf
                            @method('PUT')


                            <div class="exp-form-grid">

                                <input
                                    type="date"
                                    name="fecha"
                                    value="{{ $movimiento->fecha }}"
                                    required
                                    class="exp-input">


                            </div>


                            <textarea
                                name="descripcion"
                                required
                                class="exp-textarea"
                                style="margin-top:10px;"
                                rows="3">{{ $movimiento->descripcion }}</textarea>


                            <div style="display:flex; gap:8px; margin-top:10px;">

                                <button
                                    type="submit"
                                    class="exp-btn exp-btn-blue">

                                    💾 Guardar cambios

                                </button>


                                <button
                                    type="button"
                                    onclick="toggleForm('editar-movimiento-{{ $movimiento->id }}')"
                                    class="exp-btn exp-btn-gray">

                                    Cancelar

                                </button>

                            </div>

                        </form>

                    </div>

                @endif

            </div>


        @empty

            <div style="opacity:.6;">
                No hay movimientos registrados.
            </div>

        @endforelse

    </div>

</div>


        {{-- =================================================
             NOTAS
        ================================================== --}}



        @endif

    </section>

</div>

@endsection