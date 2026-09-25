@extends('layouts.guest')

@section('content')

<style>
    .login-wrapper {
        min-height: 80vh;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 40px 20px;
        position: relative;
        overflow: hidden;
    }

    /* Luz animada detrás del login */
    .login-glow {
        position: absolute;
        width: 280px;
        height: 280px;
        border-radius: 50%;
        background: rgba(10, 132, 255, .20);
        filter: blur(70px);
        animation: glowMove 7s ease-in-out infinite alternate;
        pointer-events: none;
    }

    @keyframes glowMove {
        0% {
            transform: translate(-100px, -50px) scale(1);
            opacity: .45;
        }

        50% {
            transform: translate(100px, 40px) scale(1.25);
            opacity: .65;
        }

        100% {
            transform: translate(-20px, 100px) scale(.9);
            opacity: .40;
        }
    }

    .login-card {
        position: relative;
        z-index: 2;
        max-width: 450px;
        width: 100%;
        padding: 42px;
        border-radius: 20px;
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        box-shadow:
            0 25px 60px rgba(0, 0, 0, .12),
            0 0 0 1px rgba(255,255,255,.5);
        animation: cardAppear .6s ease-out;
    }

    @keyframes cardAppear {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Contenedor del logo */
    .platform-logo {
        position: relative;
        width: 78px;
        height: 78px;
        margin: 0 auto 20px;
        animation: logoFloat 4s ease-in-out infinite;
    }

    @keyframes logoFloat {
        0%, 100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-7px);
        }
    }

    /* Aura del logo */
    .platform-logo::before {
        content: "";
        position: absolute;
        inset: -12px;
        border-radius: 26px;
        background: rgba(10, 132, 255, .18);
        filter: blur(18px);
        animation: logoAura 3s ease-in-out infinite alternate;
        z-index: -1;
    }

    @keyframes logoAura {
        from {
            transform: scale(.9);
            opacity: .5;
        }

        to {
            transform: scale(1.15);
            opacity: .9;
        }
    }

    .platform-logo-box {
        width: 78px;
        height: 78px;
        border-radius: 20px;
        background: linear-gradient(
            135deg,
            #0A84FF,
            #2563EB
        );
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow:
            0 12px 30px rgba(10, 132, 255, .35),
            inset 0 1px 1px rgba(255,255,255,.4);
    }

    .platform-logo-box svg {
        filter: drop-shadow(0 3px 5px rgba(0,0,0,.18));
    }

    .login-title {
        margin: 0 0 7px;
        font-size: 30px;
        font-weight: 750;
        letter-spacing: -0.8px;
        color: #111827;
        text-align: center;
    }

    .login-subtitle {
        margin: 0 0 30px;
        color: #6b7280;
        font-size: 14px;
        text-align: center;
    }

    .login-button {
        position: relative;
        overflow: hidden;
    }

    /* Brillo que cruza el botón */
    .login-button::after {
        content: "";
        position: absolute;
        top: 0;
        left: -120%;
        width: 60%;
        height: 100%;
        background: linear-gradient(
            90deg,
            transparent,
            rgba(255,255,255,.30),
            transparent
        );
        transform: skewX(-20deg);
        animation: buttonShine 4s ease-in-out infinite;
    }

    @keyframes buttonShine {
        0%, 65% {
            left: -120%;
        }

        85%, 100% {
            left: 140%;
        }
    }

    /* Respeto por usuarios que desactivan animaciones */
    @media (prefers-reduced-motion: reduce) {
        .login-glow,
        .platform-logo,
        .platform-logo::before,
        .login-card,
        .login-button::after {
            animation: none;
        }
    }
</style>


<div class="login-wrapper">

    <!-- LUZ ANIMADA -->
    <div class="login-glow"></div>


    <!-- TARJETA -->
    <div class="login-card form-card">

        <!-- LOGO -->
        <div class="platform-logo">

            <div class="platform-logo-box">

                <svg
                    width="42"
                    height="42"
                    viewBox="0 0 24 24"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                >

                    <path
                        d="M4 19V5"
                        stroke="white"
                        stroke-width="2"
                        stroke-linecap="round"
                    />

                    <path
                        d="M4 19H20"
                        stroke="white"
                        stroke-width="2"
                        stroke-linecap="round"
                    />

                    <path
                        d="M7 15L11 11L14 13L19 7"
                        stroke="white"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />

                    <path
                        d="M16 7H19V10"
                        stroke="white"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />

                </svg>

            </div>

        </div>


        <!-- TITULO -->
        <h1 class="login-title">
            Plataformas
        </h1>

        <p class="login-subtitle">
            Gestión centralizada
        </p>


        <!-- ERRORES -->
        @if ($errors->any())

            <div class="error-box">

                <ul style="
                    margin:0;
                    padding-left:20px;
                ">

                    @foreach ($errors->all() as $error)

                        <li>{{ $error }}</li>

                    @endforeach

                </ul>

            </div>

        @endif


        <!-- FORMULARIO -->
        <form method="POST"
              action="{{ route('login') }}">

            @csrf


            <!-- EMAIL -->
            <div class="form-group">

                <label class="form-label">
                    Correo electrónico
                </label>

                <input
                    type="email"
                    name="email"
                    class="form-control"
                    required
                    autofocus
                    autocomplete="email"
                    placeholder="correo@ejemplo.com"
                >

            </div>


            <!-- PASSWORD -->
            <div class="form-group">

                <label class="form-label">
                    Contraseña
                </label>

                <input
                    type="password"
                    name="password"
                    class="form-control"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                >

            </div>


            <!-- RECORDAR -->
            <div style="
                margin-bottom:22px;
                display:flex;
                align-items:center;
                gap:9px;
            ">

                <input
                    type="checkbox"
                    name="remember"
                    id="remember"
                >

                <label
                    for="remember"
                    style="
                        font-size:14px;
                        color:#4b5563;
                        cursor:pointer;
                    "
                >
                    Recordarme
                </label>

            </div>


            <!-- BOTON -->
            <button
                type="submit"
                class="btn-primary-custom login-button"
                style="
                    width:100%;
                    padding:14px;
                    font-size:15px;
                    font-weight:600;
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    gap:9px;
                "
            >

                <svg
                    width="19"
                    height="19"
                    viewBox="0 0 24 24"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                >

                    <path
                        d="M10 17L15 12L10 7"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />

                    <path
                        d="M15 12H3"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                    />

                    <path
                        d="M21 19V5C21 3.89543 20.1046 3 19 3H14"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                    />

                </svg>

                <span>
                    Iniciar sesión
                </span>

            </button>

        </form>

    </div>

</div>

@endsection