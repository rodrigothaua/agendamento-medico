# 📋 Documentação de APIs - Sistema de Agendamentos

> Sistema completo de gestão de clínica médica com APIs RESTful

## 🏗️ Arquitetura da API

- **Base URL**: `http://localhost:8000/api`
- **Formato**: JSON
- **Autenticação**: API Key ou Sessão Web
- **CORS**: Habilitado para desenvolvimento

---

## 🔐 Autenticação

### Métodos de Autenticação

#### 1. **API Key** (Recomendado para integrações)
```bash
# Header
X-API-Key: admin-key-local

# Ou Query Parameter
?api_key=admin-key-local
```

#### 2. **Sessão Web** (Para admin logado)
- Automaticamente válida quando usuário está logado no painel admin
- CSRF token incluído automaticamente

---

## 📊 Configurações da Clínica

### 🔓 Endpoints Públicos

#### `GET /api/settings/public`
**Obter configurações públicas da clínica**

**Resposta:**
```json
{
  "success": true,
  "data": {
    "clinic_name": "Clínica Saúde",
    "clinic_phone": "(11) 99999-9999",
    "clinic_email": "contato@clinica.com.br",
    "clinic_address": "Rua das Flores, 123",
    "work_start_time": "08:00",
    "work_end_time": "18:00",
    "work_days": ["monday", "tuesday", "wednesday", "thursday", "friday"],
    "appointment_duration": 30,
    "max_appointments_per_day": 20,
    "advance_booking_days": 30
  }
}
```

#### `POST /api/settings/check-blocked`
**Verificar se um horário está bloqueado**

**Body:**
```json
{
  "datetime": "2025-10-20 14:30:00"
}
```

**Resposta:**
```json
{
  "success": true,
  "data": {
    "datetime": "2025-10-20 14:30:00",
    "is_blocked": true,
    "blocks": [
      {
        "id": 1,
        "date": "2025-10-20",
        "type": "full_day",
        "reason": "Feriado Nacional",
        "start_time": null,
        "end_time": null
      }
    ]
  }
}
```

### 🔒 Endpoints Protegidos

#### `GET /api/settings`
**Listar todas as configurações**

**Query Parameters:**
- `key`: Filtrar por chave específica
- `group`: Filtrar por grupo
- `type`: Filtrar por tipo

**Resposta:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "key": "clinic_name",
      "value": "Clínica Saúde",
      "type": "string",
      "group": "general",
      "description": "Nome da clínica",
      "is_public": true,
      "created_at": "2025-10-17T10:00:00.000000Z"
    }
  ]
}
```

#### `POST /api/settings`
**Criar/atualizar uma configuração**

**Body:**
```json
{
  "key": "clinic_name",
  "value": "Nova Clínica",
  "type": "string",
  "group": "general",
  "description": "Nome da clínica",
  "is_public": true
}
```

#### `POST /api/settings/bulk`
**Atualizar múltiplas configurações**

**Body:**
```json
{
  "settings": [
    {
      "key": "clinic_name",
      "value": "Clínica Atualizada",
      "type": "string",
      "group": "general"
    },
    {
      "key": "clinic_phone",
      "value": "(11) 98765-4321",
      "type": "string",
      "group": "general"
    }
  ]
}
```

#### `GET /api/settings/grouped`
**Obter configurações agrupadas**

**Resposta:**
```json
{
  "success": true,
  "data": {
    "general": [
      {"key": "clinic_name", "value": "Clínica Saúde"},
      {"key": "clinic_phone", "value": "(11) 99999-9999"}
    ],
    "schedule": [
      {"key": "work_start_time", "value": "08:00"},
      {"key": "work_end_time", "value": "18:00"}
    ]
  }
}
```

#### `DELETE /api/settings/{key}`
**Remover uma configuração**

**Resposta:**
```json
{
  "success": true,
  "message": "Configuração removida com sucesso"
}
```

---

## 🗓️ Bloqueios de Agenda

### `GET /api/settings/schedule-blocks`
**Listar bloqueios de agenda**

**Query Parameters:**
- `date`: Data específica (Y-m-d)
- `from_date`: Data inicial do período
- `to_date`: Data final do período
- `type`: Tipo de bloqueio (full_day, partial)
- `is_active`: Filtrar apenas ativos (true/false)

**Resposta:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "date": "2025-10-20",
      "end_date": null,
      "type": "full_day",
      "block_mode": "single",
      "start_time": null,
      "end_time": null,
      "reason": "Feriado Nacional",
      "is_active": true,
      "created_at": "2025-10-17T10:00:00.000000Z"
    },
    {
      "id": 2,
      "date": "2025-12-25",
      "end_date": "2025-12-31",
      "type": "full_day",
      "block_mode": "range",
      "start_time": null,
      "end_time": null,
      "reason": "Férias de fim de ano",
      "is_active": true,
      "created_at": "2025-10-17T11:00:00.000000Z"
    }
  ]
}
```

### `POST /api/settings/schedule-blocks`
**Criar novo bloqueio de agenda**

**Body (Bloqueio de dia único):**
```json
{
  "date": "2025-10-20",
  "type": "full_day",
  "block_mode": "single",
  "reason": "Feriado Nacional",
  "is_active": true
}
```

**Body (Bloqueio de período):**
```json
{
  "date": "2025-12-25",
  "end_date": "2025-12-31",
  "type": "full_day",
  "block_mode": "range",
  "reason": "Férias de fim de ano",
  "is_active": true
}
```

**Body (Bloqueio parcial):**
```json
{
  "date": "2025-10-18",
  "type": "partial",
  "block_mode": "single",
  "start_time": "12:00:00",
  "end_time": "14:00:00",
  "reason": "Reunião administrativa",
  "is_active": true
}
```

### `PUT /api/settings/schedule-blocks/{id}`
**Atualizar bloqueio existente**

**Body:** (Mesmo formato do POST)

### `DELETE /api/settings/schedule-blocks/{id}`
**Remover bloqueio de agenda**

---

## 👥 Pacientes

### `GET /api/patients`
**Listar todos os pacientes**

**Query Parameters:**
- `search`: Buscar por nome, email ou CPF
- `per_page`: Quantidade por página (padrão: 15)
- `page`: Página atual

**Resposta:**
```json
{
  "message": "Lista de pacientes recuperada com sucesso.",
  "patients": [
    {
      "id": 1,
      "name": "João Silva",
      "email": "joao@email.com",
      "phone": "(11) 99999-9999",
      "cpf": "123.456.789-00",
      "birth_date": "1990-05-15",
      "address": "Rua das Flores, 123",
      "created_at": "2025-10-17T10:00:00.000000Z",
      "updated_at": "2025-10-17T10:00:00.000000Z"
    }
  ]
}
```

### `GET /api/patients/{id}`
**Obter dados de um paciente específico**

**Resposta:**
```json
{
  "message": "Paciente encontrado com sucesso.",
  "patient": {
    "id": 1,
    "name": "João Silva",
    "email": "joao@email.com",
    "phone": "(11) 99999-9999",
    "cpf": "123.456.789-00",
    "birth_date": "1990-05-15",
    "address": "Rua das Flores, 123",
    "appointments": [
      {
        "id": 5,
        "scheduled_at": "2025-10-20T14:00:00.000000Z",
        "status": "confirmed"
      }
    ]
  }
}
```

### `POST /api/patients`
**Criar novo paciente**

**Body:**
```json
{
  "name": "Maria Santos",
  "email": "maria@email.com",
  "phone": "(11) 88888-8888",
  "cpf": "987.654.321-00",
  "birth_date": "1985-08-20",
  "address": "Av. Principal, 456"
}
```

### `PUT /api/patients/{id}`
**Atualizar dados do paciente**

**Body:** (Mesmos campos do POST)

### `DELETE /api/patients/{id}`
**Remover paciente**

---

## 📅 Agendamentos

### `GET /api/appointments`
**Listar todos os agendamentos**

**Query Parameters:**
- `status`: Filtrar por status (pending, confirmed, canceled)
- `date`: Data específica (Y-m-d)
- `patient_id`: Filtrar por paciente
- `from_date`: Data inicial
- `to_date`: Data final

**Resposta:**
```json
[
  {
    "id": 1,
    "patient_id": 5,
    "scheduled_at": "2025-10-20T14:00:00.000000Z",
    "status": "confirmed",
    "notes": "Consulta de retorno",
    "created_at": "2025-10-17T10:00:00.000000Z",
    "updated_at": "2025-10-17T10:00:00.000000Z",
    "patient": {
      "id": 5,
      "name": "João Silva",
      "email": "joao@email.com",
      "phone": "(11) 99999-9999",
      "cpf": "123.456.789-00"
    },
    "payment": {
      "id": 1,
      "amount": "150.00",
      "status": "approved",
      "method": "credit_card",
      "paid_at": "2025-10-17T10:30:00.000000Z"
    }
  }
]
```

### `GET /api/available-slots`
**Obter horários disponíveis para agendamento**

**Query Parameters:**
- `date`: Data desejada (Y-m-d) **obrigatório**

**Resposta:**
```json
{
  "available_slots": [
    "2025-10-20 09:00:00",
    "2025-10-20 09:30:00",
    "2025-10-20 10:00:00",
    "2025-10-20 10:30:00",
    "2025-10-20 14:00:00",
    "2025-10-20 14:30:00",
    "2025-10-20 15:00:00"
  ]
}
```

### `POST /api/appointments/initiate`
**Iniciar processo de agendamento**

**Body:**
```json
{
  "patient_name": "Maria Santos",
  "patient_email": "maria@email.com",
  "patient_phone": "(11) 88888-8888",
  "patient_cpf": "987.654.321-00",
  "scheduled_at": "2025-10-20 14:00:00",
  "notes": "Primeira consulta"
}
```

**Resposta:**
```json
{
  "success": true,
  "message": "Agendamento iniciado com sucesso",
  "data": {
    "appointment_id": 15,
    "patient_id": 8,
    "scheduled_at": "2025-10-20T14:00:00.000000Z",
    "status": "pending",
    "payment_required": true,
    "payment_url": "https://checkout.exemplo.com/payment/abc123"
  }
}
```

### `GET /api/appointments/{id}/confirmation`
**Obter detalhes de confirmação do agendamento**

**Resposta:**
```json
{
  "success": true,
  "data": {
    "appointment": {
      "id": 15,
      "scheduled_at": "2025-10-20T14:00:00.000000Z",
      "status": "confirmed",
      "confirmation_code": "AGD-2025-001"
    },
    "patient": {
      "name": "Maria Santos",
      "email": "maria@email.com"
    },
    "clinic": {
      "name": "Clínica Saúde",
      "address": "Rua das Flores, 123",
      "phone": "(11) 99999-9999"
    }
  }
}
```

### `POST /api/appointments/{id}/cancel`
**Cancelar agendamento**

**Body:**
```json
{
  "reason": "Motivo do cancelamento"
}
```

---

## 💳 Pagamentos

### `GET /api/payments/{id}`
**Obter detalhes do pagamento**

**Resposta:**
```json
{
  "success": true,
  "data": {
    "id": 5,
    "appointment_id": 15,
    "amount": "150.00",
    "method": "credit_card",
    "status": "approved",
    "transaction_id": "txn_abc123456",
    "paid_at": "2025-10-17T14:30:00.000000Z",
    "created_at": "2025-10-17T14:00:00.000000Z"
  }
}
```

### `POST /api/payments/{id}/process`
**Processar pagamento**

**Body:**
```json
{
  "payment_method": "credit_card",
  "card_token": "card_token_abc123",
  "installments": 1
}
```

### `POST /api/payments/{id}/reprocess`
**Reprocessar pagamento falhado**

**Body:**
```json
{
  "payment_method": "pix"
}
```

### `POST /api/payments/webhook`
**Webhook para notificações de pagamento**

**Body:**
```json
{
  "event": "payment.approved",
  "data": {
    "payment_id": "pay_abc123",
    "status": "approved",
    "amount": "150.00"
  }
}
```

---

## 📱 Utilizando a API JavaScript

### Exemplo de Uso com settings-api.js

```javascript
// Instância global já disponível
const api = window.settingsAPI;

// Obter informações da clínica
const clinicInfo = await api.getClinicInfo();
console.log(clinicInfo.name); // "Clínica Saúde"

// Verificar se está bloqueado
const isBlocked = await api.isDateBlocked('2025-10-20');

// Salvar configuração
await api.saveSetting('clinic_name', 'Nova Clínica');

// Criar bloqueio de data
await api.createScheduleBlock({
  date: '2025-12-25',
  type: 'full_day',
  reason: 'Natal'
});

// Obter horário de funcionamento
const workingHours = await api.getWorkingHours();
```

---

## ⚠️ Códigos de Erro

### HTTP Status Codes

- **200**: Sucesso
- **201**: Criado com sucesso
- **400**: Dados inválidos
- **401**: Não autorizado
- **403**: Acesso negado
- **404**: Recurso não encontrado
- **422**: Erro de validação
- **500**: Erro interno do servidor

### Formato de Erro Padrão

```json
{
  "success": false,
  "message": "Mensagem de erro",
  "errors": {
    "campo": ["Erro específico do campo"]
  }
}
```

---

## 🔄 Versionamento

- **Versão Atual**: v1.0
- **Compatibilidade**: Mantida para versões anteriores
- **Mudanças**: Documentadas em CHANGELOG.md

---

## 📞 Suporte

- **Documentação Completa**: [README.md](./README.md)
- **Issues**: GitHub Issues
- **Contato**: Equipe de desenvolvimento

---

*Última atualização: 17/10/2025*