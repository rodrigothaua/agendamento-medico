# API de Configurações - Documentação

## Visão Geral

A API de Configurações permite gerenciar as configurações da clínica e bloqueios de agenda de forma dinâmica, possibilitando integração com frontends React, Vue.js, ou qualquer aplicação que consuma APIs REST.

## Base URL
```
/api/settings
```

## Autenticação

- **Rotas Públicas**: Não requerem autenticação
- **Rotas Protegidas**: Requerem autenticação via sessão ou API Key

Para usar API Key, adicione no header:
```
X-API-Key: sua-api-key
```

Ou como parâmetro de query:
```
?api_key=sua-api-key
```

## Endpoints

### 1. Configurações Públicas

#### GET `/api/settings/public`
Retorna configurações básicas da clínica para uso público.

**Resposta:**
```json
{
  "success": true,
  "data": {
    "clinic_name": "Clínica Saúde",
    "clinic_phone": "(11) 99999-9999",
    "clinic_email": "contato@clinica.com",
    "clinic_address": "Rua das Flores, 123",
    "work_start_time": "08:00",
    "work_end_time": "18:00",
    "work_days": ["monday", "tuesday", "wednesday", "thursday", "friday"],
    "appointment_duration": 60,
    "advance_booking_days": 30
  }
}
```

### 2. Verificar Bloqueios

#### POST `/api/settings/check-blocked`
Verifica se uma data/hora específica está bloqueada.

**Payload:**
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
        "start_time": null,
        "end_time": null,
        "reason": "Férias",
        "is_active": true
      }
    ]
  }
}
```

### 3. Configurações Administrativas (Protegidas)

#### GET `/api/settings`
Lista todas as configurações ou filtradas por grupo/chave.

**Parâmetros de Query:**
- `group` (opcional): Filtrar por grupo (general, schedule, notification)
- `key` (opcional): Buscar configuração específica

**Exemplo:**
```
GET /api/settings?group=general
GET /api/settings?key=clinic_name
```

#### GET `/api/settings/grouped`
Retorna todas as configurações agrupadas por categoria.

#### POST `/api/settings`
Criar/atualizar uma configuração.

**Payload:**
```json
{
  "key": "clinic_name",
  "value": "Nova Clínica",
  "type": "string",
  "group": "general",
  "description": "Nome da clínica"
}
```

#### POST `/api/settings/bulk`
Atualizar múltiplas configurações de uma vez.

**Payload:**
```json
{
  "settings": [
    {
      "key": "clinic_name",
      "value": "Nova Clínica",
      "type": "string",
      "group": "general"
    },
    {
      "key": "work_start_time",
      "value": "09:00",
      "type": "string",
      "group": "schedule"
    }
  ]
}
```

#### DELETE `/api/settings/{key}`
Remover uma configuração específica.

### 4. Bloqueios de Agenda (Protegidas)

#### GET `/api/settings/schedule-blocks`
Listar bloqueios ativos.

**Parâmetros de Query:**
- `date` (opcional): Filtrar por data específica (YYYY-MM-DD)
- `from_date` (opcional): Data inicial do período
- `to_date` (opcional): Data final do período

#### POST `/api/settings/schedule-blocks`
Criar novo bloqueio.

**Payload:**
```json
{
  "date": "2025-12-25",
  "type": "full_day",
  "reason": "Natal"
}
```

**Para bloqueio por horário:**
```json
{
  "date": "2025-10-25",
  "type": "time_range",
  "start_time": "14:00",
  "end_time": "16:00",
  "reason": "Reunião importante"
}
```

#### PUT `/api/settings/schedule-blocks/{id}`
Atualizar bloqueio existente.

#### DELETE `/api/settings/schedule-blocks/{id}`
Remover bloqueio.

## Tipos de Dados

### Configurações

- **string**: Texto simples
- **number**: Números (inteiros ou decimais)
- **boolean**: Verdadeiro/falso
- **json**: Objetos ou arrays JSON

### Bloqueios

- **full_day**: Bloqueia o dia inteiro
- **time_range**: Bloqueia apenas um período específico

## Códigos de Status

- **200**: Sucesso
- **201**: Criado com sucesso
- **400**: Dados inválidos
- **401**: Não autorizado
- **404**: Não encontrado
- **422**: Erro de validação
- **500**: Erro interno

## Exemplos de Uso

### JavaScript/Frontend

```javascript
// Carregar configurações públicas
const config = await fetch('/api/settings/public').then(r => r.json());

// Verificar se data está bloqueada
const blocked = await fetch('/api/settings/check-blocked', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({ datetime: '2025-10-20 10:00:00' })
}).then(r => r.json());

// Usar a biblioteca helper
const clinicInfo = await settingsAPI.getClinicInfo();
const isBlocked = await settingsAPI.isDateBlocked('2025-10-20');
```

### PHP/Laravel

```php
// Usar o modelo diretamente
$clinicName = \App\Models\Setting::get('clinic_name', 'Clínica Padrão');

// Verificar bloqueio
$isBlocked = \App\Models\ScheduleBlock::isBlocked('2025-10-20 10:00:00');
```

### cURL

```bash
# Configurações públicas
curl http://localhost:8000/api/settings/public

# Verificar bloqueio
curl -X POST -H "Content-Type: application/json" \
  -d '{"datetime":"2025-10-20 10:00:00"}' \
  http://localhost:8000/api/settings/check-blocked

# Criar bloqueio (com autenticação)
curl -X POST -H "Content-Type: application/json" \
  -H "X-API-Key: sua-api-key" \
  -d '{"date":"2025-12-25","type":"full_day","reason":"Natal"}' \
  http://localhost:8000/api/settings/schedule-blocks
```

## Biblioteca Helper JavaScript

Incluída em `/js/settings-api.js`, oferece métodos simplificados:

```javascript
// Instância global disponível
const api = window.settingsAPI;

// Métodos principais
await api.getClinicInfo()           // Informações da clínica
await api.getWorkingHours()         // Horários de funcionamento
await api.isWorkingDay('monday')    // Verificar dia útil
await api.isDateBlocked('2025-10-20') // Verificar bloqueio
await api.createScheduleBlock({...}) // Criar bloqueio
await api.saveSetting(key, value)   // Salvar configuração
```

## Casos de Uso Comuns

1. **Frontend de Agendamento**: Usar configurações públicas para exibir informações da clínica e verificar disponibilidade
2. **Dashboard Admin**: Gerenciar configurações e bloqueios em tempo real
3. **Integrações**: Sincronizar dados com sistemas externos
4. **Mobile Apps**: Consumir API para apps nativos
5. **Webhooks**: Receber atualizações de configurações

## Considerações de Segurança

- Configurações sensíveis não são expostas nas rotas públicas
- Validação rigorosa de dados de entrada
- Rate limiting aplicado automaticamente
- Logs de auditoria para mudanças importantes