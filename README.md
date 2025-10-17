# 🏥 Sistema de Agendamentos - Clínica Médica

<p align="center">
<img src="https://img.shields.io/badge/Laravel-10.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
<img src="https://img.shields.io/badge/PHP-8.1+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
<img src="https://img.shields.io/badge/MySQL-8.0+-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
<img src="https://img.shields.io/badge/TailwindCSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="TailwindCSS">
</p>

Sistema completo de gestão de clínica médica com agendamentos online, controle de pacientes, pagamentos e APIs RESTful.

## ✨ Funcionalidades Principais

### 🎯 **Painel Administrativo**
- **Dashboard Completo** - Estatísticas e visão geral dos agendamentos
- **Gestão de Pacientes** - CRUD completo com busca e filtros
- **Controle de Agendamentos** - Lista e calendário com modal de detalhes
- **Sistema de Pagamentos** - Integração com gateways e controle financeiro
- **Relatórios Avançados** - Gráficos e exportação de dados
- **Configurações Flexíveis** - Personalização completa da clínica
- **Bloqueios de Agenda** - Controle de datas e períodos indisponíveis
- **Perfil do Usuário** - Gerenciamento de conta e alteração de senha

### 🌐 **APIs RESTful**
- **Configurações Públicas** - Informações da clínica para integração
- **Agendamentos** - CRUD completo com validação de horários
- **Pacientes** - Gestão completa via API
- **Horários Disponíveis** - Cálculo automático de slots livres
- **Pagamentos** - Processamento e webhooks
- **Bloqueios** - Verificação e gestão de indisponibilidades

### 🎨 **Interface Moderna**
- **Design Responsivo** - Funciona perfeitamente em mobile, tablet e desktop
- **Componentes Reutilizáveis** - Sidebar e elementos padronizados
- **Visualização Calendário** - Toggle entre lista e calendário
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

# 3. Instalar dependências Node.js
npm install

# 4. Configurar ambiente
cp .env.example .env
php artisan key:generate

# 5. Configurar banco de dados no .env
DB_DATABASE=agendamentos
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha

# 6. Executar migrações
php artisan migrate

# 7. Popular dados de teste (opcional)
php artisan db:seed

# 8. Iniciar servidor
php artisan serve
```

### Acesso Inicial
- **URL**: `http://localhost:8000`
- **Admin**: `http://localhost:8000/login`
- **API Base**: `http://localhost:8000/api`

---

## 📖 Documentação

### 📋 **Documentação da API**
- **[API_ENDPOINTS.md](./API_ENDPOINTS.md)** - Documentação completa dos endpoints
- **[API_EXAMPLES.md](./API_EXAMPLES.md)** - Exemplos práticos de integração

### 🔧 **Configuração e Uso**
- **Painel Admin**: Acesse `/admin/dashboard` após fazer login
- **API Key**: Configure no `.env` como `API_KEY=sua-chave-secreta`
- **Configurações**: Personalize via interface admin ou API

### 🗂️ **Estrutura do Projeto**

```
agendamentos/
├── app/
│   ├── Http/Controllers/
│   │   ├── Api/              # Controllers da API
│   │   ├── AdminController.php
│   │   ├── AppointmentController.php
│   │   ├── PatientController.php
│   │   ├── PaymentController.php
│   │   ├── ProfileController.php
│   │   └── SettingController.php
│   ├── Models/
│   │   ├── Appointment.php
│   │   ├── Patient.php
│   │   ├── Payment.php
│   │   ├── ScheduleBlock.php
│   │   └── Setting.php
│   └── Http/Middleware/
│       ├── ApiAuth.php       # Autenticação da API
│       └── CheckAuth.php     # Autenticação web
├── resources/views/
│   ├── admin/                # Views do painel admin
│   ├── components/           # Componentes Blade
│   └── auth/                 # Telas de login
├── public/js/
│   └── settings-api.js       # Cliente JavaScript da API
├── database/
│   ├── migrations/           # Estrutura do banco
│   └── seeders/             # Dados de teste
└── routes/
    ├── web.php              # Rotas web
    └── api.php              # Rotas da API
```

---

## 🔌 Exemplos de Uso da API

### JavaScript (Frontend)
```javascript
// Obter configurações da clínica
const response = await fetch('/api/settings/public');
const config = await response.json();

// Verificar horários disponíveis
const slots = await fetch('/api/available-slots?date=2025-10-20');

// Usar cliente JavaScript incluído
const clinicInfo = await settingsAPI.getClinicInfo();
```

### PHP (Backend)
```php
// Listar pacientes
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => 'http://localhost:8000/api/patients',
    CURLOPT_HTTPHEADER => ['X-API-Key: admin-key-local']
]);
$response = curl_exec($curl);
```

### cURL (Terminal)
```bash
# Criar agendamento
curl -X POST "http://localhost:8000/api/appointments/initiate" \
  -H "Content-Type: application/json" \
  -d '{"patient_name": "João Silva", "scheduled_at": "2025-10-20 14:00:00"}'
```

---

## 🏗️ Arquitetura Técnica

### **Backend**
- **Laravel 10.x** - Framework PHP moderno
- **Eloquent ORM** - Mapeamento objeto-relacional
- **MySQL** - Banco de dados relacional
- **APIs RESTful** - Endpoints padronizados
- **Middleware Custom** - Autenticação flexível

### **Frontend**
- **Blade Templates** - Sistema de templates do Laravel
- **TailwindCSS** - Framework CSS utilitário
- **JavaScript Vanilla** - Sem dependências pesadas
- **Componentes Reutilizáveis** - Arquitetura modular

### **Recursos Avançados**
- **Cache Inteligente** - Sistema de cache em JavaScript
- **Validação Robusta** - Server-side e client-side
- **Responsividade Total** - Mobile-first design
- **SEO Otimizado** - Estrutura semântica

---

## 🧪 Testes e Qualidade

### Executar Testes
```bash
# Testes unitários
php artisan test

# Verificação de sintaxe
php -l app/Http/Controllers/*.php

# Limpeza de cache
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### Métricas de Qualidade
- ✅ **Todas as APIs testadas e funcionais**
- ✅ **Zero erros de sintaxe**
- ✅ **Responsividade verificada**
- ✅ **Compatibilidade cross-browser**

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
```

### Variáveis de Ambiente Importantes
```env
# Banco de dados
DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=agendamentos
DB_USERNAME=usuario
DB_PASSWORD=senha

# API e Segurança
API_KEY=sua-chave-api-super-secreta
APP_KEY=base64:sua-chave-aplicacao

# Email (opcional)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
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
- **[Endpoints da API](./API_ENDPOINTS.md)** - Referência completa
- **[Exemplos de Integração](./API_EXAMPLES.md)** - Código prático
- **Issues no GitHub** - Reporte problemas

### Contato
- **Email**: suporte@sistema-agendamentos.com.br
- **GitHub Issues**: Para bugs e sugestões
- **Documentação Online**: [Link quando disponível]

---

**Desenvolvido com ❤️ para modernizar a gestão de clínicas médicas**
