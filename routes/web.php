<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
// Importa o Middleware para proteger a rota do Dashboard
use App\Http\Middleware\CheckAuth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Rota padrão do Laravel (pode ser o frontend React ou uma página de boas-vindas)
Route::get('/', function () {
    return view('welcome'); // Ou sua view principal
});

// ROTAS DE AUTENTICAÇÃO
// Exibe o formulário de login
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
// Processa o login
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
// Processa o logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// DASHBOARD ADMINISTRATIVO PROTEGIDO
// Aplica o Middleware CheckAuth para garantir que apenas usuários autenticados acessem
Route::middleware(CheckAuth::class)->prefix('admin')->group(function () {
    // Rota: /admin/dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Aqui você adicionaria outras rotas administrativas protegidas (ex: gestão de pacientes, etc.)
});
