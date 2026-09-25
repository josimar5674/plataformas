<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">

        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Plataformas</title>
            <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    @vite([
    'resources/css/app.css',
    'resources/js/app.js'
    ])

    @include('layouts.css')
    @include('layouts.theme-components')

</head>

<!-- MODAL ELIMINAR -->

<div id="deleteModal" class="modal-overlay">

    <div class="modal-box">

        <div class="modal-title">
            ⚠️ Confirmar eliminación
        </div>

        <div class="modal-text">
            Esta acción no se puede deshacer.
        </div>

        <div class="modal-actions">

            <button type="button"
                class="btn-secondary"
                onclick="cerrarModal()">

                Cancelar

            </button>

            <button type="button"
                class="btn-danger"
                id="confirmDeleteBtn">

                Eliminar

            </button>

        </div>

    </div>

</div>

<body>

    <!-- HEADER -->
    <header class="topbar">

        <!-- IZQUIERDA -->
        <div class="topbar-left">

            <a href="/" class="logo">
                💼 Portafolio
            </a>

            <nav class="menu">

                <a href="/inversiones">
                    Inversiones
                </a>

               

                <a href="/clientes">
                    Personas
                </a>
               


                <a href="/business-customers">
                    Clientes
                </a>
               


                <a href="/entidades">
                    Entidades
                </a>


            <a href="/expedientes">
                Expedientes
            </a>

                @auth

                @if(auth()->user()->role === 'admin')

                <a href="/usuarios">
                    Usuarios
                </a>

                @endif

                @if(Auth::user()->role == 'admin')
               <a href="/configuraciones">
                        ⚙️ 
                    </a>

                @endif

                @endauth

            </nav>

        </div>


        <!-- DERECHA -->
        <div class="topbar-right">

            <button id="themeToggle" class="theme-btn" title="Cambiar tema">
                🌙
            </button>

            @auth
            <div class="user-box">

                <div class="user-info">

                    <div class="user-name">
                        {{ auth()->user()->name }}
                    </div>

                    <div class="user-role">
                        {{ strtoupper(auth()->user()->role) }}
                    </div>

                </div>

                <a href="/profile"
                    class="profile-btn">

                    ⚙️ Mi Perfil

                </a>

                <form method="POST"
                    action="{{ route('logout') }}">

                    @csrf

                    <button type="submit"
                        class="logout-btn">

                        Salir

                    </button>

                </form>

            </div>

            @endauth

        </div>

    </header>

    <!-- CONTENIDO -->

    @if(session('success'))

    <div style="
        max-width:1200px;
        margin:20px auto;
        padding:14px 18px;
        border-radius:8px;
        background:#dcfce7;
        color:#166534;
        border:1px solid #86efac;
    ">
        {{ session('success') }}
    </div>

    @endif

    @if($errors->any())

    <div style="
        max-width:1200px;
        margin:20px auto;
        padding:14px 18px;
        border-radius:8px;
        background:#fee2e2;
        color:#991b1b;
        border:1px solid #fca5a5;
    ">

        <strong>Se encontraron los siguientes errores:</strong>

        <ul style="margin-top:10px; margin-left:20px;">

            @foreach($errors->all() as $error)

            <li>{{ $error }}</li>

            @endforeach

        </ul>

    </div>

    @endif
    <main class="main-content">

        @yield('content')

    </main>

</body>

</html>


<script>
    let deleteForm = null;

    function confirmarEliminacion(form) {

        deleteForm = form;

        document
            .getElementById('deleteModal')
            .classList.add('show');

    }


    document
        .getElementById('confirmDeleteBtn')
        .addEventListener('click', function() {

            if (deleteForm) {

                deleteForm.submit();

            }

        });

    // ===============================
    // TEMA CLARO / OSCURO
    // ===============================

    const themeBtn = document.getElementById('themeToggle');

    const savedTheme = localStorage.getItem('theme') || 'light';

    document.documentElement.setAttribute('data-theme', savedTheme);

    themeBtn.innerHTML = savedTheme === 'dark' ?
        '☀️' :
        '🌙';

    themeBtn.addEventListener('click', () => {

        const currentTheme = document.documentElement.getAttribute('data-theme');

        const newTheme = currentTheme === 'dark' ?
            'light' :
            'dark';

        document.documentElement.setAttribute('data-theme', newTheme);

        localStorage.setItem('theme', newTheme);

        themeBtn.innerHTML = newTheme === 'dark' ?
            '☀️' :
            '🌙';

    });

    function abrirModal(id, url) {

        fetch(url)
            .then(response => response.text())
            .then(html => {

                document.getElementById('contenedorFormularioAvaluo').innerHTML = html;

                document
                    .getElementById(id)
                    .classList
                    .add('show');

            });

    }

    function cerrarModal(id = 'deleteModal') {

        document
            .getElementById(id)
            ?.classList.remove('show');

        deleteForm = null;

    }

    document.addEventListener('keydown', function(e) {

        if (e.key === "Escape") {

            document
                .querySelectorAll('.modal-overlay-custom.show')
                .forEach(function(modal) {

                    modal.classList.remove('show');

                });

        }

    });
</script>