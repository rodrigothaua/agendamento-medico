
<!-- Reusable Confirm Dialog Component with color variants -->
<div id="confirm-dialog" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 z-50 hidden">
  <div id="confirm-dialog-box" class="bg-white rounded-lg shadow-lg p-6 max-w-sm w-full">
    <h2 class="text-lg font-bold mb-2" id="confirm-dialog-title">Confirmação</h2>
    <p class="mb-4" id="confirm-dialog-message"></p>
    <div class="flex justify-end space-x-2">
      <button id="confirm-dialog-cancel" class="bg-gray-300 px-4 py-2 rounded">Cancelar</button>
      <button id="confirm-dialog-ok" class="px-4 py-2 rounded">OK</button>
    </div>
  </div>
</div>
<script>
// Color variants for dialog types
const dialogColors = {
  delete: {
    box: 'border-t-4 border-red-600',
    title: 'text-red-700',
    ok: 'bg-red-600 text-white hover:bg-red-700',
  },
  warning: {
    box: 'border-t-4 border-yellow-500',
    title: 'text-yellow-700',
    ok: 'bg-yellow-500 text-white hover:bg-yellow-600',
  },
  info: {
    box: 'border-t-4 border-blue-600',
    title: 'text-blue-700',
    ok: 'bg-blue-600 text-white hover:bg-blue-700',
  },
  default: {
    box: '',
    title: 'text-gray-900',
    ok: 'bg-blue-600 text-white hover:bg-blue-700',
  }
};

window.confirmDialog = {
  show: function(message, onConfirm, title = 'Confirmação', type = 'default') {
    document.getElementById('confirm-dialog-title').textContent = title;
    document.getElementById('confirm-dialog-message').textContent = message;
    document.getElementById('confirm-dialog').classList.remove('hidden');
    window._confirmDialogCallback = onConfirm;

    // Apply color variant
    const colors = dialogColors[type] || dialogColors.default;
    const box = document.getElementById('confirm-dialog-box');
    box.className = 'bg-white rounded-lg shadow-lg p-6 max-w-sm w-full ' + colors.box;
    document.getElementById('confirm-dialog-title').className = 'text-lg font-bold mb-2 ' + colors.title;
    document.getElementById('confirm-dialog-ok').className = 'px-4 py-2 rounded ' + colors.ok;
  },
  close: function() {
    document.getElementById('confirm-dialog').classList.add('hidden');
    window._confirmDialogCallback = null;
  }
};
document.addEventListener('DOMContentLoaded', function() {
  document.getElementById('confirm-dialog-ok').onclick = function() {
    if (typeof window._confirmDialogCallback === 'function') window._confirmDialogCallback();
    window.confirmDialog.close();
  };
  document.getElementById('confirm-dialog-cancel').onclick = function() {
    window.confirmDialog.close();
  };
});
</script>
<!-- Usage: window.confirmDialog.show('Mensagem', function() { ... }, 'Título', 'delete|warning|info|default') -->
