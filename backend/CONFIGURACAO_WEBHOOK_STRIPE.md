# 🔔 Configuração do Webhook Stripe - Guia Completo

## 📋 Passo a Passo para Configurar Webhook

### **1. Acessar o Dashboard do Stripe**
1. Acesse [dashboard.stripe.com](https://dashboard.stripe.com)
2. Faça login com sua conta
3. Certifique-se de estar no modo **"Test"** (canto superior direito)

### **2. Navegar para Webhooks**
1. No menu lateral, clique em **"Developers"**
2. Clique em **"Webhooks"**
3. Clique no botão **"Add endpoint"** ou **"+ Add endpoint"**

### **3. Configurar o Endpoint**

#### **URL do Endpoint:**
```
http://SEU_DOMINIO/api/stripe/webhook
```

**Para desenvolvimento local:**
- Se usando `php artisan serve`: `http://localhost:8000/api/stripe/webhook`
- Se usando outro servidor: `http://SEU_IP:PORTA/api/stripe/webhook`

⚠️ **Importante:** Para desenvolvimento local, você precisará usar uma ferramenta como **ngrok** para expor sua aplicação para a internet, pois o Stripe precisa acessar o webhook externamente.

#### **Eventos para Escutar:**
Selecione os seguintes eventos:
- ✅ `payment_intent.succeeded`
- ✅ `payment_intent.payment_failed` 
- ✅ `payment_intent.canceled`
- ✅ `payment_intent.requires_action`

### **4. Configurar ngrok (Para Desenvolvimento Local)**

#### **Instalar ngrok:**
1. Baixe em [ngrok.com](https://ngrok.com)
2. Extraia o arquivo
3. Adicione ao PATH ou use diretamente

#### **Usar ngrok:**
```bash
# Iniciar seu servidor Laravel
php artisan serve --host=0.0.0.0 --port=8000

# Em outro terminal, iniciar ngrok
ngrok http 8000
```

**Resultado do ngrok:**
```
Forwarding  https://abc123.ngrok.io -> http://localhost:8000
```

#### **URL do Webhook com ngrok:**
```
https://abc123.ngrok.io/api/stripe/webhook
```

### **5. Salvar e Obter a Signing Secret**

1. **Clique em "Add endpoint"** após configurar
2. **Copie a "Signing secret"** que aparece na página do webhook
3. Ela será algo como: `whsec_1234567890abcdef...`

### **6. Atualizar o .env**

Substitua no seu arquivo `.env`:
```env
STRIPE_WEBHOOK_SECRET=whsec_SUA_CHAVE_COPIADA_DO_STEP_5
```

### **7. Limpar Cache do Laravel**
```bash
php artisan config:clear
php artisan cache:clear
```

### **8. Testar o Webhook**

#### **No Dashboard do Stripe:**
1. Vá para **Developers** → **Webhooks**
2. Clique no webhook que você criou
3. Clique em **"Send test webhook"**
4. Escolha `payment_intent.succeeded`
5. Clique em **"Send test webhook"**

#### **Verificar Logs:**
```bash
# No seu projeto Laravel
tail -f storage/logs/laravel.log
```

Você deve ver algo como:
```
[2025-10-17 15:30:00] local.INFO: Stripe Webhook recebido {"type":"payment_intent.succeeded","id":"evt_test_webhook"}
```

## 🌐 Para Produção

### **1. Domínio Real**
```
https://seudominio.com/api/stripe/webhook
```

### **2. HTTPS Obrigatório**
- O Stripe **requer HTTPS** em produção
- Certifique-se de ter certificado SSL válido

### **3. Configurar Produção**
1. Troque para modo **"Live"** no Stripe
2. Use as chaves de **produção**
3. Reconfigure o webhook com URL de produção

## 🔧 Configurações Avançadas

### **1. Múltiplos Ambientes**
```env
# Desenvolvimento
STRIPE_WEBHOOK_SECRET=whsec_test_123...

# Produção  
STRIPE_WEBHOOK_SECRET=whsec_123...
```

### **2. Validação Adicional**
Edite `app/Http/Controllers/StripeController.php` para logs mais detalhados:

```php
public function handleWebhook(Request $request): JsonResponse
{
    Log::info('Webhook recebido', [
        'headers' => $request->headers->all(),
        'ip' => $request->ip(),
        'user_agent' => $request->userAgent(),
    ]);
    
    // ... resto do código
}
```

### **3. Retry Logic**
O Stripe automaticamente tenta reenviar webhooks que falham. Configure timeout adequado:

```php
// Em config/app.php
'timeout' => 30, // segundos
```

## 🐛 Troubleshooting

### **Problema: "Webhook signature verification failed"**
**Solução:**
1. Verificar se `STRIPE_WEBHOOK_SECRET` está correto
2. Rodar `php artisan config:clear`
3. Verificar se está usando a signing secret do webhook correto

### **Problema: "Endpoint not reachable"**
**Solução:**
1. Verificar se URL está acessível externamente
2. Usar ngrok para desenvolvimento local
3. Verificar firewall/proxy

### **Problema: "Timeout"**
**Solução:**
1. Aumentar timeout no servidor
2. Otimizar processamento do webhook
3. Usar jobs assíncronos para processamento pesado

## 📊 Monitoramento

### **Dashboard do Stripe:**
- Vá em **Developers** → **Webhooks**
- Clique no seu webhook
- Veja **"Recent deliveries"** para status

### **Laravel Logs:**
```bash
# Ver logs em tempo real
tail -f storage/logs/laravel.log | grep -i stripe

# Ver logs específicos de webhook
grep "Stripe Webhook" storage/logs/laravel.log
```

## ✅ Checklist Final

- [ ] Webhook criado no dashboard Stripe
- [ ] URL correta configurada (`/api/stripe/webhook`)
- [ ] Eventos selecionados (`payment_intent.*`)
- [ ] Signing secret copiada
- [ ] `.env` atualizado com `STRIPE_WEBHOOK_SECRET`
- [ ] Cache limpo (`php artisan config:clear`)
- [ ] Teste enviado do dashboard
- [ ] Logs verificados
- [ ] HTTPS configurado (produção)

---

## 🎯 **Resultado Esperado**

Após configurar corretamente, você verá nos logs:

```
[2025-10-17 15:30:00] local.INFO: Stripe Webhook recebido {"type":"payment_intent.succeeded"}
[2025-10-17 15:30:01] local.INFO: Pagamento confirmado via webhook {"payment_id":123}
```

**Status do agendamento será automaticamente atualizado quando o pagamento for processado! 🎉**