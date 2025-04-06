# Configuración Docker para GBIT Technical Support

Este proyecto ha sido configurado para ejecutarse con Docker y PostgreSQL. A continuación se detallan los pasos para poner en marcha el entorno de desarrollo.

## Requisitos previos

- Docker
- Docker Compose

## Estructura de contenedores

El entorno Docker incluye los siguientes servicios:

- **app**: Aplicación Laravel con PHP 8.2-FPM
- **nginx**: Servidor web Nginx
- **db**: Base de datos PostgreSQL 15

## Configuración inicial

1. Ejecuta el script de configuración automática:

```bash
./docker-setup.sh
```

Este script realizará las siguientes acciones:
- Copiar el archivo `.env.docker` a `.env`
- Construir y levantar los contenedores
- Instalar dependencias de Composer
- Generar clave de la aplicación
- Ejecutar migraciones
- Configurar permisos en directorios

## Configuración manual

Si prefieres realizar la configuración manualmente, sigue estos pasos:

1. Copia el archivo de entorno para Docker:
```bash
cp .env.docker .env
```

2. Construye y levanta los contenedores:
```bash
docker-compose up -d
```

3. Instala las dependencias de Composer:
```bash
docker-compose exec app composer install
```

4. Genera la clave de la aplicación:
```bash
docker-compose exec app php artisan key:generate
```

5. Ejecuta las migraciones:
```bash
docker-compose exec app php artisan migrate
```

6. Configura los permisos:
```bash
docker-compose exec app chmod -R 777 storage bootstrap/cache
```

## Acceso a la aplicación

Una vez completada la configuración, puedes acceder a la aplicación en:

- **URL**: http://localhost:8000

## Credenciales de la base de datos

- **Host**: db
- **Puerto**: 5432
- **Base de datos**: gbit_technical_support
- **Usuario**: postgres
- **Contraseña**: postgres

## Comandos útiles

- Iniciar los contenedores:
```bash
docker-compose up -d
```

- Detener los contenedores:
```bash
docker-compose down
```

- Ejecutar comandos de Artisan:
```bash
docker-compose exec app php artisan [comando]
```

- Acceder a la terminal del contenedor de la aplicación:
```bash
docker-compose exec app bash
```

- Ver logs de los contenedores:
```bash
docker-compose logs
```

- Ver logs de un contenedor específico:
```bash
docker-compose logs [servicio]
```

## Solución de problemas

Si encuentras problemas con los permisos, puedes ejecutar:
```bash
docker-compose exec app chmod -R 777 storage bootstrap/cache
```

Si necesitas reconstruir los contenedores:
```bash
docker-compose build --no-cache
docker-compose up -d
```
