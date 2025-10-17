<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
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

    // Rotas de Agendamentos
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('admin.appointments.index');
    Route::get('/appointments/{id}', [AppointmentController::class, 'show'])->name('admin.appointments.show');
    Route::patch('/appointments/{id}/update-status', [AppointmentController::class, 'updateStatus'])->name('admin.appointments.update-status');
    Route::delete('/appointments/{id}', [AppointmentController::class, 'destroy'])->name('admin.appointments.destroy');

    // Rotas de Pacientes
    Route::get('/patients', [PatientController::class, 'index'])->name('admin.patients.index');
    Route::get('/patients/create', [PatientController::class, 'create'])->name('admin.patients.create');
    Route::post('/patients', [PatientController::class, 'store'])->name('admin.patients.store');
    Route::get('/patients/{id}', [PatientController::class, 'show'])->name('admin.patients.show');
    Route::get('/patients/{id}/edit', [PatientController::class, 'edit'])->name('admin.patients.edit');
    Route::put('/patients/{id}', [PatientController::class, 'update'])->name('admin.patients.update');
    Route::delete('/patients/{id}', [PatientController::class, 'destroy'])->name('admin.patients.destroy');

    // Rotas de Pagamentos
    Route::get('/payments', [PaymentController::class, 'index'])->name('admin.payments.index');
    Route::get('/payments/report', [PaymentController::class, 'report'])->name('admin.payments.report');
    Route::get('/payments/{id}', [PaymentController::class, 'show'])->name('admin.payments.show');
    Route::patch('/payments/{id}/update-status', [PaymentController::class, 'updateStatus'])->name('admin.payments.update-status');
    Route::delete('/payments/{id}', [PaymentController::class, 'destroy'])->name('admin.payments.destroy');

    // Rotas de Relatórios
    Route::get('/reports', [ReportController::class, 'index'])->name('admin.reports.index');
    Route::get('/reports/appointments', [ReportController::class, 'appointments'])->name('admin.reports.appointments');
    Route::get('/reports/patients', [ReportController::class, 'patients'])->name('admin.reports.patients');
    Route::get('/reports/financial', [ReportController::class, 'financial'])->name('admin.reports.financial');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('admin.reports.export');
    Route::get('/reports/chart-data', [ReportController::class, 'chartData'])->name('admin.reports.chart-data');

    // Aqui você adicionaria outras rotas administrativas protegidas
});
