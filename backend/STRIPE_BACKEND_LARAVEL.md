# Integração Stripe - Backend Laravel

## ✅ Implementação Concluída

### 📦 Dependências Instaladas
- **stripe/stripe-php** v18.0.0 - SDK oficial do Stripe para PHP

### ⚙️ Configuração
- **Variáveis de ambiente** adicionadas ao `.env`
- **Configuração de serviços** em `config/services.php`
- **Migration** executada para adicionar campos do Stripe

### 🎯 Componentes Implementados

#### 1. **StripeController** (`app/Http/Controllers/StripeController.php`)
**Funcionalidades:**
- ✅ `createPaymentIntent()` - Cria Payment Intent no Stripe
- ✅ `confirmPayment()` - Confirma pagamento e cria agendamento
- ✅ `handleWebhook()` - Processa webhooks do Stripe
- ✅ Validação robusta de dados de entrada
- ✅ Tratamento de erros e logging
- ✅ Transações de banco de dados seguras

#### 2. **StripeService** (`app/Services/StripeService.php`)
**Funcionalidades:**
- ✅ Criação e gerenciamento de Payment Intents
- ✅ Verificação de assinatura de webhooks
- ✅ Formatação de valores (centavos ↔ reais)
- ✅ Suporte específico para PIX e cartão
- ✅ Tratamento de exceções

#### 3. **Rotas API** (`routes/api.php`)
**Endpoints implementados:**
- `POST /api/stripe/create-payment-intent`
- `POST /api/stripe/confirm-payment`
- `POST /api/stripe/webhook`

#### 4. **Model Payment** Atualizado
**Novos campos:**
- `currency` - Moeda da transação
- `transaction_date` - Data da transação
- `stripe_payment_intent_id` - ID do Payment Intent
- `stripe_payment_method_id` - ID do método de pagamento
- `stripe_charge_id` - ID da cobrança
- `metadata` - Dados adicionais em JSON

### 🔧 Configuração Necessária

#### 1. **Chaves do Stripe**
Edite o arquivo `.env` e substitua os valores:

```env
STRIPE_PUBLISHABLE_KEY=pk_test_SUA_CHAVE_PUBLISHABLE
STRIPE_SECRET_KEY=sk_test_SUA_CHAVE_SECRETA
STRIPE_WEBHOOK_SECRET=whsec_SUA_CHAVE_WEBHOOK
```

#### 2. **Como obter as chaves:**
1. Acesse [dashboard.stripe.com](https://dashboard.stripe.com)
2. Vá em **Developers** → **API Keys**
3. Copie as chaves de teste ou produção
4. Para webhook: **Developers** → **Webhooks** → **Add endpoint**

### 🚀 Endpoints da API

#### **POST** `/api/stripe/create-payment-intent`
Cria um Payment Intent no Stripe para iniciar o pagamento.

**Request:**
```json
{
  "amount": 150.00,
  "currency": "brl",
  "appointment_data": {
    "patient_name": "João Silva",
    "patient_email": "joao@email.com",
    "scheduled_at": "2025-10-20 14:30:00",
    "doctor_id": 1,
    "patient_id": 1,
    "notes": "Consulta de rotina"
  }
}
```

**Response:**
```json
{
  "client_secret": "pi_xxx_secret_xxx",
  "payment_intent_id": "pi_xxx",
  "amount": 15000,
  "currency": "brl"
}
```

#### **POST** `/api/stripe/confirm-payment`
Confirma o pagamento e cria o agendamento no banco.

**Request:**
```json
{
  "payment_intent_id": "pi_xxx",
  "appointment_data": {
    "patient_id": 1,
    "doctor_id": 1,
    "scheduled_at": "2025-10-20 14:30:00",
    "notes": "Consulta de rotina"
  }
}
```

**Response:**
```json
{
  "success": true,
  "appointment": {
    "id": 123,
    "patient_id": 1,
    "doctor_id": 1,
    "scheduled_at": "2025-10-20T14:30:00.000000Z",
    "status": "confirmed"
  },
  "payment": {
    "id": 456,
    "amount": 150.00,
    "status": "completed",
    "stripe_payment_intent_id": "pi_xxx"
  },
  "receipt_url": "https://pay.stripe.com/receipts/xxx"
}
```

### 🔔 Webhooks

#### **POST** `/api/stripe/webhook`
Endpoint para receber eventos do Stripe.

**Eventos tratados:**
- `payment_intent.succeeded` - Pagamento bem-sucedido
- `payment_intent.payment_failed` - Falha no pagamento
- `payment_intent.canceled` - Pagamento cancelado

**Configuração no Stripe:**
1. URL do webhook: `https://seu-dominio.com/api/stripe/webhook`
2. Eventos: `payment_intent.*`
3. Copie a chave de assinatura para `STRIPE_WEBHOOK_SECRET`

### 🧪 Testes

#### **Cartões de Teste:**
```
Cartão de sucesso: 4242 4242 4242 4242
Cartão recusado: 4000 0000 0000 0002
Cartão requer 3D Secure: 4000 0025 0000 3155
```

#### **Teste da API:**
```bash
# Testar criação de Payment Intent
curl -X POST http://localhost:8000/api/stripe/create-payment-intent \
  -H "Content-Type: application/json" \
  -d '{
    "amount": 150.00,
    "currency": "brl",
    "appointment_data": {
      "patient_name": "Teste",
      "patient_email": "teste@email.com",
      "scheduled_at": "2025-10-20 14:30:00",
      "doctor_id": 1,
      "patient_id": 1
    }
  }'
```

### 🔐 Segurança

#### **Implementado:**
- ✅ Validação de entrada com Laravel Validator
- ✅ Verificação de assinatura de webhooks
- ✅ Transações de banco de dados
- ✅ Logging de erros e eventos
- ✅ Chaves secretas no backend apenas

#### **Recomendações:**
- 🔸 Implementar rate limiting nas rotas
- 🔸 Adicionar autenticação para rotas sensíveis
- 🔸 Configurar CORS apropriadamente
- 🔸 Usar HTTPS em produção
- 🔸 Monitorar logs de erro

### 📊 Logs e Monitoramento

Os seguintes eventos são logados:
- Criação de Payment Intents
- Confirmação de pagamentos
- Recebimento de webhooks
- Erros de integração

**Localização dos logs:** `storage/logs/laravel.log`

### 🐛 Troubleshooting

#### **Problemas Comuns:**

**1. "Stripe não configurado"**
- Verificar se as chaves estão no `.env`
- Rodar `php artisan config:cache`

**2. "Payment Intent não encontrado"**
- Verificar se o ID está correto
- Confirmar se está usando ambiente correto (test/live)

**3. "Webhook signature invalid"**
- Verificar `STRIPE_WEBHOOK_SECRET`
- Confirmar URL do webhook no dashboard

**4. "Database error"**
- Verificar se as migrations foram executadas
- Confirmar conexão com banco de dados

### 🚀 Próximos Passos

1. **Configurar webhooks no Stripe Dashboard**
2. **Testar integração completa frontend ↔ backend**
3. **Implementar notificações por email**
4. **Configurar ambiente de produção**
5. **Implementar monitoramento e alertas**

### 📚 Recursos

- [Documentação Stripe PHP](https://stripe.com/docs/api/php)
- [Laravel Service Container](https://laravel.com/docs/container)
- [Stripe Webhooks](https://stripe.com/docs/webhooks)
- [Stripe Testing](https://stripe.com/docs/testing)

---
**Status:** ✅ Backend implementado e pronto para uso
**Próximo:** Configurar chaves e testar integração