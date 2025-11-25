# 💰 MTN MOMO API - TARIFICATION ET CONFIGURATION COMPLÈTE

## 🎯 INFORMATIONS API MTN MOMO

### 📊 TARIFS ET COMMISSIONS

**API MTN MoMo Collection (Débit direct)** :

1. **Frais MTN** :
   - Sandbox (Tests) : **GRATUIT** ✅
   - Production : Variable selon le pays
     * Bénin : ~1.5% par transaction
     * Cameroun : ~1.5% par transaction
     * Côte d'Ivoire : ~1.5% par transaction

2. **Votre commission** :
   - Ajoutez 2% supplémentaire
   - Total client : 3.5% (1.5% MTN + 2% vous)

**Exemple** :
```
Vote = 100 FCFA
Frais MTN (1.5%) = 1.5 FCFA
Votre commission (2%) = 2 FCFA
Total client = 103.5 FCFA → Arrondi à 104 FCFA
```

---

## 📝 CE QU'IL FAUT FOURNIR À MTN

### Documents requis :

1. **Informations entreprise** :
   - Nom de l'entreprise
   - Numéro d'enregistrement
   - Adresse physique
   - Pays d'opération

2. **Informations techniques** :
   - URL du site web
   - Description de l'application
   - Volume de transactions estimé
   - URL du webhook (callback)

3. **Documents légaux** :
   - Copie du registre de commerce
   - Pièce d'identité du représentant
   - Justificatif de domicile

4. **Informations bancaires** :
   - RIB / IBAN
   - Nom de la banque
   - Pour recevoir les fonds collectés

---

## 🔧 CONFIGURATION TECHNIQUE

### 1. Inscription sur MTN Developer Portal

**URL** : https://momodeveloper.mtn.com/

**Étapes** :
1. Créer un compte développeur
2. Vérifier votre email
3. Créer un nouveau produit "Collection"
4. Souscrire aux APIs :
   - **Collection** (pour collecter l'argent) ✅
   - **Remittance** (pour envoyer - pas nécessaire)
   - **Disbursement** (pour distribuer - pas nécessaire)

### 2. Obtenir les credentials

Après validation de votre compte :

```
Primary Key (Ocp-Apim-Subscription-Key) : xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
Secondary Key : xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

### 3. Créer un API User

```bash
# Via l'API ou le portal
curl -X POST https://sandbox.momodeveloper.mtn.com/v1_0/apiuser \
  -H "Ocp-Apim-Subscription-Key: your_subscription_key" \
  -H "X-Reference-Id: your_uuid" \
  -H "Content-Type: application/json" \
  -d '{
    "providerCallbackHost": "votre-domaine.com"
  }'
```

### 4. Générer l'API Key

```bash
curl -X POST https://sandbox.momodeveloper.mtn.com/v1_0/apiuser/{uuid}/apikey \
  -H "Ocp-Apim-Subscription-Key: your_subscription_key"
```

---

## 💵 CALCUL DES COMMISSIONS

### Dans votre code :

```php
// app/Services/MoMoPaymentService.php

private function calculateFees($baseAmount)
{
    // Frais MTN : 1.5%
    $mtnFee = $baseAmount * 0.015;
    
    // Votre commission : 2%
    $yourCommission = $baseAmount * 0.02;
    
    // Total
    $totalFee = $mtnFee + $yourCommission;
    
    // Montant final
    $totalAmount = $baseAmount + $totalFee;
    
    return [
        'base_amount' => $baseAmount,
        'mtn_fee' => $mtnFee,
        'your_commission' => $yourCommission,
        'total_fee' => $totalFee,
        'total_amount' => ceil($totalAmount) // Arrondi supérieur
    ];
}
```

### Exemple d'utilisation :

```php
// Pour 5 votes (500 FCFA base)
$fees = $this->calculateFees(500);

// Résultat :
[
    'base_amount' => 500,        // Prix des votes
    'mtn_fee' => 7.5,           // 1.5% pour MTN
    'your_commission' => 10,     // 2% pour vous
    'total_fee' => 17.5,
    'total_amount' => 518        // Ce que le client paie
]
```

---

## 🌍 ENVIRONNEMENTS MTN MOMO

### Sandbox (Tests) :

```env
MTN_MOMO_ENVIRONMENT=sandbox
MTN_MOMO_API_USER=sandbox_user_uuid
MTN_MOMO_API_KEY=sandbox_key
MTN_MOMO_SUBSCRIPTION_KEY=sandbox_subscription_key
```

**Base URL** : `https://sandbox.momodeveloper.mtn.com`

### Production (par pays) :

**Bénin** :
```env
MTN_MOMO_ENVIRONMENT=mtnbenin
```
Base URL : `https://proxy.momoapi.mtn.bj`

**Cameroun** :
```env
MTN_MOMO_ENVIRONMENT=mtncameroon
```
Base URL : `https://proxy.momoapi.mtn.cm`

**Côte d'Ivoire** :
```env
MTN_MOMO_ENVIRONMENT=mtnivorycoast
```
Base URL : `https://proxy.momoapi.mtn.ci`

**Congo** :
```env
MTN_MOMO_ENVIRONMENT=mtncongo
```

**Ghana** :
```env
MTN_MOMO_ENVIRONMENT=mtnghana
```

---

## 🔑 CREDENTIALS COMPLETS

### Ce que vous recevrez de MTN :

```
1. Subscription Key (Primary)
   → Pour authentifier vos appels API
   
2. Subscription Key (Secondary)
   → Backup de la primary
   
3. API User UUID
   → Identifiant unique de votre application
   
4. API Key
   → Clé secrète pour OAuth
   
5. Callback URL
   → URL où MTN envoie les notifications
```

---

## 📦 INTÉGRATION DANS VOTRE PROJET

### Mise à jour du SandboxPaymentController :

```php
public function initiate(Request $request)
{
    $validated = $request->validate([
        'miss_id' => 'required|exists:misses,id',
        'operator' => 'required|in:mtn,moov,celtiis',
        'phone_number' => 'required|string',
        'amount' => 'required|numeric|min:100',
        'vote_count' => 'required|integer|min:1'
    ]);

    // Calculer les frais
    $baseAmount = $validated['amount'];
    $fees = $this->calculateFees($baseAmount);
    
    // Montant total que le client paie
    $totalAmount = $fees['total_amount'];
    
    // Créer le paiement
    $payment = PaymentSandbox::create([
        'reference' => strtoupper(Str::uuid()->toString()),
        'miss_id' => $validated['miss_id'],
        'operator' => $validated['operator'],
        'phone_number' => $validated['phone_number'],
        'amount' => $totalAmount,  // Montant total
        'base_amount' => $baseAmount,  // Montant sans frais
        'commission' => $fees['your_commission'],  // Votre gain
        'vote_count' => $validated['vote_count'],
        'status' => 'pending',
        'expires_at' => now()->addMinutes(5)
    ]);

    // Déclencher le débit via MTN API
    $result = $this->momoService->requestToPay(
        $validated['operator'],
        $validated['phone_number'],
        $totalAmount,  // Débiter le total
        $payment->reference
    );

    return response()->json([
        'success' => true,
        'reference' => $payment->reference,
        'amount' => $totalAmount,
        'base_amount' => $baseAmount,
        'fees' => $fees['total_fee'],
        'message' => 'Vérifiez votre téléphone'
    ]);
}

private function calculateFees($baseAmount)
{
    $mtnFee = $baseAmount * 0.015;  // 1.5%
    $yourCommission = $baseAmount * 0.02;  // 2%
    $totalFee = $mtnFee + $yourCommission;
    
    return [
        'base_amount' => $baseAmount,
        'mtn_fee' => $mtnFee,
        'your_commission' => $yourCommission,
        'total_fee' => $totalFee,
        'total_amount' => ceil($baseAmount + $totalFee)
    ];
}
```

---

## 📋 PROCESSUS D'INSCRIPTION COMPLET

### Phase 1 : Sandbox (Tests - GRATUIT)

1. **Créer un compte** : https://momodeveloper.mtn.com/signup
2. **Vérifier email**
3. **Créer un produit** : "Miss ESGIS Voting"
4. **Souscrire à Collection API**
5. **Obtenir Primary Key**
6. **Créer API User** (via script ou portal)
7. **Générer API Key**
8. **Tester** avec numéros sandbox

### Phase 2 : Production (Payant)

1. **Soumettre dossier** :
   - Formulaire KYC (Know Your Customer)
   - Documents légaux
   - Informations bancaires

2. **Validation MTN** :
   - Délai : 3-5 jours ouvrables
   - Vérification des documents
   - Approbation commerciale

3. **Activation** :
   - Credentials de production fournis
   - Migration du code sandbox → production
   - Tests en production

4. **Go Live** :
   - Activation du compte marchand
   - Début de la collecte réelle

---

## 💸 STRUCTURE DE COÛTS

### Tarifs MTN (Production) :

| Montant transaction | Frais MTN | Votre commission (2%) | Total frais |
|---------------------|-----------|----------------------|-------------|
| 100 FCFA | 1.5 FCFA | 2 FCFA | 3.5 FCFA |
| 500 FCFA | 7.5 FCFA | 10 FCFA | 17.5 FCFA |
| 1000 FCFA | 15 FCFA | 20 FCFA | 35 FCFA |
| 5000 FCFA | 75 FCFA | 100 FCFA | 175 FCFA |

### Revenus estimés :

**Scénario 1** : 1000 votes/mois
- Votes : 1000 × 100 FCFA = 100,000 FCFA
- Votre commission (2%) = 2,000 FCFA/mois

**Scénario 2** : 10,000 votes/mois
- Votes : 10,000 × 100 FCFA = 1,000,000 FCFA
- Votre commission (2%) = 20,000 FCFA/mois

---

## 🔄 WEBHOOK MTN (Callback)

MTN peut envoyer des notifications à votre serveur :

```php
// Route dans api.php
Route::post('/webhook/mtn', [MtnWebhookController::class, 'receive']);

// MtnWebhookController.php
public function receive(Request $request)
{
    $data = $request->all();
    
    // MTN envoie :
    // {
    //   "financialTransactionId": "123456",
    //   "externalId": "votre_reference",
    //   "amount": "500",
    //   "currency": "XOF",
    //   "payer": { "partyId": "91234567" },
    //   "status": "SUCCESSFUL"
    // }
    
    if ($data['status'] === 'SUCCESSFUL') {
        $payment = PaymentSandbox::where('reference', $data['externalId'])->first();
        if ($payment) {
            $this->confirmPayment($payment);
        }
    }
    
    return response()->json(['message' => 'OK'], 200);
}
```

---

## 🚀 SCRIPT DE MIGRATION VERS MTN API

```bash
#!/bin/bash
# scripts/migrate_to_mtn_api.sh

echo "🚀 Migration vers MTN MoMo API"
echo ""

# Ajouter la colonne commission
php artisan make:migration add_commission_to_payment_sandbox --table=payment_sandbox

# Mettre à jour le contrôleur avec les frais
# Tester en sandbox
# Puis passer en production

echo "✅ Migration terminée"
```

---

## 📞 CONTACTS MTN

### Support Développeur :
- Email : apisupport@mtn.com
- Portal : https://momodeveloper.mtn.com/support

### Par pays :

**Bénin** :
- Email : api.benin@mtn.com
- Tél : +229 XX XX XX XX

**Cameroun** :
- Email : api.cameroon@mtn.com

**Côte d'Ivoire** :
- Email : api.ivorycoast@mtn.com

---

## 🎯 ÉTAPES POUR DÉMARRER

### Option A : Tests gratuits (Sandbox)

```bash
1. Inscription sur momodeveloper.mtn.com
2. Créer un produit
3. Obtenir Primary Key
4. Configurer dans .env :
   MTN_MOMO_ENVIRONMENT=sandbox
   MTN_MOMO_SUBSCRIPTION_KEY=your_key
5. Tester avec numéros fictifs
```

### Option B : Production directe

```bash
1. Contacter MTN via le portal
2. Remplir le formulaire KYC
3. Soumettre les documents
4. Attendre validation (3-5 jours)
5. Recevoir credentials de production
6. Configurer et déployer
```

---

## 💡 RECOMMANDATION POUR VOTRE PROJET

### Stratégie tarifaire :

```php
// Dans votre configuration
const VOTE_PRICE = 100;  // Prix de base
const MTN_FEE_RATE = 0.015;  // 1.5%
const YOUR_COMMISSION_RATE = 0.02;  // 2%

function calculateVotePrice($voteCount) {
    $baseAmount = $voteCount * VOTE_PRICE;
    $totalFees = $baseAmount * (MTN_FEE_RATE + YOUR_COMMISSION_RATE);
    return [
        'base' => $baseAmount,
        'fees' => ceil($totalFees),
        'total' => $baseAmount + ceil($totalFees)
    ];
}
```

### Affichage transparent :

```html
<!-- Dans la page de vote -->
<div class="pricing-breakdown">
    <div>Prix du vote : 500 FCFA</div>
    <div>Frais de transaction : 18 FCFA</div>
    <div class="total">Total à payer : 518 FCFA</div>
</div>
```

---

## 🔐 SÉCURITÉ ET BONNES PRATIQUES

### 1. Protéger les credentials :

```bash
# .env (JAMAIS commiter)
MTN_MOMO_API_USER=xxx
MTN_MOMO_API_KEY=xxx
MTN_MOMO_SUBSCRIPTION_KEY=xxx
```

### 2. Vérifier les webhooks :

```php
// Vérifier que la requête vient bien de MTN
$allowedIPs = [
    '196.201.0.0/16',  // Range IP MTN
    '41.202.0.0/16'
];
```

### 3. Logger toutes les transactions :

```php
Log::channel('momo')->info('Payment initiated', [
    'reference' => $reference,
    'amount' => $amount,
    'phone' => $phone
]);
```

---

## 📊 TABLEAU DE BORD (Dashboard)

### Métriques à suivre :

```sql
-- Volume de transactions
SELECT COUNT(*), SUM(amount), SUM(commission)
FROM payment_sandbox
WHERE status = 'confirmed'
AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY);

-- Par opérateur
SELECT operator, COUNT(*), SUM(commission) as revenue
FROM payment_sandbox
WHERE status = 'confirmed'
GROUP BY operator;

-- Taux de réussite
SELECT 
    status,
    COUNT(*) as count,
    ROUND(COUNT(*) * 100.0 / SUM(COUNT(*)) OVER(), 2) as percentage
FROM payment_sandbox
GROUP BY status;
```

---

## 🎯 CHECKLIST COMPLÈTE

### Sandbox (Tests) :
- [ ] Compte MTN Developer créé
- [ ] Produit "Collection" créé
- [ ] Primary Key obtenue
- [ ] API User créé
- [ ] API Key générée
- [ ] Configuration dans .env
- [ ] Test avec numéro sandbox
- [ ] Vérification du statut OK
- [ ] Webhook testé

### Production :
- [ ] Dossier KYC soumis
- [ ] Documents validés par MTN
- [ ] Credentials production reçus
- [ ] Configuration mise à jour
- [ ] Tests en production
- [ ] Monitoring activé
- [ ] Go Live !

---

## 💰 ESTIMATION DES REVENUS

### Avec 2% de commission :

| Votes/mois | Volume | Votre commission |
|------------|--------|------------------|
| 1,000 | 100,000 FCFA | 2,000 FCFA |
| 5,000 | 500,000 FCFA | 10,000 FCFA |
| 10,000 | 1,000,000 FCFA | 20,000 FCFA |
| 50,000 | 5,000,000 FCFA | 100,000 FCFA |
| 100,000 | 10,000,000 FCFA | 200,000 FCFA |

**Note** : Ces revenus s'ajoutent aux bénéfices des votes eux-mêmes !

---

## 🔄 ALTERNATIVE : SANS COMMISSION

Si vous préférez ne pas ajouter de frais :

```php
// Option 1 : Vous absorbez les frais MTN
$clientPays = $baseAmount;  // 500 FCFA
$youReceive = $baseAmount - ($baseAmount * 0.015);  // 492.5 FCFA

// Option 2 : Client paie les frais MTN, vous ne prenez rien
$clientPays = $baseAmount + ($baseAmount * 0.015);  // 507.5 FCFA
$youReceive = $baseAmount;  // 500 FCFA
```

---

## 🎯 MA RECOMMANDATION

**Pour commencer** :
1. Testez d'abord en **Sandbox** (gratuit)
2. Validez le flux complet
3. Puis passez en production

**Pour les frais** :
1. Commencez **sans commission** (absorber les frais MTN)
2. Une fois le système rodé, ajoutez 1-2%
3. Soyez transparent avec les utilisateurs

**Pour l'inscription** :
1. Utilisez le mode **Sandbox** pendant le développement
2. Contactez MTN Bénin pour la production : +229 21 30 08 00

---

📚 **Ressources** :
- Portal : https://momodeveloper.mtn.com/
- Docs API : https://momodeveloper.mtn.com/api-documentation/
- GitHub : https://github.com/MTN-Group/MoMoAPIs_Nodejs_sdk

🚀 **Prêt à démarrer avec MTN MoMo API !**
