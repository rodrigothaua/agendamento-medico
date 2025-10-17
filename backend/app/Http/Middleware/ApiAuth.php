<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiAuth
{
    public function handle(Request $request, Closure $next)
    {
        // Verificar se está em sessão (para chamadas do admin) - só se sessão estiver disponível
        if ($request->hasSession() && ($request->session()->has('user_id') || $request->session()->has('user_name'))) {
            return $next($request);
        }

        // Verificar API key se fornecida (para integrações externas)
        $apiKey = $request->header('X-API-Key') ?: $request->query('api_key');
        
        if ($apiKey && $this->isValidApiKey($apiKey)) {
            return $next($request);
        }

        return response()->json([
            'success' => false,
            'message' => 'Acesso não autorizado'
        ], 401);
    }

    private function isValidApiKey($apiKey)
    {
        // Por enquanto, usar uma chave simples. 
        // Em produção, isso deveria vir do banco de dados ou configuração
        $validKeys = [
            env('API_KEY', 'clinic-api-key-2025'),
            'admin-key-local'
        ];

        return in_array($apiKey, $validKeys);
    }
}