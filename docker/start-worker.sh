#!/bin/bash

# Iniciar el worker de colas de Laravel en segundo plano
php /var/www/artisan queue:work --tries=3 --timeout=60 --sleep=3 >> /var/www/storage/logs/worker.log 2>&1 &

# Mantener el contenedor en ejecución
tail -f /dev/null
