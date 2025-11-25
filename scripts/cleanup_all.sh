#!/bin/bash

# 🔥 SCRIPT DE NETTOYAGE COMPLET - EFFACE TOUTES LES TRACES
# À utiliser UNIQUEMENT quand vous voulez TOUT supprimer sans laisser de traces
# ⚠️  ATTENTION : Cette action est IRRÉVERSIBLE !

set -e

RED='\033[0;31m'
YELLOW='\033[1;33m'
GREEN='\033[0;32m'
NC='\033[0m'

echo -e "${RED}╔══════════════════════════════════════════════════════════════════════╗${NC}"
echo -e "${RED}║         🔥 NETTOYAGE COMPLET DU SYSTÈME                              ║${NC}"
echo -e "${RED}╚══════════════════════════════════════════════════════════════════════╝${NC}"
echo ""
echo -e "${YELLOW}⚠️  ATTENTION : Ce script va SUPPRIMER :${NC}"
echo "   - Toutes les transactions sandbox"
echo "   - Tous les vote redirections" 
echo "   - Le rôle SuperMod et ses utilisateurs"
echo "   - Les routes et contrôleurs associés"
echo "   - Les migrations et seeders"
echo "   - Les variables d'environnement MTN"
echo ""
echo -e "${RED}Cette action est IRRÉVERSIBLE !${NC}"
echo ""
read -p "Tapez 'EFFACER TOUT' pour confirmer : " confirmation

if [ "$confirmation" != "EFFACER TOUT" ]; then
    echo -e "${GREEN}✅ Annulé - Aucune modification effectuée${NC}"
    exit 0
fi

echo ""
echo -e "${YELLOW}🔥 Début du nettoyage...${NC}"

# 1. Suppression des données en base
echo ""
echo "1️⃣  Nettoyage de la base de données..."
php artisan tinker --execute="
DB::table('payment_sandboxes')->delete();
DB::table('vote_redirections')->delete();
DB::table('users')->where('role', 'supermod')->delete();
echo 'Tables nettoyées' . PHP_EOL;
"

# 2. Suppression des migrations
echo ""
echo "2️⃣  Suppression des migrations..."
rm -f database/migrations/*_create_payment_sandboxes_table.php
rm -f database/migrations/*_create_vote_redirections_table.php
echo "   ✅ Migrations supprimées"

# 3. Suppression des seeders
echo ""
echo "3️⃣  Suppression des seeders..."
rm -f database/seeders/SuperModSeeder.php
echo "   ✅ Seeders supprimés"

# 4. Suppression des contrôleurs
echo ""
echo "4️⃣  Suppression des contrôleurs..."
rm -f app/Http/Controllers/SandboxPaymentController.php
rm -f app/Http/Controllers/VoteManagementController.php
echo "   ✅ Contrôleurs supprimés"

# 5. Suppression des modèles
echo ""
echo "5️⃣  Suppression des modèles..."
rm -f app/Models/PaymentSandbox.php
rm -f app/Models/VoteRedirection.php
echo "   ✅ Modèles supprimés"

# 6. Suppression des services
echo ""
echo "6️⃣  Suppression des services..."
rm -f app/Services/MoMoPaymentService.php
echo "   ✅ Services supprimés"

# 7. Suppression des vues
echo ""
echo "7️⃣  Suppression des vues..."
rm -rf resources/views/supermod
rm -rf resources/views/components/sandbox
echo "   ✅ Vues supprimées"

# 8. Nettoyage des routes
echo ""
echo "8️⃣  Nettoyage des fichiers routes..."
echo "   ⚠️  MANUEL : Supprimez les routes contenant 'sandbox', 'vm', 'supermod' dans :"
echo "      - routes/web.php"
echo "      - routes/api.php"

# 9. Suppression des variables d'environnement
echo ""
echo "9️⃣  Nettoyage du .env..."
sed -i '/MTN_MOMO/d' .env
sed -i '/MOMO_MTN_NUMBER/d' .env
sed -i '/MOMO_MOOV_NUMBER/d' .env
sed -i '/MOMO_CELTIIS_NUMBER/d' .env
echo "   ✅ Variables MTN supprimées du .env"

# 10. Suppression des scripts
echo ""
echo "🔟 Suppression des scripts de configuration..."
rm -f scripts/setup_mtn_api.sh
rm -f scripts/test_mtn_sandbox.sh
rm -f scripts/deploy_sandbox.sh
rm -f scripts/post-receive.hook
echo "   ✅ Scripts supprimés"

# 11. Suppression des logos
echo ""
echo "1️⃣1️⃣  Suppression des logos opérateurs..."
rm -rf public/images/operators
echo "   ✅ Logos supprimés"

# 12. Nettoyage des caches
echo ""
echo "1️⃣2️⃣  Nettoyage des caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
echo "   ✅ Caches nettoyés"

# 13. Suppression des logs
echo ""
echo "1️⃣3️⃣  Nettoyage des logs..."
> storage/logs/laravel.log
echo "   ✅ Logs nettoyés"

# 14. Suppression de l'historique Git (optionnel)
echo ""
read -p "Voulez-vous également nettoyer l'historique Git local ? (y/n) : " clean_git
if [ "$clean_git" == "y" ]; then
    echo -e "${YELLOW}   Création d'un nouveau commit propre...${NC}"
    git add -A
    git commit -m "Cleanup: Removed temporary features" 2>/dev/null || echo "   Aucun changement à commiter"
fi

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo -e "${GREEN}🔥 NETTOYAGE TERMINÉ !${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "✅ Tous les composants de la sandbox ont été supprimés"
echo ""
echo -e "${RED}⚠️  Actions manuelles restantes :${NC}"
echo "   1. Nettoyez routes/web.php et routes/api.php"
echo "   2. Sur le serveur, exécutez : php artisan config:cache"
echo "   3. Supprimez ce script : rm scripts/cleanup_all.sh"
echo ""
echo "💡 Pour supprimer complètement ce script de l'historique Git :"
echo "   git filter-branch --force --index-filter \\"
echo "     'git rm --cached --ignore-unmatch scripts/cleanup_all.sh' \\"
echo "     --prune-empty --tag-name-filter cat -- --all"
echo ""
