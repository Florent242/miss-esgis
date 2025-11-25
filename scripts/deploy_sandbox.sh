#!/bin/bash

# Script de déploiement simple - À exécuter sur le SERVEUR après un git pull
# Ne modifie PAS la base de données, seulement les fichiers

echo "🚀 Déploiement sandbox..."

# Vérifier qu'on est dans le bon répertoire
if [ ! -f "artisan" ]; then
    echo "❌ Erreur : Pas dans un projet Laravel"
    exit 1
fi

# 1. Migration (seulement les nouvelles tables, ne touche pas aux données)
echo "📋 Migration des nouvelles tables..."
php artisan migrate --path=database/migrations/*_create_payment_sandboxes_table.php --force 2>/dev/null || echo "⚠️  Table déjà existante"

# 2. Création des répertoires
echo "📂 Création des répertoires..."
mkdir -p public/images/operators
chmod -R 755 public/images/operators

# 3. Optimisation
echo "⚡ Optimisation..."
php artisan config:cache
php artisan route:cache  
php artisan view:cache

echo "✅ Déploiement terminé !"
echo ""
echo "📝 N'oubliez pas de :"
echo "   1. Ajouter les variables MTN dans .env (voir .env.example)"
echo "   2. Téléverser les logos dans public/images/operators/"
echo "   3. Exécuter : php artisan config:cache"
