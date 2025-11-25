

# 📱 SANDBOX MOBILE MONEY - DOCUMENTATION COMPLÈTE

## 🎯 OBJECTIF

Remplacer KKiaPay par un système de sandbox qui :
- ✅ Utilise **un seul compte MoMo** (pas besoin de numéro marchand)
- ✅ Supporte **MTN, Moov, Celtiis**
- ✅ Interface qui se superpose comme KKiaPay
- ✅ Validation via **SMS Gateway API** (webhook)
- ✅ 100% automatisé et invisible

---

## 🔧 COMMENT ÇA MARCHE

### Flux de paiement :

```
1. Client clique sur "Voter"
   ↓
2. Sandbox s'ouvre (modal)
   ↓
3. Client choisit opérateur (MTN/Moov/Celtiis)
   ↓
4. Client entre son numéro de téléphone
   ↓
5. Système affiche : "Envoyez XXX FCFA au numéro YYY"
   ↓
6. Client fait le transfert MoMo depuis son téléphone
   ↓
7. Vous recevez un SMS de confirmation
   ↓
8. SMS Gateway API envoie le SMS à votre webhook
   ↓
9. Backend valide automatiquement le paiement
   ↓
10. Vote créé et enregistré ✅
```

---

## 📊 ARCHITECTURE

### Base de données : Table `payment_sandbox`

```sql
reference          → Identifiant unique (SBX-ABC123-timestamp)
miss_id            → Candidate pour qui on vote
operator           → mtn, moov, ou celtiis
phone_number       → Numéro du client
amount             → Montant total
vote_count         → Nombre de votes
status             → pending, confirmed, failed, expired
momo_number        → Votre numéro MoMo de réception
sms_content        → Contenu du SMS reçu
sms_received_at    → Quand le SMS est arrivé
expires_at         → Expiration (10 minutes)
```

### Statuts :
- `pending` : En attente du paiement
- `confirmed` : Paiement reçu et validé
- `failed` : Échec du paiement
- `expired` : Délai expiré (10 min)

---

## 🔐 CONFIGURATION

### 1. Variables d'environnement (.env)

```env
# Numéros MoMo de réception (VOS numéros)
MOMO_MTN_NUMBER=91234567
MOMO_MOOV_NUMBER=97234567
MOMO_CELTIIS_NUMBER=99234567

# SMS Gateway API
SMS_GATEWAY_API_KEY=votre_cle_api_secrete_ici
SMS_GATEWAY_WEBHOOK_URL=https://votre-domaine.com/api/webhook/sms
```

### 2. Configuration SMS Gateway API

Sur votre compte SMS Gateway API :
1. Configurer le webhook : `https://votre-domaine.com/api/webhook/sms`
2. Ajouter le header : `X-API-Key: votre_cle_api_secrete_ici`
3. Activer la réception des SMS pour votre numéro SIM

---

## 📱 INTERFACE UTILISATEUR

### Modal Sandbox (comme KKiaPay)

**Étape 1 : Choix de l'opérateur**
- Boutons MTN / Moov / Celtiis
- Design moderne avec logos et couleurs

**Étape 2 : Numéro de téléphone**
- Input pour le numéro du client
- Instructions de paiement claires
- Code USSD affiché

**Étape 3 : Attente du paiement**
- Animation de chargement
- Numéro de réception affiché
- Montant à envoyer affiché
- Compteur de temps (10 minutes)
- Vérification automatique toutes les 3 secondes

**Étape 4 : Confirmation**
- Message de succès
- Redirection automatique

---

## 🔄 WEBHOOKS SMS GATEWAY API

### Format du webhook reçu :

```json
{
  "from": "22991234567",
  "message": "Vous avez recu 500 FCFA de 91234567. Ref: ABC123. Solde: 1000 FCFA",
  "timestamp": "2025-11-25T10:30:00Z"
}
```

### Parsing automatique :

Le système détecte automatiquement :
- Le montant (500 FCFA)
- Le numéro de l'expéditeur (91234567)
- L'opérateur (MTN/Moov basé sur le numéro)

### Matching intelligent :

Le système trouve la transaction en attente qui correspond :
- Même montant
- Même numéro (8 derniers chiffres)
- Statut pending
- Non expirée

---

## 🛠️ COMMANDES UTILES

### Monitorer les paiements en attente :
```bash
php artisan payments:monitor
```

### Expirer les vieux paiements :
```bash
php artisan payments:expire
```

### Voir tous les paiements :
```bash
php artisan tinker
>>> PaymentSandbox::all();
```

### Confirmer manuellement un paiement :
```bash
php artisan tinker
>>> $payment = PaymentSandbox::where('reference', 'SBX-XXX')->first();
>>> $payment->status = 'confirmed';
>>> $payment->save();
```

---

## 🧪 TESTS

### Test du webhook SMS :

```bash
curl -X POST https://votre-domaine.com/api/webhook/sms \
  -H "Content-Type: application/json" \
  -H "X-API-Key: votre_cle_api" \
  -d '{
    "from": "22991234567",
    "message": "Vous avez recu 500 FCFA de 91234567. Ref: ABC123"
  }'
```

### Test d'initialisation :

```bash
curl -X POST https://votre-domaine.com/api/sandbox/initiate \
  -H "Content-Type: application/json" \
  -d '{
    "miss_id": 1,
    "operator": "mtn",
    "phone_number": "91234567",
    "amount": 500,
    "vote_count": 5
  }'
```

---

## 📋 FLUX COMPLET - EXEMPLE

### Scénario : Client veut voter 5 fois (500 FCFA)

1. **Client clique sur "Voter"**
   → Sandbox s'ouvre

2. **Client choisit MTN**
   → Sandbox affiche le formulaire

3. **Client entre son numéro : 91234567**
   → Sandbox affiche :
   - "Envoyez 500 FCFA au 91234567"
   - Instructions USSD
   - Référence : SBX-ABC123...

4. **Client fait le transfert MoMo**
   → Depuis son téléphone : *155# → Transfert → 91234567 → 500 FCFA

5. **Vous recevez le SMS**
   → "Vous avez reçu 500 FCFA de 91234567..."

6. **SMS Gateway API envoie le webhook**
   → POST /api/webhook/sms avec le contenu du SMS

7. **Backend valide automatiquement**
   → Parse le SMS
   → Trouve la transaction (référence SBX-ABC123...)
   → Crée la transaction officielle
   → Crée 5 votes
   → Marque le paiement comme confirmé

8. **Frontend détecte la confirmation**
   → Arrête la vérification automatique
   → Affiche "Paiement confirmé ✅"
   → Redirige vers la page de succès

---

## 🔒 SÉCURITÉ

### Protection du webhook :
- ✅ Vérification de l'API Key
- ✅ Log de toutes les tentatives
- ✅ Exclusion du CSRF pour le webhook
- ✅ Validation stricte du format SMS

### Protection contre la fraude :
- ✅ Expiration automatique (10 minutes)
- ✅ Matching strict (montant + numéro)
- ✅ Numéro de téléphone validé
- ✅ Un seul paiement par référence

### Logs complets :
- Tous les SMS reçus
- Toutes les initialisations
- Toutes les confirmations
- Toutes les erreurs

---

## 🎨 PERSONNALISATION

### Changer les numéros MoMo :

Dans `.env` :
```env
MOMO_MTN_NUMBER=99999999
MOMO_MOOV_NUMBER=97777777
MOMO_CELTIIS_NUMBER=95555555
```

### Changer le délai d'expiration :

Dans `SandboxPaymentController.php` :
```php
'expires_at' => now()->addMinutes(15)  // 15 minutes au lieu de 10
```

### Ajouter un opérateur :

1. Modifier la migration (enum operator)
2. Ajouter dans le modal
3. Ajouter le parsing SMS
4. Ajouter la configuration

---

## 📊 STATISTIQUES

### Voir les statistiques :
```sql
-- Paiements par statut
SELECT status, COUNT(*) as count, SUM(amount) as total
FROM payment_sandbox
GROUP BY status;

-- Paiements par opérateur
SELECT operator, COUNT(*) as count, SUM(vote_count) as votes
FROM payment_sandbox
WHERE status = 'confirmed'
GROUP BY operator;

-- Taux de conversion
SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
    ROUND(SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) / COUNT(*) * 100, 2) as taux
FROM payment_sandbox;
```

---

## 🚨 PROBLÈMES COURANTS

### Webhook ne reçoit pas les SMS :
1. Vérifier la configuration SMS Gateway API
2. Vérifier que l'URL du webhook est correcte
3. Vérifier l'API Key
4. Consulter les logs : `storage/logs/laravel.log`

### Paiement non détecté :
1. Vérifier le format du SMS reçu
2. Ajuster le pattern de parsing si nécessaire
3. Vérifier que le montant correspond exactement
4. Vérifier que le numéro correspond (8 derniers chiffres)

### Paiement expire trop vite :
1. Augmenter le délai dans `SandboxPaymentController`
2. Ou exécuter moins souvent `payments:expire`

---

## 🔧 MAINTENANCE

### Nettoyer les vieux paiements :
```bash
# Supprimer les paiements de plus de 30 jours
php artisan tinker
>>> PaymentSandbox::where('created_at', '<', now()->subDays(30))->delete();
```

### Automatiser le nettoyage :

Dans `app/Console/Kernel.php` :
```php
$schedule->command('payments:expire')->everyFiveMinutes();
```

---

## 📞 INTÉGRATION SMS GATEWAY API

### Services compatibles :
- SMS Gateway API (Android app)
- Twilio
- Nexmo/Vonage
- Africa's Talking
- Tout service avec webhook

### Format attendu :
Le webhook doit envoyer :
- `from` ou `sender` : Numéro de l'expéditeur
- `message` ou `text` : Contenu du SMS
- Header `X-API-Key` : Votre clé secrète

---

## ✅ AVANTAGES DE CE SYSTÈME

1. **Pas de numéro marchand** : Utilisez votre propre compte MoMo
2. **Multi-opérateurs** : MTN, Moov, Celtiis
3. **Automatisé** : Webhook valide automatiquement
4. **Interface moderne** : Modal comme KKiaPay
5. **Traçable** : Tous les SMS et paiements logués
6. **Sécurisé** : API Key, matching strict, expiration
7. **Économique** : Pas de frais de plateforme de paiement

---

## 🎯 MISE EN PRODUCTION

1. Configurer vos numéros MoMo dans `.env`
2. Configurer SMS Gateway API avec le webhook
3. Tester le webhook manuellement
4. Remplacer KKiaPay par la sandbox
5. Monitorer les paiements régulièrement

---

**Version** : 1.0  
**Date** : 25 novembre 2025  
**Statut** : Production Ready 🚀
