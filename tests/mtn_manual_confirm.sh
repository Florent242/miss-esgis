#!/bin/bash

# Simulation de confirmation manuelle MTN (pour tests)
# En sandbox, on peut manuellement marquer un paiement comme "successful"

SITE_URL="https://reine-esgis.com"

echo "=========================================="
echo "🔧 SIMULATION CONFIRMATION MTN SANDBOX"
echo "=========================================="
echo ""

# Étape 1: Créer un paiement
echo "1️⃣ Création d'un paiement test..."
INIT_RESPONSE=$(curl -s -X POST "$SITE_URL/api/sandbox/initiate" \
  -H "Content-Type: application/json" \
  -d '{
    "miss_id": 1,
    "operator": "mtn",
    "phone_number": "46733123453",
    "amount": 100,
    "vote_count": 1
  }')

echo "$INIT_RESPONSE" | python3 -m json.tool
REFERENCE=$(echo "$INIT_RESPONSE" | grep -o '"reference":"[^"]*' | cut -d'"' -f4)
echo ""

if [ -z "$REFERENCE" ]; then
    echo "❌ Erreur: Impossible de créer le paiement"
    exit 1
fi

echo "✅ Paiement créé: $REFERENCE"
echo ""

# Étape 2: Vérifier le statut initial
echo "2️⃣ Statut initial:"
curl -s -X POST "$SITE_URL/api/sandbox/status" \
  -H "Content-Type: application/json" \
  -d "{\"reference\": \"$REFERENCE\"}" | python3 -m json.tool
echo ""

# Étape 3: Simuler la vérification MTN (qui mettrait à jour le statut)
echo "3️⃣ Simulation: En production, MTN enverrait une notification..."
echo "   Pour tester la confirmation, vous pouvez manuellement:"
echo ""
echo "   A. Via MySQL:"
echo "   UPDATE payment_sandbox SET status='confirmed' WHERE reference='$REFERENCE';"
echo ""
echo "   B. Via artisan tinker:"
echo "   \$payment = App\\Models\\PaymentSandbox::where('reference', '$REFERENCE')->first();"
echo "   \$payment->status = 'confirmed';"
echo "   \$payment->save();"
echo ""
echo "   Puis relancez la vérification de statut pour voir le changement."
echo ""
echo "=========================================="
