<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CheckAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next)
    {
        // Verifica se a chave 'authenticated' está na sessão.
        if (!Session::get('authenticated')) {
            // Se não estiver autenticado, redireciona para a rota de login
            return redirect()->route('login');
        }
        return $next($request);
    }
}
