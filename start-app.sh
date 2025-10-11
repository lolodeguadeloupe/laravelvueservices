#!/bin/bash

# =============================================================================
# Script de Lancement - Plateforme de Services à Domicile
# =============================================================================
# Description: Lance l'application Laravel Vue Services avec Sail
# Auteur: Claude Code Assistant
# Usage: ./start-app.sh [options]
# =============================================================================

set -e  # Arrêter en cas d'erreur

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
PROJECT_NAME="Services à Domicile"
LOG_FILE="startup.log"
REQUIRED_PORTS=(3306 6379 8080 5173)

# =============================================================================
# FONCTIONS UTILITAIRES
# =============================================================================

print_header() {
    clear
    echo -e "${PURPLE}"
    echo "╔══════════════════════════════════════════════════════════════╗"
    echo "║                    🏠 SERVICES À DOMICILE 🏠                  ║"
    echo "║                  Script de Lancement Laravel                ║"
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
# VÉRIFICATIONS SYSTÈME
# =============================================================================

check_requirements() {
    print_step "Vérification des prérequis système..."
    
    # Vérifier Docker
    if ! command -v docker &> /dev/null; then
        print_error "Docker n'est pas installé. Installez Docker Desktop."
        exit 1
    fi
    print_success "Docker trouvé: $(docker --version | cut -d' ' -f3)"
    
    # Vérifier Docker Compose
    if ! docker compose version &> /dev/null; then
        print_error "Docker Compose n'est pas disponible."
        exit 1
    fi
    print_success "Docker Compose trouvé"
    
    # Vérifier Node.js
    if ! command -v node &> /dev/null; then
        print_error "Node.js n'est pas installé."
        exit 1
    fi
    print_success "Node.js trouvé: $(node --version)"
    
    # Vérifier npm
    if ! command -v npm &> /dev/null; then
        print_error "npm n'est pas installé."
        exit 1
    fi
    print_success "npm trouvé: $(npm --version)"
}

check_ports() {
    print_step "Vérification des ports requis..."
    
    for port in "${REQUIRED_PORTS[@]}"; do
        if lsof -i :$port &> /dev/null; then
            print_warning "Port $port déjà utilisé. L'application pourrait ne pas démarrer correctement."
        else
            print_success "Port $port disponible"
        fi
    done
}

# =============================================================================
# CONFIGURATION ENVIRONNEMENT
# =============================================================================

setup_environment() {
    print_step "Configuration de l'environnement..."
    
    # Copier .env si nécessaire
    if [ ! -f .env ]; then
        if [ -f .env.example ]; then
            cp .env.example .env
            print_success "Fichier .env créé depuis .env.example"
        else
            print_error "Fichier .env.example introuvable"
            exit 1
        fi
    else
        print_success "Fichier .env existant"
    fi
    
    # Configurer pour Sail/MySQL
    if grep -q "DB_CONNECTION=sqlite" .env; then
        print_step "Configuration MySQL pour Sail..."
        sed -i 's/DB_CONNECTION=sqlite/DB_CONNECTION=mysql/' .env
        sed -i 's/# DB_HOST=127.0.0.1/DB_HOST=mysql/' .env
        sed -i 's/# DB_PORT=3306/DB_PORT=3306/' .env
        sed -i 's/# DB_DATABASE=laravel/DB_DATABASE=laravelvueservices/' .env
        sed -i 's/# DB_USERNAME=root/DB_USERNAME=sail/' .env
        sed -i 's/# DB_PASSWORD=/DB_PASSWORD=password/' .env
        print_success "Configuration MySQL appliquée"
    fi
    
    # Ajouter variables Sail si absentes
    if ! grep -q "SAIL_" .env; then
        echo "" >> .env
        echo "# Sail Configuration" >> .env
        echo "APP_PORT=8080" >> .env
        echo "FORWARD_DB_PORT=3307" >> .env
        echo "FORWARD_REDIS_PORT=6380" >> .env
        echo "FORWARD_MAILPIT_PORT=1026" >> .env
        echo "FORWARD_MAILPIT_DASHBOARD_PORT=8026" >> .env
        print_success "Variables Sail ajoutées"
    fi
}

# =============================================================================
# GESTION DES CONTENEURS DOCKER
# =============================================================================

start_docker_services() {
    print_step "Démarrage des services Docker avec Sail..."
    
    # Vérifier si Docker est en cours d'exécution
    if ! docker info &> /dev/null; then
        print_error "Docker n'est pas en cours d'exécution. Démarrez Docker Desktop."
        exit 1
    fi
    
    # Créer le alias sail s'il n'existe pas
    if [ ! -f ./sail ]; then
        print_step "Création de l'alias Sail..."
        echo '#!/bin/bash' > ./sail
        echo 'docker run --rm \\' >> ./sail
        echo '    -u "$(id -u):$(id -g)" \\' >> ./sail
        echo '    -v $(pwd):/var/www/html \\' >> ./sail
        echo '    -w /var/www/html \\' >> ./sail
        echo '    laravelsail/php84-composer:latest \\' >> ./sail
        echo '    bash' >> ./sail
        chmod +x ./sail
        print_success "Alias Sail créé"
    fi
    
    # Démarrer les conteneurs
    print_step "Lancement des conteneurs Docker..."
    ./vendor/bin/sail up -d
    print_success "Conteneurs Docker démarrés"
    
    # Attendre que MySQL soit prêt
    print_step "Attente de la disponibilité de MySQL..."
    sleep 10
    for i in {1..30}; do
        if ./vendor/bin/sail artisan tinker --execute="DB::connection()->getPdo(); echo 'MySQL ready';" &> /dev/null; then
            print_success "MySQL est prêt"
            break
        fi
        if [ $i -eq 30 ]; then
            print_error "Timeout: MySQL n'est pas disponible après 5 minutes"
            exit 1
        fi
        sleep 10
        echo -n "."
    done
    echo
}

# =============================================================================
# INSTALLATION ET CONFIGURATION
# =============================================================================

install_dependencies() {
    print_step "Installation des dépendances..."
    
    # Dépendances Composer
    if [ ! -d "vendor" ] || [ ! -f "vendor/autoload.php" ]; then
        print_step "Installation des dépendances Composer..."
        ./vendor/bin/sail composer install --no-interaction --prefer-dist --optimize-autoloader
        print_success "Dépendances Composer installées"
    else
        print_success "Dépendances Composer déjà installées"
    fi
    
    # Dépendances Node.js
    if [ ! -d "node_modules" ]; then
        print_step "Installation des dépendances Node.js..."
        npm ci
        print_success "Dépendances Node.js installées"
    else
        print_success "Dépendances Node.js déjà installées"
    fi
}

setup_application() {
    print_step "Configuration de l'application Laravel..."
    
    # Générer la clé d'application
    if ! grep -q "APP_KEY=base64:" .env; then
        ./vendor/bin/sail artisan key:generate
        print_success "Clé d'application générée"
    else
        print_success "Clé d'application déjà configurée"
    fi
    
    # Exécuter les migrations
    print_step "Exécution des migrations..."
    ./vendor/bin/sail artisan migrate --force
    print_success "Migrations exécutées"
    
    # Vider le cache
    ./vendor/bin/sail artisan config:clear
    ./vendor/bin/sail artisan cache:clear
    ./vendor/bin/sail artisan view:clear
    ./vendor/bin/sail artisan route:clear
    print_success "Cache vidé"
    
    # Créer le lien de stockage
    ./vendor/bin/sail artisan storage:link
    print_success "Lien de stockage créé"
}

seed_demo_data() {
    print_step "Chargement des données de démonstration..."
    
    if ./vendor/bin/sail artisan db:seed --class=DemoDataSeeder --force 2>/dev/null; then
        print_success "Données de démonstration chargées"
    else
        print_warning "Impossible de charger les données de démonstration (normal si déjà chargées)"
    fi
}

# =============================================================================
# DÉMARRAGE DES SERVICES
# =============================================================================

start_development_servers() {
    print_step "Démarrage des serveurs de développement..."
    
    # Construire les assets une première fois
    print_step "Construction initiale des assets..."
    npm run build
    print_success "Assets construits"
    
    # Démarrer le queue worker en arrière-plan
    print_step "Démarrage du queue worker..."
    ./vendor/bin/sail artisan queue:work --daemon --tries=3 &
    QUEUE_PID=$!
    print_success "Queue worker démarré (PID: $QUEUE_PID)"
    
    # Enregistrer le PID pour pouvoir l'arrêter plus tard
    echo $QUEUE_PID > queue_worker.pid
    
    # Démarrer Vite en arrière-plan
    print_step "Démarrage de Vite (serveur de développement frontend)..."
    npm run dev &
    VITE_PID=$!
    print_success "Vite démarré (PID: $VITE_PID)"
    
    # Enregistrer le PID
    echo $VITE_PID > vite_dev.pid
}

# =============================================================================
# AFFICHAGE DES INFORMATIONS
# =============================================================================

show_application_info() {
    echo
    echo -e "${GREEN}╔══════════════════════════════════════════════════════════════╗"
    echo -e "║                    🎉 APPLICATION DÉMARRÉE 🎉                ║"
    echo -e "╚══════════════════════════════════════════════════════════════╝${NC}"
    echo
    echo -e "${WHITE}📱 Application principale:${NC}"
    echo -e "   ${CYAN}http://localhost:8080${NC}"
    echo
    echo -e "${WHITE}🔧 Services de développement:${NC}"
    echo -e "   ${BLUE}Frontend (Vite):${NC}     http://localhost:5173"
    echo -e "   ${BLUE}Base de données:${NC}     MySQL sur port 3307"
    echo -e "   ${BLUE}Cache Redis:${NC}        Port 6380"
    echo -e "   ${BLUE}Emails (Mailpit):${NC}   http://localhost:8026"
    echo
    echo -e "${WHITE}⚙️  Commandes utiles:${NC}"
    echo -e "   ${YELLOW}./vendor/bin/sail artisan tinker${NC}     - Console Laravel"
    echo -e "   ${YELLOW}./vendor/bin/sail artisan migrate${NC}    - Exécuter migrations"
    echo -e "   ${YELLOW}./vendor/bin/sail logs${NC}               - Voir les logs"
    echo -e "   ${YELLOW}./stop-app.sh${NC}                       - Arrêter l'application"
    echo
    echo -e "${WHITE}📊 Status des services:${NC}"
    
    # Vérifier les services
    if curl -s http://localhost:8080 > /dev/null; then
        echo -e "   ${GREEN}✅ Laravel Application${NC}"
    else
        echo -e "   ${RED}❌ Laravel Application${NC}"
    fi
    
    if curl -s http://localhost:5173 > /dev/null; then
        echo -e "   ${GREEN}✅ Vite Development Server${NC}"
    else
        echo -e "   ${YELLOW}⏳ Vite Development Server (démarrage...)${NC}"
    fi
    
    if ./vendor/bin/sail artisan tinker --execute="DB::connection()->getPdo(); echo 'OK';" &> /dev/null; then
        echo -e "   ${GREEN}✅ MySQL Database${NC}"
    else
        echo -e "   ${RED}❌ MySQL Database${NC}"
    fi
    
    echo
    echo -e "${GREEN}🚀 Votre plateforme de services à domicile est prête !${NC}"
    echo
}

# =============================================================================
# GESTION DES ERREURS ET NETTOYAGE
# =============================================================================

cleanup_on_exit() {
    echo
    print_info "Nettoyage en cours..."
    
    # Arrêter les processus en arrière-plan si nécessaire
    if [ -f queue_worker.pid ]; then
        QUEUE_PID=$(cat queue_worker.pid)
        if kill -0 $QUEUE_PID 2>/dev/null; then
            kill $QUEUE_PID
            print_success "Queue worker arrêté"
        fi
        rm -f queue_worker.pid
    fi
    
    if [ -f vite_dev.pid ]; then
        VITE_PID=$(cat vite_dev.pid)
        if kill -0 $VITE_PID 2>/dev/null; then
            kill $VITE_PID
            print_success "Vite arrêté"
        fi
        rm -f vite_dev.pid
    fi
}

trap cleanup_on_exit EXIT

# =============================================================================
# FONCTION PRINCIPALE
# =============================================================================

main() {
    print_header
    
    # Rediriger la sortie vers un fichier de log en plus de l'affichage
    exec > >(tee -a $LOG_FILE)
    exec 2>&1
    
    echo "=== Démarrage du script à $(date) ==="
    
    # Étapes d'initialisation
    check_requirements
    check_ports
    setup_environment
    
    # Démarrage Docker et services
    start_docker_services
    
    # Installation et configuration
    install_dependencies
    setup_application
    seed_demo_data
    
    # Démarrage des serveurs de développement
    start_development_servers
    
    # Attendre un peu que tout se stabilise
    sleep 5
    
    # Afficher les informations finales
    show_application_info
    
    echo "=== Script terminé avec succès à $(date) ==="
    
    # Garder le script actif pour maintenir les processus
    print_info "Appuyez sur Ctrl+C pour arrêter l'application..."
    
    # Boucle infinie pour maintenir le script actif
    while true; do
        sleep 1
    done
}

# =============================================================================
# EXÉCUTION
# =============================================================================

# Vérifier si le script est exécuté directement
if [[ "${BASH_SOURCE[0]}" == "${0}" ]]; then
    main "$@"
fi