FROM php:8.3-fpm

# Argumentos definidos en docker-compose.yml
ARG user
ARG uid

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    nodejs \
    npm \
    iputils-ping \
    tzdata

# Configurar zona horaria
ENV TZ=America/Caracas
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone
RUN echo "date.timezone = ${TZ}" > /usr/local/etc/php/conf.d/99-timezone.ini

# Limpiar cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar extensiones PHP
RUN docker-php-ext-install pdo pdo_pgsql pgsql mbstring exif pcntl bcmath gd dom

# Obtener Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Crear usuario del sistema
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Establecer directorio de trabajo
WORKDIR /var/www

# Copiar todos los archivos de la aplicación al contenedor
COPY . .

# Instalar dependencias de PHP y Node.js, y construir los assets como root
RUN composer install --no-dev --optimize-autoloader && \
    npm install && \
    npm run build && \
    rm -rf node_modules

# Ajustar permisos para la aplicación
RUN chown -R $user:$user /var/www

# Cambiar al usuario no-root
USER $user

# El CMD por defecto de php:8.3-fpm es ["php-fpm"], que iniciará el servidor.