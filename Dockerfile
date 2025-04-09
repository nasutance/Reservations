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

# Installer les extensions PHP requises par Laravel
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Créer un dossier pour l’application
WORKDIR /var/www

# Copier les fichiers du projet dans le conteneur
COPY . .

# Installer les dépendances du projet
RUN composer install --optimize-autoloader --no-dev

# Donner les bonnes permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage

# Définir l'utilisateur pour exécuter les processus
USER www-data

CMD ["php-fpm"]
