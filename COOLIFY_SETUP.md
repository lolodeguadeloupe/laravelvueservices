# Guide de Configuration Coolify - ServicesPro

## 🎯 Vue d'ensemble

Ce guide détaille les étapes exactes pour déployer ServicesPro sur Coolify avec la configuration Docker optimisée.

## ✅ Prérequis

- [ ] Repository Git accessible par Coolify
- [ ] Coolify v4+ installé et configuré
- [ ] Domaine DNS pointant vers le serveur Coolify
- [ ] Certificats SSL (automatique avec Let's Encrypt)

## 📋 Liste de Vérification Avant Déploiement

### Fichiers Présents
- [ ] `docker-compose.prod.yml` - Configuration des services
- [ ] `Dockerfile.prod` - Image Docker optimisée
- [ ] `deploy.sh` - Script de déploiement automatisé
- [ ] `.dockerignore` - Optimisation du build
- [ ] `.env.prod.example` - Template des variables
- [ ] `docker/nginx.conf` - Configuration Nginx
- [ ] `docker/supervisord.conf` - Supervision des processus

### Code Prêt
- [ ] Code commité et pushé sur la branche principale
- [ ] Tests passants (si applicable)
- [ ] Assets frontend buildés localement au moins une fois

---

## 🚀 Configuration Coolify - Étapes Détaillées

### Étape 1 : Création du Projet

1. **Connecter le Repository**
   - Dans Coolify, cliquer sur "New Resource"
   - Sélectionner "Docker Compose"
   - Connecter votre repository Git
   - Choisir la branche `main` (ou votre branche de production)

2. **Configuration Docker Compose**
   - **Compose File** : `docker-compose.prod.yml`
   - **Build Context** : `/` (racine du projet)
   - **Auto-deploy** : Activé pour les pushs

### Étape 2 : Variables d'Environnement

Copier ces variables depuis `.env.prod.example` et les adapter :

#### 🔧 Application (Obligatoire)
```env
APP_NAME=ServicesPro
APP_ENV=production
APP_DEBUG=false
APP_URL=https://votre-domaine.com
APP_LOCALE=fr
```

#### 🗄️ Base de Données (Obligatoire)
```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=servicespro
DB_USERNAME=servicespro
DB_PASSWORD=votre_mot_de_passe_securise
```

#### ⚡ Cache & Performance (Obligatoire)
```env
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_HOST=redis
```

#### 🚨 Premier Déploiement (Important)
```env
INITIAL_DEPLOY=true
```
> ⚠️ **À supprimer après le premier déploiement réussi**

#### 💳 Paiements Stripe (Optionnel)
```env
STRIPE_KEY=pk_live_...
STRIPE_SECRET=sk_live_...
STRIPE_WEBHOOK_SECRET=whsec_...
```

#### 📧 Email (Optionnel)
```env
MAIL_MAILER=smtp
MAIL_HOST=votre-smtp
MAIL_PORT=587
MAIL_USERNAME=votre-email
MAIL_PASSWORD=votre-mot-de-passe
MAIL_FROM_ADDRESS=noreply@votre-domaine.com
```

### Étape 3 : Configuration du Domaine

1. **Ajouter le Domaine**
   - Dans les paramètres du projet Coolify
   - Section "Domains"
   - Ajouter `votre-domaine.com`

2. **Configuration SSL**
   - Activer "Generate SSL Certificate"
   - Choisir "Let's Encrypt"
   - Vérifier que le DNS pointe vers le serveur

3. **Redirection HTTPS**
   - Activer "Force HTTPS"
   - Activer "Include WWW"

### Étape 4 : Configuration du Proxy

1. **Port d'Exposition**
   - Container : `servicespro-app`
   - Port : `80`
   - Protocol : `HTTP`

2. **Health Check**
   - URL : `/up`
   - Interval : `30s`
   - Timeout : `10s`

---

## 🔄 Processus de Déploiement

### Premier Déploiement

1. **Lancer le Build**
   - Cliquer sur "Deploy" dans Coolify
   - Surveiller les logs de build
   - Vérifier que tous les services démarrent

2. **Vérification des Services**
   ```bash
   # Vérifier que tous les containers sont up
   docker ps | grep servicespro
   
   # Logs du container principal
   docker logs servicespro-app
   
   # Test de connectivité base de données
   docker exec servicespro-app php artisan tinker
   # Dans tinker : DB::connection()->getPdo();
   ```

3. **Post-Déploiement**
   - Tester l'application sur `https://votre-domaine.com`
   - Vérifier les logs d'erreur
   - **IMPORTANT** : Supprimer `INITIAL_DEPLOY=true` des variables

### Déploiements Suivants

Les déploiements sont automatiques lors des pushs Git :
1. Push sur la branche principale
2. Coolify détecte les changements
3. Build automatique des images
4. Redéploiement sans interruption

---

## 📊 Monitoring & Maintenance

### Logs Disponibles

1. **Application Laravel**
   ```bash
   docker logs servicespro-app
   docker exec servicespro-app tail -f storage/logs/laravel.log
   ```

2. **Queue Worker**
   ```bash
   docker logs servicespro-queue
   ```

3. **Base de Données**
   ```bash
   docker logs servicespro-mysql
   ```

### Health Checks

- **Application** : `https://votre-domaine.com/up`
- **Status Database** : Vérifié automatiquement par Docker
- **Status Redis** : Vérifié automatiquement par Docker

### Commandes de Maintenance

```bash
# Artisan via Docker
docker exec servicespro-app php artisan migrate
docker exec servicespro-app php artisan cache:clear
docker exec servicespro-app php artisan queue:restart

# Backup base de données
docker exec servicespro-mysql mysqldump -u servicespro -p servicespro > backup.sql
```

---

## 🛠️ Dépannage

### Problèmes Courants

1. **Erreur "APP_KEY not set"**
   - Solution : Redéployer, la clé sera générée automatiquement

2. **Erreur de connexion base de données**
   - Vérifier les variables `DB_*`
   - Vérifier que le container MySQL est healthy

3. **Assets CSS/JS manquants**
   - Vérifier que `npm run build` s'exécute dans le Dockerfile
   - Contrôler les permissions du dossier `public/build/`

4. **Queue ne traite pas les jobs**
   - Vérifier que le container `queue` fonctionne
   - Redémarrer : `docker restart servicespro-queue`

### Commandes de Debug

```bash
# Entrer dans le container
docker exec -it servicespro-app sh

# Vérifier la configuration Laravel
php artisan config:show

# Tester la base de données
php artisan tinker
>>> DB::connection()->getPdo()

# Vérifier les routes
php artisan route:list
```

---

## 🔒 Sécurité

### Variables Sensibles
- Toujours utiliser des mots de passe forts
- Régénérer `APP_KEY` en production
- Configurer Stripe en mode live uniquement en production
- Utiliser HTTPS obligatoirement

### Sauvegardes
- Base de données : Backup automatique recommandé
- Volumes : Snapshot des volumes Docker
- Code : Repository Git comme source de vérité

---

## 📞 Support

En cas de problème :
1. Consulter les logs Coolify
2. Vérifier les logs des containers Docker
3. Consulter la documentation Laravel/Inertia
4. Tester en local avec `docker-compose.prod.yml`

---

## ✅ Checklist Post-Déploiement

- [ ] Application accessible via HTTPS
- [ ] SSL certificat valide
- [ ] Base de données connectée
- [ ] Cache Redis fonctionnel
- [ ] Queue worker actif
- [ ] Emails de test envoyés (si configuré)
- [ ] Paiements de test Stripe (si configuré)
- [ ] Suppression de `INITIAL_DEPLOY=true`
- [ ] Monitoring configuré
- [ ] Sauvegardes programmées