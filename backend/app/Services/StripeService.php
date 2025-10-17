<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use Illuminate\Support\Facades\Log;

class StripeService
{
    public function __construct()
    {
        // Configurar a chave secreta do Stripe
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Cria um Payment Intent no Stripe
     *
     * @param array $data
     * @return PaymentIntent
     * @throws \Exception
     */
    public function createPaymentIntent(array $data): PaymentIntent
    {
        try {
            $paymentIntentData = [
                'amount' => $data['amount'],
                'currency' => $data['currency'] ?? 'brl',
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
                'metadata' => $data['metadata'] ?? [],
            ];

            // Adicionar descrição se fornecida
            if (isset($data['description'])) {
                $paymentIntentData['description'] = $data['description'];
            }

            // Configurar métodos de pagamento específicos se fornecidos
            if (isset($data['payment_method_types'])) {
                $paymentIntentData['payment_method_types'] = $data['payment_method_types'];
                unset($paymentIntentData['automatic_payment_methods']);
            }

            $paymentIntent = PaymentIntent::create($paymentIntentData);

            Log::info('Payment Intent criado com sucesso', [
                'payment_intent_id' => $paymentIntent->id,
                'amount' => $paymentIntent->amount,
                'currency' => $paymentIntent->currency,
            ]);

            return $paymentIntent;

        } catch (\Exception $e) {
            Log::error('Erro ao criar Payment Intent no Stripe: ' . $e->getMessage(), [
                'data' => $data,
                'trace' => $e->getTraceAsString()
            ]);
            throw new \Exception('Falha ao criar Payment Intent: ' . $e->getMessage());
        }
    }

    /**
     * Recupera um Payment Intent do Stripe
     *
     * @param string $paymentIntentId
     * @return PaymentIntent
     * @throws \Exception
     */
    public function retrievePaymentIntent(string $paymentIntentId): PaymentIntent
    {
        try {
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);

            Log::info('Payment Intent recuperado', [
                'payment_intent_id' => $paymentIntent->id,
                'status' => $paymentIntent->status,
            ]);

            return $paymentIntent;

        } catch (\Exception $e) {
            Log::error('Erro ao recuperar Payment Intent do Stripe: ' . $e->getMessage(), [
                'payment_intent_id' => $paymentIntentId,
                'trace' => $e->getTraceAsString()
            ]);
            throw new \Exception('Falha ao recuperar Payment Intent: ' . $e->getMessage());
        }
    }

    /**
     * Atualiza um Payment Intent no Stripe
     *
     * @param string $paymentIntentId
     * @param array $data
     * @return PaymentIntent
     * @throws \Exception
     */
    public function updatePaymentIntent(string $paymentIntentId, array $data): PaymentIntent
    {
        try {
            $paymentIntent = PaymentIntent::update($paymentIntentId, $data);

            Log::info('Payment Intent atualizado', [
                'payment_intent_id' => $paymentIntent->id,
                'updated_fields' => array_keys($data),
            ]);

            return $paymentIntent;

        } catch (\Exception $e) {
            Log::error('Erro ao atualizar Payment Intent no Stripe: ' . $e->getMessage(), [
                'payment_intent_id' => $paymentIntentId,
                'data' => $data,
                'trace' => $e->getTraceAsString()
            ]);
            throw new \Exception('Falha ao atualizar Payment Intent: ' . $e->getMessage());
        }
    }

    /**
     * Cancela um Payment Intent no Stripe
     *
     * @param string $paymentIntentId
     * @return PaymentIntent
     * @throws \Exception
     */
    public function cancelPaymentIntent(string $paymentIntentId): PaymentIntent
    {
        try {
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);
            $paymentIntent->cancel();

            Log::info('Payment Intent cancelado', [
                'payment_intent_id' => $paymentIntent->id,
            ]);

            return $paymentIntent;

        } catch (\Exception $e) {
            Log::error('Erro ao cancelar Payment Intent no Stripe: ' . $e->getMessage(), [
                'payment_intent_id' => $paymentIntentId,
                'trace' => $e->getTraceAsString()
            ]);
            throw new \Exception('Falha ao cancelar Payment Intent: ' . $e->getMessage());
        }
    }

    /**
     * Verifica a assinatura do webhook do Stripe
     *
     * @param string $payload
     * @param string $sigHeader
     * @param string $webhookSecret
     * @return \Stripe\Event
     * @throws \Exception
     */
    public function verifyWebhookSignature(string $payload, string $sigHeader, string $webhookSecret): \Stripe\Event
    {
        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                $webhookSecret
            );

            Log::info('Webhook do Stripe verificado com sucesso', [
                'event_id' => $event->id,
                'event_type' => $event->type,
            ]);

            return $event;

        } catch (SignatureVerificationException $e) {
            Log::error('Falha na verificação da assinatura do webhook Stripe: ' . $e->getMessage());
            throw new \Exception('Assinatura do webhook inválida');
        } catch (\Exception $e) {
            Log::error('Erro ao processar webhook do Stripe: ' . $e->getMessage());
            throw new \Exception('Falha ao processar webhook: ' . $e->getMessage());
        }
    }

    /**
     * Formata valor para Stripe (converte para centavos)
     *
     * @param float $amount
     * @param string $currency
     * @return int
     */
    public function formatAmountForStripe(float $amount, string $currency = 'brl'): int
    {
        // Moedas que não usam centavos (zero-decimal currencies)
        $zeroDecimalCurrencies = ['jpy', 'krw', 'clp', 'isk', 'xof', 'xpf'];

        if (in_array(strtolower($currency), $zeroDecimalCurrencies)) {
            return (int) $amount;
        }

        // Para BRL e a maioria das moedas, multiplicar por 100
        return (int) ($amount * 100);
    }

    /**
     * Formata valor do Stripe para display (converte de centavos)
     *
     * @param int $amount
     * @param string $currency
     * @return float
     */
    public function formatAmountFromStripe(int $amount, string $currency = 'brl'): float
    {
        // Moedas que não usam centavos
        $zeroDecimalCurrencies = ['jpy', 'krw', 'clp', 'isk', 'xof', 'xpf'];

        if (in_array(strtolower($currency), $zeroDecimalCurrencies)) {
            return (float) $amount;
        }

        // Para BRL e a maioria das moedas, dividir por 100
        return $amount / 100;
    }

    /**
     * Cria Payment Intent para PIX (específico para Brasil)
     *
     * @param array $data
     * @return PaymentIntent
     * @throws \Exception
     */
    public function createPixPaymentIntent(array $data): PaymentIntent
    {
        try {
            $data['payment_method_types'] = ['pix'];
            $data['currency'] = 'brl';

            return $this->createPaymentIntent($data);

        } catch (\Exception $e) {
            Log::error('Erro ao criar Payment Intent PIX: ' . $e->getMessage());
            throw new \Exception('Falha ao criar pagamento PIX: ' . $e->getMessage());
        }
    }

    /**
     * Cria Payment Intent para cartão de crédito
     *
     * @param array $data
     * @return PaymentIntent
     * @throws \Exception
     */
    public function createCardPaymentIntent(array $data): PaymentIntent
    {
        try {
            $data['payment_method_types'] = ['card'];

            return $this->createPaymentIntent($data);

        } catch (\Exception $e) {
            Log::error('Erro ao criar Payment Intent para cartão: ' . $e->getMessage());
            throw new \Exception('Falha ao criar pagamento com cartão: ' . $e->getMessage());
        }
    }

    /**
     * Obtém informações de configuração do Stripe
     *
     * @return array
     */
    public function getStripeConfig(): array
    {
        return [
            'publishable_key' => config('services.stripe.key'),
            'currency' => config('services.stripe.currency', 'brl'),
            'country' => config('services.stripe.country', 'BR'),
        ];
    }
}