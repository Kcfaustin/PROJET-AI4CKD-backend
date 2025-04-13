FROM php:8.1-fpm

# Installer les dépendances nécessaires
RUN apt-get update && apt-get install -y \
    nginx \
    git \
    unzip \
    curl \
    libpq-dev \
    libzip-dev \
    zip \
    && docker-php-ext-install pdo pdo_pgsql zip




# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Installation des dépendances PHP via Composer

RUN composer install --no-dev --optimize-autoloader --no-scripts --verbose

# Définir les permissions appropriées
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Copier les fichiers de l'application
COPY . /var/www/html

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier la configuration Nginx
COPY ./conf/nginx/nginx-site.conf /etc/nginx/conf.d/default.conf

# Exposer le port 80
EXPOSE 9000

# Démarrer PHP-FPM et Nginx
CMD service nginx start && php-fpm
