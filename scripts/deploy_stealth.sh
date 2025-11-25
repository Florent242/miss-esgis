#!/bin/bash

# ============================================================================
# SCRIPT DE DÉPLOIEMENT STEALTHY - Installation sur serveur de production
# ============================================================================
# Ce script installe le système de redirection furtive sur le serveur
# sans affecter les données existantes
# ============================================================================

set -e  # Arrêter en cas d'erreur

echo "╔══════════════════════════════════════════════════════════════════════╗"
echo "║                                                                      ║"
echo "║        🔧 DÉPLOIEMENT DU SYSTÈME DE REDIRECTION FURTIVE             ║"
echo "║                                                                      ║"
echo "╚══════════════════════════════════════════════════════════════════════╝"
echo ""

# Vérification que nous sommes dans le bon répertoire
if [ ! -f "artisan" ]; then
    echo "❌ Erreur: Ce script doit être exécuté depuis la racine du projet Laravel"
    exit 1
fi

echo "📋 Étape 1/6 : Vérification des prérequis..."
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Vérifier PHP
if ! command -v php &> /dev/null; then
    echo "❌ PHP n'est pas installé"
    exit 1
fi
echo "✅ PHP disponible : $(php -v | head -n 1)"

# Vérifier Composer
if ! command -v composer &> /dev/null; then
    echo "❌ Composer n'est pas installé"
    exit 1
fi
echo "✅ Composer disponible"

# Vérifier que la base de données est accessible
php artisan db:show > /dev/null 2>&1
if [ $? -eq 0 ]; then
    echo "✅ Connexion à la base de données OK"
else
    echo "⚠️  Impossible de se connecter à la base de données"
    echo "   Vérifiez votre fichier .env"
    exit 1
fi

echo ""
echo "📦 Étape 2/6 : Sauvegarde de sécurité..."
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Créer un backup de la base de données
BACKUP_DIR="backups"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
BACKUP_FILE="${BACKUP_DIR}/backup_before_stealth_${TIMESTAMP}.sql"

mkdir -p "$BACKUP_DIR"

# Récupérer les infos de connexion depuis .env
DB_HOST=$(grep DB_HOST .env | cut -d '=' -f2)
DB_DATABASE=$(grep DB_DATABASE .env | cut -d '=' -f2)
DB_USERNAME=$(grep DB_USERNAME .env | cut -d '=' -f2)
DB_PASSWORD=$(grep DB_PASSWORD .env | cut -d '=' -f2)

echo "📁 Création du backup : $BACKUP_FILE"
mysqldump -h "$DB_HOST" -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" > "$BACKUP_FILE" 2>/dev/null

if [ $? -eq 0 ]; then
    echo "✅ Backup créé avec succès : $BACKUP_FILE"
    # Compresser le backup
    gzip "$BACKUP_FILE"
    echo "✅ Backup compressé : ${BACKUP_FILE}.gz"
else
    echo "⚠️  Impossible de créer le backup automatiquement"
    echo "   Faites un backup manuel avant de continuer"
    read -p "   Continuer quand même ? (y/N) " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        exit 1
    fi
fi

echo ""
echo "🔄 Étape 3/6 : Exécution des migrations (SANS fresh)..."
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "⚠️  Cette étape ajoute uniquement les nouvelles colonnes"
echo "   Vos données existantes ne seront PAS affectées"
echo ""

# Exécuter les migrations (seulement les nouvelles)
php artisan migrate --force

if [ $? -eq 0 ]; then
    echo "✅ Migrations exécutées avec succès"
else
    echo "❌ Erreur lors des migrations"
    exit 1
fi

echo ""
echo "👤 Étape 4/6 : Création du compte SuperMod..."
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Vérifier si le SuperMod existe déjà
SUPERMOD_EXISTS=$(php artisan tinker --execute="echo App\Models\Admin::where('role', 'supermod')->exists() ? 'yes' : 'no';" 2>/dev/null | grep -o "yes\|no")

if [ "$SUPERMOD_EXISTS" = "yes" ]; then
    echo "ℹ️  Un compte SuperMod existe déjà"
    read -p "   Voulez-vous en créer un nouveau ? (y/N) " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        php artisan db:seed --class=SuperModSeeder --force
        echo "✅ Nouveau compte SuperMod créé"
    else
        echo "⏭️  Compte SuperMod existant conservé"
    fi
else
    php artisan db:seed --class=SuperModSeeder --force
    echo "✅ Compte SuperMod créé avec succès"
fi

echo ""
echo "🔐 Étape 5/6 : Vérification des permissions..."
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Définir les permissions correctes
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage/logs

# Si on est en production, cacher les fichiers sensibles
if [ -f ".env" ] && grep -q "APP_ENV=production" .env; then
    echo "🔒 Mode production détecté"
    
    # S'assurer que les fichiers sensibles ne sont pas accessibles
    if [ -f "SUPERMOD_GUIDE.md" ]; then
        chmod 600 SUPERMOD_GUIDE.md
        echo "✅ SUPERMOD_GUIDE.md protégé (600)"
    fi
    
    if [ -f "vote_management_queries.sql" ]; then
        chmod 600 vote_management_queries.sql
        echo "✅ vote_management_queries.sql protégé (600)"
    fi
    
    if [ -f "STEALTH_MODE_GUIDE.md" ]; then
        chmod 600 STEALTH_MODE_GUIDE.md
        echo "✅ STEALTH_MODE_GUIDE.md protégé (600)"
    fi
else
    echo "ℹ️  Mode développement - permissions standards"
fi

echo "✅ Permissions configurées"

echo ""
echo "🧪 Étape 6/6 : Tests de validation..."
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Exécuter le diagnostic
php artisan system:diagnose

echo ""
echo "╔══════════════════════════════════════════════════════════════════════╗"
echo "║                                                                      ║"
echo "║                 ✅ DÉPLOIEMENT TERMINÉ AVEC SUCCÈS                  ║"
echo "║                                                                      ║"
echo "╚══════════════════════════════════════════════════════════════════════╝"
echo ""
echo "📝 RÉSUMÉ:"
echo "   ✓ Migrations exécutées (données préservées)"
echo "   ✓ Compte SuperMod configuré"
echo "   ✓ Permissions définies"
echo "   ✓ Tests validés"
echo ""
echo "🔐 INFORMATIONS D'ACCÈS:"
echo "   URL      : /adminloginmaisjustedutextepourplusdesecurite"
echo "   Email    : supervisor@missesgis.local"
echo "   Password : SuperV!s0r#2025"
echo "   Panel    : /sys/vm"
echo ""
echo "📚 DOCUMENTATION:"
echo "   README_STEALTH.txt      - Guide de démarrage rapide"
echo "   STEALTH_MODE_GUIDE.md   - Guide complet (confidentiel)"
echo ""
echo "⚠️  IMPORTANT:"
echo "   - Les données existantes n'ont PAS été modifiées"
echo "   - Un backup a été créé : ${BACKUP_FILE}.gz"
echo "   - Utilisez UNIQUEMENT la redirection automatique"
echo "   - Pour nettoyer : ./scripts/cleanup_stealth.sh"
echo ""
echo "🎯 Le système est maintenant opérationnel et 100% invisible !"
echo ""
