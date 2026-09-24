<div class="card-seccion">

    <h3>🔔 Alertas</h3>


    <!-- ================================================= -->
    <!-- NUEVA ALERTA -->
    <!-- ================================================= -->

    <form
        method="POST"
        action="{{ route('alerts.store') }}"
    >

        @csrf


        <!-- ================================================= -->
        <!-- MODELO RELACIONADO -->
        <!-- ================================================= -->

        <input
            type="hidden"
            name="alertable_type"
            value="{{ $modelClass }}"
        >

                    <input
                type="hidden"
                name="referencia"
                value="{{ $referencia ?? '' }}"
            >

        <input
            type="hidden"
            name="alertable_id"
            value="{{ $modelo->id }}"
        >

  


        <!-- ================================================= -->
        <!-- FECHA / HORA / RECURRENCIA -->
        <!-- ================================================= -->

        <div style="
            display:flex;
            gap:6px;
            margin-bottom:8px;
        ">

            <!-- FECHA -->

            <div style="flex:0.5;">

                <label>
                    Fecha de alerta
                </label>

                <input
                    type="date"
                    name="fecha"
                    class="form-control"
                    required
                >

            </div>


            <!-- HORA -->

            <div style="flex:0.5;">

                <label>
                    Hora de alerta
                </label>

                <input
                    type="time"
                    name="hora"
                    class="form-control"
                    required
                >

            </div>


            <!-- RECURRENCIA -->

                <div style="flex:0.5;">

                <label>
                    Recurrencia
                </label>

                <select
                    name="recurrencia"
                    class="form-control"
                >

                    <option value="none">
                        Una vez
                    </option>

                    <option value="daily">
                        Diaria
                    </option>

                    <option value="weekly">
                        Semanal
                    </option>

                    <option value="monthly">
                        Mensual
                    </option>

                    <option value="yearly">
                        Anual
                    </option>

                </select>

            </div>

        </div>


        <!-- ================================================= -->
        <!-- ASUNTO -->
        <!-- ================================================= -->

        <div style="
            margin-bottom:15px;
        ">

            <label>
                Asunto
            </label>

            <input
                type="text"
                name="asunto"
                class="form-control"
                placeholder="Asunto del correo..."
                maxlength="255"
                required
            >

        </div>


        <!-- ================================================= -->
        <!-- MENSAJE -->
        <!-- ================================================= -->

        <div style="
            margin-bottom:15px;
        ">

            <label>
                Mensaje
            </label>

            <textarea
                name="mensaje"
                class="form-control"
                rows="4"
                placeholder="Escriba el mensaje de la alerta..."
                required
            ></textarea>

        </div>


        <!-- ================================================= -->
        <!-- DESTINATARIOS -->
        <!-- ================================================= -->

        <div style="
            display:flex;
            gap:15px;
            margin-bottom:15px;
            align-items:flex-start;
        ">


            <!-- ================================================= -->
            <!-- PERSONAS -->
            <!-- ================================================= -->

            <div style="
                flex:1;
                min-width:0;
            ">

                <label style="
                    display:block;
                    margin-bottom:8px;
                ">

                    Personas

                </label>


                <div style="
                    border:1px solid var(--border);
                    border-radius:8px;
                    padding:8px;
                    height:180px;
                    overflow-y:auto;
                ">


                    @foreach(\App\Models\Cliente::whereNotNull('email')
                        ->where('email', '!=', '')
                        ->orderBy('nombre')
                        ->get() as $persona)


                        <label style="
                            display:flex !important;
                            align-items:center !important;
                            justify-content:flex-start !important;
                            width:100% !important;
                            box-sizing:border-box;
                            gap:10px;
                            padding:10px;
                            margin:0;
                            cursor:pointer;
                            border-radius:6px;
                        ">


                            <input
                                type="checkbox"
                                name="personas[]"
                                value="{{ $persona->id }}"
                                style="
                                    flex:0 0 auto;
                                    width:16px;
                                    height:16px;
                                    margin:0;
                                "
                            >


                            <div style="
                                display:flex;
                                flex-direction:column;
                                align-items:flex-start;
                                justify-content:center;
                                min-width:0;
                            ">

                                <span style="
                                    font-weight:600;
                                    color:var(--text);
                                ">

                                    {{ $persona->nombre }}

                                </span>


                                <small style="
                                    color:var(--text-secondary);
                                    font-size:12px;
                                ">

                                    {{ $persona->email }}

                                </small>

                            </div>


                        </label>


                    @endforeach


                </div>

            </div>


            <!-- ================================================= -->
            <!-- CORREOS ADICIONALES -->
            <!-- ================================================= -->

            <div style="
                flex:1;
                min-width:0;
            ">

                <label style="
                    display:block;
                    margin-bottom:25px;
                ">

                    Correos adicionales

                </label>


                <textarea
                    name="emails_adicionales"
                    class="form-control"
                    rows="8"
                    placeholder="correo@empresa.com, otro@gmail.com"
                    style="
                        resize:vertical;
                        box-sizing:border-box;
                        width:100%;
                    "
                ></textarea>


            </div>


        </div>


        <!-- ================================================= -->
        <!-- ACTIVAR -->
        <!-- ================================================= -->

        <div style="
            display:flex;
            align-items:center;
            gap:8px;
            margin-bottom:15px;
        ">

          <input type="hidden" name="active" value="1">

            <span style="
                color:var(--text);
                cursor:pointer;
            ">

           
            </span>

        </div>


        <!-- ================================================= -->
        <!-- BOTÓN -->
        <!-- ================================================= -->

        <div style="
            display:flex;
            justify-content:flex-end;
        ">

            <button
                type="submit"
                class="btn-primary-custom"
            >

                ➕ Crear alerta

            </button>

        </div>


    </form>


    <!-- ================================================= -->
    <!-- LISTADO -->
    <!-- ================================================= -->

    <div style="
        margin-top:25px;
    ">


        @forelse($modelo->alertas()->latest('next_run_at')->get() as $alerta)


            <div class="card-info">


                <div style="
                    flex:1;
                ">


                    <!-- ================================================= -->
                    <!-- ASUNTO -->
                    <!-- ================================================= -->

                    <div style="
                        font-weight:600;
                        font-size:15px;
                    ">

                        {{ $alerta->asunto }}

                    </div>


                    @php
    $metadata = $alerta->metadata;

    if (is_string($metadata)) {
        $metadata = json_decode($metadata, true);
    }
@endphp

@if(
    is_array($metadata) &&
    !empty($metadata['referencia']) &&
    isset($metadata['valor']) &&
    $metadata['valor'] !== ''
)

    @php
        $nombreReferencia = match ($metadata['referencia']) {
            'matricula' => 'Matrícula',
            'codigo' => 'Código',
            'identificador_tributario' => 'Identificador tributario',
            default => ucfirst(
                str_replace(
                    '_',
                    ' ',
                    $metadata['referencia']
                )
            ),
        };
    @endphp

<div style="
    margin-top:4px;
    font-size:13px;
    color:var(--text-secondary);
">

    🏷️

    <strong style="color:var(--text);">
        {{ $metadata['valor'] }}
    </strong>

</div>

@endif

                    <!-- ================================================= -->
                    <!-- MENSAJE -->
                    <!-- ================================================= -->

                    <div style="
                        margin-top:4px;
                    ">

                        {{ $alerta->mensaje }}

                    </div>


                    <!-- ================================================= -->
                    <!-- FECHA / HORA -->
                    <!-- ================================================= -->

                    <div style="
                        margin-top:5px;
                        font-size:13px;
                        color:var(--text-secondary);
                    ">

                        📅

                        {{ $alerta->fecha->format('d/m/Y') }}

                        &nbsp;

                        🕐

                        {{ \Carbon\Carbon::parse($alerta->hora)->format('H:i') }}


                        @if($alerta->recurrencia !== 'none')

                            &nbsp;

                            🔁

                            @switch($alerta->recurrencia)

                                @case('daily')

                                    Diaria

                                    @break

                                @case('weekly')

                                    Semanal

                                    @break

                                @case('monthly')

                                    Mensual

                                    @break

                                @case('yearly')

                                    Anual

                                    @break

                            @endswitch

                        @endif

                    </div>


                    <!-- ================================================= -->
                    <!-- ESTADO -->
                    <!-- ================================================= -->

                 <div style="
    margin-top:6px;
">

    <span
        data-alert-status
        style="
            color:{{ $alerta->active ? '#16a34a' : '#6b7280' }};
            font-size:13px;
        "
    >
        {{ $alerta->active ? '🟢 Activa' : '⚪ Inactiva' }}
    </span>

</div>

<!-- ================================================= -->
<!-- DESTINATARIOS -->
<!-- ================================================= -->

<div style="
    margin-top:10px;
    font-size:12px;
    color:var(--text-secondary);
">

    <div style="
        margin-bottom:7px;
        font-weight:600;
        color:var(--text);
    ">

        👥 Destinatarios
        ({{ $alerta->destinatarios ? $alerta->destinatarios->count() : 0 }})

    </div>


    @if($alerta->destinatarios && $alerta->destinatarios->count() > 0)

        <div style="
            display:flex;
            flex-direction:column;
            gap:5px;
        ">

            @foreach($alerta->destinatarios as $destinatario)

                <div style="
                    display:flex;
                    align-items:center;
                    gap:8px;
                    padding:6px 9px;
                    border:1px solid var(--border);
                    border-radius:7px;
                ">

                    <span style="
                        flex:0 0 auto;
                    ">

                        {{ $destinatario->type === 'persona' ? '👤' : '✉️' }}

                    </span>


                    <div style="
                        display:flex;
                        flex-direction:column;
                        min-width:0;
                    ">

                        @if($destinatario->name)

                            <span style="
                                font-weight:600;
                                color:var(--text);
                            ">

                                {{ $destinatario->name }}

                            </span>

                        @endif


                        <span style="
                            color:var(--text-secondary);
                            word-break:break-all;
                        ">

                            {{ $destinatario->email }}

                        </span>

                    </div>

                </div>

            @endforeach

        </div>


    @else

        <div style="
            color:var(--text-secondary);
        ">

            No hay destinatarios registrados.

        </div>

    @endif

</div>


                </div>

<form
    action="{{ route('alerts.toggle', $alerta) }}"
    method="POST"
    style="display:inline;"
    onsubmit="toggleAlert(event, this)"
>
    @csrf
    @method('PATCH')

  <button
    type="submit"
    style="
        border:none;
        background:none;
        cursor:pointer;
        font-size:13px;
        color:{{ $alerta->active ? '#dc2626' : '#16a34a' }};
        font-weight:500;
    "
>
    {{ $alerta->active ? 'Desactivar' : 'Activar' }}
</button>

</form>

                <!-- ================================================= -->
                <!-- ELIMINAR -->
                <!-- ================================================= -->

                <form
                    method="POST"
                    action="{{ route('alerts.destroy', $alerta->id) }}"
                >

                    @csrf

                    @method('DELETE')


                    <button
                        type="submit"
                        style="
                            border:none;
                            background:none;
                            color:#ef4444;
                            cursor:pointer;
                            font-size:16px;
                        "
                        title="Eliminar alerta"
                    >

                        🗑️

                    </button>

                </form>


            </div>


        @empty


            <div style="
                color:var(--text-secondary);
                padding:10px;
            ">

                No hay alertas registradas.

            </div>


        @endforelse


    </div>


</div>

<script>
async function toggleAlert(event, form) {
    event.preventDefault();

    const button = form.querySelector('button');
    const originalText = button.textContent.trim();

    button.disabled = true;
    button.textContent = 'Procesando...';

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN':
                    form.querySelector('input[name="_token"]').value,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type':
                    'application/x-www-form-urlencoded; charset=UTF-8'
            },
            body: new URLSearchParams(new FormData(form))
        });

        if (!response.ok) {
            throw new Error(
                'No se pudo cambiar el estado de la alerta.'
            );
        }

        const data = await response.json();

        /*
         * Cambiar texto y color del botón
         */
        if (data.active) {

            button.textContent = 'Desactivar';
            button.style.color = '#dc2626';

        } else {

            button.textContent = 'Activar';
            button.style.color = '#16a34a';
        }

        /*
         * Cambiar estado visual
         */
        const card = form.closest('.card-info');

        const status = card.querySelector('[data-alert-status]');

        if (status) {

            if (data.active) {

                status.textContent = '🟢 Activa';
                status.style.color = '#16a34a';

            } else {

                status.textContent = '⚪ Inactiva';
                status.style.color = '#6b7280';
            }
        }

    } catch (error) {

        button.textContent = originalText;

        alert(error.message);

    } finally {

        button.disabled = false;
    }
}
</script>