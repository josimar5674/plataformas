@extends('layouts.app')

@section('content')

<div class="max-w-4xl mx-auto">

    <div class="mb-6">

        <h1 class="text-3xl font-bold">
            Nuevo Expediente
        </h1>

        <p class="text-gray-500">
            Registra un nuevo expediente.
        </p>

    </div>

    <div class="panel-card">

        <form method="POST" action="{{ route('expedientes.store') }}">

            @csrf

            <div class="grid grid-cols-2 gap-4">

                <div>
                    <label>Número de expediente</label>

                    <input
                        type="text"
                        name="numero_expediente"
                        value="{{ old('numero_expediente') }}"
                        required
                        class="border p-2 rounded w-full">
                </div>

                <div>
                    <label>Tipo de trámite</label>

                    <select
                        name="tipo_tramite"
                        required
                        class="border p-2 rounded w-full">

                        <option value="Judicial">
                            Judicial
                        </option>

                        <option value="Administrativo">
                            Administrativo
                        </option>

                    </select>
                </div>

                <div>
                    <label>Matrícula</label>

                    <input
                        type="text"
                        name="matricula"
                        class="border p-2 rounded w-full">
                </div>

                <div>
                    <label>Sede</label>

                    <input
                        type="text"
                        name="sede"
                        class="border p-2 rounded w-full">
                </div>

                <div>
                    <label>Asignado</label>

                    <input
                        type="text"
                        name="asignado"
                        class="border p-2 rounded w-full">
                </div>

                <div>
                    <label>Cuantía</label>

                    <input
                        type="number"
                        step="0.01"
                        name="cuantia"
                        class="border p-2 rounded w-full">
                </div>

                <div>
                    <label>Fecha de presentación</label>

                    <input
                        type="date"
                        name="fecha_presentacion"
                        class="border p-2 rounded w-full">
                </div>

                <div class="col-span-2">

                    <label>Pretensión principal</label>

                    <textarea
                        name="pretension_principal"
                        rows="3"
                        class="border p-2 rounded w-full"></textarea>

                </div>

                <div class="col-span-2">

                    <label>Descripción del proceso</label>

                    <textarea
                        name="descripcion_proceso"
                        rows="4"
                        class="border p-2 rounded w-full"></textarea>

                </div>

            </div>

            <div class="flex gap-3 mt-6">

                <button
                    type="submit"
                    class="bg-blue-500 text-white px-4 py-2 rounded">

                    💾 Crear Expediente

                </button>

                <a
                    href="{{ route('expedientes.index') }}"
                    class="bg-gray-500 text-white px-4 py-2 rounded">

                    Cancelar

                </a>

            </div>

        </form>

    </div>

</div>

@endsection