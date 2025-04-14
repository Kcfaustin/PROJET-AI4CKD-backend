#!/usr/bin/env bash

set -eo pipefail

APP_DIR="/var/www/html"  # Correction du chemin selon votre structure Docker

echo "Installation des dépendances Composer"
composer install --no-dev --working-dir="$APP_DIR"

echo "→ Configuration des permissions"
chmod -R 775 "${APP_DIR}/storage" "${APP_DIR}/bootstrap/cache"
chown -R www-data:www-data "${APP_DIR}/storage"

#echo "Génération de la clé d'application"
#php artisan key:generate --force

echo "Mise en cache de la configuration"
php artisan config:cache

echo "Mise en cache des routes"
php artisan route:cache

echo "→ Optimisation de l'application"
php "$APP_DIR/artisan" view:cache

echo "→ Mise en place du lien de stockage"
php "$APP_DIR/artisan" storage:link --force

echo "Exécution des migrations"
php artisan migrate --force

echo "Exécution des seeders"
php artisan db:seed

echo "→ Réactivation des scripts Composer"
composer run-script post-autoload-dump --working-dir="$APP_DIR"

echo "✅ Déploiement terminé avec succès"
