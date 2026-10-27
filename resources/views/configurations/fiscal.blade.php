<div class="fiscal-page">

    {{-- ENCABEZADO --}}
    <header class="fiscal-header">
        <h2>🧾 Configuración fiscal</h2>

        <p>
            Administra las tasas de impuestos y el tipo de cambio mensual
            que utilizará el módulo Finanzas.
        </p>
    </header>

    {{-- MENSAJES DE VALIDACIÓN --}}
    @if($errors->any())
        <div class="alert alert-danger fiscal-alert">
            <strong>Revisa los siguientes datos:</strong>

            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- =====================================================
         TIPO DE CAMBIO
    ====================================================== --}}
    <section class="fiscal-section">

        <div class="fiscal-section-header">
            <div>
                <h3>Tipo de cambio mensual</h3>

                <p>
                    Configura la tasa USD → HNL correspondiente a cada mes.
                    Las tasas de otros períodos se conservarán en el historial.
                </p>
            </div>
        </div>

        <form
            method="POST"
            action="{{ route('fiscal.exchange-rates.store') }}"
            class="fiscal-exchange-form"
        >
            @csrf

            <div class="fiscal-field">
                <label for="exchange_year">Año</label>

                <input
                    id="exchange_year"
                    class="form-control fiscal-control"
                    type="number"
                    name="year"
                    min="2020"
                    max="2200"
                    value="{{ old('year', $selectedYear) }}"
                    required
                >
            </div>

            <div class="fiscal-field">
                <label for="exchange_month">Mes</label>

                <select
                    id="exchange_month"
                    class="form-control fiscal-control fiscal-select"
                    name="month"
                    required
                >
                    @foreach([
                        1 => 'Enero',
                        2 => 'Febrero',
                        3 => 'Marzo',
                        4 => 'Abril',
                        5 => 'Mayo',
                        6 => 'Junio',
                        7 => 'Julio',
                        8 => 'Agosto',
                        9 => 'Septiembre',
                        10 => 'Octubre',
                        11 => 'Noviembre',
                        12 => 'Diciembre'
                    ] as $number => $monthName)

                        <option
                            value="{{ $number }}"
                            @selected((int) old('month', $selectedMonth) === $number)
                        >
                            {{ $monthName }}
                        </option>

                    @endforeach
                </select>
            </div>

            <div class="fiscal-field">
                <label for="exchange_rate">Lempiras por dólar</label>

                <input
                    id="exchange_rate"
                    class="form-control fiscal-control"
                    type="number"
                    name="rate"
                    min="0.0001"
                    step="0.0001"
                    value="{{ old('rate', $currentExchangeRate?->rate) }}"
                    placeholder="Ej. 26.9455"
                    required
                >
            </div>

            <div class="fiscal-button-wrapper">
                <button
                    type="submit"
                    class="btn-primary-custom fiscal-save-button"
                >
                    💾 Guardar tasa
                </button>
            </div>

        </form>

        {{-- ESTADO DE LA TASA --}}
        @if($currentExchangeRate)

            <div class="fiscal-notice fiscal-notice-success">
                <span>✓</span>

                <div>
                    Tasa registrada para
                    <strong>
                        {{ sprintf('%02d', $selectedMonth) }}/{{ $selectedYear }}
                    </strong>

                    <div class="fiscal-notice-detail">
                        {{ number_format((float) $currentExchangeRate->rate, 4) }}
                        HNL por USD
                    </div>
                </div>
            </div>

        @else

            <div class="fiscal-notice fiscal-notice-warning">
                <span>!</span>

                <div>
                    No hay una tasa registrada para
                    <strong>
                        {{ sprintf('%02d', $selectedMonth) }}/{{ $selectedYear }}
                    </strong>.

                    <div class="fiscal-notice-detail">
                        Registra la tasa antes de crear ingresos para este período.
                    </div>
                </div>
            </div>

        @endif

        {{-- HISTORIAL DE TASAS --}}
        <div class="fiscal-subsection">

            <h4>Historial de tasas</h4>

            <div class="fiscal-table-container">

                <table class="table fiscal-table">

                    <thead>
                        <tr>
                            <th>Período</th>
                            <th>USD → HNL</th>
                            <th>Actualizado por</th>
                            <th>Última actualización</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($financeExchangeRates as $exchangeRate)

                            <tr>
                                <td>
                                    <span class="fiscal-period">
                                        {{ sprintf('%02d', $exchangeRate->month) }}/{{ $exchangeRate->year }}
                                    </span>
                                </td>

                                <td>
                                    <strong>
                                        {{ number_format((float) $exchangeRate->rate, 4) }}
                                    </strong>
                                </td>

                                <td>
                                    {{ $exchangeRate->updatedBy?->email ?? 'Usuario no disponible' }}
                                </td>

                                <td>
                                    {{ $exchangeRate->updated_at?->format('d/m/Y H:i') }}
                                </td>

                                <td>
                                    <a
                                            class="btn-secondary fiscal-action-button"
                                        href="{{ url('/configuraciones') }}?section=fiscal&year={{ $exchangeRate->year }}&month={{ $exchangeRate->month }}"
                                    >
                                        Cargar
                                    </a>
                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="5" class="fiscal-empty-state">
                                    Todavía no hay tasas de cambio registradas.
                                </td>
                            </tr>

                        @endforelse
                    </tbody>

                </table>

            </div>
        </div>

    </section>


    {{-- =====================================================
         IMPUESTOS
    ====================================================== --}}
    <section class="fiscal-section">

        <div class="fiscal-section-header">
            <div>
                <h3>Impuestos disponibles</h3>

                <p>
                    Administra los impuestos que podrán seleccionarse
                    al registrar ingresos en Finanzas.
                </p>
            </div>
        </div>

        {{-- AGREGAR IMPUESTO --}}
        <form
            method="POST"
            action="{{ route('fiscal.taxes.store') }}"
            class="fiscal-tax-form"
        >
            @csrf

            <div class="fiscal-field">
                <label for="tax_name">Nombre del impuesto</label>

                <input
                    id="tax_name"
                    class="form-control fiscal-control"
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    placeholder="Ej. ISV 15 %"
                    maxlength="120"
                    required
                >
            </div>

            <div class="fiscal-field">
                <label for="tax_percentage">Porcentaje (%)</label>

                <input
                    id="tax_percentage"
                    class="form-control fiscal-control"
                    type="number"
                    name="percentage"
                    min="0"
                    max="100"
                    step="0.0001"
                    value="{{ old('percentage') }}"
                    placeholder="15"
                    required
                >
            </div>

            <div class="fiscal-button-wrapper">
                <button
                    type="submit"
                    class="btn-primary-custom fiscal-save-button"
                >
                    ＋ Agregar impuesto
                </button>
            </div>

        </form>

        {{-- TABLA DE IMPUESTOS --}}
        <div class="fiscal-table-container">

            <table class="table fiscal-table fiscal-tax-table">

                <thead>
                    <tr>
                        <th>Nombre del impuesto</th>
                        <th>Porcentaje</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($financeTaxRates as $taxRate)

                        <tr>

                            {{-- NOMBRE EDITABLE --}}
                            <td>
                                <form
                                    id="tax-update-{{ $taxRate->id }}"
                                    method="POST"
                                    action="{{ route('fiscal.taxes.update', $taxRate) }}"
                                    class="fiscal-tax-edit-form"
                                >
                                    @csrf
                                    @method('PUT')

                                    <input
                                        class="form-control fiscal-control"
                                        type="text"
                                        name="name"
                                        value="{{ $taxRate->name }}"
                                        maxlength="120"
                                        aria-label="Nombre del impuesto"
                                        required
                                    >

                                    <div class="fiscal-percentage-field">

                                        <input
                                            class="form-control fiscal-control"
                                            type="number"
                                            name="percentage"
                                            value="{{ $taxRate->percentage }}"
                                            min="0"
                                            max="100"
                                            step="0.0001"
                                            aria-label="Porcentaje del impuesto"
                                            required
                                        >

                                        <span>%</span>

                                    </div>
                                </form>
                            </td>

                            {{-- TASA ACTUAL --}}
                            <td>
                                <span class="fiscal-rate-badge">
                                    {{ number_format((float) $taxRate->percentage, 4) }} %
                                </span>
                            </td>

                            {{-- ESTADO --}}
                            <td>
                                <span class="configuration-status {{ $taxRate->active ? 'active' : 'inactive' }}">
                                    {{ $taxRate->active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>

                            {{-- ACCIONES --}}
                            <td>
                                <div class="fiscal-tax-actions">

                                    <button
                                        type="submit"
                                        form="tax-update-{{ $taxRate->id }}"
                                        class="btn-primary-custom fiscal-action-button"
                                    >
                                        Guardar
                                    </button>

                                    <form
                                        method="POST"
                                        action="{{ route('fiscal.taxes.toggle', $taxRate) }}"
                                        onsubmit="return confirm('¿Deseas {{ $taxRate->active ? 'desactivar' : 'activar' }} este impuesto?');"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <button
                                            type="submit"
                                            class="btn-secondary fiscal-action-button"
                                        >
                                            {{ $taxRate->active ? 'Desactivar' : 'Activar' }}
                                        </button>
                                    </form>

                                </div>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="4" class="fiscal-empty-state">
                                <div class="fiscal-empty-icon">🧾</div>

                                <strong>No hay impuestos configurados</strong>

                                <p>
                                    Agrega el primer impuesto utilizando el formulario anterior.
                                </p>
                            </td>
                        </tr>

                    @endforelse
                </tbody>

            </table>

        </div>

    </section>

</div>