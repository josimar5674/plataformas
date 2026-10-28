@extends('layouts.app')

@section('content')

<div class="form-card">

    <!-- TITULO -->
    <div class="form-title">

        👤 Crear Usuario

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
          action="/usuarios">

        @csrf


        <!-- ===================================================== -->
        <!-- NOMBRE -->
        <!-- ===================================================== -->

        <div class="form-group">

            <label class="form-label">

                Nombre Completo

            </label>

            <input type="text"
                   name="name"
                   class="form-control"
                   value="{{ old('name') }}"
                   required>

        </div>


        <!-- ===================================================== -->
        <!-- EMAIL -->
        <!-- ===================================================== -->

        <div class="form-group">

            <label class="form-label">

                Correo Electrónico

            </label>

            <input type="email"
                   name="email"
                   class="form-control"
                   value="{{ old('email') }}"
                   required>

        </div>


        <!-- ===================================================== -->
        <!-- PASSWORD -->
        <!-- ===================================================== -->

        <div class="form-group">

            <label class="form-label">

                Contraseña

            </label>

            <input type="password"
                   name="password"
                   class="form-control"
                   required>

        </div>


        <!-- ===================================================== -->
        <!-- ROL -->
        <!-- ===================================================== -->

        <div class="form-group">

            <label class="form-label">

                Rol

            </label>

            <select name="role"
                    id="role"
                    class="form-control">

                <option value="user"
                    {{ old('role', 'user') == 'user' ? 'selected' : '' }}>

                    Usuario

                </option>

                <option value="admin"
                    {{ old('role') == 'admin' ? 'selected' : '' }}>

                    Administrador

                </option>

            </select>

        </div>


        <!-- ===================================================== -->
        <!-- PERMISOS -->
        <!-- ===================================================== -->

        <div id="permisosContainer"
             style="display:none;">


            <!-- ================================================= -->
            <!-- PESTAÑAS -->
            <!-- ================================================= -->

            <div class="permission-tabs">

                <button type="button"
                        id="tabInversiones"
                        class="permission-tab active"
                        onclick="mostrarPermisos('inversiones')">

                    🏢 Inversiones

                </button>


                <button type="button"
                        id="tabClientes"
                        class="permission-tab"
                        onclick="mostrarPermisos('clientes')">

                    👤 Clientes

                </button>

                <button type="button"
                        id="tabFinanzas"
                        class="permission-tab"
                        onclick="mostrarPermisos('finanzas')">

                    💰 Finanzas

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

                        <div style="
                            border:1px solid #3a465c;
                            padding:15px;
                            border-radius:10px;
                            margin-bottom:15px;
                        ">


                            <!-- INVERSIÓN -->

                            <label style="
                                display:flex;
                                align-items:center;
                                gap:10px;
                                cursor:pointer;
                            ">

                                <input
                                    type="checkbox"
                                    name="inversiones[]"
                                    value="{{ $inversion->id }}"
                                    {{ old('inversiones') &&
                                       in_array(
                                           $inversion->id,
                                           old('inversiones')
                                       )
                                       ? 'checked'
                                       : '' }}
                                >

                          
                                

                            </label>

                            <strong>

                                {{ $inversion->nombre }}

                                </strong>


                            <!-- MÓDULOS -->

                            <div style="
                                margin-top:10px;
                                margin-left:25px;
                                display:grid;
                                grid-template-columns:repeat(3, minmax(0, 1fr));
                                gap:8px;
                            ">


                                <!-- AVALÚOS -->

                                <label>

                                    <input
                                        type="checkbox"
                                        name="permisos[{{ $inversion->id }}][avaluos]"
                                        {{ old(
                                            "permisos.{$inversion->id}.avaluos"
                                        ) ? 'checked' : '' }}
                                    >

                                    Avalúos

                                </label>


                                <!-- ACTIVOS -->

                                <label>

                                    <input
                                        type="checkbox"
                                        name="permisos[{{ $inversion->id }}][activos]"
                                        {{ old(
                                            "permisos.{$inversion->id}.activos"
                                        ) ? 'checked' : '' }}
                                    >

                                    Activos

                                </label>


                                <!-- SERVICIOS -->

                                <label>

                                    <input
                                        type="checkbox"
                                        name="permisos[{{ $inversion->id }}][servicios]"
                                        {{ old(
                                            "permisos.{$inversion->id}.servicios"
                                        ) ? 'checked' : '' }}
                                    >

                                    Servicios

                                </label>


                                <!-- COMERCIAL -->

                                <label>

                                    <input
                                        type="checkbox"
                                        name="permisos[{{ $inversion->id }}][comercial]"
                                        {{ old(
                                            "permisos.{$inversion->id}.comercial"
                                        ) ? 'checked' : '' }}
                                    >

                                    Comercial

                                </label>


                                <!-- ENTIDADES -->

                                <label>

                                    <input
                                        type="checkbox"
                                        name="permisos[{{ $inversion->id }}][entidades]"
                                        {{ old(
                                            "permisos.{$inversion->id}.entidades"
                                        ) ? 'checked' : '' }}
                                    >

                                    Entidades

                                </label>


                                <!-- ESTADO RESULTADOS -->

                                <label>

                                    <input
                                        type="checkbox"
                                        name="permisos[{{ $inversion->id }}][estado_resultados]"
                                        {{ old(
                                            "permisos.{$inversion->id}.estado_resultados"
                                        ) ? 'checked' : '' }}
                                    >

                                    Estado Resultados

                                </label>


                                <!-- ACTIVOS REGISTRALES -->

                                <label>

                                    <input
                                        type="checkbox"
                                        name="permisos[{{ $inversion->id }}][activos_registrales]"
                                        {{ old(
                                            "permisos.{$inversion->id}.activos_registrales"
                                        ) ? 'checked' : '' }}
                                    >

                                    Activos Registrales

                                </label>


                                <!-- BITÁCORAS -->

                                <label>

                                    <input
                                        type="checkbox"
                                        name="permisos[{{ $inversion->id }}][bitacoras]"
                                        {{ old(
                                            "permisos.{$inversion->id}.bitacoras"
                                        ) ? 'checked' : '' }}
                                    >

                                    Bitácoras

                                </label>


                            </div>

                        </div>

                    @endforeach

                </div>

            </div>


            <!-- ================================================= -->
            <!-- BUSINESS CUSTOMERS -->
            <!-- ================================================= -->

            <div id="permisos-clientes"
                 style="display:none;">

                <div class="form-group">

                    <label class="form-label">

                        Business Customers Permitidos

                    </label>


                    @if($businessCustomers->count())

                        <div style="
                            display:grid;
                            grid-template-columns:repeat(2, minmax(0, 1fr));
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

                                        background:rgba(255,255,255,0.02) !important;

                                        cursor:pointer !important;

                                        margin:0 !important;

                                        overflow:hidden !important;
                                    "
                                >


                                    <!-- CHECKBOX -->

                                    <input
                                        type="checkbox"
                                        name="business_customers[]"
                                        value="{{ $businessCustomer->id }}"

                                        {{ old('business_customers') &&
                                           in_array(
                                               $businessCustomer->id,
                                               old('business_customers')
                                           )
                                           ? 'checked'
                                           : '' }}

                                        style="
                                            flex:0 0 auto !important;
                                            width:16px !important;
                                            height:16px !important;
                                            margin:0 !important;
                                        "
                                    >


                                    <!-- INFORMACIÓN -->

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


        <!-- ===================================================== -->
        <!-- ESTADO -->
        <!-- ===================================================== -->

        <div class="form-group">

            <label class="form-label">

                Estado

            </label>


            <select name="estado"
                    class="form-control">

                <option value="1"
                    {{ old('estado', '1') == '1' ? 'selected' : '' }}>

                    Activo

                </option>

                <option value="0"
                    {{ old('estado') == '0' ? 'selected' : '' }}>

                    Inactivo

                </option>

            </select>

        </div>


<div id="permisos-finanzas" style="display:none;">
    <div class="form-group">
        <label class="form-label">Módulos de Finanzas permitidos</label>
        <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;margin-top:15px;">
            @foreach(['ingresos'=>'Ingresos','egresos'=>'Egresos','resumen'=>'Resumen'] as $key=>$label)
                <label style="display:flex!important;align-items:center!important;gap:12px!important;padding:14px 16px!important;border:1px solid var(--border)!important;border-radius:10px!important;background:var(--surface)!important;cursor:pointer!important;margin:0!important;">
                    <input type="checkbox" name="finanzas[{{ $key }}]" value="1" @checked(old('finanzas.'.$key)) style="width:16px;height:16px;margin:0;">
                    <span>{{ $label }}</span>
                </label>
            @endforeach
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

                💾 Guardar Usuario

            </button>


            <a href="/usuarios"
               class="btn-secondary">

                Cancelar

            </a>

        </div>


    </form>

</div>



<!-- ============================================================= -->
<!-- JAVASCRIPT -->
<!-- ============================================================= -->

<script>

const roleSelect =
    document.getElementById('role');

const permisosContainer =
    document.getElementById('permisosContainer');


function actualizarPermisos()
{

    if (roleSelect.value === 'user')
    {

        permisosContainer.style.display =
            'block';

    }
    else
    {

        permisosContainer.style.display =
            'none';

    }

}


function mostrarPermisos(tipo)
{

    const inversiones =
        document.getElementById(
            'permisos-inversiones'
        );

    const clientes =
        document.getElementById(
            'permisos-clientes'
        );

    const finanzas = document.getElementById('permisos-finanzas');
    const tabFinanzas = document.getElementById('tabFinanzas');

    const tabInversiones =
        document.getElementById(
            'tabInversiones'
        );

    const tabClientes =
        document.getElementById(
            'tabClientes'
        );


    if (tipo === 'inversiones')
    {

        inversiones.style.display =
            'block';

        clientes.style.display =
            'none';
        if (finanzas) finanzas.style.display = 'none';
        if (tabFinanzas) tabFinanzas.classList.remove('active');

        tabInversiones.classList.add(
            'active'
        );

        tabClientes.classList.remove(
            'active'
        );

    }


    if (tipo === 'clientes')
    {

        inversiones.style.display =
            'none';

        clientes.style.display =
            'block';
        if (finanzas) finanzas.style.display = 'none';
        if (tabFinanzas) tabFinanzas.classList.remove('active');

        tabInversiones.classList.remove(
            'active'
        );

        tabClientes.classList.add(
            'active'
        );

    }

    if (tipo === 'finanzas') {
        inversiones.style.display = 'none';
        clientes.style.display = 'none';
        finanzas.style.display = 'block';
        tabInversiones.classList.remove('active');
        tabClientes.classList.remove('active');
        tabFinanzas.classList.add('active');
    }

}


roleSelect.addEventListener(
    'change',
    actualizarPermisos
);


actualizarPermisos();

</script>

@endsection