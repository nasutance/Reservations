# Dockerfile

FROM php:8.3-fpm

# Installer les dépendances système
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    libzip-dev \
    mariadb-client \
    nano

# Installer les extensions PHP nécessaires à Laravel
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /var/www

# Copier le code source
COPY . .

# Installer les dépendances PHP
RUN composer install --optimize-autoloader --no-dev

# Donner les bonnes permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage

# Lancer php-fpm
CMD ["php", "-S", "0.0.0.0:80", "-t", "public"]

