<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Middleware de autorización por rol.
 *
 * Verifica que el usuario autenticado tenga uno de los roles
 * permitidos para la ruta. Si no está autenticado redirige al login;
 * si no tiene el rol adecuado retorna 403.
 *
 * Uso en rutas: ->middleware('role:admin,tutor')
 */
class RoleMiddleware
{
    /**
     * Maneja la solicitud entrante.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure                  $next
     * @param  string  ...$roles  Roles permitidos
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!in_array($user->role, $roles, true)) {
            abort(403);
        }

        return $next($request);
    }
}
