#!/bin/bash

# =============================================================================
# Script d'Installation Complète - Plateforme de Services à Domicile
# =============================================================================
# Description: Installation complète depuis zéro avec Sail
# Auteur: Claude Code Assistant
# Usage: ./fresh-install.sh [options]
# =============================================================================

set -e

# Couleurs pour l'affichage
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
WHITE='\033[1;37m'
NC='\033[0m' # No Color

# Variables
INSTALL_DEMO_DATA=true
RESET_DATABASE=true

# =============================================================================
# FONCTIONS UTILITAIRES
# =============================================================================

print_header() {
    clear
    echo -e "${PURPLE}"
    echo "╔══════════════════════════════════════════════════════════════╗"
    echo "║                🚀 INSTALLATION COMPLÈTE 🚀                   ║"
    echo "║              Services à Domicile Laravel                    ║"
    echo "║                 Installation depuis zéro                    ║"
    echo "╚══════════════════════════════════════════════════════════════╝"
    echo -e "${NC}"
}

print_step() {
    echo -e "${CYAN}➤ $1${NC}"
}

print_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

print_info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

# =============================================================================
# FONCTIONS D'INSTALLATION
# =============================================================================

confirm_fresh_install() {
    echo -e "${YELLOW}"
    echo "⚠️  ATTENTION: Cette installation va:"
    echo "   • Supprimer toutes les données existantes"
    echo "   • Réinstaller toutes les dépendances"
    echo "   • Reconfigurer la base de données"
    echo "   • Recharger les données de démonstration"
    echo -e "${NC}"
    
    read -p "Êtes-vous sûr de vouloir continuer? [y/N] " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        print_info "Installation annulée"
        exit 0
    fi
}

stop_existing_services() {
    print_step "Arrêt des services existants..."
    
    if [ -f "./stop-app.sh" ]; then
        ./stop-app.sh > /dev/null 2>&1 || true
        print_success "Services existants arrêtés"
    else
        print_warning "Script stop-app.sh non trouvé, arrêt manuel..."
        # Arrêt manuel des processus
        pkill -f "vite" || true
        pkill -f "queue:work" || true
        pkill -f "npm run dev" || true
        docker compose down || true
    fi
}

clean_environment() {
    print_step "Nettoyage de l'environnement..."
    
    # Supprimer les dossiers générés
    print_info "Suppression des dossiers temporaires..."
    rm -rf node_modules/
    rm -rf vendor/
    rm -rf public/build/
    rm -rf storage/logs/*.log
    
    # Nettoyer les fichiers de cache
    rm -rf bootstrap/cache/*.php || true
    rm -rf storage/framework/cache/data/* || true
    rm -rf storage/framework/sessions/* || true
    rm -rf storage/framework/views/* || true
    
    # Supprimer les fichiers PID
    rm -f *.pid
    rm -f startup.log
    
    print_success "Environnement nettoyé"
}

setup_fresh_environment() {
    print_step "Configuration de l'environnement..."
    
    # Sauvegarder .env existant si présent
    if [ -f .env ]; then
        cp .env .env.backup.$(date +%Y%m%d_%H%M%S)
        print_info "Sauvegarde de .env créée"
    fi
    
    # Créer nouveau .env depuis example
    if [ -f .env.example ]; then
        cp .env.example .env
        print_success "Nouveau fichier .env créé"
    else
        print_error "Fichier .env.example introuvable"
        exit 1
    fi
    
    # Configuration pour Sail/MySQL
    print_step "Configuration MySQL pour Sail..."
    cat > .env << EOF
APP_NAME="Services à Domicile"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8080

APP_LOCALE=fr
APP_FALLBACK_LOCALE=fr
APP_FAKER_LOCALE=fr_FR

APP_MAINTENANCE_DRIVER=file
PHP_CLI_SERVER_WORKERS=4
BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

# Base de données MySQL avec Sail
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravelvueservices
DB_USERNAME=sail
DB_PASSWORD=password

# Cache et sessions Redis
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

# Configuration email
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@servicespro.local"
MAIL_FROM_NAME="Services à Domicile"

# Stripe (pour les paiements)
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...

# Configuration Sail
APP_PORT=8080
FORWARD_DB_PORT=3307
FORWARD_REDIS_PORT=6380
FORWARD_MAILPIT_PORT=1026
FORWARD_MAILPIT_DASHBOARD_PORT=8026
VITE_PORT=5173

# Configuration Vite
VITE_APP_NAME="Services à Domicile"
EOF
    
    print_success "Configuration .env créée"
}

install_fresh_dependencies() {
    print_step "Installation des dépendances depuis zéro..."
    
    # Vérifier Docker
    if ! docker info &> /dev/null; then
        print_error "Docker n'est pas en cours d'exécution"
        exit 1
    fi
    
    # Installer les dépendances avec Sail depuis une image temporaire
    print_step "Installation Composer avec Sail..."
    
    # Créer un conteneur temporaire pour Composer
    docker run --rm \
        -u "$(id -u):$(id -g)" \
        -v $(pwd):/var/www/html \
        -w /var/www/html \
        laravelsail/php84-composer:latest \
        composer install --ignore-platform-reqs --no-scripts
    
    print_success "Dépendances Composer installées"
    
    # Maintenant Sail est disponible, démarrer les conteneurs
    print_step "Démarrage des conteneurs Docker..."
    ./vendor/bin/sail up -d
    
    # Attendre que les services soient prêts
    print_step "Attente des services Docker..."
    sleep 15
    
    # Finaliser l'installation Composer
    ./vendor/bin/sail composer install
    print_success "Installation Composer finalisée"
    
    # Installer Node.js dependencies
    print_step "Installation des dépendances Node.js..."
    npm ci
    print_success "Dépendances Node.js installées"
}

setup_fresh_application() {
    print_step "Configuration de l'application Laravel..."
    
    # Générer la clé d'application
    ./vendor/bin/sail artisan key:generate
    print_success "Clé d'application générée"
    
    # Effacer tous les caches
    ./vendor/bin/sail artisan config:clear
    ./vendor/bin/sail artisan cache:clear
    ./vendor/bin/sail artisan view:clear
    ./vendor/bin/sail artisan route:clear
    print_success "Caches vidés"
    
    # Créer le lien de stockage
    ./vendor/bin/sail artisan storage:link
    print_success "Lien de stockage créé"
}

setup_fresh_database() {
    if [ "$RESET_DATABASE" = true ]; then
        print_step "Configuration de la base de données..."
        
        # Attendre que MySQL soit prêt
        print_info "Vérification de la disponibilité de MySQL..."
        for i in {1..30}; do
            if ./vendor/bin/sail artisan tinker --execute="DB::connection()->getPdo(); echo 'OK';" &> /dev/null; then
                print_success "MySQL est disponible"
                break
            fi
            if [ $i -eq 30 ]; then
                print_error "MySQL non disponible après 5 minutes"
                exit 1
            fi
            sleep 10
            echo -n "."
        done
        echo
        
        # Reset complet de la base de données
        print_step "Reset complet de la base de données..."
        ./vendor/bin/sail artisan migrate:fresh --force
        print_success "Base de données réinitialisée"
        
        # Installer les données de base (permissions, rôles)
        print_step "Installation des données de base..."
        ./vendor/bin/sail artisan db:seed --class=DatabaseSeeder --force
        print_success "Données de base installées"
        
        if [ "$INSTALL_DEMO_DATA" = true ]; then
            print_step "Installation des données de démonstration..."
            if ./vendor/bin/sail artisan db:seed --class=DemoDataSeeder --force; then
                print_success "Données de démonstration installées"
            else
                print_warning "Impossible d'installer les données de démonstration"
            fi
        fi
    fi
}

build_assets() {
    print_step "Construction des assets frontend..."
    
    # Build de production
    npm run build
    print_success "Assets construits"
}

# =============================================================================
# FONCTION PRINCIPALE
# =============================================================================

main() {
    print_header
    
    echo "=== Installation complète démarrée à $(date) ==="
    
    # Confirmation utilisateur
    confirm_fresh_install
    
    # Arrêt des services existants
    stop_existing_services
    
    # Nettoyage complet
    clean_environment
    
    # Configuration environnement
    setup_fresh_environment
    
    # Installation dépendances
    install_fresh_dependencies
    
    # Configuration application
    setup_fresh_application
    
    # Configuration base de données
    setup_fresh_database
    
    # Construction assets
    build_assets
    
    echo
    echo -e "${GREEN}╔══════════════════════════════════════════════════════════════╗"
    echo -e "║               🎉 INSTALLATION TERMINÉE ! 🎉                  ║"
    echo -e "╚══════════════════════════════════════════════════════════════╝${NC}"
    echo
    echo -e "${WHITE}🚀 Votre application est maintenant installée !${NC}"
    echo
    echo -e "${YELLOW}📋 Prochaines étapes:${NC}"
    echo -e "   1. ${CYAN}./start-app.sh${NC}     - Démarrer l'application"
    echo -e "   2. Ouvrir ${CYAN}http://localhost:8080${NC}"
    echo -e "   3. Créer votre premier compte admin"
    echo
    echo -e "${BLUE}💡 Comptes de démonstration disponibles:${NC}"
    echo -e "   • ${WHITE}Admin:${NC} admin@example.com / password"
    echo -e "   • ${WHITE}Client:${NC} client@example.com / password"
    echo -e "   • ${WHITE}Prestataire:${NC} provider@example.com / password"
    echo
    
    echo "=== Installation terminée avec succès à $(date) ==="
}

# =============================================================================
# OPTIONS DE LIGNE DE COMMANDE
# =============================================================================

show_help() {
    echo "Usage: $0 [OPTIONS]"
    echo
    echo "Installation complète de l'application Laravel Vue Services"
    echo
    echo "OPTIONS:"
    echo "  -h, --help        Afficher cette aide"
    echo "  --no-demo         Ne pas installer les données de démonstration"
    echo "  --keep-database   Garder la base de données existante"
    echo
    echo "EXEMPLES:"
    echo "  $0                Installation complète avec données de démo"
    echo "  $0 --no-demo      Installation sans données de démonstration"
    echo
}

# Analyse des arguments
while [[ $# -gt 0 ]]; do
    case $1 in
        -h|--help)
            show_help
            exit 0
            ;;
        --no-demo)
            INSTALL_DEMO_DATA=false
            shift
            ;;
        --keep-database)
            RESET_DATABASE=false
            shift
            ;;
        *)
            print_error "Option inconnue: $1"
            show_help
            exit 1
            ;;
    esac
done

# =============================================================================
# EXÉCUTION
# =============================================================================

if [[ "${BASH_SOURCE[0]}" == "${0}" ]]; then
    main "$@"
fi