<?php

namespace App\Http\Controllers;

use App\Models\Expediente;
use App\Models\Sujeto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SujetoController extends Controller
{
    public function store(Request $request, Expediente $expediente)
    {
        $puedeEditar =
            Auth::user()->role === 'admin'
            || $expediente->user_id === Auth::id();

        if (!$puedeEditar) {
            abort(403);
        }

        $request->validate([
            'tipo' => 'required',
            'nombre' => 'required',
            'identificacion' => 'nullable|string|max:255',
            'cah' => 'nullable|string|max:255',
        ]);

      $sujeto = $expediente->sujetos()->create([
    'tipo' => $request->tipo,
    'nombre' => $request->nombre,
    'identificacion' => $request->identificacion,
    'cah' => $request->cah,
]);

      if ($request->expectsJson()) {
    $sujeto->load('documentos');

    return response()->json([
        'success' => true,
        'message' => 'Sujeto agregado correctamente.',
        'sujeto' => $sujeto,
    ]);
}

return back()->with(
    'success',
    'Sujeto agregado correctamente.'
);
    }

    public function update(Request $request, Sujeto $sujeto)
    {
        $expediente = $sujeto->expediente;

        $puedeEditar =
            Auth::user()->role === 'admin'
            || (
                $expediente->user_id === Auth::id()
                && $expediente->permite_edicion
            );

        if (!$puedeEditar) {
            abort(403);
        }

        $request->validate([
            'tipo' => 'required',
            'nombre' => 'required',
            'identificacion' => 'nullable|string|max:255',
            'cah' => 'nullable|string|max:255',
        ]);

        $sujeto->update([
            'tipo' => $request->tipo,
            'nombre' => $request->nombre,
            'identificacion' => $request->identificacion,
            'cah' => $request->cah,
        ]);

        return back()->with(
            'success',
            'Sujeto actualizado correctamente.'
        );
    }

    public function destroy(Sujeto $sujeto)
    {
        $expediente = $sujeto->expediente;

        $puedeEditar =
            Auth::user()->role === 'admin'
            || (
                $expediente->user_id === Auth::id()
                && $expediente->permite_edicion
            );

        if (!$puedeEditar) {
            abort(403);
        }

        $sujeto->delete();

        return back()->with(
            'success',
            'Sujeto eliminado correctamente.'
        );
    }
}