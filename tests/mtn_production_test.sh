#!/bin/bash

# Test MTN MoMo en Production
# Date: 25 Nov 2025
# Environnement: Production (XOF currency)

SITE_URL="https://reine-esgis.com"
LOG_FILE="/var/www/miss-esgis/storage/logs/mtn_tests.log"

echo "=== Test MTN MoMo Production ===" | tee -a $LOG_FILE
echo "Date: $(date)" | tee -a $LOG_FILE
echo "" | tee -a $LOG_FILE

# Test 1: Avec numéro de test sandbox (devrait échouer en production)
echo "Test 1: Numéro sandbox (attendu: échec)" | tee -a $LOG_FILE
RESPONSE1=$(curl -s -X POST "$SITE_URL/api/sandbox/initiate" \
  -H "Content-Type: application/json" \
  -d '{
    "miss_id": 1,
    "operator": "mtn",
    "phone_number": "46733123453",
    "amount": 100,
    "vote_count": 1
  }')
echo "$RESPONSE1" | python3 -m json.tool 2>/dev/null || echo "$RESPONSE1"
echo "$RESPONSE1" >> $LOG_FILE
echo "" | tee -a $LOG_FILE

# Test 2: Avec numéro MTN Bénin réel
echo "Test 2: Numéro MTN Bénin (229 01 61 80 49 72)" | tee -a $LOG_FILE
RESPONSE2=$(curl -s -X POST "$SITE_URL/api/sandbox/initiate" \
  -H "Content-Type: application/json" \
  -d '{
    "miss_id": 1,
    "operator": "mtn",
    "phone_number": "229 01 61 80 49 72",
    "amount": 100,
    "vote_count": 1
  }')
echo "$RESPONSE2" | python3 -m json.tool 2>/dev/null || echo "$RESPONSE2"
echo "$RESPONSE2" >> $LOG_FILE

# Extraire la référence si succès
REF=$(echo "$RESPONSE2" | grep -o '"reference":"[^"]*' | cut -d'"' -f4)

if [ ! -z "$REF" ]; then
    echo "" | tee -a $LOG_FILE
    echo "Référence générée: $REF" | tee -a $LOG_FILE
    
    # Test 3: Vérifier le statut
    echo "" | tee -a $LOG_FILE
    echo "Test 3: Vérification du statut (après 5 secondes)" | tee -a $LOG_FILE
    sleep 5
    
    RESPONSE3=$(curl -s -X POST "$SITE_URL/api/sandbox/status" \
      -H "Content-Type: application/json" \
      -d "{\"reference\": \"$REF\"}")
    echo "$RESPONSE3" | python3 -m json.tool 2>/dev/null || echo "$RESPONSE3"
    echo "$RESPONSE3" >> $LOG_FILE
fi

echo "" | tee -a $LOG_FILE
echo "=== Fin des tests ===" | tee -a $LOG_FILE
echo "Logs sauvegardés dans: $LOG_FILE" | tee -a $LOG_FILE
