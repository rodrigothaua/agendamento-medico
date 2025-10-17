/**
 * Utilitários Comuns do Sistema de Agendamentos
 */

class AdminUtils {
    /**
     * Confirma uma ação com o usuário
     */
    static confirm(message, callback) {
        if (confirm(message)) {
            callback();
        }
    }

    /**
     * Submete um formulário com confirmação
     */
    static submitWithConfirmation(formId, message) {
        this.confirm(message, () => {
            document.getElementById(formId).submit();
        });
    }

    /**
     * Redireciona para uma URL
     */
    static redirect(url) {
        window.location.href = url;
    }

    /**
     * Abre um modal
     */
    static openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
    }

    /**
     * Fecha um modal
     */
    static closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }

    /**
     * Formata data para exibição
     */
    static formatDate(dateString, format = 'dd/MM/yyyy HH:mm') {
        const date = new Date(dateString);
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');

        return format
            .replace('dd', day)
            .replace('MM', month)
            .replace('yyyy', year)
            .replace('HH', hours)
            .replace('mm', minutes);
    }

    /**
     * Formata valor monetário
     */
    static formatCurrency(value) {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        }).format(value);
    }

    /**
     * Copia texto para a área de transferência
     */
    static async copyToClipboard(text) {
        try {
            await navigator.clipboard.writeText(text);
            toast.success('Texto copiado para a área de transferência!');
        } catch (err) {
            toast.error('Erro ao copiar texto');
        }
    }

    /**
     * Valida CPF
     */
    static validateCPF(cpf) {
        cpf = cpf.replace(/[^\d]+/g, '');
        if (cpf.length !== 11 || !!cpf.match(/(\d)\1{10}/)) return false;
        
        let sum = 0;
        let remainder;
        
        for (let i = 1; i <= 9; i++) {
            sum = sum + parseInt(cpf.substring(i-1, i)) * (11 - i);
        }
        
        remainder = (sum * 10) % 11;
        if ((remainder == 10) || (remainder == 11)) remainder = 0;
        if (remainder != parseInt(cpf.substring(9, 10))) return false;
        
        sum = 0;
        for (let i = 1; i <= 10; i++) {
            sum = sum + parseInt(cpf.substring(i-1, i)) * (12 - i);
        }
        
        remainder = (sum * 10) % 11;
        if ((remainder == 10) || (remainder == 11)) remainder = 0;
        if (remainder != parseInt(cpf.substring(10, 11))) return false;
        
        return true;
    }

    /**
     * Formata CPF
     */
    static formatCPF(cpf) {
        cpf = cpf.replace(/\D/g, '');
        cpf = cpf.replace(/(\d{3})(\d)/, '$1.$2');
        cpf = cpf.replace(/(\d{3})(\d)/, '$1.$2');
        cpf = cpf.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        return cpf;
    }

    /**
     * Formata telefone
     */
    static formatPhone(phone) {
        phone = phone.replace(/\D/g, '');
        if (phone.length === 11) {
            phone = phone.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
        } else if (phone.length === 10) {
            phone = phone.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');
        }
        return phone;
    }

    /**
     * Debounce para otimizar buscas
     */
    static debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    /**
     * Loading spinner para botões
     */
    static showButtonLoading(buttonId, text = 'Carregando...') {
        const button = document.getElementById(buttonId);
        if (button) {
            button.disabled = true;
            button.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                ${text}
            `;
        }
    }

    /**
     * Remove loading do botão
     */
    static hideButtonLoading(buttonId, originalText) {
        const button = document.getElementById(buttonId);
        if (button) {
            button.disabled = false;
            button.innerHTML = originalText;
        }
    }
}

/**
 * Funcionalidades específicas de agendamentos
 */
class AppointmentManager {
    static updateStatus(appointmentId, status) {
        const statusTexts = {
            'confirmed': 'confirmar',
            'canceled': 'cancelar',
            'completed': 'concluir'
        };
        
        AdminUtils.confirm(
            `Tem certeza que deseja ${statusTexts[status]} este agendamento?`,
            () => {
                const form = document.getElementById('status-form');
                const statusInput = document.getElementById('status-input');
                
                if (form && statusInput) {
                    form.action = `/admin/appointments/${appointmentId}/update-status`;
                    statusInput.value = status;
                    form.submit();
                }
            }
        );
    }

    static confirmDelete(appointmentId, patientName, scheduledAt) {
        AdminUtils.confirm(
            `Tem certeza que deseja excluir o agendamento de ${patientName} para ${scheduledAt}?`,
            () => {
                const form = document.getElementById('delete-form');
                if (form) {
                    form.action = `/admin/appointments/${appointmentId}`;
                    form.submit();
                }
            }
        );
    }
}

/**
 * Funcionalidades específicas de pacientes
 */
class PatientManager {
    static confirmDelete(patientId, patientName) {
        AdminUtils.confirm(
            `Tem certeza que deseja excluir o paciente ${patientName}?`,
            () => {
                const form = document.getElementById('delete-form');
                if (form) {
                    form.action = `/admin/patients/${patientId}`;
                    form.submit();
                }
            }
        );
    }
}

// Torna as classes disponíveis globalmente
window.AdminUtils = AdminUtils;
window.AppointmentManager = AppointmentManager;
window.PatientManager = PatientManager;

// Funcionalidades de inicialização
document.addEventListener('DOMContentLoaded', function() {
    // Auto-format inputs
    const cpfInputs = document.querySelectorAll('input[data-format="cpf"]');
    cpfInputs.forEach(input => {
        input.addEventListener('input', function() {
            this.value = AdminUtils.formatCPF(this.value);
        });
    });

    const phoneInputs = document.querySelectorAll('input[data-format="phone"]');
    phoneInputs.forEach(input => {
        input.addEventListener('input', function() {
            this.value = AdminUtils.formatPhone(this.value);
        });
    });

    // Auto-focus primeiro input em modais
    const modals = document.querySelectorAll('[role="dialog"]');
    modals.forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (!modal.classList.contains('hidden')) {
                const firstInput = modal.querySelector('input, select, textarea');
                if (firstInput) {
                    firstInput.focus();
                }
            }
        });
    });
});