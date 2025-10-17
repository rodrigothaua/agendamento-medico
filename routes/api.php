<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\PatientController;

// Rotas públicas (não requerem autenticação)
// --------------------------------------------------------------------------------

// 1. Home: Disponibilidade
Route::get('available-slots', [AppointmentController::class, 'getAvailableSlots']);

// 2. Inicia o Agendamento (Reserva e Geração de Pagamento)
Route::post('appointments/initiate', [AppointmentController::class, 'initiateAppointment']);

// Rotas de Gestão de Agendamentos
// --------------------------------------------------------------------------------

// 3. Listagem de Agendamentos (ADMIN) - ROTA ADICIONADA
Route::get('appointments', [AppointmentController::class, 'listAppointments']);

// 4. Confirmação (Pós-Pagamento)
Route::get('appointments/{id}/confirmation', [AppointmentController::class, 'getConfirmationDetails']);

// 5. Cancelamento Manual (Pelo Paciente/Clínica)
Route::post('appointments/{id}/cancel', [AppointmentController::class, 'cancelAppointment']);

// Rotas de Pagamento
// --------------------------------------------------------------------------------
Route::prefix('payments')->group(function () {
    // 6. Detalhes do Pagamento
    Route::get('{id}', [PaymentController::class, 'getPaymentDetails']);

    // 7. Processa o Pagamento (gera Pix QR Code ou token/link para Cartão)
    Route::post('{id}/process', [PaymentController::class, 'processPayment']);

    // 8. Reprocessa Pagamento (em caso de falha) - ROTA CORRIGIDA (Removido /payments duplicado)
    Route::post('{id}/reprocess', [PaymentController::class, 'reprocessPayment']);

    // 9. Webhook (Para a plataforma de pagamento nos notificar)
    Route::post('webhook', [PaymentController::class, 'handleWebhook']);
});


// Rotas de Gestão de Pacientes (Admin)
// --------------------------------------------------------------------------------
Route::apiResource('patients', PatientController::class);
