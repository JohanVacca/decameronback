FROM php:8.2-apache

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Actualizar e instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    libonig-dev \
    iputils-ping \
    netcat-openbsd \
    net-tools \
    procps \
    libpq-dev

# Instalar extensiones de PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd pdo_pgsql zip mbstring exif pcntl bcmath sockets

# Instalar y habilitar la extensión xdebug para PHP
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Copiar Composer desde la imagen oficial de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Copiar los archivos del proyecto
COPY . /var/www/html

# Cambiar permisos de directorios de almacenamiento
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Instalar dependencias del proyecto sin ejecutar scripts
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install --no-scripts

# Copiar la configuración de Xdebug al contenedor
COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# Copiar la configuración del Virtual Host de Apache
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

# Crear script de inicio
COPY start_apache.sh /usr/local/bin/start_apache.sh
RUN chmod +x /usr/local/bin/start_apache.sh

# Exponer el puerto 80 para Apache
EXPOSE 80

# Definir el comando para ejecutar el servidor Apache y el comando Artisan
CMD ["start_apache.sh"]
