FROM php:8.3-apache

# Dependências do sistema
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    && docker-php-ext-install pdo pdo_pgsql zip

# Apache config
RUN a2enmod rewrite

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copia o projeto
COPY . /var/www/html

WORKDIR /var/www/html

# Permissões do Laravel
RUN chown -R www-data:www-data storage bootstrap/cache

# Instala dependências PHP
RUN composer install --no-dev --optimize-autoloader

# Apache usa a pasta public
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

EXPOSE 80