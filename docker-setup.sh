#!/bin/bash

# Copiar el archivo .env.docker a .env
cp .env.docker .env

# Construir y levantar los contenedores
docker-compose up -d

# Instalar dependencias de Composer
docker-compose exec app composer install

# Generar clave de la aplicación
docker-compose exec app php artisan key:generate

# Ejecutar migraciones
docker-compose exec app php artisan migrate

# Dar permisos de escritura a los directorios necesarios
docker-compose exec app chmod -R 777 storage bootstrap/cache

echo "La aplicación está disponible en: http://localhost:8000"
echo "Base de datos PostgreSQL configurada en el contenedor 'gbit-db'"
echo "Credenciales de la base de datos:"
echo "  - Host: db"
echo "  - Puerto: 5432"
echo "  - Base de datos: gbit_technical_support"
echo "  - Usuario: postgres"
echo "  - Contraseña: 8@6%XX?m4@\CS&"
