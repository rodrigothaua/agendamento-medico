# 🗓️ Teste da Funcionalidade de Bloqueios de Data

## 📋 Como testar os bloqueios de data

### 1. **Iniciar o servidor**
```bash
cd /d/agendamentos/backend
php artisan serve --host=0.0.0.0 --port=8000
```

### 2. **Criar bloqueios de teste**

#### **Bloquear dia inteiro (exemplo: Natal)**
```bash
curl -X POST http://localhost:8000/api/settings/schedule-blocks \
  -H "Content-Type: application/json" \
  -H "X-API-Key: admin-key-local" \
  -d '{
    "date": "2025-12-25",
    "type": "full_day",
    "reason": "Natal - Feriado"
  }'
```

#### **Bloquear horário específico (exemplo: Intervalo para almoço)**
```bash
curl -X POST http://localhost:8000/api/settings/schedule-blocks \
  -H "Content-Type: application/json" \
  -H "X-API-Key: admin-key-local" \
  -d '{
    "date": "2025-10-20",
    "type": "time_range",
    "start_time": "12:00",
    "end_time": "13:00",
    "reason": "Intervalo para almoço"
  }'
```

### 3. **Testar disponibilidade de horários**

#### **Verificar data normal (sem bloqueios)**
```bash
curl "http://localhost:8000/api/available-slots?date=2025-10-18"
```

**Resposta esperada:**
```json
{
  "available_slots": [
    "2025-10-18 09:00:00",
    "2025-10-18 09:30:00",
    "2025-10-18 10:00:00",
    // ... mais horários
  ],
  "date": "2025-10-18",
  "work_hours": {
    "start": "09:00",
    "end": "17:00",
    "duration": 30
  },
  "active_blocks": [],
  "total_slots": 16,
  "available_count": 16,
  "booked_count": 0
}
```

#### **Verificar data com bloqueio parcial**
```bash
curl "http://localhost:8000/api/available-slots?date=2025-10-20"
```

**Resposta esperada:**
```json
{
  "available_slots": [
    "2025-10-20 09:00:00",
    "2025-10-20 09:30:00",
    "2025-10-20 10:00:00",
    "2025-10-20 10:30:00",
    "2025-10-20 11:00:00",
    "2025-10-20 11:30:00",
    // Pula 12:00 e 12:30 (bloqueados)
    "2025-10-20 13:00:00",
    "2025-10-20 13:30:00",
    // ... resto dos horários
  ],
  "active_blocks": [
    {
      "id": 1,
      "type": "time_range",
      "start_time": "12:00",
      "end_time": "13:00",
      "reason": "Intervalo para almoço"
    }
  ]
}
```

#### **Verificar data totalmente bloqueada**
```bash
curl "http://localhost:8000/api/available-slots?date=2025-12-25"
```

**Resposta esperada:**
```json
{
  "available_slots": [],
  "blocked_reason": "Data totalmente bloqueada"
}
```

### 4. **Testar calendário de disponibilidade**

```bash
curl "http://localhost:8000/api/availability-calendar?start_date=2025-10-18&end_date=2025-10-25"
```

**Resposta esperada:**
```json
{
  "availability": [
    {
      "date": "2025-10-18",
      "is_work_day": true,
      "is_fully_blocked": false,
      "available_slots": 16,
      "total_slots": 16,
      "status": "available"
    },
    {
      "date": "2025-10-19",
      "is_work_day": false,
      "is_fully_blocked": false,
      "available_slots": 0,
      "total_slots": 0,
      "status": "closed"
    },
    {
      "date": "2025-10-20",
      "is_work_day": true,
      "is_fully_blocked": false,
      "available_slots": 14,
      "total_slots": 16,
      "status": "available"
    }
  ]
}
```

### 5. **Verificar bloqueios existentes**

```bash
curl -H "X-API-Key: admin-key-local" \
  "http://localhost:8000/api/settings/schedule-blocks"
```

### 6. **Para o Frontend - Exemplos de Uso**

#### **JavaScript/Fetch**
```javascript
// Verificar disponibilidade de uma data
async function checkAvailability(date) {
  const response = await fetch(`http://localhost:8000/api/available-slots?date=${date}`);
  const data = await response.json();
  
  if (data.available_slots.length === 0) {
    console.log('Data indisponível:', data.blocked_reason || 'Todos os horários ocupados');
  } else {
    console.log('Horários disponíveis:', data.available_slots);
  }
}

// Obter calendário de disponibilidade
async function getCalendarAvailability(startDate, endDate) {
  const response = await fetch(
    `http://localhost:8000/api/availability-calendar?start_date=${startDate}&end_date=${endDate}`
  );
  const data = await response.json();
  
  data.availability.forEach(day => {
    console.log(`${day.date}: ${day.status} (${day.available_slots} slots)`);
  });
}

// Usar as funções
checkAvailability('2025-10-20');
getCalendarAvailability('2025-10-18', '2025-10-25');
```

#### **React Hook Example**
```typescript
import { useState, useEffect } from 'react';

interface AvailabilityDay {
  date: string;
  is_work_day: boolean;
  is_fully_blocked: boolean;
  available_slots: number;
  total_slots: number;
  status: 'available' | 'limited' | 'full' | 'blocked' | 'closed';
}

export function useAvailabilityCalendar(startDate: string, endDate: string) {
  const [availability, setAvailability] = useState<AvailabilityDay[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    async function fetchAvailability() {
      try {
        const response = await fetch(
          `${API_BASE_URL}/api/availability-calendar?start_date=${startDate}&end_date=${endDate}`
        );
        const data = await response.json();
        setAvailability(data.availability);
      } catch (error) {
        console.error('Erro ao buscar disponibilidade:', error);
      } finally {
        setLoading(false);
      }
    }

    fetchAvailability();
  }, [startDate, endDate]);

  return { availability, loading };
}
```

### 7. **Comandos de Gestão de Bloqueios**

#### **Listar bloqueios**
```bash
curl -H "X-API-Key: admin-key-local" \
  "http://localhost:8000/api/settings/schedule-blocks"
```

#### **Atualizar bloqueio**
```bash
curl -X PUT http://localhost:8000/api/settings/schedule-blocks/1 \
  -H "Content-Type: application/json" \
  -H "X-API-Key: admin-key-local" \
  -d '{
    "date": "2025-10-20",
    "type": "time_range",
    "start_time": "12:00",
    "end_time": "14:00",
    "reason": "Reunião administrativa"
  }'
```

#### **Remover bloqueio**
```bash
curl -X DELETE http://localhost:8000/api/settings/schedule-blocks/1 \
  -H "X-API-Key: admin-key-local"
```

## ✅ Status da Funcionalidade

- **✅ Bloqueios de dia inteiro**: Funcionais
- **✅ Bloqueios de horário específico**: Funcionais  
- **✅ Integração com available-slots**: Implementada
- **✅ API de calendário**: Implementada
- **✅ Configurações de dias de trabalho**: Respeitadas
- **✅ Documentação**: Atualizada

**A funcionalidade de bloqueios está totalmente integrada e funcionando! 🎉**