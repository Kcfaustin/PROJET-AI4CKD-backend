FROM richarvey/nginx-php-fpm:1.7.2

COPY . .

# Configuration de l'image
ENV SKIP_COMPOSER 1
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1

# Configuration de Laravel
ENV APP_ENV=production
ENV APP_DEBUG=false
ENV LOG_CHANNEL=stderr
ENV APP_NAME="NéphroSuivi"
ENV APP_URL=""
ENV DB_CONNECTION=mysql
ENV DB_HOST=127.0.0.1
ENV DB_PORT=3306
ENV DB_DATABASE=nom_de_la_base
ENV DB_USERNAME=utilisateur
ENV DB_PASSWORD=mot_de_passe



# Autoriser composer à s'exécuter en tant que superutilisateur
ENV COMPOSER_ALLOW_SUPERUSER 1

CMD ["/start.sh"]
