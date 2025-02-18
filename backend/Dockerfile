# Usa una imagen base de PHP con Apache
FROM php:8.1-apache

# Instala extensiones y dependencias necesarias
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copia el código de la aplicación al contenedor
COPY . /var/www/html

# Establece los permisos para Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Habilita módulos de Apache necesarios
RUN a2enmod rewrite

# Define el directorio de trabajo
WORKDIR /var/www/html

# Expone el puerto
EXPOSE 80

# Comando por defecto al iniciar el contenedor
CMD ["apache2-foreground"]

