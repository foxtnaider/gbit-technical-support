#!/bin/bash

# Ejecutar dentro del contenedor para arreglar los problemas de Vite
echo "Configurando entorno para Vite..."

# Forzar modo producción para Vite
echo "NODE_ENV=production" >> .env

# Instalar dependencias de npm si no están instaladas
if [ ! -d "node_modules" ]; then
  echo "Instalando dependencias de npm..."
  npm install
fi

# Compilar assets para producción
echo "Compilando assets para producción..."
npm run build

# Limpiar caché de Laravel
echo "Limpiando caché de Laravel..."
php artisan optimize:clear

echo "¡Listo! Los assets deberían cargarse correctamente ahora."
