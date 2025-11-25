#!/bin/bash

echo "🧹 Nettoyage du cache de production..."

# Se connecter au serveur et nettoyer
ssh reineesgis@serveur << 'ENDSSH'
cd /var/www/miss-esgis

# Nettoyer tous les caches
php artisan view:clear
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Re-optimiser sans view:cache (problème avec input-label)
php artisan config:cache
php artisan route:cache

echo "✅ Cache nettoyé avec succès"
ENDSSH

echo "✅ Terminé"
