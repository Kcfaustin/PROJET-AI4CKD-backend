#!/usr/bin/env bash

# Script pour configurer les variables d'environnement Swagger sur Heroku
# Usage: ./scripts/configure-heroku-swagger.sh your-app-name

if [ -z "$1" ]; then
    echo "Usage: $0 <heroku-app-name>"
    echo "Example: $0 hackathonbackend-73ba5772822d"
    exit 1
fi

APP_NAME=$1
APP_URL="https://${APP_NAME}.herokuapp.com"

echo "🔧 Configuration de Swagger pour l'application Heroku: $APP_NAME"
echo "📍 URL de l'application: $APP_URL"
echo ""

echo "⚙️  Configuration des variables d'environnement Swagger..."

heroku config:set \
    L5_SWAGGER_GENERATE_ALWAYS=true \
    L5_SWAGGER_USE_ABSOLUTE_PATH=true \
    L5_SWAGGER_FORCE_HTTPS=false \
    L5_SWAGGER_CONST_HOST="${APP_URL}/api" \
    --app "$APP_NAME"

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ Configuration Swagger terminée avec succès!"
    echo ""
    echo "📝 Prochaines étapes:"
    echo "1. Redéployez votre application (git push heroku main)"
    echo "2. Le script post_compile générera automatiquement la documentation"
    echo "3. Accédez à la documentation: ${APP_URL}/api/documentation"
    echo ""
else
    echo ""
    echo "❌ Erreur lors de la configuration. Vérifiez que:"
    echo "   - Heroku CLI est installé"
    echo "   - Vous êtes connecté (heroku login)"
    echo "   - Le nom de l'application est correct"
    echo ""
    exit 1
fi
