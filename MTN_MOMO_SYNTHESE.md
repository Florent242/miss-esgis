# 🎯 Synthèse MTN MoMo - Configuration et Tests

**Date**: 25 Novembre 2025  
**Status**: ✅ OPÉRATIONNEL EN SANDBOX - TOUS LES ENDPOINTS FONCTIONNELS

---

## 📊 État Actuel

### ✅ Ce qui fonctionne

- **Environnement**: SANDBOX
- **Authentification**: OK avec vos clés
- **RequestToPay**: OK (statut 202 Accepted)
- **Vérification statut**: ✅ OK (erreur 500 corrigée)
- **Formats de numéro**: Tous formats acceptés

### 🔧 Configuration Active

```env
MTN_MOMO_API_USER=9ac129dd-f753-4eac-b515-13da14e32534
MTN_MOMO_API_KEY=e44e106fcd4c43b09c7049c587f325a2
MTN_MOMO_SUBSCRIPTION_KEY=aa3d492186e2441fbfaeb684b09e02e8
MTN_MOMO_SECONDARY_KEY=039f3ed61e734aedae7ebbe5cc157fab
MTN_MOMO_ENVIRONMENT=sandbox
```

---

## 🐛 Problèmes Résolus

### 1. ❌ Erreur 400 Bad Request
**Cause**: UUID en majuscules  
**Solution**: Changé `strtoupper()` → `strtolower()`

### 2. ❌ Token Error (HTML Response)
**Cause**: Header `Content-Length` manquant  
**Solution**: Ajouté `'Content-Length' => '0'` pour `/collection/token/`

### 3. ❌ Invalid Currency (500 Error)
**Cause**: XOF non supporté en sandbox  
**Solution**: EUR pour sandbox, XOF pour production

### 4. ❌ Status endpoint 500 Error
**Cause**: Colonne `sms_received_at` inexistante dans la table  
**Solution**: Supprimé la ligne `$payment->sms_received_at = now()`

---

## 🧪 Tests Automatiques

### Script de Test Sandbox
```bash
/var/www/miss-esgis/tests/mtn_sandbox_test.sh
```

**Tests effectués**:
- ✅ Paiement simple (100 FCFA)
- ✅ Format numéro béninois avec espaces
- ✅ Vote multiple (200 FCFA = 2 votes)
- ✅ Vérification de statut

**Résultats**: 3/3 tests réussis

### Logs
```bash
/var/www/miss-esgis/storage/logs/mtn_sandbox_tests.log
```

---

## 🔄 Modifications du Code

### 1. `app/Services/MoMoPaymentService.php`

#### Ajout méthode getCurrency
```php
private function getCurrency($operator, $environment)
{
    if ($operator === 'mtn') {
        return $environment === 'production' ? 'XOF' : 'EUR';
    }
    return 'XOF';
}
```

#### Fix token avec Content-Length
```php
$tokenResponse = Http::withHeaders([
    'Ocp-Apim-Subscription-Key' => $subscriptionKey,
    'Content-Length' => '0',  // CRUCIAL
])->withBasicAuth($apiUser, $apiKey)
  ->post($baseUrl . '/collection/token/');
```

### 2. `app/Http/Controllers/SandboxPaymentController.php`

#### UUID en lowercase
```php
// Avant
$reference = strtoupper(Str::uuid()->toString());

// Après  
$reference = strtolower(Str::uuid()->toString());
```

---

## 🚀 Passage en Production

### ⚠️ Attention
Vos clés actuelles sont **SANDBOX UNIQUEMENT**. Pour la production:

### Étape 1: Obtenir des Clés Production
1. Aller sur https://momodeveloper.mtn.com
2. Créer une nouvelle **Product Subscription** en mode **Production**
3. Activer "Collections" pour la production
4. Obtenir nouvelles Primary/Secondary keys

### Étape 2: Créer API User Production
```bash
# Avec les nouvelles clés production
curl -X POST https://proxy.momoapi.mtn.com/v1_0/apiuser \
  -H "X-Reference-Id: <nouveau-uuid>" \
  -H "Ocp-Apim-Subscription-Key: <production-primary-key>" \
  -d '{"providerCallbackHost": "reine-esgis.com"}'
```

### Étape 3: Mettre à jour .env
```env
MTN_MOMO_API_USER=<nouveau-uuid-production>
MTN_MOMO_API_KEY=<nouvelle-api-key-production>
MTN_MOMO_SUBSCRIPTION_KEY=<production-primary-key>
MTN_MOMO_ENVIRONMENT=production
```

### Étape 4: Tester
```bash
/var/www/miss-esgis/tests/mtn_production_test.sh
```

---

## 📝 Différences Sandbox vs Production

| Aspect | Sandbox | Production |
|--------|---------|------------|
| **URL** | sandbox.momodeveloper.mtn.com | proxy.momoapi.mtn.com |
| **Currency** | EUR | XOF |
| **Numéros** | 46733123453 (test) | Vrais numéros MTN |
| **Paiements** | Simulés | Réels |
| **Clés** | reine-esgis (actuelles) | Nouvelles clés requis |
| **Validation** | Auto | Requiert approbation MTN |

---

## 🎯 Test API Direct

### Obtenir un token
```bash
curl -X POST https://sandbox.momodeveloper.mtn.com/collection/token/ \
  -u "9ac129dd-f753-4eac-b515-13da14e32534:e44e106fcd4c43b09c7049c587f325a2" \
  -H "Ocp-Apim-Subscription-Key: aa3d492186e2441fbfaeb684b09e02e8" \
  -H "Content-Length: 0"
```

### Faire un requestToPay
```bash
curl -X POST https://sandbox.momodeveloper.mtn.com/collection/v1_0/requesttopay \
  -H "Authorization: Bearer <TOKEN>" \
  -H "X-Reference-Id: <uuid-lowercase>" \
  -H "X-Target-Environment: sandbox" \
  -H "Ocp-Apim-Subscription-Key: aa3d492186e2441fbfaeb684b09e02e8" \
  -H "Content-Type: application/json" \
  -d '{
    "amount": "100",
    "currency": "EUR",
    "externalId": "<uuid>",
    "payer": {
      "partyIdType": "MSISDN",
      "partyId": "46733123453"
    },
    "payerMessage": "Vote",
    "payeeNote": "Test"
  }'
```

---

## 📞 Support

**Documentation MTN**: https://momodeveloper.mtn.com/api-documentation  
**Support Email**: momo@mtn.com  
**Portal**: https://momodeveloper.mtn.com

---

## ✅ Checklist Prochaines Étapes

- [x] Fix erreur 400 Bad Request
- [x] Implémenter authentification correcte
- [x] Gérer currency dynamique (EUR/XOF)
- [x] UUID en lowercase
- [x] Tests automatiques sandbox
- [ ] Obtenir clés production MTN
- [ ] Créer API user production
- [ ] Tests avec vrais numéros MTN Bénin
- [ ] Go-live avec MTN
