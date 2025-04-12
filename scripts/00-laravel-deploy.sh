#!/usr/bin/env bash
set -eo pipefail

APP_DIR="/var/www" # Correction du chemin selon votre structure Docker

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
php artisan key:generate --force

echo "→ Mise en place du lien de stockage"
php artisan storage:link --force

echo "→ Optimisation de l'application"
php artisan config:cache && php artisan route:cache && php artisan view:cache

echo "→ Exécution des migrations de base de données"
php artisan migrate --force --no-interaction

echo "→ Réactivation des scripts Composer"
composer run-script post-autoload-dump --working-dir="$APP_DIR"

echo "✅ Déploiement terminé avec succès"
