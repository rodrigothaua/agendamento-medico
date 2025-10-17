# 🚀 Exemplos de Integração - API Sistema de Agendamentos

> Guia prático com exemplos de código para integração com as APIs

## 📋 Índice

1. [Configuração Inicial](#configuração-inicial)
2. [Exemplos JavaScript](#exemplos-javascript)
3. [Exemplos PHP](#exemplos-php)
4. [Exemplos Python](#exemplos-python)
5. [Exemplos cURL](#exemplos-curl)
6. [Casos de Uso Comuns](#casos-de-uso-comuns)

---

## 🔧 Configuração Inicial

### JavaScript (Frontend)
```javascript
// Configurar base URL e API Key
const API_BASE = 'http://localhost:8000/api';
const API_KEY = 'admin-key-local';

const headers = {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-API-Key': API_KEY
};
```

### PHP (Backend)
```php
<?php
class ClinicAPI {
    private $baseUrl = 'http://localhost:8000/api';
    private $apiKey = 'admin-key-local';
    
    private function makeRequest($endpoint, $method = 'GET', $data = null) {
        $url = $this->baseUrl . $endpoint;
        
        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json',
                'X-API-Key: ' . $this->apiKey
            ]
        ];
        
        if ($method === 'POST') {
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = json_encode($data);
        } elseif ($method === 'PUT') {
            $options[CURLOPT_CUSTOMREQUEST] = 'PUT';
            $options[CURLOPT_POSTFIELDS] = json_encode($data);
        } elseif ($method === 'DELETE') {
            $options[CURLOPT_CUSTOMREQUEST] = 'DELETE';
        }
        
        $curl = curl_init();
        curl_setopt_array($curl, $options);
        $response = curl_exec($curl);
        curl_close($curl);
        
        return json_decode($response, true);
    }
}
?>
```

---

## 💻 Exemplos JavaScript

### 1. Obter Configurações da Clínica
```javascript
async function getClinicInfo() {
    try {
        const response = await fetch(`${API_BASE}/settings/public`);
        const data = await response.json();
        
        if (data.success) {
            document.getElementById('clinic-name').textContent = data.data.clinic_name;
            document.getElementById('clinic-phone').textContent = data.data.clinic_phone;
        }
    } catch (error) {
        console.error('Erro ao buscar informações da clínica:', error);
    }
}
```

### 2. Verificar Horários Disponíveis
```javascript
async function getAvailableSlots(date) {
    try {
        const response = await fetch(`${API_BASE}/available-slots?date=${date}`);
        const data = await response.json();
        
        const slotsContainer = document.getElementById('available-slots');
        slotsContainer.innerHTML = '';
        
        data.available_slots.forEach(slot => {
            const button = document.createElement('button');
            button.textContent = new Date(slot).toLocaleTimeString('pt-BR', {
                hour: '2-digit',
                minute: '2-digit'
            });
            button.onclick = () => selectSlot(slot);
            slotsContainer.appendChild(button);
        });
    } catch (error) {
        console.error('Erro ao buscar horários:', error);
    }
}
```

### 3. Criar Agendamento
```javascript
async function createAppointment(appointmentData) {
    try {
        const response = await fetch(`${API_BASE}/appointments/initiate`, {
            method: 'POST',
            headers: headers,
            body: JSON.stringify({
                patient_name: appointmentData.name,
                patient_email: appointmentData.email,
                patient_phone: appointmentData.phone,
                patient_cpf: appointmentData.cpf,
                scheduled_at: appointmentData.datetime,
                notes: appointmentData.notes || ''
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Redirecionar para pagamento se necessário
            if (result.data.payment_required) {
                window.location.href = result.data.payment_url;
            } else {
                showSuccessMessage('Agendamento criado com sucesso!');
            }
        } else {
            showErrorMessage(result.message);
        }
    } catch (error) {
        console.error('Erro ao criar agendamento:', error);
    }
}
```

### 4. Verificar se Data está Bloqueada
```javascript
async function checkDateBlocked(datetime) {
    try {
        const response = await fetch(`${API_BASE}/settings/check-blocked`, {
            method: 'POST',
            headers: headers,
            body: JSON.stringify({ datetime })
        });
        
        const result = await response.json();
        return result.data.is_blocked;
    } catch (error) {
        console.error('Erro ao verificar bloqueio:', error);
        return false;
    }
}
```

---

## 🐘 Exemplos PHP

### 1. Listar Pacientes
```php
<?php
$api = new ClinicAPI();

function listPatients($search = null) {
    global $api;
    
    $endpoint = '/patients';
    if ($search) {
        $endpoint .= '?search=' . urlencode($search);
    }
    
    $response = $api->makeRequest($endpoint);
    return $response['patients'] ?? [];
}

// Uso
$patients = listPatients('João');
foreach ($patients as $patient) {
    echo "Nome: {$patient['name']}, Email: {$patient['email']}\n";
}
?>
```

### 2. Criar Bloqueio de Agenda
```php
<?php
function createScheduleBlock($date, $type, $reason, $endDate = null) {
    global $api;
    
    $data = [
        'date' => $date,
        'type' => $type,
        'block_mode' => $endDate ? 'range' : 'single',
        'reason' => $reason,
        'is_active' => true
    ];
    
    if ($endDate) {
        $data['end_date'] = $endDate;
    }
    
    $response = $api->makeRequest('/settings/schedule-blocks', 'POST', $data);
    return $response;
}

// Bloquear um dia
$result = createScheduleBlock('2025-12-25', 'full_day', 'Natal');

// Bloquear período de férias
$result = createScheduleBlock('2025-12-20', 'full_day', 'Férias', '2025-12-31');
?>
```

### 3. Atualizar Configurações
```php
<?php
function updateClinicSettings($settings) {
    global $api;
    
    $data = ['settings' => []];
    
    foreach ($settings as $key => $value) {
        $data['settings'][] = [
            'key' => $key,
            'value' => $value,
            'type' => is_numeric($value) ? 'integer' : 'string',
            'group' => 'general'
        ];
    }
    
    $response = $api->makeRequest('/settings/bulk', 'POST', $data);
    return $response;
}

// Uso
$newSettings = [
    'clinic_name' => 'Clínica Renovada',
    'clinic_phone' => '(11) 98765-4321',
    'work_start_time' => '08:00',
    'work_end_time' => '18:00'
];

$result = updateClinicSettings($newSettings);
?>
```

---

## 🐍 Exemplos Python

### 1. Cliente Python para API
```python
import requests
import json
from datetime import datetime

class ClinicAPI:
    def __init__(self, base_url='http://localhost:8000/api', api_key='admin-key-local'):
        self.base_url = base_url
        self.headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-API-Key': api_key
        }
    
    def make_request(self, endpoint, method='GET', data=None):
        url = f"{self.base_url}{endpoint}"
        
        try:
            if method == 'GET':
                response = requests.get(url, headers=self.headers)
            elif method == 'POST':
                response = requests.post(url, headers=self.headers, json=data)
            elif method == 'PUT':
                response = requests.put(url, headers=self.headers, json=data)
            elif method == 'DELETE':
                response = requests.delete(url, headers=self.headers)
            
            return response.json()
        except requests.RequestException as e:
            print(f"Erro na requisição: {e}")
            return None
```

### 2. Buscar Agendamentos do Dia
```python
def get_today_appointments(api):
    today = datetime.now().strftime('%Y-%m-%d')
    
    try:
        response = api.make_request(f'/appointments?date={today}')
        appointments = response or []
        
        print(f"Agendamentos de {today}:")
        for appointment in appointments:
            patient_name = appointment['patient']['name']
            time = appointment['scheduled_at']
            status = appointment['status']
            print(f"- {time}: {patient_name} ({status})")
            
        return appointments
    except Exception as e:
        print(f"Erro ao buscar agendamentos: {e}")
        return []

# Uso
api = ClinicAPI()
appointments = get_today_appointments(api)
```

### 3. Monitorar Pagamentos
```python
def monitor_payments(api):
    try:
        # Buscar agendamentos pendentes
        response = api.make_request('/appointments?status=pending')
        pending_appointments = response or []
        
        for appointment in pending_appointments:
            if appointment.get('payment'):
                payment_id = appointment['payment']['id']
                
                # Verificar status do pagamento
                payment_response = api.make_request(f'/payments/{payment_id}')
                if payment_response and payment_response['success']:
                    payment = payment_response['data']
                    
                    if payment['status'] == 'approved':
                        print(f"Pagamento aprovado para agendamento {appointment['id']}")
                    elif payment['status'] == 'failed':
                        print(f"Pagamento falhou para agendamento {appointment['id']}")
                        
    except Exception as e:
        print(f"Erro ao monitorar pagamentos: {e}")

# Executar monitoramento
api = ClinicAPI()
monitor_payments(api)
```

---

## 🌐 Exemplos cURL

### 1. Obter Configurações Públicas
```bash
curl -X GET "http://localhost:8000/api/settings/public" \
  -H "Accept: application/json"
```

### 2. Criar Paciente
```bash
curl -X POST "http://localhost:8000/api/patients" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "X-API-Key: admin-key-local" \
  -d '{
    "name": "Maria Silva",
    "email": "maria@email.com",
    "phone": "(11) 99999-9999",
    "cpf": "123.456.789-00",
    "birth_date": "1990-05-15",
    "address": "Rua das Flores, 123"
  }'
```

### 3. Verificar Horários Disponíveis
```bash
curl -X GET "http://localhost:8000/api/available-slots?date=2025-10-20" \
  -H "Accept: application/json"
```

### 4. Criar Bloqueio de Agenda
```bash
curl -X POST "http://localhost:8000/api/settings/schedule-blocks" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "X-API-Key: admin-key-local" \
  -d '{
    "date": "2025-12-25",
    "type": "full_day",
    "block_mode": "single",
    "reason": "Natal",
    "is_active": true
  }'
```

### 5. Atualizar Configuração
```bash
curl -X POST "http://localhost:8000/api/settings" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "X-API-Key: admin-key-local" \
  -d '{
    "key": "clinic_name",
    "value": "Clínica Renovada",
    "type": "string",
    "group": "general"
  }'
```

---

## 📋 Casos de Uso Comuns

### 1. Widget de Agendamento Online
```javascript
class AppointmentWidget {
    constructor(containerId, apiConfig) {
        this.container = document.getElementById(containerId);
        this.api = new ClinicAPI(apiConfig);
        this.init();
    }
    
    async init() {
        await this.loadClinicInfo();
        this.renderCalendar();
        this.bindEvents();
    }
    
    async loadClinicInfo() {
        const info = await this.api.getPublicConfig();
        this.clinicInfo = info;
    }
    
    async renderCalendar() {
        // Implementar calendário com horários disponíveis
        const today = new Date().toISOString().split('T')[0];
        const slots = await this.api.getAvailableSlots(today);
        
        this.container.innerHTML = `
            <div class="appointment-widget">
                <h3>${this.clinicInfo.clinic_name}</h3>
                <div class="date-picker"></div>
                <div class="time-slots">${this.renderSlots(slots)}</div>
                <form class="appointment-form"></form>
            </div>
        `;
    }
    
    renderSlots(slots) {
        return slots.available_slots.map(slot => 
            `<button class="time-slot" data-time="${slot}">
                ${new Date(slot).toLocaleTimeString('pt-BR')}
             </button>`
        ).join('');
    }
}

// Uso do widget
const widget = new AppointmentWidget('appointment-container', {
    baseUrl: 'https://clinica.com.br/api',
    apiKey: 'public-key'
});
```

### 2. Dashboard de Monitoramento
```javascript
class ClinicDashboard {
    constructor() {
        this.api = window.settingsAPI;
        this.init();
    }
    
    async init() {
        await this.loadStats();
        setInterval(() => this.updateStats(), 30000); // Atualizar a cada 30s
    }
    
    async loadStats() {
        try {
            // Carregar agendamentos de hoje
            const today = new Date().toISOString().split('T')[0];
            const appointments = await this.api.request(`/appointments?date=${today}`);
            
            // Contar por status
            const stats = {
                total: appointments.length,
                confirmed: appointments.filter(a => a.status === 'confirmed').length,
                pending: appointments.filter(a => a.status === 'pending').length,
                canceled: appointments.filter(a => a.status === 'canceled').length
            };
            
            this.updateStatsDisplay(stats);
            
        } catch (error) {
            console.error('Erro ao carregar estatísticas:', error);
        }
    }
    
    updateStatsDisplay(stats) {
        document.getElementById('total-appointments').textContent = stats.total;
        document.getElementById('confirmed-appointments').textContent = stats.confirmed;
        document.getElementById('pending-appointments').textContent = stats.pending;
        document.getElementById('canceled-appointments').textContent = stats.canceled;
    }
}

// Inicializar dashboard
document.addEventListener('DOMContentLoaded', () => {
    new ClinicDashboard();
});
```

### 3. Sincronização com Sistema Externo
```php
<?php
class ClinicSync {
    private $clinicAPI;
    private $externalAPI;
    
    public function __construct() {
        $this->clinicAPI = new ClinicAPI();
        // Configurar API externa
    }
    
    public function syncAppointments() {
        // Buscar agendamentos das últimas 24 horas
        $yesterday = date('Y-m-d H:i:s', strtotime('-24 hours'));
        $appointments = $this->clinicAPI->makeRequest(
            "/appointments?from_date={$yesterday}"
        );
        
        foreach ($appointments as $appointment) {
            $this->sendToExternalSystem($appointment);
        }
    }
    
    private function sendToExternalSystem($appointment) {
        // Implementar envio para sistema externo
        $data = [
            'external_id' => $appointment['id'],
            'patient_name' => $appointment['patient']['name'],
            'scheduled_at' => $appointment['scheduled_at'],
            'status' => $appointment['status']
        ];
        
        // Enviar para API externa
        // $this->externalAPI->createAppointment($data);
    }
}

// Executar sincronização via cron job
$sync = new ClinicSync();
$sync->syncAppointments();
?>
```

### 4. Notificações Automáticas
```python
import schedule
import time
from datetime import datetime, timedelta

def send_appointment_reminders():
    api = ClinicAPI()
    
    # Buscar agendamentos para amanhã
    tomorrow = (datetime.now() + timedelta(days=1)).strftime('%Y-%m-%d')
    
    try:
        response = api.make_request(f'/appointments?date={tomorrow}&status=confirmed')
        appointments = response or []
        
        for appointment in appointments:
            patient = appointment['patient']
            scheduled_time = appointment['scheduled_at']
            
            # Enviar lembrete por email/SMS
            send_reminder(
                to=patient['email'],
                message=f"Lembrete: Você tem consulta agendada para {scheduled_time}"
            )
            
            print(f"Lembrete enviado para {patient['name']}")
            
    except Exception as e:
        print(f"Erro ao enviar lembretes: {e}")

def send_reminder(to, message):
    # Implementar envio de email/SMS
    pass

# Agendar execução diária às 18h
schedule.every().day.at("18:00").do(send_appointment_reminders)

while True:
    schedule.run_pending()
    time.sleep(60)
```

---

## 🔧 Utilitários

### Validação de CPF
```javascript
function validateCPF(cpf) {
    // Remove caracteres especiais
    cpf = cpf.replace(/[^\d]/g, '');
    
    if (cpf.length !== 11 || /^(\d)\1{10}$/.test(cpf)) {
        return false;
    }
    
    // Validação do dígito verificador
    let sum = 0;
    for (let i = 0; i < 9; i++) {
        sum += parseInt(cpf.charAt(i)) * (10 - i);
    }
    
    let remainder = 11 - (sum % 11);
    if (remainder === 10 || remainder === 11) remainder = 0;
    if (remainder !== parseInt(cpf.charAt(9))) return false;
    
    sum = 0;
    for (let i = 0; i < 10; i++) {
        sum += parseInt(cpf.charAt(i)) * (11 - i);
    }
    
    remainder = 11 - (sum % 11);
    if (remainder === 10 || remainder === 11) remainder = 0;
    
    return remainder === parseInt(cpf.charAt(10));
}
```

### Formatação de Telefone
```javascript
function formatPhone(phone) {
    const cleaned = phone.replace(/\D/g, '');
    
    if (cleaned.length === 10) {
        return cleaned.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');
    } else if (cleaned.length === 11) {
        return cleaned.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
    }
    
    return phone;
}
```

---

*Última atualização: 17/10/2025*