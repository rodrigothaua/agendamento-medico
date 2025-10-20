<!-- Toast container always top-right for compatibility -->
<div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2 max-w-xs w-full sm:max-w-sm">
    <!-- Toasts will be added here by JavaScript -->
</div>

<script>
class ToastManager {
    constructor() {
        this.container = document.getElementById('toast-container');
        this.toastCount = 0;
    }

    show(message, type = 'info', duration = 5000) {
        const toastId = 'toast-' + (++this.toastCount);
        const toast = this.createToast(toastId, message, type);
        
        this.container.appendChild(toast);
        
        // Animate in
        setTimeout(() => {
            toast.classList.remove('translate-x-full', 'opacity-0');
        }, 10);
        
        // Auto remove
        if (duration > 0) {
            setTimeout(() => {
                this.remove(toastId);
            }, duration);
        }
        
        return toastId;
    }

    createToast(id, message, type) {
        const toast = document.createElement('div');
        toast.id = id;
    const styleVariants = {
        success: 'bg-green-50 border-l-4 border-green-500',
        error:   'bg-red-50 border-l-4 border-red-500',
        warning: 'bg-yellow-50 border-l-4 border-yellow-500',
        info:    'bg-blue-50 border-l-4 border-blue-500'
    };
    const colors = {
        success: 'text-green-600',
        error: 'text-red-600',
        warning: 'text-yellow-600',
        info: 'text-blue-600'
    };
    const icons = {
        success: 'fas fa-check-circle',
        error: 'fas fa-exclamation-triangle',
        warning: 'fas fa-exclamation-circle',
        info: 'fas fa-info-circle'
    };
    toast.className = `transform translate-x-full opacity-0 transition-all duration-300 ease-in-out max-w-xs w-full sm:max-w-sm shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden ${styleVariants[type] || styleVariants.info}`;
    toast.innerHTML = `
        <div class="p-4 break-words">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="${icons[type]} ${colors[type]}"></i>
                </div>
                <div class="ml-3 w-0 flex-1 pt-0.5">
                    <p class="text-sm font-medium text-gray-900 break-words whitespace-pre-line">${message}</p>
                </div>
                <div class="ml-4 flex-shrink-0 flex">
                    <button class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none" onclick="toastManager.remove('${id}')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    return toast;
    }

    remove(toastId) {
        const toast = document.getElementById(toastId);
        if (toast) {
            toast.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 300);
        }
    }

    success(message, duration = 5000) {
        return this.show(message, 'success', duration);
    }

    error(message, duration = 5000) {
        return this.show(message, 'error', duration);
    }

    warning(message, duration = 5000) {
        return this.show(message, 'warning', duration);
    }

    info(message, duration = 5000) {
        return this.show(message, 'info', duration);
    }
}

// Initialize toast manager
const toastManager = new ToastManager();

// Make it globally available
window.toastManager = toastManager;
window.toast = {
    success: (message, duration) => toastManager.success(message, duration),
    error: (message, duration) => toastManager.error(message, duration),
    warning: (message, duration) => toastManager.warning(message, duration),
    info: (message, duration) => toastManager.info(message, duration)
};

// Show session messages as toasts
document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
        toast.success('{{ session('success') }}');
    @endif
    
    @if(session('error'))
        toast.error('{{ session('error') }}');
    @endif
    
    @if(session('warning'))
        toast.warning('{{ session('warning') }}');
    @endif
    
    @if(session('info'))
        toast.info('{{ session('info') }}');
    @endif
});
</script>