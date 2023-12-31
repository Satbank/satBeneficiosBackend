<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle($request, Closure $next)
    {
        // Verifica se o usuário está autenticado
        if (!Auth::check()) {
            return response()->json(['message' => 'Acesso não autorizado'], 401);
        }

        // Verifica se o perfil_id é igual a 1 (admin)
       
        if (Auth::user()->perfils_id !== 1) {
            return response()->json(['message' => 'Acesso proibido. Você não é um administrador.'], 403);
        }

        // Se o usuário é admin, permite o acesso à rota
        return $next($request);
    }
}
