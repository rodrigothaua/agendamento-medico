<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\StripeController;

// Rotas públicas (não requerem autenticação)
// --------------------------------------------------------------------------------

// 1. Home: Disponibilidade
Route::get('available-slots', [AppointmentController::class, 'getAvailableSlots']);

// 1.1. Disponibilidade para calendário (múltiplas datas)
Route::get('availability-calendar', [AppointmentController::class, 'getAvailabilityCalendar']);

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

// Rotas do Stripe
// --------------------------------------------------------------------------------
Route::prefix('stripe')->group(function () {
    // Criar Payment Intent
    Route::post('create-payment-intent', [StripeController::class, 'createPaymentIntent']);
    
    // Confirmar pagamento
    Route::post('confirm-payment', [StripeController::class, 'confirmPayment']);
    
    // Webhook do Stripe (não deve ter CSRF)
    Route::post('webhook', [StripeController::class, 'handleWebhook'])->middleware('api');
});


// Rotas de Gestão de Pacientes (Admin)
// --------------------------------------------------------------------------------
Route::apiResource('patients', PatientController::class);

// Rotas de Configurações da API
// --------------------------------------------------------------------------------
use App\Http\Controllers\Api\SettingApiController;

Route::prefix('settings')->group(function () {
    // Configuração pública (sem autenticação - para frontend público)
    Route::get('/public', [SettingApiController::class, 'getPublicConfig']);
    Route::post('/check-blocked', [SettingApiController::class, 'checkBlocked']);
    
    // Rotas protegidas (requerem autenticação)
    Route::middleware('api.auth')->group(function () {
        // Configurações gerais
        Route::get('/', [SettingApiController::class, 'index']); // ?group=general&key=clinic_name
        Route::get('/grouped', [SettingApiController::class, 'getGrouped']);
        Route::post('/', [SettingApiController::class, 'store']);
        Route::post('/bulk', [SettingApiController::class, 'bulkUpdate']);
        Route::delete('/{key}', [SettingApiController::class, 'destroy']);
        
        // Bloqueios de agenda
        Route::get('/schedule-blocks', [SettingApiController::class, 'getScheduleBlocks']);
        Route::post('/schedule-blocks', [SettingApiController::class, 'createScheduleBlock']);
        Route::put('/schedule-blocks/{id}', [SettingApiController::class, 'updateScheduleBlock']);
        Route::delete('/schedule-blocks/{id}', [SettingApiController::class, 'deleteScheduleBlock']);
    });
});
