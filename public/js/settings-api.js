/**
 * Settings API Client
 * Helper para consumir a API de configurações
 */
class SettingsAPI {
    constructor(baseUrl = '/api') {
        this.baseUrl = baseUrl;
        this.cache = new Map();
        this.cacheTimeout = 5 * 60 * 1000; // 5 minutos
    }

    /**
     * Fazer requisição HTTP
     */
    async request(endpoint, options = {}) {
        const url = `${this.baseUrl}${endpoint}`;
        
        const defaultOptions = {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        };

        // Adicionar CSRF token se disponível
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (csrfToken) {
            defaultOptions.headers['X-CSRF-TOKEN'] = csrfToken;
        }

        const config = {
            ...defaultOptions,
            ...options,
            headers: {
                ...defaultOptions.headers,
                ...options.headers
            }
        };

        try {
            const response = await fetch(url, config);
            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || `HTTP ${response.status}`);
            }

            return data;
        } catch (error) {
            console.error('Settings API Error:', error);
            throw error;
        }
    }

    /**
     * Obter configurações públicas (sem cache para dados críticos)
     */
    async getPublicConfig() {
        const cacheKey = 'public_config';
        const cached = this.getCached(cacheKey);
        
        if (cached) {
            return cached;
        }

        const response = await this.request('/settings/public');
        this.setCache(cacheKey, response.data);
        
        return response.data;
    }

    /**
     * Obter configuração específica
     */
    async getSetting(key) {
        const response = await this.request(`/settings?key=${encodeURIComponent(key)}`);
        return response.data;
    }

    /**
     * Obter configurações por grupo
     */
    async getSettingsByGroup(group) {
        const cacheKey = `group_${group}`;
        const cached = this.getCached(cacheKey);
        
        if (cached) {
            return cached;
        }

        const response = await this.request(`/settings?group=${encodeURIComponent(group)}`);
        this.setCache(cacheKey, response.data);
        
        return response.data;
    }

    /**
     * Obter todas as configurações agrupadas
     */
    async getAllGrouped() {
        const cacheKey = 'all_grouped';
        const cached = this.getCached(cacheKey);
        
        if (cached) {
            return cached;
        }

        const response = await this.request('/settings/grouped');
        this.setCache(cacheKey, response.data);
        
        return response.data;
    }

    /**
     * Salvar uma configuração
     */
    async saveSetting(key, value, type = 'string', group = 'general', description = null) {
        const response = await this.request('/settings', {
            method: 'POST',
            body: JSON.stringify({
                key,
                value,
                type,
                group,
                description
            })
        });

        // Limpar cache relacionado
        this.clearCacheByGroup(group);
        
        return response.data;
    }

    /**
     * Salvar múltiplas configurações
     */
    async saveSettings(settings) {
        const response = await this.request('/settings/bulk', {
            method: 'POST',
            body: JSON.stringify({
                settings
            })
        });

        // Limpar todo o cache
        this.clearCache();
        
        return response.data;
    }

    /**
     * Remover uma configuração
     */
    async deleteSetting(key) {
        const response = await this.request(`/settings/${encodeURIComponent(key)}`, {
            method: 'DELETE'
        });

        // Limpar cache
        this.clearCache();
        
        return response;
    }

    /**
     * Obter bloqueios de agenda
     */
    async getScheduleBlocks(filters = {}) {
        let endpoint = '/settings/schedule-blocks';
        const params = new URLSearchParams();
        
        if (filters.date) params.append('date', filters.date);
        if (filters.from_date) params.append('from_date', filters.from_date);
        if (filters.to_date) params.append('to_date', filters.to_date);
        
        if (params.toString()) {
            endpoint += '?' + params.toString();
        }

        const response = await this.request(endpoint);
        return response.data;
    }

    /**
     * Criar bloqueio de agenda
     */
    async createScheduleBlock(blockData) {
        const response = await this.request('/settings/schedule-blocks', {
            method: 'POST',
            body: JSON.stringify(blockData)
        });
        
        return response.data;
    }

    /**
     * Atualizar bloqueio de agenda
     */
    async updateScheduleBlock(id, blockData) {
        const response = await this.request(`/settings/schedule-blocks/${id}`, {
            method: 'PUT',
            body: JSON.stringify(blockData)
        });
        
        return response.data;
    }

    /**
     * Remover bloqueio de agenda
     */
    async deleteScheduleBlock(id) {
        const response = await this.request(`/settings/schedule-blocks/${id}`, {
            method: 'DELETE'
        });
        
        return response;
    }

    /**
     * Verificar se um horário está bloqueado
     */
    async checkBlocked(datetime) {
        const response = await this.request('/settings/check-blocked', {
            method: 'POST',
            body: JSON.stringify({ datetime })
        });
        
        return response.data;
    }

    /**
     * Gerenciamento de cache
     */
    getCached(key) {
        const cached = this.cache.get(key);
        if (!cached) return null;
        
        const now = Date.now();
        if (now - cached.timestamp > this.cacheTimeout) {
            this.cache.delete(key);
            return null;
        }
        
        return cached.data;
    }

    setCache(key, data) {
        this.cache.set(key, {
            data,
            timestamp: Date.now()
        });
    }

    clearCache() {
        this.cache.clear();
    }

    clearCacheByGroup(group) {
        for (const [key] of this.cache) {
            if (key.includes(group) || key === 'all_grouped') {
                this.cache.delete(key);
            }
        }
    }

    /**
     * Helpers úteis
     */
    async getClinicInfo() {
        const config = await this.getPublicConfig();
        return {
            name: config.clinic_name,
            phone: config.clinic_phone,
            email: config.clinic_email,
            address: config.clinic_address
        };
    }

    async getWorkingHours() {
        const config = await this.getPublicConfig();
        return {
            start: config.work_start_time,
            end: config.work_end_time,
            days: config.work_days,
            duration: config.appointment_duration
        };
    }

    async isWorkingDay(dayName) {
        const workingHours = await this.getWorkingHours();
        return workingHours.days.includes(dayName.toLowerCase());
    }

    async isDateBlocked(date) {
        try {
            const result = await this.checkBlocked(date);
            return result.is_blocked;
        } catch (error) {
            console.warn('Erro ao verificar bloqueio:', error);
            return false;
        }
    }
}

// Instância global da API
window.settingsAPI = new SettingsAPI();

// Função global para atualizar nome da clínica em todas as páginas
window.updateClinicName = async function() {
    try {
        const clinicInfo = await settingsAPI.getClinicInfo();
        
        if (clinicInfo.name) {
            // Atualizar elementos com ID específicos
            const elementsToUpdate = [
                'clinic-name',           // Nome na sidebar
                'clinic-title',          // Nome no título da página
                'clinic-name-header',    // Nome no cabeçalho
                'clinic-display-name'    // Nome em outros locais
            ];
            
            elementsToUpdate.forEach(elementId => {
                const element = document.getElementById(elementId);
                if (element) {
                    element.textContent = clinicInfo.name;
                }
            });
            
            // Atualizar título da página
            const currentTitle = document.title;
            const titleParts = currentTitle.split(' - ');
            if (titleParts.length > 1) {
                document.title = `${titleParts[0]} - ${clinicInfo.name}`;
            }
            
            // Atualizar elementos com classes específicas
            const classSelectors = [
                '.dynamic-clinic-name',
                '.clinic-name'
            ];
            
            classSelectors.forEach(selector => {
                const elements = document.querySelectorAll(selector);
                elements.forEach(element => {
                    element.textContent = clinicInfo.name;
                });
            });
            
            // Atualizar placeholders e valores de input
            const inputElements = document.querySelectorAll('input[name="clinic_name"]');
            inputElements.forEach(input => {
                if (!input.value || input.value === 'Clínica Saúde') {
                    input.value = clinicInfo.name;
                }
            });
            
            console.log('Nome da clínica atualizado:', clinicInfo.name);
        }
    } catch (error) {
        console.warn('Não foi possível carregar o nome da clínica:', error.message);
    }
};

// Auto-executar quando a página carregar
document.addEventListener('DOMContentLoaded', function() {
    // Aguardar um pouco para garantir que todos os elementos foram renderizados
    setTimeout(updateClinicName, 100);
});

// Exportar para uso em módulos
if (typeof module !== 'undefined' && module.exports) {
    module.exports = SettingsAPI;
}

// Exemplo de uso:
/*
// Obter informações da clínica
const clinicInfo = await settingsAPI.getClinicInfo();
console.log(clinicInfo.name);

// Verificar horário de funcionamento
const isWorking = await settingsAPI.isWorkingDay('monday');

// Verificar se uma data está bloqueada
const isBlocked = await settingsAPI.isDateBlocked('2025-10-20');

// Salvar configuração
await settingsAPI.saveSetting('clinic_name', 'Nova Clínica', 'string', 'general');

// Criar bloqueio
await settingsAPI.createScheduleBlock({
    date: '2025-12-25',
    type: 'full_day',
    reason: 'Natal'
});
*/