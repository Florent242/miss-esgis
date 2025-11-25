#!/bin/bash

# ============================================================================
# TEST MTN MOMO API SANDBOX
# ============================================================================

echo "╔══════════════════════════════════════════════════════════════════════╗"
echo "║                                                                      ║"
echo "║              🧪 TEST MTN MOMO API SANDBOX                           ║"
echo "║                                                                      ║"
echo "╚══════════════════════════════════════════════════════════════════════╝"
echo ""

if [ ! -f "artisan" ]; then
    echo "❌ Exécuter depuis la racine du projet"
    exit 1
fi

# Vérifier la configuration
API_USER=$(grep MTN_MOMO_API_USER .env | cut -d '=' -f2)
API_KEY=$(grep MTN_MOMO_API_KEY .env | cut -d '=' -f2)
SUBSCRIPTION_KEY=$(grep MTN_MOMO_SUBSCRIPTION_KEY .env | cut -d '=' -f2)

if [ -z "$API_USER" ] || [ -z "$API_KEY" ] || [ -z "$SUBSCRIPTION_KEY" ]; then
    echo "❌ Configuration MTN incomplète dans .env"
    echo ""
    echo "Exécutez d'abord : bash scripts/setup_mtn_api.sh"
    exit 1
fi

echo "📋 Configuration détectée :"
echo "   API User : ${API_USER:0:20}..."
echo "   API Key : ${API_KEY:0:20}..."
echo "   Subscription Key : ${SUBSCRIPTION_KEY:0:20}..."
echo ""

SITE_URL=$(grep APP_URL .env | cut -d '=' -f2 | tr -d ' ')
SITE_URL=${SITE_URL:-"http://127.0.0.1:8000"}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🧪 TEST 1 : Initialisation du paiement"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

# Obtenir une candidate
MISS_ID=$(php artisan tinker --execute="echo App\Models\Miss::where('statut', 'active')->first()->id ?? 1;" 2>/dev/null | tail -1)

echo "Candidate ID : $MISS_ID"
echo "Numéro de test : 46733123450 (succès automatique)"
echo ""
echo "Envoi de la requête de paiement..."

INIT_RESPONSE=$(curl -s -X POST "${SITE_URL}/api/sandbox/initiate" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d "{
    \"miss_id\": ${MISS_ID},
    \"operator\": \"mtn\",
    \"phone_number\": \"46733123450\",
    \"amount\": 500,
    \"vote_count\": 5
  }")

echo "$INIT_RESPONSE" | python3 -m json.tool 2>/dev/null || echo "$INIT_RESPONSE"
echo ""

# Extraire la référence
REFERENCE=$(echo "$INIT_RESPONSE" | grep -o '"reference":"[^"]*"' | cut -d '"' -f4)

if [ -z "$REFERENCE" ]; then
    echo "❌ Échec de l'initialisation du paiement"
    echo ""
    echo "Vérifiez :"
    echo "   1. Que les credentials MTN sont corrects"
    echo "   2. Que vous êtes en mode sandbox"
    echo "   3. Les logs : tail -f storage/logs/laravel.log"
    exit 1
fi

echo "✅ Paiement initié : $REFERENCE"
echo ""

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🧪 TEST 2 : Vérification du statut (polling)"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

for i in {1..10}; do
    echo "Tentative $i/10..."
    
    STATUS_RESPONSE=$(curl -s -X POST "${SITE_URL}/api/sandbox/status" \
      -H "Content-Type: application/json" \
      -d "{\"reference\": \"${REFERENCE}\"}")
    
    STATUS=$(echo "$STATUS_RESPONSE" | grep -o '"status":"[^"]*"' | cut -d '"' -f4)
    
    echo "Statut : $STATUS"
    
    if [ "$STATUS" = "confirmed" ]; then
        echo ""
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
        echo "✅ PAIEMENT CONFIRMÉ !"
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
        break
    elif [ "$STATUS" = "failed" ]; then
        echo ""
        echo "❌ Paiement échoué"
        exit 1
    fi
    
    if [ $i -lt 10 ]; then
        echo "Attente de 3 secondes..."
        sleep 3
        echo ""
    fi
done

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🧪 TEST 3 : Vérification des votes"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

php artisan tinker --execute="
\$payment = App\Models\PaymentSandbox::where('reference', '${REFERENCE}')->first();
if (\$payment && \$payment->status === 'confirmed') {
    \$votes = App\Models\Vote::where('transaction_id', '>', 0)
        ->latest()
        ->take(\$payment->vote_count)
        ->count();
    
    echo '✅ Paiement confirmé\n';
    echo 'Votes créés : ' . \$votes . '/' . \$payment->vote_count . '\n';
    echo 'Montant : ' . \$payment->amount . ' FCFA\n';
    
    if (\$votes === \$payment->vote_count) {
        echo '\n🎉 TEST COMPLET RÉUSSI !\n';
    }
} else {
    echo '❌ Paiement non confirmé\n';
}
"

echo ""
echo "╔══════════════════════════════════════════════════════════════════════╗"
echo "║                                                                      ║"
echo "║                  ✅ TESTS TERMINÉS                                  ║"
echo "║                                                                      ║"
echo "╚══════════════════════════════════════════════════════════════════════╝"
echo ""
echo "📝 Si le test a réussi :"
echo "   → MTN MoMo API fonctionne en sandbox"
echo "   → Les paiements sont traités automatiquement"
echo "   → Le pop-up USSD est déclenché"
echo "   → Les votes sont créés correctement"
echo ""
echo "🚀 Prochaine étape :"
echo "   → Tester sur le site web"
echo "   → Puis demander l'accès PRODUCTION à MTN"
echo ""
