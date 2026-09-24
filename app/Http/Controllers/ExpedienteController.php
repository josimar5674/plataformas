<?php

namespace App\Http\Controllers;

use App\Models\Expediente;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpedienteController extends Controller
{
    public function index()
    {
        $expedientes = Expediente::with('user')
            ->when(
                Auth::user()->role !== 'admin',
                fn ($query) => $query->where('user_id', Auth::id())
            )
            ->latest()
            ->get();

        return view('expedientes.index', [
            'expedientes' => $expedientes,
            'expedienteSeleccionado' => null,
            'usuarios' => Auth::user()->role === 'admin'
                ? User::all()
                : [],
        ]);
    }

    public function create()
    {
        return view('expedientes.create', [
            'usuarios' => Auth::user()->role === 'admin'
                ? User::all()
                : [],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'numero_expediente' => 'required|unique:expedientes,numero_expediente',
            'tipo_tramite' => 'required',
            'cuantia' => 'nullable|numeric',
        ]);

        Expediente::create([
            'user_id' => Auth::id(),
            'numero_expediente' => $request->numero_expediente,
            'tipo_tramite' => $request->tipo_tramite,
            'matricula' => $request->matricula,
            'sede' => $request->sede,
            'asignado' => $request->asignado,
            'pretension_principal' => $request->pretension_principal,
            'cuantia' => $request->cuantia,
            'fecha_presentacion' => $request->fecha_presentacion,
            'descripcion_proceso' => $request->descripcion_proceso,
            'estado' => 'pendiente',
        ]);

        return redirect()
            ->route('expedientes.index')
            ->with('success', 'Expediente creado correctamente.');
    }

    public function show(Expediente $expediente)
    {
        if (
            Auth::user()->role !== 'admin'
            && $expediente->user_id !== Auth::id()
        ) {
            abort(403);
        }

        $expedientes = Expediente::with('user')
            ->when(
                Auth::user()->role !== 'admin',
                fn ($query) => $query->where('user_id', Auth::id())
            )
            ->latest()
            ->get();

        $expediente->load([
            'user',
            'sujetos.documentos',
            'sujetos.notas',
            'movimientos',
            'documentos',
            'notas',
        ]);

        return view('expedientes.index', [
            'expedientes' => $expedientes,
            'expedienteSeleccionado' => $expediente,
            'usuarios' => Auth::user()->role === 'admin'
                ? User::all()
                : [],
        ]);
    }

    public function update(Request $request, Expediente $expediente)
    {
        $puedeEditar =
            Auth::user()->role === 'admin'
            || $expediente->user_id === Auth::id();

        if (!$puedeEditar) {
            abort(403);
        }

        $request->validate([
            'tipo_tramite' => 'required',
            'matricula' => 'nullable|string|max:255',
            'sede' => 'nullable|string|max:255',
            'asignado' => 'nullable|string|max:255',
            'pretension_principal' => 'nullable|string',
            'cuantia' => 'nullable|numeric',
            'descripcion_proceso' => 'nullable|string',
        ]);

        $expediente->update([
            'tipo_tramite' => $request->tipo_tramite,
            'matricula' => $request->matricula,
            'sede' => $request->sede,
            'asignado' => $request->asignado,
            'pretension_principal' => $request->pretension_principal,
            'cuantia' => $request->cuantia,
            'descripcion_proceso' => $request->descripcion_proceso,
            'user_id' => $request->user_id ?? $expediente->user_id,
            'permite_edicion' => $request->has('permite_edicion'),
        ]);

        return back()->with(
            'success',
            'Expediente actualizado correctamente.'
        );
    }

    public function updateEstado(
        Request $request,
        Expediente $expediente
    ) {
        $request->validate([
            'estado' => 'required|in:pendiente,en_proceso,audiencia,resuelto,cerrado',
        ]);

        $expediente->update([
            'estado' => $request->estado,
        ]);

        return back()->with(
            'success',
            'Estado actualizado correctamente.'
        );
    }
}