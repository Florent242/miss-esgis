# 📦 Instructions de Déploiement

## Sur le serveur (reineesgis@serveur)

```bash
cd /var/www/miss-esgis

# 1. Pull les changements
git pull origin main

# 2. Nettoyer tous les caches
php artisan view:clear
php artisan cache:clear  
php artisan config:clear
php artisan route:clear

# 3. Re-optimiser
php artisan config:cache
php artisan route:cache

# 4. Redémarrer PHP-FPM
sudo systemctl restart php8.2-fpm

# 5. Tester l'API
curl https://reine-esgis.com/api/sandbox/operators
```

## Vérifier que tout fonctionne

```bash
# Voir les logs en temps réel
tail -f storage/logs/laravel.log

# Tester le paiement
curl -X POST https://reine-esgis.com/api/sandbox/initiate \
  -H "Content-Type: application/json" \
  -d '{"operator":"mtn","phone":"97000000","amount":100,"miss_id":1}'
```

## En cas de problème

1. Vérifier les logs: `tail -100 storage/logs/laravel.log`
2. Vérifier le .env: `grep MTN .env`
3. Nettoyer tout: `php artisan optimize:clear`
