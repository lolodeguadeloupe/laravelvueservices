# ServicesPro - Guide de Déploiement Coolify

## 🎯 Vue d'ensemble

ServicesPro est une plateforme de services à domicile construite avec Laravel 12 + Vue 3 + Inertia.js, optimisée pour le déploiement avec **Coolify**.

## 🚀 Déploiement avec Coolify

### 1. Prérequis Coolify

- Coolify v4+ installé
- Serveur avec Docker et Docker Compose
- Domaine configuré avec SSL

### 2. Configuration du Projet

1. **Créer un nouveau projet** dans Coolify
2. **Sélectionner "Docker Compose"** comme méthode de déploiement
3. **Repository Git** : votre repository contenant ce code
4. **Fichier Compose** : `docker-compose.prod.yml`

### 3. Variables d'Environnement

Configurez ces variables dans Coolify (à partir de `.env.prod.example`) :

#### 🔧 Application
```env
APP_NAME=ServicesPro
APP_ENV=production
APP_KEY=base64:... # Généré automatiquement
APP_DEBUG=false
APP_URL=https://votre-domaine.com
```

#### 🗄️ Base de Données
```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_DATABASE=servicespro
DB_USERNAME=servicespro
DB_PASSWORD=mot_de_passe_sécurisé
```

#### ⚡ Performance & Cache
```env
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_HOST=redis
```

#### 💳 Paiements (Stripe)
```env
STRIPE_KEY=pk_live_...
STRIPE_SECRET=sk_live_...
STRIPE_WEBHOOK_SECRET=whsec_...
```

#### 📧 Email (SMTP)
```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email@domain.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
```

### 4. Configuration du Domaine

1. **Ajouter votre domaine** dans Coolify
2. **Activer SSL automatique** (Let's Encrypt)
3. **Configurer le proxy** vers le port 80 du container `app`

### 5. Base de Données

#### Option A : Utiliser MySQL de Coolify
1. Créer une **base MySQL** dans Coolify
2. Utiliser les variables générées par Coolify
3. Le container `mysql` dans docker-compose sera ignoré

#### Option B : Utiliser le container MySQL inclus
1. Les variables DB_* pointent vers le container `mysql`
2. Les données sont persistées dans le volume `mysql-data`

### 6. Déploiement Initial

1. **Premier déploiement** :
   - Ajouter `INITIAL_DEPLOY=true` dans les variables
   - Cela exécutera les seeders de base de données

2. **Déploiements suivants** :
   - Supprimer ou mettre `INITIAL_DEPLOY=false`

## 🏗️ Architecture de Production

### Services Docker

- **app** : Application Laravel principal (port 80)
- **queue** : Worker pour les tâches en arrière-plan
- **scheduler** : Cron jobs Laravel
- **mysql** : Base de données MySQL 8.0
- **redis** : Cache et sessions

### Optimisations Incluses

✅ **Performance** :
- OpCache activé
- Configuration optimisée
- Cache Redis pour sessions/cache
- Assets buildés et minifiés

✅ **Sécurité** :
- HTTPS forcé
- Cookies sécurisés
- Variables d'environnement protégées
- Permissions fichiers correctes

✅ **Monitoring** :
- Health checks sur tous les services
- Restart automatique en cas d'échec
- Logs centralisés

✅ **Scalabilité** :
- Workers de queue séparés
- Sessions en Redis
- Cache distribué

## 🔄 Workflow de Déploiement

1. **Push sur GitHub** → Coolify détecte les changements
2. **Build automatique** → Construction des images Docker
3. **Deploy script** → `deploy.sh` exécute :
   - Installation des dépendances
   - Build des assets frontend
   - Migrations de base de données
   - Optimisations Laravel
   - Configuration des permissions

4. **Validation** → Health checks vérifient le bon fonctionnement

## 📊 Monitoring & Logs

### Logs Disponibles
- **Application** : `/var/www/html/storage/logs/laravel.log`
- **Nginx** : Logs de proxy Coolify
- **MySQL** : Logs de base de données
- **Queue** : Logs des workers

### Health Checks
- **App** : `GET /up` (Laravel health check)
- **MySQL** : `mysqladmin ping`
- **Redis** : `redis-cli ping`

## 🛠️ Maintenance

### Commandes Utiles
```bash
# Artisan via Docker
docker exec -it app-container php artisan queue:work

# Logs en temps réel
docker logs -f app-container

# Backup base de données
docker exec mysql-container mysqldump -u root -p servicespro > backup.sql
```

### Mise à jour
1. **Code** : Push vers GitHub → déploiement automatique
2. **Dependencies** : Mis à jour automatiquement lors du build
3. **Base de données** : Migrations automatiques via `deploy.sh`

## 🚨 Troubleshooting

### Problèmes Courants

1. **App Key manquante** :
   - Vérifier que `APP_KEY` est généré
   - Redéployer si nécessaire

2. **Erreurs de base de données** :
   - Vérifier la connectivité entre containers
   - Contrôler les variables DB_*

3. **Assets manquants** :
   - Vérifier que `npm run build` s'exécute correctement
   - Contrôler les permissions du dossier `public/build/`

4. **Queue non traitée** :
   - Vérifier que le container `queue` fonctionne
   - Contrôler la connexion Redis

### Support
En cas de problème, vérifiez :
1. Les logs Coolify
2. Les logs des containers Docker
3. La documentation Laravel/Inertia/Vue