# Utilisez une image officielle PHP-FPM avec Alpine (plus légère)
FROM php:8.2-fpm-alpine

# Installer les dépendances système nécessaires
RUN apk update && apk add --no-cache \
    nginx \
    supervisor \
    postgresql-dev \
    zip \
    unzip \
    git \
    curl

# Installer les extensions PHP nécessaires
RUN docker-php-ext-install pdo pdo_pgsql

# Créer la structure de répertoires correcte
RUN mkdir -p /var/www/html /run/nginx

# Copier TOUTE l'application dans /var/www (pas dans public!)
COPY . /var/www

# Définir le répertoire de travail correct
WORKDIR /var/www

# Installer Composer et les dépendances
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-dev --optimize-autoloader

# Configurer les permissions
RUN chown -R www-data:www-data /var/www/storage \
    && chmod -R 775 storage bootstrap/cache

# Copier la configuration Nginx
COPY ./conf/nginx/nginx-site.conf /etc/nginx/conf.d/default.conf

# Copier le fichier de configuration PHP
COPY ./conf/php/php.ini /usr/local/etc/php/conf.d/app.ini

# Exposer le port 80 pour Nginx
EXPOSE 80

# Commande de démarrage optimisée
CMD sh -c "php-fpm && nginx -g 'daemon off;'"
