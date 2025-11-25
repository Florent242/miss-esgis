#!/bin/bash

# Script pour configurer MTN MoMo Sandbox
# Documentation: https://momodeveloper.mtn.com/api-documentation/

echo "========================================="
echo "MTN MoMo Sandbox Setup"
echo "========================================="
echo ""

# Vérifier la subscription key
read -p "Entrez votre Ocp-Apim-Subscription-Key: " SUB_KEY

if [ -z "$SUB_KEY" ]; then
    echo "❌ Subscription key requise"
    exit 1
fi

echo ""
echo "1️⃣ Création de l'API User..."

# Générer un UUID pour l'API User
API_USER=$(uuidgen)

# Créer l'API User
RESPONSE=$(curl -s -X POST "https://sandbox.momodeveloper.mtn.com/v1_0/apiuser" \
  -H "X-Reference-Id: $API_USER" \
  -H "Ocp-Apim-Subscription-Key: $SUB_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "providerCallbackHost": "webhook.site"
  }')

if [ $? -eq 0 ]; then
    echo "✅ API User créé: $API_USER"
else
    echo "❌ Erreur lors de la création de l'API User"
    echo "$RESPONSE"
    exit 1
fi

echo ""
echo "2️⃣ Génération de l'API Key..."
sleep 2

# Générer l'API Key
KEY_RESPONSE=$(curl -s -X POST "https://sandbox.momodeveloper.mtn.com/v1_0/apiuser/$API_USER/apikey" \
  -H "Ocp-Apim-Subscription-Key: $SUB_KEY")

API_KEY=$(echo $KEY_RESPONSE | grep -oP '"apiKey"\s*:\s*"\K[^"]+')

if [ -z "$API_KEY" ]; then
    echo "❌ Erreur lors de la génération de l'API Key"
    echo "$KEY_RESPONSE"
    exit 1
fi

echo "✅ API Key générée"

echo ""
echo "========================================="
echo "✅ Configuration terminée!"
echo "========================================="
echo ""
echo "Ajoutez ces valeurs dans votre fichier .env:"
echo ""
echo "MTN_MOMO_API_USER=$API_USER"
echo "MTN_MOMO_API_KEY=$API_KEY"
echo "MTN_MOMO_SUBSCRIPTION_KEY=$SUB_KEY"
echo "MTN_MOMO_ENVIRONMENT=sandbox"
echo ""
echo "========================================="
echo "🧪 Test avec ces numéros:"
echo "========================================="
echo "✅ 46733123450 - Paiement réussit toujours"
echo "❌ 46733123451 - Paiement échoue toujours"
echo "⏳ 46733123452 - Paiement en attente"
echo ""
