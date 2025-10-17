<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\User; // Certifique-se de ter este Model!

class AuthController extends Controller
{
    /**
     * Exibe o formulário de login.
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Processa as credenciais de login.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 1. Tenta encontrar o usuário pelo email
        $user = User::where('email', $request->email)->first();

        // 2. Verifica se o usuário existe e se a senha está correta
        if ($user && Hash::check($request->password, $user->password)) {
            
            // Autenticação bem-sucedida

            // Define variáveis de sessão
            Session::put('authenticated', true);
            // Armazena o nome do usuário para exibição no Dashboard
            Session::put('user_name', $user->name); 
            // Armazena o ID do usuário (pode ser útil futuramente)
            Session::put('user_id', $user->id); 

            // Redireciona para o dashboard
            return redirect()->route('admin.dashboard');

        } else {
            // Falha na autenticação
            return back()->withErrors(['email' => 'Credenciais fornecidas não correspondem aos nossos registros.'])->withInput($request->only('email'));
        }
    }

    /**
     * Realiza o logout do usuário.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        Session::flush();
        return redirect()->route('login');
    }
}
