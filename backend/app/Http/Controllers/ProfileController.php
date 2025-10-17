<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules\Password;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit()
    {
        $userId = Session::get('user_id');
        $user = User::find($userId);
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Usuário não encontrado.');
        }
        
        return view('admin.profile.edit', compact('user'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $userId = Session::get('user_id');
        $user = User::find($userId);
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Usuário não encontrado.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update($validated);
        
        // Atualiza o nome na sessão também
        Session::put('user_name', $validated['name']);

        return redirect()->route('admin.profile.edit')->with('success', 'Perfil atualizado com sucesso!');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $userId = Session::get('user_id');
        $user = User::find($userId);
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Usuário não encontrado.');
        }

        $validated = $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ], [
            'current_password.required' => 'A senha atual é obrigatória.',
            'password.required' => 'A nova senha é obrigatória.',
            'password.confirmed' => 'A confirmação da senha não confere.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
            'password.mixed_case' => 'A senha deve conter pelo menos uma letra maiúscula e uma minúscula.',
            'password.numbers' => 'A senha deve conter pelo menos um número.',
        ]);

        // Verificar se a senha atual está correta
        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'A senha atual está incorreta.']);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.profile.edit')->with('success', 'Senha alterada com sucesso!');
    }

    /**
     * Show user activity log (future implementation).
     */
    public function activity()
    {
        // Placeholder for activity log functionality
        return view('admin.profile.activity');
    }
}
