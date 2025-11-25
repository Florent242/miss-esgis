# 🎯 Guide Complet MTN MoMo - Miss ESGIS

## ✅ STATUS: OPÉRATIONNEL EN SANDBOX

L'intégration MTN MoMo fonctionne parfaitement en mode **SANDBOX** (test).

---

## 🚀 Quick Start - Lancer les Tests

### Test Sandbox Complet
```bash
/var/www/miss-esgis/tests/mtn_sandbox_test.sh
```

### Test Workflow Complet (simule un vote utilisateur)
```bash
/var/www/miss-esgis/tests/mtn_workflow_test.sh
```

### Test Production (nécessite clés production)
```bash
/var/www/miss-esgis/tests/mtn_production_test.sh
```

---

## 📚 Documentation Disponible

| Fichier | Description |
|---------|-------------|
| **MTN_MOMO_SYNTHESE.md** | 📖 Synthèse complète : problèmes résolus, config, tests |
| **MTN_MOMO_FIX.md** | 🔧 Détails des corrections appliquées (25 Nov 2025) |
| **MTN_PRODUCTION_SETUP.md** | 🚀 Guide pour passer en production |
| **MTN_MOMO_PRICING.md** | 💰 Tarification et frais MTN |

---

## 🧪 Tests Disponibles

### 1. Tests Sandbox (`mtn_sandbox_test.sh`)
- ✅ Paiement simple (100 FCFA)
- ✅ Format numéro béninois
- ✅ Vote multiple (200 FCFA = 2 votes)
- ✅ Vérification de statut

**Résultat**: 3/3 tests réussis ✅

### 2. Workflow Complet (`mtn_workflow_test.sh`)
Simule un utilisateur réel qui vote:
1. Vérifie les opérateurs disponibles
2. Initie un paiement (300 FCFA = 3 votes)
3. Attend la confirmation
4. Vérifie le statut
5. Consulte les logs

**Résultat**: Workflow complet opérationnel ✅

### 3. Test Production (`mtn_production_test.sh`)
⚠️ Nécessite des clés production MTN (pas encore disponibles)

---

## 📊 Configuration Actuelle

### Environnement
```
MTN_MOMO_ENVIRONMENT=sandbox
```

### Clés (Sandbox uniquement)
```
MTN_MOMO_API_USER=9ac129dd-f753-4eac-b515-13da14e32534
MTN_MOMO_API_KEY=e44e106fcd4c43b09c7049c587f325a2
MTN_MOMO_SUBSCRIPTION_KEY=aa3d492186e2441fbfaeb684b09e02e8
```

### Caractéristiques Sandbox
- **URL**: `https://sandbox.momodeveloper.mtn.com`
- **Currency**: EUR (pas XOF)
- **Numéro test**: `46733123453`
- **Paiements**: Simulés (pas de vrais débits)

---

## 🔧 Modifications Apportées

### Code Modifié
1. **app/Services/MoMoPaymentService.php**
   - Ajout header `Content-Length: 0` pour token
   - Currency dynamique (EUR sandbox, XOF production)
   - Méthode `getCurrency()`

2. **app/Http/Controllers/SandboxPaymentController.php**
   - UUID en lowercase (`strtolower()`)

3. **.env**
   - Ajout `MTN_MOMO_SECONDARY_KEY`

### Problèmes Résolus
- ✅ Erreur 400 Bad Request → UUID lowercase
- ✅ Token error HTML → Header Content-Length
- ✅ Invalid currency → EUR pour sandbox

---

## 📈 Logs et Monitoring

### Logs de Test
```bash
# Logs des tests sandbox
/var/www/miss-esgis/storage/logs/mtn_sandbox_tests.log

# Logs du workflow complet
/var/www/miss-esgis/storage/logs/mtn_workflow_test.log

# Logs Laravel (tous)
/var/www/miss-esgis/storage/logs/laravel.log
```

### Surveiller en temps réel
```bash
tail -f /var/www/miss-esgis/storage/logs/laravel.log | grep -i mtn
```

---

## 🌍 Passage en Production

### Étapes Requises

1. **Obtenir clés production MTN**
   - Aller sur https://momodeveloper.mtn.com
   - Créer Product Subscription "Production"
   - Activer Collections

2. **Créer API User production**
   ```bash
   # Utiliser les nouvelles clés
   curl -X POST https://proxy.momoapi.mtn.com/v1_0/apiuser ...
   ```

3. **Mettre à jour .env**
   ```env
   MTN_MOMO_ENVIRONMENT=production
   MTN_MOMO_API_USER=<nouveau-uuid>
   MTN_MOMO_API_KEY=<nouvelle-key>
   MTN_MOMO_SUBSCRIPTION_KEY=<production-key>
   ```

4. **Tester**
   ```bash
   /var/www/miss-esgis/tests/mtn_production_test.sh
   ```

### Différences Production
| Aspect | Sandbox | Production |
|--------|---------|------------|
| Currency | EUR | XOF |
| Numéros | Test: 46733123453 | Vrais MTN Bénin |
| Paiements | Simulés | Réels |
| URL | sandbox.momodeveloper.mtn.com | proxy.momoapi.mtn.com |

---

## 🎯 Exemples d'Utilisation

### Via API Direct
```bash
curl -X POST https://reine-esgis.com/api/sandbox/initiate \
  -H "Content-Type: application/json" \
  -d '{
    "miss_id": 1,
    "operator": "mtn",
    "phone_number": "46733123453",
    "amount": 100,
    "vote_count": 1
  }'
```

**Réponse:**
```json
{
  "success": true,
  "reference": "7af0743a-5e30-4631-a2a9-2063e0ae453b",
  "operator": "mtn",
  "amount": 100,
  "message": "Vérifiez votre téléphone pour confirmer le paiement"
}
```

### Vérifier le Statut
```bash
curl -X POST https://reine-esgis.com/api/sandbox/status \
  -H "Content-Type: application/json" \
  -d '{"reference": "7af0743a-5e30-4631-a2a9-2063e0ae453b"}'
```

**Réponse:**
```json
{
  "status": "pending",
  "reference": "7af0743a-5e30-4631-a2a9-2063e0ae453b"
}
```

---

## 📞 Support & Resources

- **Documentation MTN**: https://momodeveloper.mtn.com/api-documentation
- **Portal**: https://momodeveloper.mtn.com
- **Support**: momo@mtn.com

---

## ✅ Checklist

- [x] Fix erreur 400 Bad Request
- [x] Authentification MTN fonctionnelle
- [x] Currency dynamique (EUR/XOF)
- [x] UUID en lowercase
- [x] Tests automatiques sandbox
- [x] Documentation complète
- [x] Logs et monitoring
- [ ] Obtenir clés production MTN
- [ ] Tests avec vrais numéros
- [ ] Go-live production

---

**Dernière mise à jour**: 25 Novembre 2025  
**Status**: ✅ Prêt pour tests sandbox | ⏳ En attente clés production
