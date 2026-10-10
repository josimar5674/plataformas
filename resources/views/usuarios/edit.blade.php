@extends('layouts.app')

@section('content')



<div class="form-card">

    <!-- TITULO -->
    <div class="form-title">
        👤 Editar Usuario
    </div>
 
    @if ($errors->any())

        <div class="error-box">

            <ul style="margin:0; padding-left:20px;">

                @foreach ($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif

    <form method="POST"
          action="/usuarios/{{ $usuario->id }}">

        @csrf
        @method('PUT')


        <!-- NOMBRE -->
  <div class="form-group">

    <label for="name">
        Nombre de usuario
    </label>

    <input
        type="text"
        id="name"
        name="name"
        value="{{ old('name', $usuario->name) }}"
        required
    >

</div>


        <!-- EMAIL -->
        <div class="form-group">

            <label class="form-label">
                Correo Electrónico
            </label>

            <input type="email"
                   name="email"
                   class="form-control"
                   value="{{ old('email', $usuario->email) }}">

        </div>


        <!-- CONTRASEÑA -->
<div class="form-group">

    <label class="form-label">
        Nueva Contraseña
    </label>

    <input
        type="password"
        name="password"
        class="form-control"
        autocomplete="new-password"
        placeholder="Dejar vacío para conservar la actual"
    >

</div>


<!-- CONFIRMAR CONTRASEÑA -->
<div class="form-group">

    <label class="form-label">
        Confirmar Nueva Contraseña
    </label>

    <input
        type="password"
        name="password_confirmation"
        class="form-control"
        autocomplete="new-password"
        placeholder="Repita la nueva contraseña"
    >

</div>


        <!-- ROL -->
        <div class="form-group">

            <label class="form-label">
                Rol
            </label>

            <select name="role"
                    class="form-control">

                <option value="user"
                    {{ $usuario->role == 'user' ? 'selected' : '' }}>

                    Usuario

                </option>

                <option value="admin"
                    {{ $usuario->role == 'admin' ? 'selected' : '' }}>

                    Administrador

                </option>

            </select>

        </div>


    <!-- ================================================= -->
    <!-- Estados -->
    <!-- ================================================= -->


        <div class="form-group">

            <label class="form-label">
                Estado
            </label>


            <select name="estado"
                    class="form-control">

                <option value="1"
                    {{ $usuario->estado ? 'selected' : '' }}>

                    Activo

                </option>

                <option value="0"
                    {{ !$usuario->estado ? 'selected' : '' }}>

                    Inactivo

                </option>

            </select>

        </div>

<!-- ===================================================== -->
<!-- PERMISOS -->
<!-- ===================================================== -->

<div id="permisosContainer">

    <!-- ================================================= -->
    <!-- PESTAÑAS -->
    <!-- ================================================= -->

    <div class="permission-tabs">

        <!-- INVERSIONES -->

        <button
            type="button"
            id="tabInversiones"
            class="permission-tab active"
            onclick="mostrarPermisos('inversiones')"
        >

            🏢 Inversiones

        </button>


        <!-- PERSONAS -->

        <button
            type="button"
            id="tabClientes"
            class="permission-tab"
            onclick="mostrarPermisos('clientes')"
        >

            👤 Personas

        </button>


        <!-- ENTIDADES -->

        <button
            type="button"
            id="tabEntidades"
            class="permission-tab"
            onclick="mostrarPermisos('entidades')"
        >

            🏛️ Entidades

        </button>


        <!-- BUSINESS CUSTOMERS -->

        <button
            type="button"
            id="tabBusinessCustomers"
            class="permission-tab"
            onclick="mostrarPermisos('businessCustomers')"
        >

            💼 Clientes

        </button>

        <button
                type="button"
                id="tabFacturas"
                class="permission-tab"
                onclick="mostrarPermisos('facturas')"
            >
                Facturas
        </button>

    </div>


    <!-- ================================================= -->
    <!-- INVERSIONES -->
    <!-- ================================================= -->

    <div id="permisos-inversiones">

        <div class="form-group">

            <label class="form-label">

                Inversiones Permitidas

            </label>


            @foreach($inversiones as $inversion)

                @php
                    $permiso =
                        $permisos[$inversion->id] ?? null;
                @endphp


                <div style="
                    border:1px solid #ddd;
                    padding:15px;
                    border-radius:8px;
                    margin-bottom:15px;
                ">

                    <!-- INVERSIÓN -->

                    <label>

                        <input
                            type="checkbox"
                            name="inversiones[]"
                            value="{{ $inversion->id }}"
                            {{ in_array(
                                $inversion->id,
                                $inversionesUsuario
                            ) ? 'checked' : '' }}
                        >

                        <strong>

                            {{ $inversion->nombre }}

                        </strong>

                    </label>


                    <!-- MÓDULOS -->

                    <div style="
                        margin-top:10px;
                        margin-left:25px;
                        display:grid;
                        grid-template-columns:repeat(3,1fr);
                        gap:8px;
                    ">

                        <!-- AVALÚOS -->

                        <label>

                            <input
                                type="checkbox"
                                name="permisos[{{ $inversion->id }}][avaluos]"
                                {{ $permiso && $permiso->avaluos
                                    ? 'checked'
                                    : '' }}
                            >

                            Avalúos

                        </label>


                        <!-- ACTIVOS -->

                        <label>

                            <input
                                type="checkbox"
                                name="permisos[{{ $inversion->id }}][activos]"
                                {{ $permiso && $permiso->activos
                                    ? 'checked'
                                    : '' }}
                            >

                            Activos

                        </label>


                        <!-- SERVICIOS -->

                        <label>

                            <input
                                type="checkbox"
                                name="permisos[{{ $inversion->id }}][servicios]"
                                {{ $permiso && $permiso->servicios
                                    ? 'checked'
                                    : '' }}
                            >

                            Servicios

                        </label>


                        <!-- COMERCIAL -->

                        <label>

                            <input
                                type="checkbox"
                                name="permisos[{{ $inversion->id }}][comercial]"
                                {{ $permiso && $permiso->comercial
                                    ? 'checked'
                                    : '' }}
                            >

                            Comercial

                        </label>


                        <!-- ENTIDADES -->

                        <label>

                            <input
                                type="checkbox"
                                name="permisos[{{ $inversion->id }}][entidades]"
                                {{ $permiso && $permiso->entidades
                                    ? 'checked'
                                    : '' }}
                            >

                            Entidades

                        </label>


                        <!-- ESTADO RESULTADOS -->

                        <label>

                            <input
                                type="checkbox"
                                name="permisos[{{ $inversion->id }}][estado_resultados]"
                                {{ $permiso && $permiso->estado_resultados
                                    ? 'checked'
                                    : '' }}
                            >

                            Estado Resultados

                        </label>


                        <!-- ACTIVOS REGISTRALES -->

                        <label>

                            <input
                                type="checkbox"
                                name="permisos[{{ $inversion->id }}][activos_registrales]"
                                {{ $permiso && $permiso->activos_registrales
                                    ? 'checked'
                                    : '' }}
                            >

                            Activos Registrales

                        </label>


                        <!-- BITÁCORAS -->

                        <label>

                            <input
                                type="checkbox"
                                name="permisos[{{ $inversion->id }}][bitacoras]"
                                {{ $permiso && $permiso->bitacoras
                                    ? 'checked'
                                    : '' }}
                            >

                            Bitácoras

                        </label>

                    </div>

                </div>

            @endforeach

        </div>

    </div>


    <!-- ================================================= -->
    <!-- PERSONAS -->
    <!-- ================================================= -->

    <div
        id="permisos-clientes"
        style="display:none;"
    >

        <div class="form-group">

            <label class="form-label">

                Personas Permitidas

            </label>


            @if($clientes->count())

                <div style="
                    display:grid;
                    grid-template-columns:repeat(
                        2,
                        minmax(0, 1fr)
                    );
                    gap:12px;
                ">

                    @foreach($clientes as $cliente)

                        <label
                            style="
                                display:flex !important;
                                align-items:center !important;
                                gap:12px !important;

                                width:100% !important;
                                box-sizing:border-box !important;

                                padding:14px 16px !important;

                                border:1px solid #3a465c !important;
                                border-radius:10px !important;

                                background:rgba(
                                    255,
                                    255,
                                    255,
                                    0.02
                                ) !important;

                                cursor:pointer !important;

                                margin:0 !important;

                                overflow:hidden !important;
                            "
                        >

                            <input
                                type="checkbox"
                                name="clientes[]"
                                value="{{ $cliente->id }}"
                                {{ in_array(
                                    $cliente->id,
                                    $clientesUsuario
                                ) ? 'checked' : '' }}

                                style="
                                    flex:0 0 auto !important;
                                    width:16px !important;
                                    height:16px !important;
                                    margin:0 !important;
                                "
                            >


                            <div style="
                                min-width:0;
                                flex:1;
                                overflow:hidden;
                            ">

                                <div style="
                                    font-weight:600;
                                    font-size:15px;
                                    white-space:nowrap;
                                    overflow:hidden;
                                    text-overflow:ellipsis;
                                ">

                                    {{ $cliente->nombre }}

                                </div>


                                @if($cliente->email)

                                    <div style="
                                        margin-top:4px;
                                        font-size:12px;
                                        opacity:.65;
                                        white-space:nowrap;
                                        overflow:hidden;
                                        text-overflow:ellipsis;
                                    ">

                                        {{ $cliente->email }}

                                    </div>

                                @endif

                            </div>

                        </label>

                    @endforeach

                </div>

            @else

                <div style="
                    padding:20px;
                    border:1px dashed #555;
                    border-radius:10px;
                    text-align:center;
                    opacity:.7;
                ">

                    No hay personas registradas.

                </div>

            @endif

        </div>

    </div>


    <!-- ================================================= -->
    <!-- ENTIDADES -->
    <!-- ================================================= -->

    <div
        id="permisos-entidades"
        style="display:none;"
    >

        <div class="form-group">

            <label class="form-label">

                Entidades Permitidas

            </label>


            @if($entidades->count())

                <div style="
                    display:grid;
                    grid-template-columns:repeat(
                        2,
                        minmax(0, 1fr)
                    );
                    gap:12px;
                ">

                    @foreach($entidades as $entidad)

                        <label
                            style="
                                display:flex !important;
                                align-items:center !important;
                                gap:12px !important;

                                width:100% !important;
                                box-sizing:border-box !important;

                                padding:14px 16px !important;

                                border:1px solid #3a465c !important;
                                border-radius:10px !important;

                                background:rgba(
                                    255,
                                    255,
                                    255,
                                    0.02
                                ) !important;

                                cursor:pointer !important;

                                margin:0 !important;

                                overflow:hidden !important;
                            "
                        >

                            <input
                                type="checkbox"
                                name="entidades[]"
                                value="{{ $entidad->id }}"
                                {{ in_array(
                                    $entidad->id,
                                    $entidadesUsuario
                                ) ? 'checked' : '' }}

                                style="
                                    flex:0 0 auto !important;
                                    width:16px !important;
                                    height:16px !important;
                                    margin:0 !important;
                                "
                            >


                            <div style="
                                min-width:0;
                                flex:1;
                                overflow:hidden;
                            ">

                                <div style="
                                    font-weight:600;
                                    font-size:15px;
                                    white-space:nowrap;
                                    overflow:hidden;
                                    text-overflow:ellipsis;
                                ">

                                    {{ $entidad->denominacion_social
                                        ?: 'Entidad #' . $entidad->id }}

                                </div>


                                @if($entidad->matricula)

                                    <div style="
                                        margin-top:4px;
                                        font-size:12px;
                                        opacity:.65;
                                        white-space:nowrap;
                                        overflow:hidden;
                                        text-overflow:ellipsis;
                                    ">

                                        Matrícula:
                                        {{ $entidad->matricula }}

                                    </div>

                                @endif

                            </div>

                        </label>

                    @endforeach

                </div>

            @else

                <div style="
                    padding:20px;
                    border:1px dashed #555;
                    border-radius:10px;
                    text-align:center;
                    opacity:.7;
                ">

                    No hay entidades registradas.

                </div>

            @endif

        </div>

    </div>


    <!-- ================================================= -->
    <!-- BUSINESS CUSTOMERS -->
    <!-- ================================================= -->

    <div
        id="permisos-businessCustomers"
        style="display:none;"
    >

        <div class="form-group">

            <label class="form-label">

                Business Customers Permitidos

            </label>


            @if($businessCustomers->count())

                <div style="
                    display:grid;
                    grid-template-columns:repeat(
                        2,
                        minmax(0, 1fr)
                    );
                    gap:12px;
                ">

                    @foreach($businessCustomers as $businessCustomer)

                        <label
                            style="
                                display:flex !important;
                                align-items:center !important;
                                gap:12px !important;

                                width:100% !important;
                                box-sizing:border-box !important;

                                padding:14px 16px !important;

                                border:1px solid #3a465c !important;
                                border-radius:10px !important;

                                background:rgba(
                                    255,
                                    255,
                                    255,
                                    0.02
                                ) !important;

                                cursor:pointer !important;

                                margin:0 !important;

                                overflow:hidden !important;
                            "
                        >

                            <input
                                type="checkbox"
                                name="business_customers[]"
                                value="{{ $businessCustomer->id }}"
                                {{ in_array(
                                    $businessCustomer->id,
                                    $businessCustomersUsuario
                                ) ? 'checked' : '' }}

                                style="
                                    flex:0 0 auto !important;
                                    width:16px !important;
                                    height:16px !important;
                                    margin:0 !important;
                                "
                            >


                            <div style="
                                min-width:0;
                                flex:1;
                                overflow:hidden;
                            ">

                                <div style="
                                    font-weight:600;
                                    font-size:15px;
                                    white-space:nowrap;
                                    overflow:hidden;
                                    text-overflow:ellipsis;
                                ">

                                    {{ $businessCustomer->nombre }}

                                </div>


                                @if($businessCustomer->identificador_tributario)

                                    <div style="
                                        margin-top:4px;
                                        font-size:12px;
                                        opacity:.65;
                                        white-space:nowrap;
                                        overflow:hidden;
                                        text-overflow:ellipsis;
                                    ">

                                        ID:
                                        {{ $businessCustomer->identificador_tributario }}

                                    </div>

                                @endif

                            </div>

                        </label>

                    @endforeach

                </div>

            @else

                <div style="
                    padding:20px;
                    border:1px dashed #555;
                    border-radius:10px;
                    text-align:center;
                    opacity:.7;
                ">

                    No hay Business Customers registrados.

                </div>

            @endif

        </div>

    </div>

</div>

<!-- FACTURAS -->

<div id="permisos-facturas" style="display:none;">

    <div class="form-group">

        <label class="form-label">
            Tipos de facturas permitidos
        </label>

        <div style="
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            margin-top: 15px;
        ">

            @forelse($tiposFacturas as $tipo)

                <label style="
                    display: flex !important;
                    align-items: center !important;
                    justify-content: flex-start !important;
                    gap: 12px !important;
                    width: 100% !important;
                    box-sizing: border-box !important;
                    padding: 14px 16px !important;
                    border: 1px solid var(--border) !important;
                    border-radius: 10px !important;
                    background: var(--surface) !important;
                    cursor: pointer !important;
                    margin: 0 !important;
                ">

                    <input
                        type="checkbox"
                        name="tipos_facturas[]"
                        value="{{ $tipo->id }}"
                        {{ in_array($tipo->id, $tiposFacturasUsuario) ? 'checked' : '' }}
                        style="
                            flex: 0 0 auto !important;
                            width: 16px !important;
                            height: 16px !important;
                            margin: 0 !important;
                        "
                    >

                    <span style="
                        flex: 1;
                        min-width: 0;
                        text-align: left;
                    ">
                        {{ $tipo->name }}
                    </span>

                </label>

            @empty

                <p style="color: var(--text-secondary);">
                    No hay tipos de facturas activos en Configuraciones.
                </p>

            @endforelse

        </div>

    </div>

</div>





        <!-- ===================================================== -->
        <!-- BOTONES -->
        <!-- ===================================================== -->

        <div style="
            display:flex;
            gap:15px;
            margin-top:25px;
        ">

            <button type="submit"
                    class="btn-primary-custom">

                💾 Actualizar Usuario

            </button>


            <a href="/usuarios"
               class="btn-secondary">

                Cancelar

            </a>

        </div>


    </form>

</div>


<!-- ============================================================= -->
<!-- JAVASCRIPT DE PESTAÑAS -->
<!-- ============================================================= -->


@endsection


<script>

function mostrarPermisos(tipo)
{
    const tipos = [
        'inversiones',
        'clientes',
        'entidades',
        'businessCustomers',
        'facturas'
    ];

    // Ocultar todos los paneles
    tipos.forEach(function(nombre) {
        const panel = document.getElementById('permisos-' + nombre);

        if (panel) {
            panel.style.display = 'none';
        }
    });

    // Desactivar todas las pestañas
    document.querySelectorAll('.permission-tab').forEach(function(tab) {
        tab.classList.remove('active');
    });

    // Mostrar el panel seleccionado
    const panelSeleccionado = document.getElementById('permisos-' + tipo);

    if (panelSeleccionado) {
        panelSeleccionado.style.display = 'block';
    }

    // Activar la pestaña seleccionada
    const botones = {
        inversiones: 'tabInversiones',
        clientes: 'tabClientes',
        entidades: 'tabEntidades',
        businessCustomers: 'tabBusinessCustomers',
        facturas: 'tabFacturas'
    };

    const boton = document.getElementById(botones[tipo]);

    if (boton) {
        boton.classList.add('active');
    }
}

</script>