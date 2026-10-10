@extends('layouts.app')

@section('content')



<!-- ===================================== -->
<!-- HEADER -->
<!-- ===================================== -->

<div class="configuration-header">

    <div>

        <h1>
            ⚙️ Configuraciones
        </h1>

        <small>
            Catálogos y opciones del sistema
        </small>

    </div>


    <!-- NUEVO CATÁLOGO -->

    <button
        type="button"
        class="btn-primary-custom"
        onclick="abrirModalCatalogo()">

        ➕ Nuevo catálogo

    </button>

</div>


@if(session('success'))

    <div class="alert alert-success">

        {{ session('success') }}

    </div>

@endif


@if(session('error'))

    <div class="alert alert-danger">

        {{ session('error') }}

    </div>

@endif

<!-- ===================================== -->
<!-- LAYOUT -->
<!-- ===================================== -->

<div class="configuration-layout">


    <!-- ================================= -->
    <!-- MENÚ DE CATÁLOGOS -->
    <!-- ================================= -->

    <div class="configuration-menu">

        <div class="configuration-menu-title">

            📋 Catálogos

        </div>

        <a
    href="/configuraciones?section=google-workspace"
    class="{{ request('section') === 'google-workspace' ? 'active' : '' }}"
>
    📧 Google Workspace
</a>

<a
    href="/configuraciones?section=device-activation"
    class="{{ request('section') === 'device-activation' ? 'active' : '' }}"
>
    📱 Activación de dispositivos
</a>



        @forelse($catalogs as $catalogItem)

            <a
                href="/configuraciones?catalog={{ $catalogItem->id }}"
                class="{{ isset($catalogSelected) && $catalogSelected->id == $catalogItem->id ? 'active' : '' }}">

                {{ $catalogItem->name }}

            </a>

        @empty

            <div style="
                padding:10px;
                color:var(--text-secondary);
                font-size:13px;
            ">

                No hay catálogos configurados.

            </div>

        @endforelse

    </div>


  


<!-- ================================= -->
<!-- CONTENIDO -->
<!-- ================================= -->

<div class="configuration-content">


 @if(request('section') === 'google-workspace')

    @include('configurations.google-workspace')

@elseif(request('section') === 'device-activation')

    @include('configurations.device-activation')

@elseif($catalogSelected)


        <!-- ========================= -->
        <!-- CATÁLOGO SELECCIONADO -->
        <!-- ========================= -->

        <h3>

            {{ $catalogSelected->name }}

        </h3>


        @if($catalogSelected->description)

            <p style="
                color:var(--text-secondary);
                margin-top:-5px;
                margin-bottom:20px;
            ">

                {{ $catalogSelected->description }}

            </p>

        @endif


        <!-- ========================= -->
        <!-- AGREGAR OPCIÓN -->
        <!-- ========================= -->

        <form
            method="POST"
            action="/configuraciones">

            @csrf

            <input
                type="hidden"
                name="catalog_id"
                value="{{ $catalogSelected->id }}">


            <div class="configuration-add">

                <input
                    type="text"
                    name="name"
                    class="form-control"
                    placeholder="Nueva opción..."
                    required>


                <button
                    type="submit"
                    class="btn-primary-custom">

                    ➕ Agregar

                </button>

            </div>

        </form>


        <!-- ========================= -->
        <!-- LISTADO DE OPCIONES -->
        <!-- ========================= -->

        @forelse($options as $option)

            <div class="configuration-item">


                <div>

                    <div class="configuration-item-name">

                        {{ $option->name }}

                    </div>


                    @if($option->description)

                        <div style="
                            color:var(--text-secondary);
                            font-size:13px;
                            margin-top:3px;
                        ">

                            {{ $option->description }}

                        </div>

                    @endif

                </div>


                <div class="configuration-actions">


                    <!-- ESTADO -->

                    @if($option->active)

                        <span class="configuration-status active">

                            Activo

                        </span>

                    @else

                        <span class="configuration-status inactive">

                            Inactivo

                        </span>

                    @endif


                    <!-- ================= -->
                    <!-- TOGGLE -->
                    <!-- ================= -->

                    <form
                        method="POST"
                        action="/configuraciones/{{ $option->id }}/toggle">

                        @csrf
                        @method('PATCH')


                        <button
                            type="submit"
                            class="btn-secondary">

                            {{ $option->active
                                ? 'Desactivar'
                                : 'Activar'
                            }}

                        </button>

                    </form>


                    <!-- ================= -->
                    <!-- ELIMINAR -->
                    <!-- ================= -->

                    <form
                        method="POST"
                        action="/configuraciones/{{ $option->id }}"

                        onsubmit="
                            event.preventDefault();
                            confirmarEliminacion(this);
                        ">

                        @csrf
                        @method('DELETE')


                        <button
                            type="submit"
                            class="btn-danger">

                            🗑️

                        </button>

                    </form>


                </div>

            </div>


        @empty

            <div style="
                padding:20px;
                text-align:center;
                color:var(--text-secondary);
            ">

                No hay opciones configuradas.

            </div>

        @endforelse


    @else


        <!-- ========================= -->
        <!-- SIN CATÁLOGO -->
        <!-- ========================= -->

        <div style="
            padding:40px 20px;
            text-align:center;
        ">

            <div style="
                font-size:45px;
                margin-bottom:15px;
            ">

                ⚙️

            </div>


            <h3>

                Configuración del sistema

            </h3>


            <p style="
                color:var(--text-secondary);
            ">

                Seleccione un catálogo del menú
                para administrar sus opciones.

            </p>


            <button
                type="button"
                class="btn-primary-custom"
                onclick="abrirModalCatalogo()">

                ➕ Crear catálogo

            </button>

        </div>


    @endif


</div>

</div>


<!-- ===================================== -->
<!-- MODAL NUEVO CATÁLOGO -->
<!-- ===================================== -->

<div
    id="modalCatalogo"

    style="
        display:none;
        position:fixed;
        inset:0;
        background:rgba(0,0,0,.5);
        z-index:9999;

        align-items:center;
        justify-content:center;
    "
>


    <div
        style="
            width:450px;
            max-width:90%;

            background:var(--surface);
            color:var(--text);

            border:1px solid var(--border);

            border-radius:12px;

            padding:25px;

            box-shadow:var(--shadow);
        "
    >


        <h3 style="
            margin-top:0;
            color:var(--text);
        ">

            📋 Nuevo catálogo

        </h3>


        <p style="
            color:var(--text-secondary);
            margin-bottom:20px;
        ">

            Escriba el nombre del nuevo catálogo.

        </p>


        <form
            method="POST"
            action="/configuraciones/catalogos">

            @csrf


            <div class="form-group">

                <label>

                    Nombre del catálogo

                </label>


          <div class="form-group">



    <input
        type="text"
        name="name"
        class="form-control"
        placeholder="Ej. Tipo de Inmueble"
        required>

</div>

<div class="form-group" style="margin-top:15px;">

    <label>
        Descripción
    </label>

    <textarea
        name="description"
        class="form-control"
        rows="3"
        placeholder="Descripción del catálogo..."></textarea>

</div>


            <div style="
                display:flex;
                justify-content:flex-end;
                gap:10px;
                margin-top:20px;
            ">


                <button
                    type="button"
                    class="btn-secondary"
                    onclick="cerrarModalCatalogo()">

                    Cancelar

                </button>


                <button
                    type="submit"
                    class="btn-primary-custom">

                    💾 Crear catálogo

                </button>


            </div>


        </form>


   </div>

</div>

<script>

function abrirModalCatalogo()
{
    const modal = document.getElementById('modalCatalogo');

    if (!modal) {
        return;
    }

    modal.style.display = 'flex';

    const input = modal.querySelector('input[name="name"]');

    if (input) {
        setTimeout(function () {
            input.focus();
        }, 100);
    }
}

function cerrarModalCatalogo()
{
    const modal = document.getElementById('modalCatalogo');

    if (!modal) {
        return;
    }

    modal.style.display = 'none';
}

const modalCatalogo = document.getElementById('modalCatalogo');

if (modalCatalogo) {

    modalCatalogo.addEventListener('click', function(e) {

        if (e.target === this) {
            cerrarModalCatalogo();
        }

    });

}

document.addEventListener('keydown', function(e) {

    if (e.key === 'Escape') {
        cerrarModalCatalogo();
    }

});

</script>

@endsection