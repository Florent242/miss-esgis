#!/bin/bash

# ============================================================================
# SCRIPT DE TEST DE LA SANDBOX MOBILE MONEY
# ============================================================================

echo "🧪 TEST DE LA SANDBOX MOBILE MONEY"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

if [ ! -f "artisan" ]; then
    echo "❌ Exécuter depuis la racine du projet"
    exit 1
fi

# Récupérer l'URL du site
SITE_URL=$(grep APP_URL .env | cut -d '=' -f2 | tr -d ' ')
SITE_URL=${SITE_URL:-"http://127.0.0.1:8000"}

API_KEY=$(grep SMS_GATEWAY_API_KEY .env | cut -d '=' -f2 | tr -d ' ')

echo "📋 Configuration :"
echo "  URL : $SITE_URL"
echo "  API Key : ${API_KEY:0:10}..."
echo ""

echo "🧪 TEST 1 : Initialisation d'un paiement"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Obtenir une candidate active
MISS_ID=$(php artisan tinker --execute="echo App\Models\Miss::where('statut', 'active')->first()->id ?? 1;" 2>/dev/null | tail -1)

echo "Candidate ID : $MISS_ID"
echo "Initialisation du paiement..."

INIT_RESPONSE=$(curl -s -X POST "${SITE_URL}/api/sandbox/initiate" \
  -H "Content-Type: application/json" \
  -d "{
    \"miss_id\": ${MISS_ID},
    \"operator\": \"mtn\",
    \"phone_number\": \"91234567\",
    \"amount\": 500,
    \"vote_count\": 5
  }")

echo "Réponse : $INIT_RESPONSE"

# Extraire la référence
REFERENCE=$(echo "$INIT_RESPONSE" | grep -o '"reference":"[^"]*"' | cut -d '"' -f4)

if [ -z "$REFERENCE" ]; then
    echo "❌ Échec de l'initialisation"
    exit 1
fi

echo "✅ Paiement initié : $REFERENCE"
echo ""

echo "🧪 TEST 2 : Vérification du statut (pending)"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

STATUS_RESPONSE=$(curl -s -X POST "${SITE_URL}/api/sandbox/status" \
  -H "Content-Type: application/json" \
  -d "{\"reference\": \"${REFERENCE}\"}")

echo "Statut : $STATUS_RESPONSE"
echo ""

echo "🧪 TEST 3 : Simulation d'un SMS de paiement"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

echo "Envoi du webhook SMS..."

WEBHOOK_RESPONSE=$(curl -s -X POST "${SITE_URL}/api/webhook/sms" \
  -H "Content-Type: application/json" \
  -H "X-API-Key: ${API_KEY}" \
  -d "{
    \"from\": \"22991234567\",
    \"message\": \"Vous avez recu 500 FCFA de 91234567. Ref: ${REFERENCE}. Solde: 10000 FCFA\"
  }")

echo "Réponse webhook : $WEBHOOK_RESPONSE"
echo ""

echo "🧪 TEST 4 : Vérification du statut (confirmed)"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

sleep 2

STATUS_RESPONSE=$(curl -s -X POST "${SITE_URL}/api/sandbox/status" \
  -H "Content-Type: application/json" \
  -d "{\"reference\": \"${REFERENCE}\"}")

echo "Statut final : $STATUS_RESPONSE"
echo ""

echo "🧪 TEST 5 : Vérification des votes créés"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

php artisan tinker --execute="
\$payment = App\Models\PaymentSandbox::where('reference', '${REFERENCE}')->first();
if (\$payment) {
    echo 'Statut : ' . \$payment->status . '\n';
    if (\$payment->status === 'confirmed') {
        \$votes = App\Models\Vote::where('transaction_id', '>', 0)->latest()->take(5)->count();
        echo 'Votes créés : ' . \$votes . '\n';
        echo '✅ TEST RÉUSSI !\n';
    }
} else {
    echo '❌ Paiement non trouvé\n';
}
"

echo ""
echo "╔══════════════════════════════════════════════════════════════════════╗"
echo "║                                                                      ║"
echo "║                    ✅ TESTS TERMINÉS                                ║"
echo "║                                                                      ║"
echo "╚══════════════════════════════════════════════════════════════════════╝"
echo ""
echo "📝 Si tous les tests passent :"
echo "   → La sandbox est opérationnelle"
echo "   → Le webhook fonctionne"
echo "   → Les paiements sont validés automatiquement"
echo ""
echo "📚 Voir la documentation : SANDBOX_MOMO_GUIDE.md"
echo ""
