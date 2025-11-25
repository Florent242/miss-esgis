# Configuration .env Production

## Variables MTN MoMo à ajouter

Sur votre serveur de production, éditez le fichier `.env` et ajoutez ces lignes :

```env
# MTN MoMo Configuration
MTN_MOMO_ENVIRONMENT=sandbox
MTN_MOMO_SUBSCRIPTION_KEY=aa3d492186e2441fbfaeb684b09e02e8
MTN_MOMO_API_USER=8a63ad08-0b36-4931-a448-f8d39f2f28d3
MTN_MOMO_API_KEY=667b86fd4fae4a17817104ba82a5c876
MTN_MOMO_CALLBACK_HOST=https://reine-esgis.com
```

## Étapes de déploiement

1. **Connectez-vous au serveur via SSH**
   ```bash
   ssh votre-utilisateur@reine-esgis.com
   ```

2. **Naviguez vers le répertoire du projet**
   ```bash
   cd /chemin/vers/miss-esgis
   ```

3. **Éditez le fichier .env**
   ```bash
   nano .env
   ```
   
   Ajoutez les lignes ci-dessus à la fin du fichier, puis sauvegardez (Ctrl+X, puis Y, puis Entrée)

4. **Lancez le script de déploiement**
   ```bash
   bash scripts/deploy_to_production.sh
   ```

5. **Vérifiez que tout fonctionne**
   ```bash
   # Test de l'API
   curl https://reine-esgis.com/api/sandbox/operators
   
   # Vérification des logs
   tail -f storage/logs/laravel.log
   ```

## Pour passer en PRODUCTION (Plus tard)

Quand vous serez prêt à passer du sandbox à la production MTN MoMo :

1. Sur https://momodeveloper.mtn.com/, générez de nouvelles clés pour l'environnement **Production**

2. Modifiez `.env` :
   ```env
   MTN_MOMO_ENVIRONMENT=production
   MTN_MOMO_SUBSCRIPTION_KEY=VOTRE_CLE_PRODUCTION
   MTN_MOMO_API_USER=GENERE_PAR_LE_SCRIPT
   MTN_MOMO_API_KEY=GENERE_PAR_LE_SCRIPT
   ```

3. Relancez le script de configuration :
   ```bash
   bash scripts/setup_mtn_api.sh
   ```

4. Relancez le déploiement :
   ```bash
   bash scripts/deploy_to_production.sh
   ```

## Dépannage

### Erreur 500 lors du paiement

```bash
# Vérifier les logs
tail -n 50 storage/logs/laravel.log

# Vérifier la configuration
php artisan config:show momo

# Clear tous les caches
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
```

### Variables .env non reconnues

```bash
# Rechargez la configuration
php artisan config:clear
php artisan config:cache
```

### Permissions incorrectes

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```
