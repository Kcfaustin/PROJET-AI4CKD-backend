FROM php:8.2-fpm

# Installer les dépendances
RUN apt-get update && apt-get install -y \
    nginx \
    git \
    unzip \
    curl \
    libpq-dev \
    libzip-dev \
    zip \
    && docker-php-ext-install pdo pdo_pgsql zip opcache \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Créer la structure de dossiers nécessaire pour Nginx
RUN mkdir -p /var/log/nginx /var/lib/nginx \
    && chown -R www-data:www-data /var/log/nginx /var/lib/nginx

# Copier l'application
COPY . /var/www

# Définir les permissions
RUN chown -R www-data:www-data /var/www/storage \
    && chmod -R 775 storage bootstrap/cache

WORKDIR /var/www

# Copier la configuration Nginx
COPY ./conf/nginx/nginx-site.conf /etc/nginx/conf.d/default.conf

# Vérifier la configuration Nginx
RUN nginx -t

# Commande de démarrage corrigée
CMD bash -c "php-fpm --daemonize && nginx -g 'daemon off;'"
