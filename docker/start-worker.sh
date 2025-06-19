#!/bin/bash

# Iniciar el worker de colas de Laravel en segundo plano
php /var/www/artisan queue:work --tries=3 --timeout=60 --sleep=3 >> /var/www/storage/logs/worker.log 2>&1 &

# Iniciar el scheduler de Laravel en segundo plano (cada minuto)
(
  while [ true ]
  do
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] Running schedule:run"
    php /var/www/artisan schedule:run --verbose --no-interaction
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] Finished schedule:run"
    sleep 60
  done
) >> /var/www/storage/logs/scheduler.log 2>&1 &

# Mantener el contenedor en ejecución
tail -f /dev/null
