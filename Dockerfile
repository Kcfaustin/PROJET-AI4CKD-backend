FROM php:8.2-fpm

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

# Copier les fichiers de l'application
COPY . /var/www

# Définir le répertoire de travail
WORKDIR /var/www

# Copier la configuration Nginx
COPY ./conf/nginx/nginx-site.conf /etc/nginx/conf.d/default.conf


# Exposer le port 80
EXPOSE 80

# Démarrer PHP-FPM et Nginx
CMD service nginx start && php-fpm
