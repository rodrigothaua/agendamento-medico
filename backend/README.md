# 🏥 Sistema de Agendamentos - Clínica Médica

<p align="center">
<img src="https://img.shields.io/badge/Laravel-10.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
<img src="https://img.shields.io/badge/PHP-8.1+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
<img src="https://img.shields.io/badge/MySQL-8.0+-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
<img src="https://img.shields.io/badge/Stripe-635BFF?style=for-the-badge&logo=stripe&logoColor=white" alt="Stripe">
<img src="https://img.shields.io/badge/TailwindCSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="TailwindCSS">
</p>

Sistema completo de gestão de clínica médica com agendamentos online, controle de pacientes, **pagamentos integrados com Stripe** e APIs RESTful avançadas.

## ✨ Funcionalidades Principais

### 🎯 **Painel Administrativo**
- **Dashboard Completo** - Estatísticas e visão geral dos agendamentos
- **Gestão de Pacientes** - CRUD completo com busca e filtros
- **Controle de Agendamentos** - Lista e calendário com modal de detalhes
- **Sistema de Pagamentos** - **Integração Stripe completa** com cartão de crédito
- **Relatórios Avançados** - Gráficos e exportação de dados
- **Configurações Flexíveis** - Personalização completa da clínica
- **Bloqueios de Agenda** - Controle avançado de datas e períodos indisponíveis
- **Perfil do Usuário** - Gerenciamento de conta e alteração de senha

### 🌐 **APIs RESTful Avançadas**
- **Configurações Públicas** - Informações da clínica para integração
- **Agendamentos** - CRUD completo com validação inteligente de horários
- **Pacientes** - Gestão completa via API
- **Horários Disponíveis** - Cálculo automático considerando bloqueios
- **Calendário de Disponibilidade** - **NOVO!** API para visualização mensal
- **Pagamentos Stripe** - **NOVO!** Payment Intents e confirmação
- **Bloqueios Inteligentes** - Verificação e gestão de indisponibilidades
- **Webhooks Stripe** - **NOVO!** Processamento automático de eventos

### 💳 **Sistema de Pagamentos (Stripe)**
- **Payment Intents** - Fluxo seguro de pagamento
- **Cartão de Crédito** - Processamento PCI compliant
- **Webhooks** - Confirmação automática de pagamentos
- **Recibos** - Links automáticos para comprovantes
- **Multi-moeda** - Suporte a BRL e outras moedas
- **Testes** - Ambiente sandbox completo

### 🎨 **Interface Moderna**
- **Design Responsivo** - Funciona perfeitamente em mobile, tablet e desktop
- **Componentes Reutilizáveis** - Sidebar e elementos padronizados
- **Visualização Calendário** - Toggle entre lista e calendário com status visuais
- **Modals Interativos** - Detalhes completos sem sair da página
- **Animações Suaves** - Transições e feedbacks visuais
- **Tema Consistente** - Paleta de cores profissional

## 🚀 Instalação Rápida

### Pré-requisitos
- **PHP 8.1+**
- **Composer**
- **Node.js 16+**
- **MySQL 8.0+**

### Passos de Instalação

```bash
# 1. Clonar o repositório
git clone <repository-url>
cd agendamentos

# 2. Instalar dependências PHP
composer install

# 3. Instalar dependências Node.js (se aplicável)
npm install

# 4. Configurar ambiente
cp .env.example .env
php artisan key:generate

# 5. Configurar banco de dados no .env
DB_DATABASE=agendamentos
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha

# 6. Configurar Stripe no .env
STRIPE_PUBLISHABLE_KEY=pk_test_sua_chave_publishable
STRIPE_SECRET_KEY=sk_test_sua_chave_secreta
STRIPE_WEBHOOK_SECRET=whsec_sua_chave_webhook

# 7. Executar migrações
php artisan migrate

# 8. Popular dados de teste (opcional)
php artisan db:seed

# 9. Iniciar servidor
php artisan serve
```

### Acesso Inicial
- **URL**: `http://localhost:8000`
- **Admin**: `http://localhost:8000/login`
- **API Base**: `http://localhost:8000/api`
- **Stripe Dashboard**: [dashboard.stripe.com](https://dashboard.stripe.com)

---

## � Endpoints da API

### 🌍 **APIs Públicas (sem autenticação)**

#### **Configurações e Disponibilidade**
```http
GET /api/settings/public                    # Configurações da clínica
GET /api/available-slots?date=2025-10-20    # Horários disponíveis
GET /api/availability-calendar?start_date=2025-10-20&end_date=2025-10-26  # Calendário
POST /api/settings/check-blocked            # Verificar se data está bloqueada
```

#### **Agendamentos**
```http
POST /api/appointments/initiate             # Iniciar agendamento
GET /api/appointments/{id}/confirmation     # Detalhes de confirmação
POST /api/appointments/{id}/cancel          # Cancelar agendamento
```

#### **Pagamentos Stripe**
```http
POST /api/stripe/create-payment-intent      # Criar Payment Intent
POST /api/stripe/confirm-payment            # Confirmar pagamento
POST /api/stripe/webhook                    # Webhook do Stripe
```

### 🔐 **APIs Protegidas (requer X-API-Key)**

#### **Gestão de Pacientes**
```http
GET /api/patients                           # Listar pacientes
POST /api/patients                          # Criar paciente
GET /api/patients/{id}                      # Detalhes do paciente
PUT /api/patients/{id}                      # Atualizar paciente
DELETE /api/patients/{id}                   # Remover paciente
```

#### **Gestão de Agendamentos**
```http
GET /api/appointments                       # Listar agendamentos
GET /api/appointments/{id}                  # Detalhes do agendamento
```

#### **Configurações Avançadas**
```http
GET /api/settings                           # Listar configurações
POST /api/settings                          # Criar/atualizar configuração
POST /api/settings/bulk                     # Atualizar múltiplas
DELETE /api/settings/{key}                  # Remover configuração
GET /api/settings/grouped                   # Configurações agrupadas
```

#### **Bloqueios de Agenda**
```http
GET /api/settings/schedule-blocks           # Listar bloqueios
POST /api/settings/schedule-blocks          # Criar bloqueio
PUT /api/settings/schedule-blocks/{id}      # Atualizar bloqueio
DELETE /api/settings/schedule-blocks/{id}   # Remover bloqueio
```

### 📱 **Exemplo de Resposta - Horários Disponíveis**
```json
{
  "available_slots": [
    "2025-10-20 09:00:00",
    "2025-10-20 09:30:00",
    "2025-10-20 10:00:00"
  ],
  "date": "2025-10-20",
  "work_hours": {
    "start": "09:00",
    "end": "17:00", 
    "duration": 30
  },
  "active_blocks": [
    {
      "id": 1,
      "type": "time_range",
      "start_time": "12:00",
      "end_time": "13:00",
      "reason": "Intervalo para almoço"
    }
  ],
  "total_slots": 16,
  "available_count": 14,
  "booked_count": 0
}
```

### 📅 **Exemplo de Resposta - Calendário**
```json
{
  "availability": [
    {
      "date": "2025-10-20",
      "is_work_day": true,
      "is_fully_blocked": false,
      "available_slots": 14,
      "total_slots": 16,
      "status": "available"
    },
    {
      "date": "2025-10-21", 
      "is_work_day": false,
      "is_fully_blocked": false,
      "available_slots": 0,
      "total_slots": 0,
      "status": "closed"
    }
  ]
}
```

### 💳 **Exemplo - Pagamento Stripe**
```json
// POST /api/stripe/create-payment-intent
{
  "amount": 150.00,
  "currency": "brl",
  "appointment_data": {
    "patient_name": "João Silva",
    "patient_email": "joao@email.com",
    "scheduled_at": "2025-10-20 14:30:00",
    "doctor_id": 1,
    "patient_id": 1
  }
}

// Resposta
{
  "client_secret": "pi_xxx_secret_xxx",
  "payment_intent_id": "pi_xxx",
  "amount": 15000,
  "currency": "brl"
}
```

---

## �📖 Documentação

### 📋 **Documentação da API**
- **[API_ENDPOINTS.md](./API_ENDPOINTS.md)** - Documentação completa dos endpoints
- **[API_EXAMPLES.md](./API_EXAMPLES.md)** - Exemplos práticos de integração
- **[STRIPE_BACKEND_LARAVEL.md](./STRIPE_BACKEND_LARAVEL.md)** - **NOVO!** Integração Stripe
- **[TESTE_BLOQUEIOS_DATAS.md](./TESTE_BLOQUEIOS_DATAS.md)** - **NOVO!** Sistema de bloqueios

### 🔧 **Configuração e Uso**
- **Painel Admin**: Acesse `/admin/dashboard` após fazer login
- **API Key**: Configure no `.env` como `API_KEY=sua-chave-secreta`
- **Stripe**: Configure chaves em `.env` para pagamentos
- **Configurações**: Personalize via interface admin ou API
- **Bloqueios**: Gerencie datas indisponíveis via admin ou API

### 🗂️ **Estrutura do Projeto**

```
agendamentos/
├── app/
│   ├── Http/Controllers/
│   │   ├── Api/              # Controllers da API
│   │   │   ├── AppointmentController.php
│   │   │   ├── PatientController.php
│   │   │   ├── PaymentController.php
│   │   │   └── SettingApiController.php
│   │   ├── StripeController.php    # 🆕 Integração Stripe
│   │   ├── AdminController.php
│   │   ├── AppointmentController.php
│   │   ├── PatientController.php
│   │   ├── PaymentController.php
│   │   ├── ProfileController.php
│   │   └── SettingController.php
│   ├── Models/
│   │   ├── Appointment.php
│   │   ├── Patient.php
│   │   ├── Payment.php          # 🆕 Atualizado com campos Stripe
│   │   ├── ScheduleBlock.php    # 🆕 Sistema de bloqueios
│   │   ├── Setting.php
│   │   └── User.php
│   ├── Services/
│   │   └── StripeService.php    # 🆕 Serviço do Stripe
│   └── Http/Middleware/
│       ├── ApiAuth.php         # Autenticação da API
│       └── CheckAuth.php       # Autenticação web
├── resources/views/
│   ├── admin/                  # Views do painel admin
│   ├── components/             # Componentes Blade
│   └── auth/                   # Telas de login
├── public/js/
│   └── settings-api.js         # Cliente JavaScript da API
├── database/
│   ├── migrations/             # Estrutura do banco
│   │   ├── *_create_schedule_blocks_table.php  # 🆕 Bloqueios
│   │   └── *_add_stripe_fields_to_payments.php # 🆕 Stripe
│   └── seeders/               # Dados de teste
├── docs/                      # 🆕 Documentação técnica
│   ├── STRIPE_BACKEND_LARAVEL.md
│   ├── TESTE_BLOQUEIOS_DATAS.md
│   └── CONFIGURACAO_WEBHOOK_STRIPE.md
└── routes/
    ├── web.php                # Rotas web
    └── api.php                # Rotas da API (expandidas)
```

---

## 🔌 Exemplos de Uso da API

### 🌐 **JavaScript/Frontend (Disponibilidade)**
```javascript
// Verificar horários disponíveis para uma data
const checkAvailability = async (date) => {
  const response = await fetch(`/api/available-slots?date=${date}`);
  const data = await response.json();
  
  if (data.available_slots.length === 0) {
    console.log('Data indisponível:', data.blocked_reason || 'Sem horários');
  } else {
    console.log('Horários disponíveis:', data.available_slots);
    console.log('Bloqueios ativos:', data.active_blocks);
  }
};

// Obter calendário de disponibilidade
const getCalendar = async (startDate, endDate) => {
  const response = await fetch(
    `/api/availability-calendar?start_date=${startDate}&end_date=${endDate}`
  );
  const data = await response.json();
  
  // Aplicar classes CSS baseadas no status
  data.availability.forEach(day => {
    const element = document.querySelector(`[data-date="${day.date}"]`);
    element.className = `calendar-day ${day.status}`;
    
    // Tooltip informativo
    element.title = `${day.available_slots} slots disponíveis`;
  });
};

// Usar cliente JavaScript incluído
const clinicInfo = await settingsAPI.getClinicInfo();
const isBlocked = await settingsAPI.isDateBlocked('2025-10-20');
```

### 💳 **JavaScript/Stripe (Pagamento)**
```javascript
// Criar Payment Intent
const createPayment = async (appointmentData) => {
  const response = await fetch('/api/stripe/create-payment-intent', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      amount: 150.00,
      currency: 'brl',
      appointment_data: appointmentData
    })
  });
  
  const { client_secret, payment_intent_id } = await response.json();
  
  // Usar com Stripe.js no frontend
  const { error } = await stripe.confirmCardPayment(client_secret, {
    payment_method: { card: cardElement }
  });
  
  if (!error) {
    // Confirmar no backend
    await fetch('/api/stripe/confirm-payment', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        payment_intent_id,
        appointment_data: appointmentData
      })
    });
  }
};
```

### 🗓️ **JavaScript/Bloqueios (Admin)**
```javascript
// Criar bloqueio de data
const createBlock = async (blockData) => {
  await fetch('/api/settings/schedule-blocks', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-API-Key': 'admin-key-local'
    },
    body: JSON.stringify({
      date: '2025-12-25',
      type: 'full_day',
      reason: 'Natal - Feriado'
    })
  });
};

// Listar bloqueios
const listBlocks = async () => {
  const response = await fetch('/api/settings/schedule-blocks', {
    headers: { 'X-API-Key': 'admin-key-local' }
  });
  return response.json();
};
```

### 🐘 **PHP/Backend (Integração)**
```php
// Verificar disponibilidade
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => 'http://localhost:8000/api/available-slots?date=2025-10-20',
    CURLOPT_RETURNTRANSFER => true,
]);
$response = curl_exec($curl);
$data = json_decode($response, true);

// Listar pacientes (com autenticação)
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => 'http://localhost:8000/api/patients',
    CURLOPT_HTTPHEADER => ['X-API-Key: admin-key-local'],
    CURLOPT_RETURNTRANSFER => true,
]);
$patients = json_decode(curl_exec($curl), true);

// Criar agendamento
$data = [
    'patient_name' => 'João Silva',
    'patient_email' => 'joao@email.com',
    'scheduled_at' => '2025-10-20 14:00:00'
];

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => 'http://localhost:8000/api/appointments/initiate',
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($data),
    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
    CURLOPT_RETURNTRANSFER => true,
]);
$result = curl_exec($curl);
```

### 🖥️ **cURL/Terminal (Testes)**
```bash
# Verificar horários disponíveis
curl "http://localhost:8000/api/available-slots?date=2025-10-20"

# Obter calendário de disponibilidade
curl "http://localhost:8000/api/availability-calendar?start_date=2025-10-20&end_date=2025-10-26"

# Criar agendamento
curl -X POST "http://localhost:8000/api/appointments/initiate" \
  -H "Content-Type: application/json" \
  -d '{
    "patient_name": "João Silva",
    "patient_email": "joao@email.com", 
    "scheduled_at": "2025-10-20 14:00:00"
  }'

# Criar bloqueio (requer autenticação)
curl -X POST "http://localhost:8000/api/settings/schedule-blocks" \
  -H "Content-Type: application/json" \
  -H "X-API-Key: admin-key-local" \
  -d '{
    "date": "2025-12-25",
    "type": "full_day",
    "reason": "Natal"
  }'

# Criar Payment Intent (Stripe)
curl -X POST "http://localhost:8000/api/stripe/create-payment-intent" \
  -H "Content-Type: application/json" \
  -d '{
    "amount": 150.00,
    "currency": "brl",
    "appointment_data": {
      "patient_name": "Maria Silva",
      "patient_email": "maria@email.com",
      "scheduled_at": "2025-10-20 15:00:00",
      "doctor_id": 1,
      "patient_id": 1
    }
  }'
```

---

## 📊 Status Inteligentes de Disponibilidade

O sistema possui **lógica avançada** para determinar o status de cada data no calendário, proporcionando melhor experiência visual para o usuário:

### **🎯 Status Disponíveis**

| Status | Descrição | Condição | Cor Visual | Ícone |
|--------|-----------|----------|-----------|-------|
| `available` | **Totalmente disponível** | Todos os slots estão livres | 🟢 Verde | ✅ |
| `limited` | **Parcialmente disponível** | Alguns slots bloqueados/ocupados | 🟡 Amarelo | ⚠️ |
| `full` | **Totalmente ocupado** | Todos os slots reservados | 🔴 Vermelho | ❌ |
| `blocked` | **Bloqueado administrativamente** | Data com bloqueio total | 🔴 Vermelho | 🚫 |
| `closed` | **Clínica fechada** | Não é dia de funcionamento | ⚫ Cinza | 🔒 |

### **🧠 Lógica de Determinação**

```php
// Algoritmo implementado no backend
private function getDateAvailabilityStatus($isWorkDay, $isFullyBlocked, $availableCount, $totalSlots) {
    if (!$isWorkDay) {
        return 'closed';        // Clínica não funciona neste dia
    }
    
    if ($isFullyBlocked) {
        return 'blocked';       // Bloqueio administrativo total
    }
    
    if ($availableCount == 0) {
        return 'full';          // Todos os slots ocupados
    }
    
    if ($availableCount < $totalSlots && $availableCount > 0) {
        return 'limited';       // 🎯 NOVO! Parcialmente bloqueado
    }
    
    return 'available';         // Todos os slots disponíveis
}
```

### **✨ Exemplo Prático**

**Dia 24/10/2025 - Status: `limited`**
```json
{
  "date": "2025-10-24",
  "is_work_day": true,
  "is_fully_blocked": false,
  "available_slots": 6,        // ⚠️ Apenas 6 de 8 slots
  "total_slots": 8,
  "status": "limited"          // 🎯 Status inteligente!
}
```

**Horários específicos:**
- ✅ **09:00** - Disponível
- ✅ **10:00** - Disponível  
- ✅ **11:00** - Disponível
- ✅ **12:00** - Disponível
- ✅ **13:00** - Disponível
- ❌ **14:00** - Bloqueado (reunião médica)
- ❌ **15:00** - Bloqueado (reunião médica)
- ✅ **16:00** - Disponível

### **🎨 Benefícios UX**

1. **👁️ Visibilidade Imediata** - Usuário vê restrições antes de clicar
2. **🎯 Expectativas Claras** - Sabe que encontrará menos opções
3. **⚡ Decisão Rápida** - Pode escolher outro dia se preferir
4. **🎨 Interface Intuitiva** - Cores padronizadas universalmente

---

## 🏗️ Arquitetura Técnica

### **Backend**
- **Laravel 10.x** - Framework PHP moderno e robusto
- **Eloquent ORM** - Mapeamento objeto-relacional elegante
- **MySQL** - Banco de dados relacional confiável
- **APIs RESTful** - Endpoints padronizados e documentados
- **Middleware Custom** - Autenticação flexível e segura
- **Stripe Integration** - **NOVO!** Pagamentos seguros e PCI compliant
- **Advanced Validation** - **NOVO!** Validação robusta de horários e bloqueios

### **Frontend**
- **Blade Templates** - Sistema de templates nativo do Laravel
- **TailwindCSS** - Framework CSS utilitário e responsivo
- **JavaScript Vanilla** - Sem dependências pesadas, performance otimizada
- **Componentes Reutilizáveis** - Arquitetura modular e consistente
- **Stripe Elements** - **NOVO!** Formulários de pagamento seguros

### **Recursos Avançados**
- **Cache Inteligente** - Sistema de cache otimizado em JavaScript
- **Validação Robusta** - Server-side e client-side validation
- **Responsividade Total** - Mobile-first design approach
- **SEO Otimizado** - Estrutura semântica e performance
- **Smart Scheduling** - **NOVO!** Sistema inteligente de bloqueios
- **Webhook Processing** - **NOVO!** Processamento automático de eventos
- **Calendar Integration** - **NOVO!** APIs avançadas para calendários

---

## 🧪 Testes e Qualidade

### Executar Testes
```bash
# Testes unitários
php artisan test

# Verificação de sintaxe
php -l app/Http/Controllers/*.php

# Testar integração Stripe (modo sandbox)
php artisan tinker --execute="
\$service = new \App\Services\StripeService();
echo 'Stripe configurado: ' . (config('services.stripe.secret') ? 'SIM' : 'NÃO');
"

# Testar bloqueios de data
curl "http://localhost:8000/api/available-slots?date=$(date +%Y-%m-%d)"

# Limpeza de cache
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:clear
```

### Métricas de Qualidade
- ✅ **Todas as APIs testadas e funcionais**
- ✅ **Zero erros de sintaxe**
- ✅ **Integração Stripe validada**
- ✅ **Sistema de bloqueios testado**
- ✅ **Responsividade verificada**
- ✅ **Compatibilidade cross-browser**
- ✅ **Performance otimizada**

---

## 🚀 Deploy e Produção

### Configurações Recomendadas
```bash
# Otimizações para produção
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Configurar .env para produção
APP_ENV=production
APP_DEBUG=false
APP_URL=https://sua-clinica.com.br

# Configurar Stripe para produção
STRIPE_PUBLISHABLE_KEY=pk_live_sua_chave_live
STRIPE_SECRET_KEY=sk_live_sua_chave_live
STRIPE_WEBHOOK_SECRET=whsec_sua_chave_webhook_live
```

### Variáveis de Ambiente Importantes
```env
# Aplicação
APP_NAME="Sua Clínica"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://sua-clinica.com.br
APP_KEY=base64:sua-chave-aplicacao

# Banco de dados
DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=agendamentos
DB_USERNAME=usuario
DB_PASSWORD=senha

# API e Segurança
API_KEY=sua-chave-api-super-secreta

# Stripe (Pagamentos)
STRIPE_PUBLISHABLE_KEY=pk_live_sua_chave_publishable
STRIPE_SECRET_KEY=sk_live_sua_chave_secreta
STRIPE_WEBHOOK_SECRET=whsec_sua_chave_webhook
STRIPE_CURRENCY=brl
STRIPE_COUNTRY=BR

# Email (opcional)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=seu-email@gmail.com
MAIL_PASSWORD=sua-senha-app
MAIL_ENCRYPTION=tls
```

---

## 🤝 Contribuição

### Como Contribuir
1. **Fork** o projeto
2. **Crie** uma branch para sua feature (`git checkout -b feature/nova-funcionalidade`)
3. **Commit** suas alterações (`git commit -am 'Adiciona nova funcionalidade'`)
4. **Push** para a branch (`git push origin feature/nova-funcionalidade`)
5. **Abra** um Pull Request

### Padrões de Código
- **PSR-4** para autoload
- **PSR-12** para estilo de código
- **Comentários** em português
- **Commits** descritivos

---

## 📝 Licença

Este projeto está licenciado sob a **MIT License** - veja o arquivo [LICENSE](LICENSE) para detalhes.

---

## 🆘 Suporte

### Documentação
- **[Endpoints da API](./API_ENDPOINTS.md)** - Referência completa de todos os endpoints
- **[Exemplos de Integração](./API_EXAMPLES.md)** - Código prático e casos de uso
- **[Integração Stripe](./STRIPE_BACKEND_LARAVEL.md)** - **NOVO!** Guia completo do Stripe
- **[Sistema de Bloqueios](./TESTE_BLOQUEIOS_DATAS.md)** - **NOVO!** Como usar bloqueios
- **[Configuração Webhook](./CONFIGURACAO_WEBHOOK_STRIPE.md)** - **NOVO!** Setup Stripe
- **Issues no GitHub** - Reporte problemas e sugestões

### Contato
- **Email**: suporte@sistema-agendamentos.com.br
- **GitHub Issues**: Para bugs e sugestões de melhorias
- **Stripe Support**: [support.stripe.com](https://support.stripe.com) para questões de pagamento
- **Documentação Online**: [Em breve] Portal de documentação completa

---

**Desenvolvido com ❤️ para modernizar a gestão de clínicas médicas**

## 🏆 **Changelog Recente**

### **v2.0.1** - Outubro 2025 🔧
- 🐛 **Correção crítica:** Inconsistência entre endpoints `availability-calendar` e `available-slots`
- ✅ **Cálculo preciso:** Agendamentos em horários não-padrão não afetam mais a disponibilidade
- 📊 **Consistência garantida:** Ambos endpoints agora retornam dados idênticos
- 🎯 **Status correto:** Datas sem bloqueios reais agora mostram `"available"` corretamente

### **v2.0.0** - Outubro 2025 ✨
- 🎉 **Integração Stripe completa** - Pagamentos com cartão de crédito
- 🗓️ **Sistema de bloqueios avançado** - Controle total de disponibilidade
- 📊 **API de calendário** - Endpoint para visualização mensal
- 🔔 **Webhooks Stripe** - Processamento automático de eventos
- 🛡️ **Segurança aprimorada** - Validação robusta e PCI compliance
- 📱 **Status inteligentes** - **NOVO!** Lógica `limited` para bloqueios parciais
- ⚠️ **UX aprimorada** - Status visuais precisos (available/limited/full/blocked/closed)
- 🚀 **Performance otimizada** - Consultas e caching melhorados
- 📚 **Documentação expandida** - Guias técnicos completos

### **v1.0.0** - Base Inicial
- 🏥 Sistema base de agendamentos
- 👥 Gestão de pacientes
- 📋 APIs RESTful básicas
- 🎨 Interface administrativa
- ⚙️ Sistema de configurações

---

## 🎯 **Próximas Funcionalidades**

- 📧 **Notificações por email** - Confirmações e lembretes automáticos
- 📱 **App mobile** - React Native para pacientes
- 🔍 **Relatórios avançados** - Analytics e métricas detalhadas
- 💬 **Chat integrado** - Comunicação paciente-clínica
- 🗃️ **Prontuário eletrônico** - Histórico médico completo
- 🔐 **2FA** - Autenticação de dois fatores
- 🌍 **Multi-idioma** - Suporte a inglês e espanhol

**Sistema em constante evolução! 🚀**

---

## 🔧 Troubleshooting

### **📅 Verificação de Dias da Semana**

Se suspeitar que o sistema está marcando dias incorretamente, use este comando para verificar:

```bash
# Verificar datas reais
php -r "
echo 'Verificação de datas:' . PHP_EOL;
for (\$i = 20; \$i <= 27; \$i++) {
    \$date = '2025-10-' . \$i;
    \$dayName = date('l', strtotime(\$date));
    \$isWorkDay = in_array(strtolower(\$dayName), ['monday', 'tuesday', 'wednesday', 'thursday', 'friday']);
    echo \$date . ' = ' . \$dayName . ' (' . (\$isWorkDay ? 'trabalho' : 'fechado') . ')' . PHP_EOL;
}
"
```

### **🧪 Teste de API de Disponibilidade**

```bash
# Testar endpoint com datas específicas
curl -s "http://localhost:8000/api/availability-calendar?start_date=2025-10-20&end_date=2025-10-27" | jq '.'
```

### **⚙️ Problemas Comuns**

1. **"Sexta-feira aparece como fechada"**
   - ✅ Verificar se a data realmente é sexta-feira
   - ✅ Confirmar configuração `work_days` nas settings
   - ✅ Verificar se não há bloqueio `full_day` na data

2. **"Domingo aparece como disponível"**
   - ✅ Verificar se a data realmente é domingo
   - ✅ Confirmar que domingo não está em `work_days`

3. **"Status 'limited' não aparece"**
   - ✅ Verificar se há bloqueios `time_range` na data
   - ✅ Confirmar que ainda sobram slots disponíveis

4. **"Data aparece como 'limited' sem motivo aparente"** 🆕
   - ✅ Verificar se há agendamentos em horários não-padrão (ex: 13:58 em vez de 14:00)
   - ✅ Confirmar que `availability-calendar` e `available-slots` retornam valores iguais
   - ✅ **Corrigido na v2.0.1:** Endpoints agora usam lógica consistente

### **📊 Debug Avançado**

Para debug detalhado, adicione temporariamente no controller:

```php
Log::info("Debug disponibilidade", [
    'date' => $dateStr,
    'day_name' => $date->format('l'),
    'day_lower' => $dayOfWeekLower,
    'work_days' => $workDays,
    'is_work_day' => $isWorkDay,
    'available_slots' => $availableCount,
    'total_slots' => $totalSlots,
    'status' => $status
]);
```

---
