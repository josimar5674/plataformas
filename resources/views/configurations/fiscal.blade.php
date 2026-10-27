<div style="max-width: 1100px;">
    <div style="margin-bottom: 28px;">
        <h2 style="margin-bottom: 8px;">🧾 Configuración fiscal</h2>
        <p style="color: var(--text-secondary);">
            Administra las tasas de impuestos y el tipo de cambio mensual que utilizará Finanzas.
            Los movimientos financieros deberán conservar la tasa aplicada al momento de registrarse.
        </p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Revisa los siguientes datos:</strong>
            <ul style="margin: 8px 0 0 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section style="padding: 22px; border: 1px solid var(--border); border-radius: 12px; margin-bottom: 28px;">
        <h3 style="margin-top: 0;">💱 Tipo de cambio mensual</h3>
        <p style="color: var(--text-secondary); margin-bottom: 20px;">
            Introduce manualmente la tasa USD → HNL vigente para un mes. Si ese mes ya existe,
            guardar de nuevo actualizará la tasa de ese período; las tasas de otros meses no cambiarán.
        </p>

        <form method="POST" action="{{ route('fiscal.exchange-rates.store') }}">
            @csrf
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 14px; align-items: end;">
                <div class="form-group">
                    <label for="exchange_year">Año</label>
                    <input id="exchange_year" class="form-control" type="number" name="year"
                           min="2020" max="2200" value="{{ old('year', $selectedYear) }}" required>
                </div>
                <div class="form-group">
                    <label for="exchange_month">Mes</label>
                    <select id="exchange_month" class="form-control" name="month" required>
                        @foreach([1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'] as $number => $monthName)
                            <option value="{{ $number }}" @selected((int) old('month', $selectedMonth) === $number)>
                                {{ $monthName }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="exchange_rate">Lempiras por dólar</label>
                    <input id="exchange_rate" class="form-control" type="number" name="rate"
                           min="0.0001" step="0.0001" value="{{ old('rate', $currentExchangeRate?->rate) }}"
                           placeholder="Ej. 26.9455" required>
                </div>
                <div>
                    <button type="submit" class="btn-primary-custom" style="width: 100%;">
                        💾 Guardar tasa
                    </button>
                </div>
            </div>
        </form>

        @if($currentExchangeRate)
            <div class="alert alert-success" style="margin-top: 15px;">
                Tasa guardada para {{ sprintf('%02d', $selectedMonth) }}/{{ $selectedYear }}:
                <strong>{{ number_format((float) $currentExchangeRate->rate, 4) }} HNL por USD</strong>.
            </div>
        @else
            <div class="alert" style="margin-top: 15px;">
                Todavía no hay una tasa guardada para {{ sprintf('%02d', $selectedMonth) }}/{{ $selectedYear }}.
                Selecciona el mes y registra la tasa antes de crear ingresos para ese período.
            </div>
        @endif

        <h4 style="margin-top: 28px;">Historial de tasas</h4>
        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Período</th>
                        <th>USD → HNL</th>
                        <th>Actualizado por</th>
                        <th>Última actualización</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($financeExchangeRates as $exchangeRate)
                        <tr>
                            <td>{{ sprintf('%02d', $exchangeRate->month) }}/{{ $exchangeRate->year }}</td>
                            <td><strong>{{ number_format((float) $exchangeRate->rate, 4) }}</strong></td>
                            <td>{{ $exchangeRate->updatedBy?->email ?? 'Usuario no disponible' }}</td>
                            <td>{{ $exchangeRate->updated_at?->format('d/m/Y H:i') }}</td>
                            <td>
                                <a class="btn-secondary"
                                   href="{{ url('/configuraciones') }}?section=fiscal&year={{ $exchangeRate->year }}&month={{ $exchangeRate->month }}">
                                    Cargar
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 20px;">
                                Todavía no hay tasas de cambio registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section style="padding: 22px; border: 1px solid var(--border); border-radius: 12px;">
        <h3 style="margin-top: 0;">🧮 Impuestos disponibles</h3>
        <p style="color: var(--text-secondary); margin-bottom: 20px;">
            Estas tasas se podrán seleccionar en cada fila de ingresos. Desactiva una tasa cuando ya no
            quieras ofrecerla en nuevos registros; no la borres si ya está asociada a movimientos.
        </p>

        <form method="POST" action="{{ route('fiscal.taxes.store') }}"
              style="display: grid; grid-template-columns: minmax(180px, 1fr) minmax(120px, 200px) auto; gap: 12px; align-items: end; margin-bottom: 24px;">
            @csrf
            <div class="form-group">
                <label for="tax_name">Nombre del impuesto</label>
                <input id="tax_name" class="form-control" type="text" name="name"
                       value="{{ old('name') }}" placeholder="Ej. ISV 15 %" maxlength="120" required>
            </div>
            <div class="form-group">
                <label for="tax_percentage">Porcentaje (%)</label>
                <input id="tax_percentage" class="form-control" type="number" name="percentage"
                       min="0" max="100" step="0.0001" value="{{ old('percentage') }}"
                       placeholder="15" required>
            </div>
            <button type="submit" class="btn-primary-custom">➕ Agregar impuesto</button>
        </form>

        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Tasa</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($financeTaxRates as $taxRate)
                        <tr>
                            <td colspan="4" style="padding: 12px 8px;">
                                <form method="POST" action="{{ route('fiscal.taxes.update', $taxRate) }}"
                                      style="display: grid; grid-template-columns: minmax(180px, 1fr) minmax(110px, 150px) auto auto; gap: 10px; align-items: center;">
                                    @csrf
                                    @method('PUT')
                                    <input class="form-control" type="text" name="name"
                                           value="{{ $taxRate->name }}" maxlength="120" required
                                           aria-label="Nombre del impuesto">
                                    <div style="display: flex; align-items: center; gap: 6px;">
                                        <input class="form-control" type="number" name="percentage"
                                               value="{{ $taxRate->percentage }}" min="0" max="100"
                                               step="0.0001" required aria-label="Porcentaje del impuesto">
                                        <span>%</span>
                                    </div>
                                    <button type="submit" class="btn-secondary">Guardar cambios</button>
                                    <span class="configuration-status {{ $taxRate->active ? 'active' : 'inactive' }}">
                                        {{ $taxRate->active ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </form>
                                <form method="POST" action="{{ route('fiscal.taxes.toggle', $taxRate) }}"
                                      style="margin-top: 8px; text-align: right;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn-secondary">
                                        {{ $taxRate->active ? 'Desactivar' : 'Activar' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 20px;">
                                No hay impuestos configurados. Agrega, por ejemplo, ISV 15 %.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
