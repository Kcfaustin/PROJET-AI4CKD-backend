FROM richarvey/nginx-php-fpm:latest

COPY . .

# Configuration de l'image
ENV SKIP_COMPOSER 1
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1

# Configuration de Laravel
ENV LOG_CHANNEL stderr

# Autoriser Composer à s'exécuter en tant que superutilisateur
ENV COMPOSER_ALLOW_SUPERUSER 1

CMD ["/start.sh"]
