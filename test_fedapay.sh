#!/bin/bash

echo "🧪 Test d'intégration FedaPay Miss ESGIS"
echo "=========================================="
echo ""

# Configuration
API_URL="http://localhost:8000/api/fedapay"
MISS_ID=1

echo "📱 Test 1: Initier un paiement"
echo "------------------------------"
curl -X POST "$API_URL/initiate" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d "{
    \"miss_id\": $MISS_ID,
    \"phone_number\": \"+22966000001\",
    \"amount\": 500,
    \"vote_count\": 5,
    \"email\": \"test@miss-esgis.com\"
  }" | jq '.'

echo ""
echo ""
echo "🔔 Test 2: Simuler un webhook (transaction approuvée)"
echo "-----------------------------------------------------"
curl -X POST "$API_URL/webhook" \
  -H "Content-Type: application/json" \
  -d '{
    "entity": {
      "transaction": {
        "id": "999999",
        "status": "approved",
        "reference": "TEST-REF-001",
        "amount": 500,
        "currency": {"iso": "XOF"},
        "customer": {
          "email": "test@miss-esgis.com",
          "firstname": "Test",
          "lastname": "User"
        }
      }
    }
  }' | jq '.'

echo ""
echo ""
echo "✅ Tests terminés!"
echo ""
echo "📊 Vérifier les logs:"
echo "  - Laravel: tail -f storage/logs/laravel.log"
echo "  - API Pay: php /home/admin/monea-pay/api/monitor.php"
