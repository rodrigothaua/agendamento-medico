# Stripe API - Endpoints e Guia de Uso para Frontend

## Endpoints Disponíveis

### 1. Criar Payment Intent
- **Endpoint:** `POST /api/stripe/create-payment-intent`
- **Descrição:** Cria um PaymentIntent no Stripe para iniciar o pagamento.
- **Body (JSON):**
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
- **Response:**
```json
{
  "client_secret": "pi_xxx_secret_xxx",
  "payment_intent_id": "pi_xxx",
  "amount": 15000,
  "currency": "brl"
}
```

### 2. Confirmar Pagamento e Criar Agendamento
- **Endpoint:** `POST /api/stripe/confirm-payment`
- **Descrição:** Confirma o pagamento (após sucesso no Stripe) e cria o agendamento/pagamento no sistema.
- **Body (JSON):**
```json
{
  "payment_intent_id": "pi_xxx",
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
- **Response:**
```json
{
  "success": true,
  "appointment": { /* dados do agendamento */ },
  "payment": { /* dados do pagamento */ },
  "receipt_url": "https://pay.stripe.com/receipts/xxx"
}
```

### 3. Webhook do Stripe
- **Endpoint:** `POST /api/stripe/webhook`
- **Descrição:** Recebe eventos do Stripe (usado para confirmação automática, não chamado pelo frontend).

---

## Fluxo de Integração no Frontend

1. **Criar Payment Intent:**
   - Envie os dados do agendamento e valor para `/api/stripe/create-payment-intent`.
   - Receba o `client_secret` e `payment_intent_id`.

2. **Processar pagamento com Stripe.js:**
   - Use o `client_secret` com Stripe.js/Stripe Elements para coletar e processar o cartão do usuário.
   - Exemplo (React):
```js
const {error, paymentIntent} = await stripe.confirmCardPayment(clientSecret, {
  payment_method: { card: elements.getElement(CardElement) }
});
```

3. **Confirmar pagamento no backend:**
   - Após sucesso no Stripe, envie `payment_intent_id` e os dados do agendamento para `/api/stripe/confirm-payment`.
   - O backend irá criar o agendamento e registrar o pagamento.

4. **Receber resposta:**
   - Se sucesso, use o `receipt_url` para exibir comprovante ao usuário.

---

## Exemplo de Fluxo (React/JS)
```js
// 1. Criar Payment Intent
const res = await fetch('/api/stripe/create-payment-intent', {
  method: 'POST',
  headers: {'Content-Type': 'application/json'},
  body: JSON.stringify({ amount, currency: 'brl', appointment_data })
});
const { client_secret, payment_intent_id } = await res.json();

// 2. Confirmar pagamento com Stripe.js
const { error, paymentIntent } = await stripe.confirmCardPayment(client_secret, { payment_method: { card: elements.getElement(CardElement) } });
if (error) { /* tratar erro */ }

// 3. Confirmar no backend
const confirmRes = await fetch('/api/stripe/confirm-payment', {
  method: 'POST',
  headers: {'Content-Type': 'application/json'},
  body: JSON.stringify({ payment_intent_id, appointment_data })
});
const confirmData = await confirmRes.json();
if (confirmData.success) {
  // Pagamento e agendamento criados
  window.open(confirmData.receipt_url, '_blank');
}
```

---

## Exemplo de Fluxo PIX/QR Code (Stripe.js)

1. **Criar Payment Intent para PIX:**
```js
const res = await fetch('/api/stripe/create-payment-intent', {
  method: 'POST',
  headers: {'Content-Type': 'application/json'},
  body: JSON.stringify({
    amount, 
    currency: 'brl',
    appointment_data,
    payment_method_types: ['pix']
  })
});
const { client_secret } = await res.json();
```

2. **Exibir QR Code com Stripe.js:**
```js
const stripe = Stripe('SUA_CHAVE_PUBLISHABLE');
const {error, paymentIntent} = await stripe.confirmPixPayment(client_secret, {
  payment_method: {
    billing_details: {
      name: appointment_data.patient_name,
      email: appointment_data.patient_email
    }
  }
});

if (error) {
  // Tratar erro
  alert(error.message);
} else {
  // Exibir QR Code para o usuário
  const qrCodeUrl = paymentIntent.next_action.pix_display_qr_code.image_url_png;
  document.getElementById('pix-qr').src = qrCodeUrl;
  // Opcional: exibir código copia e cola
  const copiaCola = paymentIntent.next_action.pix_display_qr_code.emv;
  document.getElementById('pix-copia-cola').value = copiaCola;
}
```

3. **Monitorar status do pagamento:**
- Consulte periodicamente o status do PaymentIntent para saber se foi pago.
- Quando status for `succeeded`, confirme no backend (`/api/stripe/confirm-payment`).

---

> Consulte a [documentação oficial Stripe PIX](https://stripe.com/docs/payments/pix/accept-a-payment) para detalhes e exemplos atualizados.

## Observações
- Use as chaves do Stripe corretas para produção.
- O webhook `/api/stripe/webhook` deve ser configurado no painel Stripe para confirmação automática.
- Consulte os arquivos de documentação do projeto para exemplos completos.
