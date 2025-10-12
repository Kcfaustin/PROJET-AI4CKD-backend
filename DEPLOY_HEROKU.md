Guide de déploiement sur Heroku

Pré-requis

- Compte Heroku
- Heroku CLI installé
- Git initialisé et commit de la branche `main`

Étapes rapides

1) Ajouter le buildpack PHP (Heroku détecte normalement PHP par composer.json) :
   heroku buildpacks:set heroku/php

2) Créer une app Heroku :
   heroku create your-app-name

3) Configurer les variables d'environnement sur Heroku (remplacer les valeurs) :
   heroku config:set APP_ENV=production APP_DEBUG=false APP_KEY=base64:... APP_URL=https://your-app-name.herokuapp.com

   Variables importantes :
   - DB_CONNECTION (pgsql ou mysql) et les variables DB_* fournies par l'addon Heroku Postgres
   - MAIL_* si utilisé
   - SESSION_DRIVER=database
   
   Variables pour Swagger (IMPORTANT pour la documentation API) :
   heroku config:set L5_SWAGGER_GENERATE_ALWAYS=true
   heroku config:set L5_SWAGGER_USE_ABSOLUTE_PATH=true
   heroku config:set L5_SWAGGER_FORCE_HTTPS=false
   heroku config:set L5_SWAGGER_CONST_HOST=https://your-app-name.herokuapp.com/api

4) (Optionnel) Ajouter Heroku Postgres :
   heroku addons:create heroku-postgresql:hobby-dev

5) Déploiement automatique via GitHub (recommandé)

   Heroku propose une intégration GitHub qui déploie automatiquement la branche que vous choisissez (par ex. `main`). Pour l'activer :

   - Dans le dashboard Heroku, ouvrez votre app → Deploy → Deployment method → GitHub.
   - Connectez votre compte GitHub si nécessaire et recherchez le repository `Kcfaustin/PROJET-AI4CKD-backend`.
   - Sélectionnez la branche `main` et activez "Automatic deploys".

   Avec cette option, vous n'avez pas besoin d'exposer de token HEROKU_API_KEY dans les secrets GitHub pour déployer depuis Actions.

6) Après le premier build, exécuter les migrations/seeds (une seule fois) :
   heroku run php artisan migrate --force -a your-app-name
   heroku run php artisan db:seed --force -a your-app-name

7) (Optionnel) Lier le storage public :
   heroku run php artisan storage:link -a your-app-name

Notes

- Ce dépôt contient un `Procfile` configuré pour utiliser Apache et servir le dossier `public/`.
- Le fichier `composer.json` inclut des scripts `post-install-cmd` et `heroku-postbuild` utiles pour Heroku.
- Assurez-vous que `APP_KEY` est défini dans les config vars Heroku (générer localement avec `php artisan key:generate --show`).

Limitations

- Heroku a un filesystem éphémère. Pour les uploads persistants, configurez S3 et mettez `FILESYSTEM_DISK=s3`.
- Si vous utilisez Redis, ajoutez l'addon Redis et configurez `REDIS_URL`.

Aide

Si vous voulez, je peux automatiser la création des config vars recommandées (en listant celles présentes dans `.env.example`) ou ajouter des scripts pour une CI/CD GitHub Actions.
