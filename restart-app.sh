#!/bin/bash

# =============================================================================
# Script de Redémarrage - Plateforme de Services à Domicile
# =============================================================================
# Description: Redémarre l'application Laravel Vue Services avec Sail
# Auteur: Claude Code Assistant
# Usage: ./restart-app.sh [options]
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
    clear
    echo -e "${YELLOW}"
    echo "╔══════════════════════════════════════════════════════════════╗"
    echo "║                  🔄 REDÉMARRAGE APPLICATION 🔄                ║"
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
# FONCTION PRINCIPALE
# =============================================================================

main() {
    print_header
    
    echo "=== Redémarrage de l'application à $(date) ==="
    
    # Vérifier que les scripts existent
    if [ ! -f "./stop-app.sh" ]; then
        print_error "Script stop-app.sh introuvable"
        exit 1
    fi
    
    if [ ! -f "./start-app.sh" ]; then
        print_error "Script start-app.sh introuvable"
        exit 1
    fi
    
    # Phase 1: Arrêt
    print_step "Phase 1: Arrêt de l'application..."
    echo "----------------------------------------"
    ./stop-app.sh
    
    # Pause entre arrêt et démarrage
    print_step "Pause de sécurité..."
    sleep 3
    
    # Phase 2: Démarrage
    print_step "Phase 2: Démarrage de l'application..."
    echo "----------------------------------------"
    ./start-app.sh
    
    echo "=== Redémarrage terminé à $(date) ==="
}

# =============================================================================
# OPTIONS DE LIGNE DE COMMANDE
# =============================================================================

show_help() {
    echo "Usage: $0 [OPTIONS]"
    echo
    echo "Redémarre l'application Laravel Vue Services"
    echo
    echo "OPTIONS:"
    echo "  -h, --help     Afficher cette aide"
    echo "  -q, --quick    Redémarrage rapide (sans pause)"
    echo
    echo "EXEMPLES:"
    echo "  $0             Redémarrage normal"
    echo "  $0 --quick     Redémarrage rapide"
    echo
}

# Analyse des arguments
QUICK_RESTART=false

while [[ $# -gt 0 ]]; do
    case $1 in
        -h|--help)
            show_help
            exit 0
            ;;
        -q|--quick)
            QUICK_RESTART=true
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
    if [ "$QUICK_RESTART" = true ]; then
        print_info "Mode redémarrage rapide activé"
        # Version rapide sans pause
        print_step "Arrêt rapide..."
        ./stop-app.sh > /dev/null 2>&1
        sleep 1
        print_step "Démarrage rapide..."
        ./start-app.sh
    else
        main "$@"
    fi
fi