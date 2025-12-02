#!/bin/bash

# Script de vérification rapide du système de gestion des votes
# Usage: bash quick_check.sh

echo "🔍 VÉRIFICATION RAPIDE DU SYSTÈME"
echo "=================================="
echo ""

# Vérifier si artisan est accessible
if [ ! -f "artisan" ]; then
    echo "❌ Erreur: Ce script doit être exécuté depuis la racine du projet Laravel"
    exit 1
fi

echo "📊 Diagnostic du système..."
php artisan system:diagnose

echo ""
echo "🔑 Compte SuperMod:"
php artisan tinker --execute="
\$sm = App\Models\Admin::where('role', 'supermod')->first();
echo 'Email: ' . \$sm->email . '\n';
echo 'Créé le: ' . \$sm->created_at . '\n';
"

echo ""
echo "🛣️  Routes accessibles:"
echo "  - Connexion: /adminloginmaisjustedutextepourplusdesecurite"

echo ""
echo "✅ Vérification terminée"
