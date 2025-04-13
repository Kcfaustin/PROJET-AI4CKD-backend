#!/usr/bin/env bash
set -eo pipefail

APP_DIR="/var/www/html"  # Correction du chemin selon votre structure Docker

echo "→ Installation des dépendances Composer (production)"
composer install \
    --no-interaction \
    --no-dev \
    --prefer-dist \
    --optimize-autoloader \
    --working-dir="$APP_DIR" \
    --no-scripts # Désactive temporairement les scripts post-install

echo "→ Configuration des permissions"
chmod -R 775 "${APP_DIR}/storage" "${APP_DIR}/bootstrap/cache"
chown -R www-data:www-data "${APP_DIR}/storage"

echo "→ Génération de la clé d'application"
php "$APP_DIR/artisan" key:generate

echo "→ Mise en place du lien de stockage"
php "$APP_DIR/artisan" storage:link --force

echo "→ Optimisation de l'application"
php "$APP_DIR/artisan" config:cache
php "$APP_DIR/artisan" route:cache
php "$APP_DIR/artisan" view:cache

echo "→ Exécution des migrations de base de données"
php "$APP_DIR/artisan" migrate --force --no-interaction

echo "→ Réactivation des scripts Composer"
composer run-script post-autoload-dump --working-dir="$APP_DIR"

echo "✅ Déploiement terminé avec succès"
