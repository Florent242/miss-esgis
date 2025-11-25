#!/bin/bash

# Test Complet du Workflow de Vote MTN MoMo
# Simule un utilisateur qui vote depuis le site

SITE_URL="https://reine-esgis.com"
LOG_FILE="/var/www/miss-esgis/storage/logs/mtn_workflow_test.log"

echo "=========================================="  | tee -a $LOG_FILE
echo "🎯 TEST WORKFLOW COMPLET MTN MOMO"  | tee -a $LOG_FILE
echo "=========================================="  | tee -a $LOG_FILE
echo "Date: $(date)" | tee -a $LOG_FILE
echo "" | tee -a $LOG_FILE

# Paramètres du vote
MISS_ID=1
PHONE="46733123453"  # Numéro test sandbox
AMOUNT=300           # 3 votes
VOTE_COUNT=3

echo "📋 Paramètres du test:" | tee -a $LOG_FILE
echo "   - Candidate ID: $MISS_ID" | tee -a $LOG_FILE
echo "   - Téléphone: $PHONE" | tee -a $LOG_FILE
echo "   - Montant: $AMOUNT FCFA" | tee -a $LOG_FILE
echo "   - Nombre de votes: $VOTE_COUNT" | tee -a $LOG_FILE
echo "" | tee -a $LOG_FILE

# Étape 1: Vérifier les opérateurs disponibles
echo "📡 Étape 1: Vérification des opérateurs disponibles" | tee -a $LOG_FILE
OPERATORS=$(curl -s "$SITE_URL/api/sandbox/operators")
echo "$OPERATORS" | python3 -m json.tool 2>/dev/null || echo "$OPERATORS" | tee -a $LOG_FILE
echo "" | tee -a $LOG_FILE

# Étape 2: Initier le paiement
echo "💰 Étape 2: Initiation du paiement MTN" | tee -a $LOG_FILE
INIT_RESPONSE=$(curl -s -X POST "$SITE_URL/api/sandbox/initiate" \
  -H "Content-Type: application/json" \
  -d "{
    \"miss_id\": $MISS_ID,
    \"operator\": \"mtn\",
    \"phone_number\": \"$PHONE\",
    \"amount\": $AMOUNT,
    \"vote_count\": $VOTE_COUNT
  }")

echo "$INIT_RESPONSE" | python3 -m json.tool 2>/dev/null || echo "$INIT_RESPONSE" | tee -a $LOG_FILE
echo "" | tee -a $LOG_FILE

# Vérifier si succès
SUCCESS=$(echo "$INIT_RESPONSE" | grep -o '"success":[^,]*' | cut -d':' -f2)
REFERENCE=$(echo "$INIT_RESPONSE" | grep -o '"reference":"[^"]*' | cut -d'"' -f4)

if [ "$SUCCESS" = "true" ] && [ ! -z "$REFERENCE" ]; then
    echo "✅ Paiement initié avec succès!" | tee -a $LOG_FILE
    echo "   Référence: $REFERENCE" | tee -a $LOG_FILE
    echo "" | tee -a $LOG_FILE
    
    # Étape 3: Simuler l'attente de confirmation utilisateur
    echo "⏳ Étape 3: Attente de confirmation (simulation 3 sec)" | tee -a $LOG_FILE
    echo "   → L'utilisateur reçoit le popup USSD sur son téléphone..." | tee -a $LOG_FILE
    echo "   → L'utilisateur entre son code PIN..." | tee -a $LOG_FILE
    sleep 3
    echo "" | tee -a $LOG_FILE
    
    # Étape 4: Vérifier le statut du paiement
    echo "🔍 Étape 4: Vérification du statut du paiement" | tee -a $LOG_FILE
    STATUS_RESPONSE=$(curl -s -X POST "$SITE_URL/api/sandbox/status" \
      -H "Content-Type: application/json" \
      -d "{\"reference\": \"$REFERENCE\"}")
    
    echo "$STATUS_RESPONSE" | python3 -m json.tool 2>/dev/null || echo "$STATUS_RESPONSE" | tee -a $LOG_FILE
    echo "" | tee -a $LOG_FILE
    
    PAYMENT_STATUS=$(echo "$STATUS_RESPONSE" | grep -o '"status":"[^"]*' | cut -d'"' -f4)
    
    case $PAYMENT_STATUS in
        "confirmed")
            echo "✅ PAIEMENT CONFIRMÉ!" | tee -a $LOG_FILE
            echo "   → $VOTE_COUNT votes ont été enregistrés" | tee -a $LOG_FILE
            ;;
        "pending")
            echo "⏳ PAIEMENT EN ATTENTE" | tee -a $LOG_FILE
            echo "   → Le paiement attend confirmation de MTN" | tee -a $LOG_FILE
            echo "   → En production, MTN enverrait un SMS de confirmation" | tee -a $LOG_FILE
            ;;
        "failed")
            echo "❌ PAIEMENT ÉCHOUÉ" | tee -a $LOG_FILE
            ;;
        "expired")
            echo "⏰ PAIEMENT EXPIRÉ" | tee -a $LOG_FILE
            ;;
        *)
            echo "❓ STATUT INCONNU: $PAYMENT_STATUS" | tee -a $LOG_FILE
            ;;
    esac
    
    echo "" | tee -a $LOG_FILE
    
    # Étape 5: Vérifier les logs Laravel
    echo "📝 Étape 5: Vérification des logs" | tee -a $LOG_FILE
    echo "Dernières entrées MTN:" | tee -a $LOG_FILE
    tail -5 /var/www/miss-esgis/storage/logs/laravel.log | grep -i "mtn\|payment" | tee -a $LOG_FILE
    
else
    echo "❌ ÉCHEC de l'initiation du paiement" | tee -a $LOG_FILE
    echo "" | tee -a $LOG_FILE
fi

echo "" | tee -a $LOG_FILE
echo "=========================================="  | tee -a $LOG_FILE
echo "📊 RÉSUMÉ DU TEST"  | tee -a $LOG_FILE
echo "=========================================="  | tee -a $LOG_FILE
echo "Environnement: SANDBOX (MTN)" | tee -a $LOG_FILE
echo "Currency utilisée: EUR" | tee -a $LOG_FILE
echo "Statut final: $PAYMENT_STATUS" | tee -a $LOG_FILE
echo "" | tee -a $LOG_FILE
echo "💡 Note: En SANDBOX, les paiements restent 'pending'" | tee -a $LOG_FILE
echo "   car il n'y a pas de vrai téléphone pour confirmer." | tee -a $LOG_FILE
echo "   En PRODUCTION avec un vrai numéro MTN, le statut" | tee -a $LOG_FILE
echo "   passerait à 'confirmed' après validation USSD." | tee -a $LOG_FILE
echo "" | tee -a $LOG_FILE
echo "📄 Log complet: $LOG_FILE" | tee -a $LOG_FILE
echo "=========================================="  | tee -a $LOG_FILE
