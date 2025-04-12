FROM php:8.2-fpm

# Créer l'utilisateur et groupe www-data avant toute opération
RUN groupadd -g 1000 www-data && \
    useradd -u 1000 -ms /bin/bash -g www-data www-data

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

# Créer la structure de dossiers nécessaire
RUN mkdir -p /var/www/storage/framework/{cache,sessions,views} \
    /var/www/bootstrap/cache \
    /var/log/nginx \
    /var/lib/nginx

# Copier l'application
COPY . /var/www

# Définir les permissions avec vérification d'existence
RUN if [ -d "/var/www/storage" ]; then \
    chown -R www-data:www-data /var/www/storage \
    && chmod -R 775 /var/www/storage; \
    else echo "Avertissement: Dossier storage non trouvé"; exit 1; fi \
    && if [ -d "/var/www/bootstrap/cache" ]; then \
    chmod -R 775 /var/www/bootstrap/cache; \
    else echo "Avertissement: Dossier bootstrap/cache non trouvé"; exit 1; fi

WORKDIR /var/www

# Configuration Nginx
COPY ./conf/nginx/nginx-site.conf /etc/nginx/conf.d/default.conf

# Vérifier la configuration Nginx
RUN nginx -t

EXPOSE 80

CMD ["sh", "-c", "php-fpm --daemonize && nginx -g 'daemon off;'"]
