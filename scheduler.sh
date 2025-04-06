#!/bin/bash

# Este script ejecuta el programador de tareas de Laravel cada minuto
while true; do
    echo "Ejecutando el programador de tareas de Laravel..."
    php /var/www/html/artisan schedule:run >> /var/www/html/storage/logs/scheduler.log 2>&1
    sleep 60
done
