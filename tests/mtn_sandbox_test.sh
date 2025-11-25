#!/bin/bash

# Test MTN MoMo Sandbox
# Date: 25 Nov 2025
# Environnement: Sandbox (EUR currency)

SITE_URL="https://reine-esgis.com"
LOG_FILE="/var/www/miss-esgis/storage/logs/mtn_sandbox_tests.log"

echo "=== Test MTN MoMo SANDBOX ===" | tee -a $LOG_FILE
echo "Date: $(date)" | tee -a $LOG_FILE
echo "Environment: SANDBOX" | tee -a $LOG_FILE
echo "Currency: EUR" | tee -a $LOG_FILE
echo "" | tee -a $LOG_FILE

# Test 1: Avec numéro de test sandbox
echo "Test 1: Numéro sandbox officiel (46733123453)" | tee -a $LOG_FILE
RESPONSE1=$(curl -s -X POST "$SITE_URL/api/sandbox/initiate" \
  -H "Content-Type: application/json" \
  -d '{
    "miss_id": 1,
    "operator": "mtn",
    "phone_number": "46733123453",
    "amount": 100,
    "vote_count": 1
  }')
echo "$RESPONSE1" | python3 -m json.tool 2>/dev/null || echo "$RESPONSE1" | tee -a $LOG_FILE
echo "$RESPONSE1" >> $LOG_FILE

# Extraire la référence
REF1=$(echo "$RESPONSE1" | grep -o '"reference":"[^"]*' | cut -d'"' -f4)

if [ ! -z "$REF1" ]; then
    echo "" | tee -a $LOG_FILE
    echo "✅ Paiement initié avec succès!" | tee -a $LOG_FILE
    echo "Référence: $REF1" | tee -a $LOG_FILE
    
    # Vérifier le statut après 3 secondes
    echo "" | tee -a $LOG_FILE
    echo "Vérification du statut..." | tee -a $LOG_FILE
    sleep 3
    
    STATUS1=$(curl -s -X POST "$SITE_URL/api/sandbox/status" \
      -H "Content-Type: application/json" \
      -d "{\"reference\": \"$REF1\"}")
    echo "$STATUS1" | python3 -m json.tool 2>/dev/null || echo "$STATUS1" | tee -a $LOG_FILE
    echo "$STATUS1" >> $LOG_FILE
else
    echo "❌ Échec de l'initialisation" | tee -a $LOG_FILE
fi

echo "" | tee -a $LOG_FILE
echo "===========================================" | tee -a $LOG_FILE

# Test 2: Format numéro béninois
echo "" | tee -a $LOG_FILE
echo "Test 2: Format Bénin avec espaces" | tee -a $LOG_FILE
RESPONSE2=$(curl -s -X POST "$SITE_URL/api/sandbox/initiate" \
  -H "Content-Type: application/json" \
  -d '{
    "miss_id": 1,
    "operator": "mtn",
    "phone_number": "229 01 61 80 49 72",
    "amount": 100,
    "vote_count": 1
  }')
echo "$RESPONSE2" | python3 -m json.tool 2>/dev/null || echo "$RESPONSE2" | tee -a $LOG_FILE
echo "$RESPONSE2" >> $LOG_FILE

REF2=$(echo "$RESPONSE2" | grep -o '"reference":"[^"]*' | cut -d'"' -f4)

if [ ! -z "$REF2" ]; then
    echo "" | tee -a $LOG_FILE
    echo "✅ Paiement 2 initié!" | tee -a $LOG_FILE
    echo "Référence: $REF2" | tee -a $LOG_FILE
else
    echo "❌ Échec du paiement 2" | tee -a $LOG_FILE
fi

echo "" | tee -a $LOG_FILE
echo "===========================================" | tee -a $LOG_FILE

# Test 3: Vote multiple (200 FCFA = 2 votes)
echo "" | tee -a $LOG_FILE
echo "Test 3: Vote multiple (200 FCFA = 2 votes)" | tee -a $LOG_FILE
RESPONSE3=$(curl -s -X POST "$SITE_URL/api/sandbox/initiate" \
  -H "Content-Type: application/json" \
  -d '{
    "miss_id": 1,
    "operator": "mtn",
    "phone_number": "46733123453",
    "amount": 200,
    "vote_count": 2
  }')
echo "$RESPONSE3" | python3 -m json.tool 2>/dev/null || echo "$RESPONSE3" | tee -a $LOG_FILE
echo "$RESPONSE3" >> $LOG_FILE

REF3=$(echo "$RESPONSE3" | grep -o '"reference":"[^"]*' | cut -d'"' -f4)

if [ ! -z "$REF3" ]; then
    echo "" | tee -a $LOG_FILE
    echo "✅ Vote multiple initié!" | tee -a $LOG_FILE
    echo "Référence: $REF3" | tee -a $LOG_FILE
else
    echo "❌ Échec du vote multiple" | tee -a $LOG_FILE
fi

echo "" | tee -a $LOG_FILE
echo "=== Fin des tests SANDBOX ===" | tee -a $LOG_FILE
echo "Logs complets: $LOG_FILE" | tee -a $LOG_FILE
echo "" | tee -a $LOG_FILE
echo "📝 Note: En mode SANDBOX:" | tee -a $LOG_FILE
echo "   - Les paiements sont simulés" | tee -a $LOG_FILE
echo "   - Currency: EUR (pas XOF)" | tee -a $LOG_FILE
echo "   - Numéro test: 46733123453" | tee -a $LOG_FILE
