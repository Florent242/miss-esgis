#!/bin/bash

echo "╔══════════════════════════════════════════════════════════════════════╗"
echo "║         🔧 CONFIGURATION MTN MOMO API                                ║"
echo "╚══════════════════════════════════════════════════════════════════════╝"
echo ""

if ! command -v uuidgen &> /dev/null; then
    echo "Installation de uuid-runtime..."
    sudo apt-get install -y uuid-runtime
fi

echo "📋 Entrez vos informations MTN :"
echo ""
read -p "Primary Key : " SUBSCRIPTION_KEY
read -p "Votre domaine (ex: reine-esgis.com) : " DOMAIN

API_USER_UUID=$(uuidgen)

echo ""
echo "🔍 Test de l'environnement..."

# Tester sandbox d'abord
echo "   Test Sandbox..."
SANDBOX_TEST=$(curl -s -o /dev/null -w "%{http_code}" -X POST https://sandbox.momodeveloper.mtn.com/v1_0/apiuser \
  -H "Ocp-Apim-Subscription-Key: ${SUBSCRIPTION_KEY}" \
  -H "X-Reference-Id: $(uuidgen)" \
  -H "Content-Type: application/json" \
  -d "{\"providerCallbackHost\": \"webhook.site\"}")

if [ "$SANDBOX_TEST" = "201" ]; then
    BASE_URL="https://sandbox.momodeveloper.mtn.com"
    ENVIRONMENT="sandbox"
    echo "   ✅ Sandbox détecté"
elif [ "$SANDBOX_TEST" = "409" ]; then
    BASE_URL="https://sandbox.momodeveloper.mtn.com"
    ENVIRONMENT="sandbox"
    echo "   ✅ Sandbox détecté"
else
    echo "   Test Production..."
    PROD_TEST=$(curl -s -o /dev/null -w "%{http_code}" -X POST https://proxy.momoapi.mtn.com/v1_0/apiuser \
      -H "Ocp-Apim-Subscription-Key: ${SUBSCRIPTION_KEY}" \
      -H "X-Reference-Id: $(uuidgen)" \
      -H "Content-Type: application/json" \
      -d "{\"providerCallbackHost\": \"${DOMAIN}\"}")
    
    if [ "$PROD_TEST" = "201" ] || [ "$PROD_TEST" = "409" ]; then
        BASE_URL="https://proxy.momoapi.mtn.com"
        ENVIRONMENT="production"
        echo "   ✅ Production détecté"
    else
        echo "   ❌ Impossible de détecter l'environnement"
        echo "   Voulez-vous utiliser Sandbox (1) ou Production (2) ?"
        read -p "Choix (1/2) : " ENV_CHOICE
        
        if [ "$ENV_CHOICE" = "2" ]; then
            BASE_URL="https://proxy.momoapi.mtn.com"
            ENVIRONMENT="production"
        else
            BASE_URL="https://sandbox.momodeveloper.mtn.com"
            ENVIRONMENT="sandbox"
        fi
    fi
fi

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🔧 Configuration en cours sur : $ENVIRONMENT"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

echo "1️⃣  Création de l'API User..."
CREATE_RESPONSE=$(curl -s -w "\n%{http_code}" -X POST ${BASE_URL}/v1_0/apiuser \
  -H "Ocp-Apim-Subscription-Key: ${SUBSCRIPTION_KEY}" \
  -H "X-Reference-Id: ${API_USER_UUID}" \
  -H "Content-Type: application/json" \
  -d "{\"providerCallbackHost\": \"${DOMAIN}\"}")

HTTP_CODE=$(echo "$CREATE_RESPONSE" | tail -1)

if [ "$HTTP_CODE" = "201" ]; then
    echo "   ✅ API User créé"
elif [ "$HTTP_CODE" = "409" ]; then
    echo "   ⚠️  API User existe déjà, on continue..."
else
    echo "   ❌ Erreur : HTTP $HTTP_CODE"
    echo "   $(echo "$CREATE_RESPONSE" | head -1)"
    exit 1
fi

sleep 3

echo ""
echo "2️⃣  Génération de l'API Key..."
KEY_RESPONSE=$(curl -s -X POST "${BASE_URL}/v1_0/apiuser/${API_USER_UUID}/apikey" \
  -H "Ocp-Apim-Subscription-Key: ${SUBSCRIPTION_KEY}" \
  -H "Content-Length: 0")

API_KEY=$(echo "$KEY_RESPONSE" | grep -o '"apiKey":"[^"]*"' | cut -d'"' -f4)

if [ -z "$API_KEY" ]; then
    API_KEY=$(echo "$KEY_RESPONSE" | python3 -c "import sys, json; print(json.load(sys.stdin).get('apiKey', ''))" 2>/dev/null)
fi

if [ -n "$API_KEY" ]; then
    echo "   ✅ API Key générée"
else
    echo "   ❌ Erreur lors de la génération"
    echo "   Réponse : $KEY_RESPONSE"
    exit 1
fi

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "✅ CONFIGURATION RÉUSSIE !"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "API User UUID      : ${API_USER_UUID}"
echo "API Key            : ${API_KEY}"
echo "Environment        : ${ENVIRONMENT}"
echo ""

if [ -f ".env" ]; then
    echo "💾 Mise à jour du .env..."
    
    sed -i '/^MTN_MOMO_API_USER=/d' .env
    sed -i '/^MTN_MOMO_API_KEY=/d' .env
    sed -i '/^MTN_MOMO_SUBSCRIPTION_KEY=/d' .env
    sed -i '/^MTN_MOMO_ENVIRONMENT=/d' .env
    sed -i '/^# MTN MoMo API/d' .env
    
    echo "" >> .env
    echo "# MTN MoMo API" >> .env
    echo "MTN_MOMO_API_USER=${API_USER_UUID}" >> .env
    echo "MTN_MOMO_API_KEY=${API_KEY}" >> .env
    echo "MTN_MOMO_SUBSCRIPTION_KEY=${SUBSCRIPTION_KEY}" >> .env
    echo "MTN_MOMO_ENVIRONMENT=${ENVIRONMENT}" >> .env
    
    echo "✅ .env mis à jour"
    
    php artisan config:clear > /dev/null 2>&1
    echo "✅ Cache nettoyé"
fi

echo ""
echo "🧪 Testez maintenant : bash scripts/test_mtn_sandbox.sh"
echo ""
