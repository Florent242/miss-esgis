#!/bin/bash

# Script de déploiement pour la production
# Exécutez ce script sur le serveur de production après avoir poussé le code

echo "╔══════════════════════════════════════════════════════════════════════╗"
echo "║         🚀 DÉPLOIEMENT PRODUCTION - REINE ESGIS                      ║"
echo "╚══════════════════════════════════════════════════════════════════════╝"
echo ""

# Couleurs
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Fonction pour afficher les étapes
step() {
    echo -e "${GREEN}▶${NC} $1"
}

error() {
    echo -e "${RED}✗${NC} $1"
}

success() {
    echo -e "${GREEN}✓${NC} $1"
}

# Vérifier qu'on est bien dans le bon répertoire
if [ ! -f "artisan" ]; then
    error "Ce script doit être exécuté depuis la racine du projet Laravel"
    exit 1
fi

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
step "1. Mise en mode maintenance"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
php artisan down || error "Impossible de mettre en maintenance"

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
step "2. Pull du code depuis Git"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
git pull origin main || error "Erreur lors du pull Git"

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
step "3. Installation des dépendances Composer"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
composer install --no-dev --optimize-autoloader || error "Erreur Composer"

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
step "4. Migration de la base de données"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
php artisan migrate --force || error "Erreur lors de la migration"

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
step "5. Vérification de la configuration .env"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Vérifier les variables MTN MoMo
if ! grep -q "MTN_MOMO_API_KEY" .env; then
    echo -e "${YELLOW}⚠${NC} Variables MTN MoMo manquantes dans .env"
    echo ""
    echo "Ajoutez ces lignes dans votre fichier .env :"
    echo ""
    echo "# MTN MoMo Configuration"
    echo "MTN_MOMO_ENVIRONMENT=sandbox"
    echo "MTN_MOMO_SUBSCRIPTION_KEY=aa3d492186e2441fbfaeb684b09e02e8"
    echo "MTN_MOMO_API_USER=8a63ad08-0b36-4931-a448-f8d39f2f28d3"
    echo "MTN_MOMO_API_KEY=667b86fd4fae4a17817104ba82a5c876"
    echo "MTN_MOMO_CALLBACK_HOST=https://reine-esgis.com"
    echo ""
    echo "Puis relancez ce script."
    
    php artisan up
    exit 1
else
    success "Configuration MTN MoMo trouvée"
fi

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
step "6. Optimisation Laravel"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
step "7. Nettoyage du cache"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
php artisan cache:clear

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
step "8. Permissions des fichiers"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
step "9. Redémarrage des services"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Redémarrer PHP-FPM (ajustez selon votre version)
if systemctl list-units --type=service | grep -q "php8.2-fpm"; then
    sudo systemctl restart php8.2-fpm
elif systemctl list-units --type=service | grep -q "php8.1-fpm"; then
    sudo systemctl restart php8.1-fpm
elif systemctl list-units --type=service | grep -q "php8.0-fpm"; then
    sudo systemctl restart php8.0-fpm
fi

# Redémarrer Nginx
sudo systemctl restart nginx

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
step "10. Sortie du mode maintenance"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
php artisan up

echo ""
echo "╔══════════════════════════════════════════════════════════════════════╗"
echo "║         ✅ DÉPLOIEMENT TERMINÉ AVEC SUCCÈS !                         ║"
echo "╚══════════════════════════════════════════════════════════════════════╝"
echo ""
echo "🔍 Vérifications à faire :"
echo "   1. Testez le paiement sur https://reine-esgis.com"
echo "   2. Vérifiez les logs : tail -f storage/logs/laravel.log"
echo "   3. Testez l'API : curl https://reine-esgis.com/api/sandbox/operators"
echo ""
