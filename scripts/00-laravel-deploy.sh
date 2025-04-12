#!/usr/bin/env bash
set -e

echo "Exécution de composer"
if ! composer install --no-dev --optimize-autoloader --working-dir=/var/www/html; then
    echo "Échec de l'installation des dépendances Composer"
    exit 1
fi

echo "Mise en cache de la configuration..."
if ! php artisan config:cache; then
    echo "Échec de la mise en cache de la configuration"
    exit 1
fi

echo "Mise en cache des routes..."
if ! php artisan route:cache; then
    echo "Échec de la mise en cache des routes"
    exit 1
fi

echo "Exécution des migrations..."
if ! php artisan migrate --force; then
    echo "Échec de l'exécution des migrations"
    exit 1
fi

echo "Déploiement terminé avec succès."
