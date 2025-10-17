# 🚀 Configuração Rápida com Stripe CLI (Recomendado para Desenvolvimento)

## 📥 Método Mais Fácil: Usar Stripe CLI

### **1. Instalar Stripe CLI**

**Windows:**
```bash
# Usando Chocolatey
choco install stripe-cli

# Ou baixar direto do site
# https://github.com/stripe/stripe-cli/releases
```

**Alternativa: Download direto**
1. Acesse [github.com/stripe/stripe-cli/releases](https://github.com/stripe/stripe-cli/releases)
2. Baixe a versão para Windows
3. Extraia e adicione ao PATH

### **2. Fazer Login no Stripe**
```bash
stripe login
```
- Isso abrirá o navegador
- Faça login na sua conta Stripe
- Autorize o CLI

### **3. Escutar Webhooks Localmente**
```bash
# Inicie seu servidor Laravel primeiro
php artisan serve --host=0.0.0.0 --port=8000

# Em outro terminal, execute:
stripe listen --forward-to=localhost:8000/api/stripe/webhook
```

### **4. Copiar o Webhook Secret**
O Stripe CLI mostrará algo como:
```bash
> Ready! Your webhook signing secret is whsec_1234567890abcdef...
```

**Copie essa chave e cole no seu `.env`:**

### **5. Atualizar .env**