<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class EnsureFinancePermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(401);
        }

        if ($user->role === 'admin') {
            return $next($request);
        }

        $permissions = DB::table('user_finance_permissions')
            ->where('user_id', $user->id)
            ->first();

        if (!$permissions || !in_array($permission, ['ingresos', 'egresos', 'resumen'], true) || !($permissions->{$permission} ?? false)) {
            abort(403, 'No tienes permiso para acceder a esta sección de Finanzas.');
        }

        return $next($request);
    }
}
