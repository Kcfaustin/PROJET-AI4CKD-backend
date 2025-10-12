# Fix pour la Documentation Swagger sur Heroku

## Problème
La documentation API Swagger fonctionne en local mais affiche "Fetch error - Not Found" sur Heroku.

## Cause
Le fichier `api-docs.json` n'était pas généré lors du déploiement sur Heroku.

## Solutions Appliquées

### 1. Correction du fichier `post_compile`
Le fichier contenait une erreur de syntaxe qui empêchait l'exécution des commandes. Il a été corrigé pour exécuter directement les commandes nécessaires.

### 2. Correction de la configuration `l5-swagger.php`
La route `docs` doit inclure le paramètre `{jsonFile?}` pour permettre l'accès au fichier JSON :
```php
'routes' => [
    'api' => 'api/documentation',
    'docs' => 'docs/{jsonFile?}',  // IMPORTANT: Ne pas oublier {jsonFile?}
],
```

### 3. Configuration des Variables d'Environnement
Les variables suivantes **DOIVENT ABSOLUMENT** être configurées sur Heroku :

```bash
heroku config:set L5_SWAGGER_GENERATE_ALWAYS=true --app hackathonbackend-73ba5772822d
heroku config:set L5_SWAGGER_USE_ABSOLUTE_PATH=true --app hackathonbackend-73ba5772822d
heroku config:set L5_SWAGGER_FORCE_HTTPS=false --app hackathonbackend-73ba5772822d
heroku config:set L5_SWAGGER_CONST_HOST=https://hackathonbackend-73ba5772822d.herokuapp.com/api --app hackathonbackend-73ba5772822d
heroku config:set L5_SWAGGER_UI_ASSETS_PATH=vendor/swagger-api/swagger-ui/dist/ --app hackathonbackend-73ba5772822d
```

**OU** utilisez le script automatique :
```bash
bash scripts/configure-heroku-swagger.sh hackathonbackend-73ba5772822d
```

⚠️ **IMPORTANT** : Sans ces variables d'environnement sur Heroku, la documentation ne fonctionnera pas !

### 3. Redéploiement
Après avoir configuré les variables d'environnement, redéployez l'application :

```bash
git add .
git commit -m "Fix: Swagger documentation generation on Heroku"
git push heroku main
```

## Vérification

Une fois le déploiement terminé, vérifiez que :

1. **Le fichier api-docs.json est généré** :
   ```bash
   heroku run ls -la storage/api-docs/ --app hackathonbackend-73ba5772822d
   ```

2. **La documentation est accessible** :
   - URL de la documentation : https://hackathonbackend-73ba5772822d.herokuapp.com/api/documentation
   - URL du JSON : https://hackathonbackend-73ba5772822d.herokuapp.com/docs/api-docs.json

3. **Vérifiez les logs Heroku** :
   ```bash
   heroku logs --tail --app hackathonbackend-73ba5772822d
   ```
   Vous devriez voir : "-----> Generating Swagger documentation"

## Fichiers Modifiés

- ✅ `post_compile` - Corrigé pour exécuter les commandes directement
- ✅ `.env.heroku` - Créé avec les variables d'environnement nécessaires
- ✅ `DEPLOY_HEROKU.md` - Mis à jour avec les instructions Swagger
- ✅ `scripts/configure-heroku-swagger.sh` - Script de configuration automatique

## Notes Importantes

- Le fichier `api-docs.json` est généré automatiquement lors du déploiement grâce au script `post_compile`
- `L5_SWAGGER_FORCE_HTTPS=false` est nécessaire car Heroku gère le HTTPS en amont
- Le répertoire `storage/api-docs/` est créé automatiquement lors du build
