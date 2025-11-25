# 🚀 CONFIGURATION API MOMO OFFICIELLE (DÉBIT DIRECT)

## 🎯 AVANTAGE DU DÉBIT DIRECT

**Avec débit direct** :
- Client clique "Payer"
- Entre son numéro
- Pop-up apparaît AUTOMATIQUEMENT sur son téléphone ✅
- Client tape juste son PIN
- Paiement confirmé

**Vs transfert manuel** :
- Client doit composer *155#
- Naviguer dans les menus
- Entrer le numéro manuellement
- ❌ Plus long et compliqué

---

## 📋 PRÉREQUIS

### 1. MTN MoMo API

**Inscription** :
1. Aller sur https://momodeveloper.mtn.com/
2. Créer un compte développeur
3. Créer une souscription "Collection"
4. Obtenir vos clés API

**Credentials nécessaires** :
- `API User` (UUID)
- `API Key` (clé secrète)
- `Subscription Key` (Ocp-Apim-Subscription-Key)

### 2. Moov Africa API

**Inscription** :
1. Contacter Moov Africa Business
2. Demander l'accès à l'API "Collect"
3. Obtenir votre API Key

---

## 🔧 CONFIGURATION

### Fichier .env

```env
# MTN MoMo API (Débit Direct)
MTN_MOMO_API_USER=xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
MTN_MOMO_API_KEY=votre_api_key_secrete
MTN_MOMO_SUBSCRIPTION_KEY=votre_subscription_key
MTN_MOMO_ENVIRONMENT=mtncameroon  # ou sandbox pour les tests
MOMO_MTN_NUMBER=91234567  # Votre numéro pour référence

# Moov Money API
MOOV_API_KEY=votre_moov_api_key
MOMO_MOOV_NUMBER=97234567

# Celtiis (transfert manuel pour le moment)
MOMO_CELTIIS_NUMBER=99234567

# SMS Gateway API (fallback si API échoue)
SMS_GATEWAY_API_KEY=votre_cle_secrete_sms_gateway
```

---

## 🔄 FLUX AVEC DÉBIT DIRECT

### MTN MoMo (avec API) :

```
1. Client clique "Payer"
   ↓
2. Client entre son numéro : 91234567
   ↓
3. Client clique "Lancer le paiement"
   ↓
4. Backend appelle MTN API Collection
   ↓
5. MTN envoie un pop-up USSD au téléphone du client
   ↓
6. Client voit : "Confirmez le paiement de 500 FCFA"
   ↓
7. Client tape son PIN
   ↓
8. Backend vérifie le statut toutes les 3 secondes
   ↓
9. Statut = "SUCCESSFUL"
   ↓
10. Votes créés automatiquement ✅
```

### Moov Money (avec API) :

Même flux que MTN.

### Celtiis (sans API - fallback manuel) :

```
1. Client entre son numéro
   ↓
2. Affichage : "Composez *124# et envoyez 500 FCFA au 99234567"
   ↓
3. Attente du SMS de confirmation
   ↓
4. Webhook SMS valide le paiement
```

---

## 🧪 MODE SANDBOX (TESTS)

### MTN Sandbox :

```env
MTN_MOMO_ENVIRONMENT=sandbox
MTN_MOMO_API_USER=sandbox_user_id
MTN_MOMO_API_KEY=sandbox_api_key
MTN_MOMO_SUBSCRIPTION_KEY=sandbox_subscription_key
```

**Numéros de test MTN** :
- `46733123450` : Paiement réussit toujours
- `46733123451` : Paiement échoue toujours
- `46733123452` : Paiement en attente indéfiniment

### Test complet :

```bash
# 1. Initialiser un paiement de test
curl -X POST http://127.0.0.1:8000/api/sandbox/initiate \
  -H "Content-Type: application/json" \
  -d '{
    "miss_id": 1,
    "operator": "mtn",
    "phone_number": "46733123450",
    "amount": 500,
    "vote_count": 5
  }'

# 2. Vérifier le statut
curl -X POST http://127.0.0.1:8000/api/sandbox/status \
  -H "Content-Type: application/json" \
  -d '{"reference": "la-reference-retournee"}'
```

---

## 📊 COMPARAISON DES MÉTHODES

| Méthode | Pop-up auto | Facilité | Coût | Configuration |
|---------|-------------|----------|------|---------------|
| **MTN API** | ✅ Oui | ⭐⭐⭐⭐⭐ | Faible | Complexe |
| **Moov API** | ✅ Oui | ⭐⭐⭐⭐⭐ | Faible | Moyenne |
| **Transfert manuel + SMS** | ❌ Non | ⭐⭐⭐ | Gratuit | Simple |
| **KKiaPay** | ✅ Oui | ⭐⭐⭐⭐⭐ | 2% | Facile |

---

## 🎯 STRATÉGIE RECOMMANDÉE : HYBRIDE

```php
// Dans SandboxPaymentController.php

if (env('MTN_MOMO_API_USER')) {
    // Utiliser MTN API (débit direct)
    $result = $this->momoService->requestToPay(...);
} else {
    // Fallback : transfert manuel + SMS Gateway
    $result = ['success' => true, 'method' => 'manual'];
}
```

**Avantages** :
- ✅ MTN API si disponible (meilleure expérience)
- ✅ Transfert manuel sinon (toujours fonctionnel)
- ✅ Pas de dépendance critique
- ✅ Flexibilité maximale

---

## 🔐 SÉCURITÉ API MOMO

### Stocker les credentials en sécurité :

```bash
# Générer une clé de chiffrement
php artisan key:generate

# Les credentials dans .env sont automatiquement chiffrés
```

### Ne JAMAIS commiter :
- API Keys
- Subscription Keys
- Tokens OAuth

### Permissions minimales :
- MTN : Collection uniquement (pas Disbursement)
- Moov : Collect uniquement

---

## 📚 DOCUMENTATION OFFICIELLE

### MTN MoMo :
- Portal : https://momodeveloper.mtn.com/
- Docs : https://momodeveloper.mtn.com/api-documentation/
- Sandbox : https://sandbox.momodeveloper.mtn.com/

### Moov Africa :
- Contact : business@moov-africa.bj
- Docs : Fournie après inscription

---

## 💡 CONSEIL POUR DÉMARRER

**Option 1 : Commencer simple (recommandé)**
```
1. Utiliser le transfert manuel + SMS Gateway
2. Tester et valider le système
3. Migrer vers MTN API plus tard
```

**Option 2 : Débit direct immédiat**
```
1. S'inscrire sur MTN Developer Portal
2. Obtenir les clés API
3. Configurer dans .env
4. Tester en mode sandbox
5. Passer en production
```

---

## 🧪 TESTER LE DÉBIT DIRECT

```bash
# Avec le script de test
bash scripts/test_sandbox.sh

# Ou manuellement
php artisan tinker
>>> $service = new App\Services\MoMoPaymentService();
>>> $result = $service->requestToPay('mtn', '91234567', 500, 'TEST-'.time());
>>> print_r($result);
```

---

**Recommandation** : Commencez avec le transfert manuel + SMS Gateway (gratuit et simple), puis ajoutez MTN API quand vous êtes prêt ! 🚀
