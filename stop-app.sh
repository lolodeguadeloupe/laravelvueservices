#!/bin/bash

# =============================================================================
# Script d'Arrêt - Plateforme de Services à Domicile
# =============================================================================
# Description: Arrête tous les services de l'application Laravel Vue Services
# Auteur: Claude Code Assistant
# Usage: ./stop-app.sh [options]
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

# =============================================================================
# FONCTIONS UTILITAIRES
# =============================================================================

print_header() {
    echo -e "${RED}"
    echo "╔══════════════════════════════════════════════════════════════╗"
    echo "║                    🛑 ARRÊT DE L'APPLICATION 🛑               ║"
    echo "║                  Services à Domicile Laravel                ║"
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
# FONCTIONS D'ARRÊT
# =============================================================================

stop_development_servers() {
    print_step "Arrêt des serveurs de développement..."
    
    # Arrêter Vite si il tourne
    if [ -f vite_dev.pid ]; then
        VITE_PID=$(cat vite_dev.pid)
        if kill -0 $VITE_PID 2>/dev/null; then
            kill $VITE_PID
            print_success "Vite arrêté (PID: $VITE_PID)"
        else
            print_warning "Vite n'était pas en cours d'exécution"
        fi
        rm -f vite_dev.pid
    else
        # Essayer de trouver et arrêter les processus Vite
        VITE_PIDS=$(pgrep -f "vite" || true)
        if [ ! -z "$VITE_PIDS" ]; then
            echo $VITE_PIDS | xargs kill 2>/dev/null || true
            print_success "Processus Vite arrêtés"
        fi
    fi
    
    # Arrêter le queue worker si il tourne
    if [ -f queue_worker.pid ]; then
        QUEUE_PID=$(cat queue_worker.pid)
        if kill -0 $QUEUE_PID 2>/dev/null; then
            kill $QUEUE_PID
            print_success "Queue worker arrêté (PID: $QUEUE_PID)"
        else
            print_warning "Queue worker n'était pas en cours d'exécution"
        fi
        rm -f queue_worker.pid
    else
        # Essayer de trouver et arrêter les processus queue:work
        QUEUE_PIDS=$(pgrep -f "queue:work" || true)
        if [ ! -z "$QUEUE_PIDS" ]; then
            echo $QUEUE_PIDS | xargs kill 2>/dev/null || true
            print_success "Processus queue worker arrêtés"
        fi
    fi
    
    # Arrêter d'autres processus npm/node en cours
    NPM_PIDS=$(pgrep -f "npm run dev" || true)
    if [ ! -z "$NPM_PIDS" ]; then
        echo $NPM_PIDS | xargs kill 2>/dev/null || true
        print_success "Processus npm arrêtés"
    fi
}

stop_docker_services() {
    print_step "Arrêt des conteneurs Docker..."
    
    # Vérifier si Sail est disponible
    if [ -f "./vendor/bin/sail" ]; then
        ./vendor/bin/sail down
        print_success "Conteneurs Sail arrêtés"
    elif [ -f "./sail" ]; then
        ./sail down
        print_success "Conteneurs Sail arrêtés"
    else
        # Fallback vers docker compose direct
        if [ -f "docker-compose.yml" ]; then
            docker compose down
            print_success "Conteneurs Docker arrêtés"
        else
            print_warning "Aucun fichier docker-compose.yml trouvé"
        fi
    fi
}

cleanup_files() {
    print_step "Nettoyage des fichiers temporaires..."
    
    # Supprimer les fichiers PID
    rm -f vite_dev.pid queue_worker.pid
    
    # Supprimer les logs temporaires
    rm -f startup.log
    
    print_success "Fichiers temporaires nettoyés"
}

# =============================================================================
# FONCTION PRINCIPALE
# =============================================================================

main() {
    print_header
    
    echo "=== Arrêt de l'application à $(date) ==="
    
    # Arrêter les serveurs de développement
    stop_development_servers
    
    # Arrêter les conteneurs Docker
    stop_docker_services
    
    # Nettoyer les fichiers temporaires
    cleanup_files
    
    echo
    echo -e "${GREEN}╔══════════════════════════════════════════════════════════════╗"
    echo -e "║                  ✅ APPLICATION ARRÊTÉE ✅                    ║"
    echo -e "╚══════════════════════════════════════════════════════════════╝${NC}"
    echo
    echo -e "${WHITE}📊 Services arrêtés:${NC}"
    echo -e "   ${GREEN}✅ Serveurs de développement (Vite, Queue)${NC}"
    echo -e "   ${GREEN}✅ Conteneurs Docker (MySQL, Redis, etc.)${NC}"
    echo -e "   ${GREEN}✅ Fichiers temporaires nettoyés${NC}"
    echo
    echo -e "${BLUE}💡 Pour redémarrer l'application:${NC}"
    echo -e "   ${CYAN}./start-app.sh${NC}"
    echo
    echo -e "${BLUE}💡 Pour une installation complète:${NC}"
    echo -e "   ${CYAN}./fresh-install.sh${NC}"
    echo
    
    echo "=== Arrêt terminé à $(date) ==="
}

# =============================================================================
# EXÉCUTION
# =============================================================================

if [[ "${BASH_SOURCE[0]}" == "${0}" ]]; then
    main "$@"
fi