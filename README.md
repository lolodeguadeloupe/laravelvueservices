# 🏠 Plateforme de Services à Domicile

Une plateforme moderne de mise en relation entre clients et prestataires de services à domicile, développée avec **Laravel 12** + **Vue 3** + **Inertia.js**.

## 🚀 Démarrage Rapide

### Prérequis
- **Docker Desktop** (avec Docker Compose)
- **Node.js** 20.x+
- **npm** ou **yarn**

### Installation et Lancement

```bash
# 🎯 Démarrage simple
./start-app.sh

# 🔄 Redémarrage
./restart-app.sh

# 🆕 Installation complète depuis zéro
./fresh-install.sh

# 🛑 Arrêt de l'application
./stop-app.sh
```

**C'est tout !** 🎉 L'application sera disponible sur http://localhost:8080

---

## 📋 Scripts de Gestion

### `./start-app.sh` - Lancement Principal
Lance l'application complète avec tous les services :
- ✅ Vérification des prérequis
- ✅ Configuration automatique (.env, MySQL)
- ✅ Démarrage Docker avec Sail
- ✅ Installation des dépendances
- ✅ Migrations et données de démo
- ✅ Serveurs de développement (Laravel + Vite)

**Fonctionnalités :**
- Interface graphique avec couleurs
- Vérification des ports disponibles
- Configuration MySQL automatique pour Sail
- Données de démonstration incluses
- Queue worker pour les notifications
- Hot reload frontend avec Vite

### `./stop-app.sh` - Arrêt Propre
Arrête tous les services en douceur :
- 🛑 Serveurs de développement
- 🛑 Conteneurs Docker
- 🧹 Nettoyage des fichiers temporaires

### `./restart-app.sh` - Redémarrage
Redémarre l'application complète :
```bash
./restart-app.sh          # Redémarrage normal avec pause
./restart-app.sh --quick   # Redémarrage rapide
```

### `./fresh-install.sh` - Installation Complète
Installation depuis zéro avec options :
```bash
./fresh-install.sh              # Installation complète avec démo
./fresh-install.sh --no-demo    # Sans données de démonstration
./fresh-install.sh --keep-database  # Garder la BDD existante
```

**⚠️ Attention :** Supprime toutes les données existantes !

---

## 🌐 Services et URLs

### Application Principale
- **Frontend :** http://localhost:8080
- **API Laravel :** http://localhost:8080/api

### Services de Développement
- **Vite (Hot Reload) :** http://localhost:5173
- **Mailpit (Emails) :** http://localhost:8026
- **Base de données :** MySQL sur port 3307
- **Redis :** Port 6380

### Comptes de Démonstration
```
👑 Admin : admin@example.com / password
👤 Client : client@example.com / password  
🔧 Prestataire : provider@example.com / password
```

---

## ⚙️ Commandes Utiles

### Laravel avec Sail
```bash
# Console Laravel
./vendor/bin/sail artisan tinker

# Migrations
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan migrate:fresh --seed

# Cache
./vendor/bin/sail artisan cache:clear
./vendor/bin/sail artisan config:clear

# Logs
./vendor/bin/sail logs
./vendor/bin/sail logs -f  # En continu
```

### Frontend
```bash
# Développement avec hot reload
npm run dev

# Build de production
npm run build

# Linting et formatting
npm run lint
npm run format
```

---

## 🏗️ Architecture Technique

### Stack Principal
- **Backend :** Laravel 12 (PHP 8.4)
- **Frontend :** Vue 3 + TypeScript + Inertia.js
- **Styling :** Tailwind CSS v4 + Reka UI
- **Base de données :** MySQL 8.0
- **Cache :** Redis
- **Queue :** Redis + Laravel Horizon

### Fonctionnalités Clés
- 🔐 **Authentification :** Laravel Fortify avec 2FA
- 💳 **Paiements :** Stripe avec Laravel Cashier
- 📧 **Notifications :** Email + Database + Queue
- 👥 **Rôles :** Client / Prestataire / Admin
- ⭐ **Avis :** Système bidirectionnel avec modération
- 📱 **Responsive :** Mobile-first design
- 🚀 **Performance :** Cache Redis + optimisations

---

## 🎯 Workflow de Développement

### 1. Démarrage Quotidien
```bash
./start-app.sh
# ➜ Ouvrir http://localhost:8080
# ➜ Développer avec hot reload automatique
```

### 2. Développement
- **Vite** lance automatiquement le hot reload
- **Modifications** en temps réel sans rechargement
- **TypeScript** + **ESLint** pour la qualité du code
- **Queue worker** traite les notifications

### 3. Arrêt Propre
```bash
./stop-app.sh
# ➜ Arrêt de tous les services
# ➜ Nettoyage automatique
```

---

## 🚀 Déploiement Production

Le projet est prêt pour le déploiement avec **Coolify** :
- `docker-compose.prod.yml` - Configuration production
- `Dockerfile.prod` - Image optimisée
- `deploy.sh` - Script de déploiement

---

## 🆘 Dépannage

### Problèmes Courants

**Port déjà utilisé :**
```bash
# Changer les ports dans .env
APP_PORT=8081
VITE_PORT=5174
```

**MySQL non accessible :**
```bash
# Redémarrer les conteneurs
./restart-app.sh
```

**Cache persistant :**
```bash
# Vider tous les caches
./vendor/bin/sail artisan optimize:clear
npm run build
```

---

## 📄 Licence

Ce projet est sous licence **MIT**.

---

**Bon développement !** 🚀
