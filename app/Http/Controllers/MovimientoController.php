<?php

namespace App\Http\Controllers;

use App\Models\Movimiento;
use App\Models\Expediente;
use Illuminate\Http\Request;

class MovimientoController extends Controller
{
    /**
     * Registrar un nuevo movimiento.
     */
    public function store(Request $request, Expediente $expediente)
    {
        $validated = $request->validate([
            'fecha' => ['required', 'date'],
            'descripcion' => ['required', 'string'],
        ]);

        $movimiento = $expediente->movimientos()->create($validated);

        return redirect()
            ->route('expedientes.show', $expediente)
            ->with('success', 'Movimiento registrado correctamente.');
    }

    /**
     * Actualizar un movimiento.
     */
    public function update(Request $request, Movimiento $movimiento)
    {
        $validated = $request->validate([
            'fecha' => ['required', 'date'],
            'descripcion' => ['required', 'string'],
        ]);

        $movimiento->update($validated);

        return redirect()
            ->route('expedientes.show', $movimiento->expediente)
            ->with('success', 'Movimiento actualizado correctamente.');
    }

    /**
     * Eliminar un movimiento.
     */
    public function destroy(Movimiento $movimiento)
    {
        $expediente = $movimiento->expediente;

        $movimiento->delete();

        return redirect()
            ->route('expedientes.show', $expediente)
            ->with('success', 'Movimiento eliminado correctamente.');
    }
}