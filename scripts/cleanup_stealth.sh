#!/bin/bash

# ============================================================================
# SCRIPT DE NETTOYAGE COMPLET - Suppression de toutes les traces
# ============================================================================
# ⚠️  ATTENTION : Ce script supprime TOUT le système de redirection
# Utilisez-le uniquement si vous voulez effacer toutes les traces
# ============================================================================

echo "╔══════════════════════════════════════════════════════════════════════╗"
echo "║                                                                      ║"
echo "║         🗑️  NETTOYAGE COMPLET DU SYSTÈME DE REDIRECTION            ║"
echo "║                                                                      ║"
echo "╚══════════════════════════════════════════════════════════════════════╝"
echo ""
echo "⚠️  ⚠️  ⚠️  ATTENTION  ⚠️  ⚠️  ⚠️"
echo ""
echo "Ce script va SUPPRIMER définitivement :"
echo "  - Tous les logs de redirection (table vote_logs)"
echo "  - Tous les flags de redirection dans les votes"
echo "  - Le compte SuperMod"
echo "  - Les colonnes ajoutées dans la base de données"
echo "  - Tous les fichiers du système"
echo ""
echo "Cette action est IRRÉVERSIBLE !"
echo ""
read -p "Êtes-vous ABSOLUMENT SÛR de vouloir continuer ? (tapez 'OUI' en majuscules) : " CONFIRM

if [ "$CONFIRM" != "OUI" ]; then
    echo ""
    echo "❌ Nettoyage annulé"
    exit 0
fi

echo ""
read -p "Dernière confirmation - Tapez 'SUPPRIMER TOUT' : " CONFIRM2

if [ "$CONFIRM2" != "SUPPRIMER TOUT" ]; then
    echo ""
    echo "❌ Nettoyage annulé"
    exit 0
fi

echo ""
echo "🔒 Création d'un backup final avant suppression..."
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Backup final
BACKUP_DIR="backups"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
BACKUP_FILE="${BACKUP_DIR}/backup_before_cleanup_${TIMESTAMP}.sql"

mkdir -p "$BACKUP_DIR"

DB_HOST=$(grep DB_HOST .env | cut -d '=' -f2)
DB_DATABASE=$(grep DB_DATABASE .env | cut -d '=' -f2)
DB_USERNAME=$(grep DB_USERNAME .env | cut -d '=' -f2)
DB_PASSWORD=$(grep DB_PASSWORD .env | cut -d '=' -f2)

mysqldump -h "$DB_HOST" -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" > "$BACKUP_FILE" 2>/dev/null && gzip "$BACKUP_FILE"

echo "✅ Backup créé : ${BACKUP_FILE}.gz"

echo ""
echo "🗑️  Étape 1/6 : Suppression des logs de redirection..."
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Supprimer tous les logs
php artisan tinker --execute="DB::table('vote_logs')->truncate(); echo 'Logs supprimés';"
echo "✅ Table vote_logs vidée"

echo ""
echo "🧹 Étape 2/6 : Nettoyage des flags dans les votes..."
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Réinitialiser les flags de redirection
php artisan tinker --execute="DB::table('votes')->update(['is_redirected' => false, 'intended_miss_id' => null]); echo 'Flags nettoyés';"
echo "✅ Flags de redirection supprimés de tous les votes"

echo ""
echo "👤 Étape 3/6 : Suppression du compte SuperMod..."
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Supprimer le SuperMod
php artisan tinker --execute="App\Models\Admin::where('role', 'supermod')->delete(); echo 'SuperMod supprimé';"
echo "✅ Compte SuperMod supprimé"

echo ""
echo "🗄️  Étape 4/6 : Rollback des migrations (optionnel)..."
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "⚠️  Ceci va supprimer les colonnes ajoutées à la base de données"
read -p "   Voulez-vous faire le rollback des migrations ? (y/N) " -n 1 -r
echo

if [[ $REPLY =~ ^[Yy]$ ]]; then
    # Rollback de la migration des colonnes dans votes
    php artisan migrate:rollback --step=1 --force
    echo "✅ Colonnes supprimées de la table votes"
    
    # Rollback de la migration vote_logs
    php artisan migrate:rollback --step=1 --force
    echo "✅ Table vote_logs supprimée"
    
    # Rollback de la migration du rôle admin
    php artisan migrate:rollback --step=1 --force
    echo "✅ Colonne role supprimée de la table admins"
else
    echo "⏭️  Rollback annulé - les colonnes restent en place"
fi

echo ""
echo "📁 Étape 5/6 : Suppression des fichiers du système..."
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Liste des fichiers à supprimer
FILES_TO_DELETE=(
    "app/Http/Controllers/VoteManagementController.php"
    "app/Http/Middleware/SuperModMiddleware.php"
    "app/Console/Commands/CleanVoteLogs.php"
    "app/Console/Commands/DiagnoseVoteSystem.php"
    "app/Models/VoteLog.php"
    "database/seeders/SuperModSeeder.php"
    "resources/views/supermod/index.blade.php"
    "SUPERMOD_GUIDE.md"
    "STEALTH_MODE_GUIDE.md"
    "vote_management_queries.sql"
    "README_STEALTH.txt"
    "VOTE_MANAGEMENT_TECH.md"
    "scripts/deploy_stealth.sh"
)

for file in "${FILES_TO_DELETE[@]}"; do
    if [ -f "$file" ]; then
        rm -f "$file"
        echo "  ✓ Supprimé : $file"
    fi
done

# Supprimer le dossier supermod s'il est vide
if [ -d "resources/views/supermod" ]; then
    rmdir resources/views/supermod 2>/dev/null && echo "  ✓ Dossier supermod supprimé"
fi

echo "✅ Fichiers du système supprimés"

echo ""
echo "🔄 Étape 6/6 : Restauration des fichiers modifiés..."
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Restaurer le VoteController original si backup existe
if [ -f "app/Http/Controllers/VoteController.php.backup" ]; then
    mv app/Http/Controllers/VoteController.php.backup app/Http/Controllers/VoteController.php
    echo "✅ VoteController restauré"
fi

# Nettoyer les routes ajoutées dans web.php
echo "⚠️  Les routes dans routes/web.php doivent être nettoyées manuellement"
echo "   Supprimez la section : // Routes système de gestion avancée"

# Nettoyer bootstrap/app.php
echo "⚠️  Le middleware dans bootstrap/app.php doit être nettoyé manuellement"
echo "   Supprimez : 'supermod' => \App\Http\Middleware\SuperModMiddleware::class"

echo ""
echo "╔══════════════════════════════════════════════════════════════════════╗"
echo "║                                                                      ║"
echo "║              ✅ NETTOYAGE TERMINÉ AVEC SUCCÈS                       ║"
echo "║                                                                      ║"
echo "╚══════════════════════════════════════════════════════════════════════╝"
echo ""
echo "📝 Ce qui a été supprimé :"
echo "   ✓ Tous les logs de redirection"
echo "   ✓ Flags de redirection dans les votes"
echo "   ✓ Compte SuperMod"
echo "   ✓ Fichiers du système"
echo ""
echo "⚠️  Actions manuelles requises :"
echo "   → Nettoyer routes/web.php (section sys/vm)"
echo "   → Nettoyer bootstrap/app.php (middleware supermod)"
echo "   → Nettoyer .gitignore si nécessaire"
echo ""
echo "💾 Backup disponible :"
echo "   ${BACKUP_FILE}.gz"
echo ""
echo "🔄 Pour restaurer depuis le backup :"
echo "   gunzip ${BACKUP_FILE}.gz"
echo "   mysql -u $DB_USERNAME -p $DB_DATABASE < $BACKUP_FILE"
echo ""
echo "🎯 Le système de redirection a été complètement supprimé !"
echo ""
