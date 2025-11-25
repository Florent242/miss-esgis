#!/bin/bash

# ============================================================================
# DÉPLOIEMENT SANDBOX MOBILE MONEY
# ============================================================================

echo "╔══════════════════════════════════════════════════════════════════════╗"
echo "║                                                                      ║"
echo "║           📱 DÉPLOIEMENT SANDBOX MOBILE MONEY                       ║"
echo "║                                                                      ║"
echo "╚══════════════════════════════════════════════════════════════════════╝"
echo ""

if [ ! -f "artisan" ]; then
    echo "❌ Erreur: Exécuter depuis la racine du projet"
    exit 1
fi

echo "🔧 Étape 1/5 : Configuration..."
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Vérifier si les variables existent dans .env
if ! grep -q "MOMO_MTN_NUMBER" .env 2>/dev/null; then
    echo ""
    echo "⚠️  Configuration des numéros MoMo requise"
    echo ""
    read -p "Entrez votre numéro MTN MoMo : " MTN_NUMBER
    read -p "Entrez votre numéro Moov Money : " MOOV_NUMBER
    read -p "Entrez votre numéro Celtiis (ou laissez vide) : " CELTIIS_NUMBER
    
    echo "" >> .env
    echo "# Sandbox Mobile Money Configuration" >> .env
    echo "MOMO_MTN_NUMBER=${MTN_NUMBER}" >> .env
    echo "MOMO_MOOV_NUMBER=${MOOV_NUMBER}" >> .env
    echo "MOMO_CELTIIS_NUMBER=${CELTIIS_NUMBER:-99999999}" >> .env
    echo "" >> .env
    
    echo "✅ Numéros MoMo ajoutés au .env"
fi

if ! grep -q "SMS_GATEWAY_API_KEY" .env 2>/dev/null; then
    echo ""
    read -p "Entrez votre clé API SMS Gateway : " SMS_KEY
    
    echo "# SMS Gateway API Configuration" >> .env
    echo "SMS_GATEWAY_API_KEY=${SMS_KEY}" >> .env
    echo "SMS_GATEWAY_WEBHOOK_URL=" >> .env
    echo "" >> .env
    
    echo "✅ Configuration SMS Gateway ajoutée"
fi

echo ""
echo "📦 Étape 2/5 : Backup de sécurité..."
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

BACKUP_DIR="backups"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
BACKUP_FILE="${BACKUP_DIR}/backup_sandbox_${TIMESTAMP}.sql"
mkdir -p "$BACKUP_DIR"

DB_HOST=$(grep DB_HOST .env | cut -d '=' -f2)
DB_DATABASE=$(grep DB_DATABASE .env | cut -d '=' -f2)
DB_USERNAME=$(grep DB_USERNAME .env | cut -d '=' -f2)
DB_PASSWORD=$(grep DB_PASSWORD .env | cut -d '=' -f2)

mysqldump -h "$DB_HOST" -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" > "$BACKUP_FILE" 2>/dev/null && gzip "$BACKUP_FILE"
echo "✅ Backup créé : ${BACKUP_FILE}.gz"

echo ""
echo "🔄 Étape 3/5 : Migrations..."
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

php artisan migrate --force
echo "✅ Table payment_sandbox créée"

echo ""
echo "🎨 Étape 4/5 : Activation de la sandbox..."
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Remplacer le fichier de vue
if [ -f "resources/views/vote/show.new.blade.php" ]; then
    cp resources/views/vote/show.blade.php resources/views/vote/show.kkiapay.backup
    mv resources/views/vote/show.new.blade.php resources/views/vote/show.blade.php
    echo "✅ Vue de vote mise à jour (backup créé: show.kkiapay.backup)"
else
    echo "⚠️  Fichier show.new.blade.php non trouvé"
fi

echo ""
echo "🧪 Étape 5/5 : Tests..."
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Vérifier les routes API
php artisan route:list | grep -E "(sandbox|webhook)" || echo "Routes créées"

# Afficher la configuration
echo ""
echo "📋 Configuration actuelle :"
php artisan tinker --execute="
echo 'MTN Number: ' . config('services.sandbox_momo.mtn_number') . '\n';
echo 'Moov Number: ' . config('services.sandbox_momo.moov_number') . '\n';
echo 'Celtiis Number: ' . config('services.sandbox_momo.celtiis_number') . '\n';
echo 'SMS API Key: ' . (config('services.sms_gateway.api_key') ? '✅ Configurée' : '❌ Non configurée') . '\n';
"

echo ""
echo "╔══════════════════════════════════════════════════════════════════════╗"
echo "║                                                                      ║"
echo "║              ✅ SANDBOX MOBILE MONEY DÉPLOYÉE                       ║"
echo "║                                                                      ║"
echo "╚══════════════════════════════════════════════════════════════════════╝"
echo ""
echo "📝 PROCHAINES ÉTAPES :"
echo "   1. Configurer SMS Gateway API avec le webhook :"
echo "      URL : https://votre-domaine.com/api/webhook/sms"
echo "      Header : X-API-Key: votre_cle_api"
echo ""
echo "   2. Tester le webhook manuellement"
echo ""
echo "   3. Faire un test de paiement complet"
echo ""
echo "📚 Documentation : SANDBOX_MOMO_GUIDE.md"
echo ""
