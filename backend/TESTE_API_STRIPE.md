# Testes da API Stripe - Exemplos

## 📋 Como testar a integração

### 1. **Iniciar o servidor Laravel**
```bash
cd /d/agendamentos/backend
php artisan serve --host=0.0.0.0 --port=8000
```

### 2. **Configurar chaves do Stripe**
Edite o arquivo `.env` e adicione suas chaves de teste:
```env
STRIPE_PUBLISHABLE_KEY=pk_test_SUA_CHAVE_AQUI
STRIPE_SECRET_KEY=sk_test_SUA_CHAVE_AQUI
```

### 3. **Testes via curl ou Postman**

#### **Teste 1: Criar Payment Intent**
```bash
curl -X POST http://localhost:8000/api/stripe/create-payment-intent \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "amount": 150.00,
    "currency": "brl",
    "appointment_data": {
      "patient_name": "João Silva",
      "patient_email": "joao@teste.com",
      "scheduled_at": "2025-10-20 14:30:00",
      "doctor_id": 1,
      "patient_id": 1,
      "notes": "Consulta de rotina"
    }
  }'
```

**Resposta esperada:**
```json
{
  "client_secret": "pi_xxx_secret_xxx",
  "payment_intent_id": "pi_xxx",
  "amount": 15000,
  "currency": "brl"
}
```

#### **Teste 2: Confirmar Pagamento**
```bash
curl -X POST http://localhost:8000/api/stripe/confirm-payment \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "payment_intent_id": "pi_PAYMENT_INTENT_ID_AQUI",
    "appointment_data": {
      "patient_id": 1,
      "doctor_id": 1,
      "scheduled_at": "2025-10-20 14:30:00",
      "notes": "Consulta de rotina"
    }
  }'
```

### 4. **Teste no frontend**

#### **Arquivo de teste HTML (opcional)**
Crie um arquivo `test-stripe.html` para testar:

```html
<!DOCTYPE html>
<html>
<head>
    <title>Teste Stripe</title>
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body>
    <h1>Teste da Integração Stripe</h1>
    
    <button id="test-payment">Testar Pagamento</button>
    
    <script>
        const stripe = Stripe('pk_test_SUA_CHAVE_PUBLISHABLE');
        
        document.getElementById('test-payment').addEventListener('click', async () => {
            try {
                // 1. Criar Payment Intent
                const response = await fetch('http://localhost:8000/api/stripe/create-payment-intent', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        amount: 150.00,
                        currency: 'brl',
                        appointment_data: {
                            patient_name: 'Teste',
                            patient_email: 'teste@email.com',
                            scheduled_at: '2025-10-20 14:30:00',
                            doctor_id: 1,
                            patient_id: 1
                        }
                    })
                });
                
                const { client_secret } = await response.json();
                console.log('Payment Intent criado:', client_secret);
                
                // 2. Aqui você integraria com o frontend React
                alert('Payment Intent criado com sucesso! Verifique o console.');
                
            } catch (error) {
                console.error('Erro:', error);
                alert('Erro ao criar Payment Intent');
            }
        });
    </script>
</body>
</html>
```

### 5. **Verificar logs**
```bash
tail -f storage/logs/laravel.log
```

### 6. **Comandos úteis**

```bash
# Limpar cache
php artisan config:clear
php artisan route:clear

# Ver todas as rotas
php artisan route:list | grep stripe

# Verificar configuração
php artisan tinker
>>> config('services.stripe')

# Testar conexão com banco
php artisan migrate:status
```

### 7. **Problemas comuns e soluções**

#### **Erro: "Class StripeService not found"**
```bash
composer dump-autoload
php artisan config:clear
```

#### **Erro: "Stripe key not set"**
- Verificar arquivo `.env`
- Rodar `php artisan config:clear`

#### **Erro: "CORS"**
Adicionar no `.env`:
```env
FRONTEND_URL=http://localhost:3000
```

E configurar CORS em `config/cors.php`.

#### **Erro: "Route not found"**
```bash
php artisan route:clear
php artisan config:clear
```

### 8. **Dados de teste do Stripe**

#### **Cartões que funcionam:**
- `4242424242424242` - Visa (sucesso)
- `4000056655665556` - Visa (débito)
- `5555555555554444` - Mastercard

#### **Cartões que falham:**
- `4000000000000002` - Recusado
- `4000000000009995` - Fundos insuficientes
- `4000000000009987` - Cartão perdido

#### **Datas e CVV:**
- **Data**: Qualquer mês/ano futuro (ex: 12/28)
- **CVV**: Qualquer 3 dígitos (ex: 123)

---

## ✅ Status da implementação
- **Backend Laravel**: ✅ Completo
- **Rotas API**: ✅ Funcionando
- **Database**: ✅ Migrations aplicadas
- **Stripe Service**: ✅ Implementado
- **Webhooks**: ✅ Configurado

**Próximo passo**: Conectar com o frontend React/TypeScript já implementado!