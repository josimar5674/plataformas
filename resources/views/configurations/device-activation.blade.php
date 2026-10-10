<div style="max-width: 600px;">

    <h3>📱 Activación de dispositivos ZHX</h3>

    <p style="color: var(--text-secondary); margin-bottom: 25px;">
        Genera un PIN temporal para autorizar un dispositivo móvil.
        El PIN tiene una vigencia de un minuto y solo puede utilizarse una vez.
    </p>

    @if(session('activation_pin'))
        <div class="alert alert-success" style="text-align: center; padding: 25px;">

            <div style="font-size: 14px; margin-bottom: 10px;">
                PIN de activación
            </div>

            <div id="activationPin"
                 style="font-size: 42px; font-weight: bold; letter-spacing: 8px;">
                {{ session('activation_pin') }}
            </div>

            <div id="countdown" style="margin-top: 10px;">
                Vigencia: 60 segundos
            </div>

        </div>

        <script>
            (() => {
                let remaining = 60;

                const countdown = document.getElementById('countdown');
                const pin = document.getElementById('activationPin');

                const timer = setInterval(() => {
                    remaining--;

                    if (remaining <= 0) {
                        clearInterval(timer);
                        countdown.textContent = 'PIN expirado';
                        pin.textContent = '------';
                        return;
                    }

                    countdown.textContent = 'Vigencia: ' + remaining + ' segundos';
                }, 1000);
            })();
        </script>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.device-activation.generate') }}">
        @csrf

        <button type="submit" class="btn-primary-custom">
            🔑 Generar PIN de activación
        </button>
    </form>

</div>

<hr style="margin: 35px 0; border-color: var(--border);">

<div style="margin-bottom: 20px;">
    <h3>📱 Dispositivos vinculados</h3>

    <p style="color: var(--text-secondary);">
        Consulta los dispositivos registrados, sus usuarios y su última actividad.
    </p>
</div>

<div style="overflow-x: auto;">

    <table class="table">
        <thead>
            <tr>
                <th>Usuario</th>
                <th>Dispositivo</th>
                <th>Plataforma</th>
                <th>Estado</th>
                <th>Vinculación</th>
                <th>Última actividad</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>
            @forelse($trustedDevices as $device)
                <tr>
                    <td>
                        <strong>
                            {{ trim(($device->user->first_name ?? '') . ' ' . ($device->user->last_name ?? '')) ?: 'Sin nombre' }}
                        </strong>

                        <div style="font-size: 12px; color: var(--text-secondary);">
                            {{ $device->user->email ?? 'Usuario no disponible' }}
                        </div>
                    </td>

                    <td>
                        <strong>
                            {{ $device->device_name ?: 'Dispositivo sin nombre' }}
                        </strong>

                        <div style="font-size: 11px; color: var(--text-secondary);">
                            {{ $device->device_uuid }}
                        </div>
                    </td>

                    <td>
                        {{ $device->platform ?: 'No registrada' }}
                    </td>

                    <td>
                        @if($device->is_active)
                            <span class="configuration-status active">
                                Autorizado
                            </span>
                        @else
                            <span class="configuration-status inactive">
                                Desactivado
                            </span>
                        @endif
                    </td>

                    <td>
                        {{ $device->registered_at
                            ? $device->registered_at->format('d/m/Y H:i')
                            : 'Sin registro' }}
                    </td>

                    <td>
                        {{ $device->last_used_at
                            ? $device->last_used_at->format('d/m/Y H:i')
                            : 'Sin actividad registrada' }}
                    </td>

                    <td>
    <form
        method="POST"
        action="{{ route('admin.device-activation.destroy', $device->id) }}"
        onsubmit="return confirm('¿Seguro que deseas eliminar este dispositivo?');"
    >
        @csrf
        @method('DELETE')

        <button type="submit" class="btn-danger">
            🗑️ Eliminar
        </button>
    </form>
</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 25px;">
                        Todavía no hay dispositivos vinculados.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>