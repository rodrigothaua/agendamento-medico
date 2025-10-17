#!/bin/bash

# Script de Limpeza do Projeto Laravel - Sistema de Agendamentos
# Execute: chmod +x cleanup.sh && ./cleanup.sh

echo "🧹 Iniciando limpeza do projeto..."

# Limpar caches do Laravel
echo "📦 Limpando caches do Laravel..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear

# Limpar logs
echo "📝 Limpando logs..."
> storage/logs/laravel.log

# Limpar arquivos de cache do framework
echo "🗂️ Limpando cache do framework..."
rm -rf storage/framework/cache/data/*
rm -rf storage/framework/sessions/*
rm -rf storage/framework/views/*

# Remover arquivos temporários do sistema
echo "🗑️ Removendo arquivos temporários..."
find . -name "Thumbs.db" -delete 2>/dev/null
find . -name ".DS_Store" -delete 2>/dev/null
find . -name "desktop.ini" -delete 2>/dev/null
find . -name "*.tmp" -delete 2>/dev/null
find . -name "*.bak" -delete 2>/dev/null
find . -name "*~" -delete 2>/dev/null

# Remover node_modules se existir (pode ser recriado com npm install)
if [ -d "node_modules" ]; then
    echo "📦 Removendo node_modules (execute npm install para recriar)..."
    rm -rf node_modules
fi

# Otimizar autoloader do Composer
echo "🚀 Otimizando autoloader..."
composer dump-autoload --optimize

# Recriar cache otimizado
echo "⚡ Recriando cache otimizado..."
php artisan config:cache
php artisan route:cache

echo "✅ Limpeza concluída!"
echo ""
echo "📊 Espaço liberado:"
du -sh storage/logs/laravel.log
echo ""
echo "🎯 Para melhor performance, execute também:"
echo "  - php artisan queue:restart (se usar filas)"
echo "  - php artisan migrate:fresh --seed (para resetar BD com dados de teste)"
echo "  - npm install && npm run dev (se usar assets compilados)"