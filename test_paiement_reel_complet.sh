#!/bin/bash

echo "╔══════════════════════════════════════════════════════════════════╗"
echo "║                                                                  ║"
echo "║   💰 TEST PAIEMENT RÉEL COMPLET - reine-esgis.com              ║"
echo "║                                                                  ║"
echo "╚══════════════════════════════════════════════════════════════════╝"
echo ""
echo "⚠️  ATTENTION: Ce test va créer un VRAI paiement de 100 XOF"
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

# Demander confirmation
read -p "Voulez-vous continuer? (y/n): " confirm
if [ "$confirm" != "y" ]; then
    echo "❌ Test annulé"
    exit 0
fi

echo ""
echo "🚀 Création de la transaction LIVE via l'API Laravel..."
echo ""

# Créer la transaction via l'API Laravel
RESPONSE=$(curl -s -X POST https://reine-esgis.com/api/fedapay/initiate \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "X-CSRF-TOKEN: test" \
  -d '{
    "miss_id": 1,
    "phone_number": "+2290161804972",
    "email": "test@reine-esgis.com",
    "amount": 100,
    "vote_count": 1
  }')

echo "📥 Réponse de l'API:"
echo "$RESPONSE" | python3 -m json.tool

# Extraire l'URL de paiement
PAYMENT_URL=$(echo "$RESPONSE" | python3 -c "import sys, json; print(json.load(sys.stdin).get('payment_url', ''))" 2>/dev/null)
REFERENCE=$(echo "$RESPONSE" | python3 -c "import sys, json; print(json.load(sys.stdin).get('reference', ''))" 2>/dev/null)

if [ -z "$PAYMENT_URL" ]; then
    echo "❌ Erreur: Impossible de récupérer l'URL de paiement"
    exit 1
fi

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "✅ TRANSACTION CRÉÉE AVEC SUCCÈS !"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "📋 Référence: $REFERENCE"
echo ""
echo "🌐 URL DE PAIEMENT LIVE:"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "$PAYMENT_URL"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "🎯 INSTRUCTIONS POUR PAYER:"
echo ""
echo "1️⃣  Copiez l'URL ci-dessus"
echo "2️⃣  Ouvrez-la dans votre navigateur"
echo "3️⃣  Choisissez MTN ou Moov Money"
echo "4️⃣  Entrez: 01 61 80 49 72"
echo "5️⃣  Validez avec votre code PIN"
echo "6️⃣  100 XOF seront débités"
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "📊 APRÈS LE PAIEMENT:"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "✅ Vous recevrez un SMS de confirmation"
echo "✅ Le webhook sera envoyé automatiquement"
echo "✅ Le statut passera à 'completed'"
echo "✅ Le vote sera créé dans la base de données"
echo "✅ Vous pourrez accéder à: https://reine-esgis.com/vote/1/success"
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🔍 VÉRIFICATION:"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "# Vérifier le statut de la transaction:"
echo "curl -X POST https://reine-esgis.com/api/fedapay/status \\"
echo "  -H 'Content-Type: application/json' \\"
echo "  -d '{\"reference\": \"$REFERENCE\"}'"
echo ""
echo "# Surveiller les logs:"
echo "tail -f /home/admin/monea-pay/api/logs/webhook.log"
echo "tail -f /var/www/miss-esgis/storage/logs/laravel.log"
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "💾 Référence sauvegardée dans: last_payment_reference.txt"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Sauvegarder la référence
echo "{\"reference\": \"$REFERENCE\", \"payment_url\": \"$PAYMENT_URL\", \"created_at\": \"$(date)\"}" > last_payment_reference.txt

