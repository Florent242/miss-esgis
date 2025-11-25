# Sandbox vs Production - Comportement des Paiements

## ⚠️ PROBLÈME ACTUEL: Auto-Confirmation en Sandbox

### Symptôme
Les paiements passent automatiquement à `status: "confirmed"` SANS paiement réel.

### Cause
Vous êtes en mode **SANDBOX** (`MTN_MOMO_ENVIRONMENT=sandbox`):
- MTN Sandbox **simule** les paiements
- Les transactions sont automatiquement validées après 3-5 secondes
- **Aucun argent réel** n'est débité
- Les votes sont créés même sans paiement

## 🔍 Comportement Actuel (Sandbox)

```
1. Utilisateur clique "Payer"
   → API: POST /api/payment/initiate
   → MTN répond: 202 Accepted
   → Status: "pending"

2. Frontend vérifie le statut toutes les 2 secondes
   → API: POST /api/payment/status
   
3. Backend appelle MTN API pour vérifier
   → MTN Sandbox répond: "status": "SUCCESSFUL" (automatique!)
   
4. Backend confirme le paiement
   ✅ Status → "confirmed"
   ✅ Transaction créée
   ✅ Vote(s) créé(s)
   
❌ PROBLÈME: Pas de paiement réel!
```

## ✅ Comportement Attendu (Production)

```
1. Utilisateur clique "Payer"
   → API: POST /api/payment/initiate
   → MTN répond: 202 Accepted
   → Status: "pending"

2. MTN envoie popup USSD sur le téléphone de l'utilisateur
   "Confirmez paiement de 100 FCFA pour Vote Miss ESGIS"
   "Entrez votre code PIN: ****"

3. Si utilisateur confirme avec son PIN:
   → MTN débite le compte
   → MTN marque le paiement comme "SUCCESSFUL"
   
4. Frontend vérifie le statut
   → Backend appelle MTN API
   → MTN répond: "status": "SUCCESSFUL"
   
5. Backend confirme le paiement
   ✅ Status → "confirmed"
   ✅ Transaction créée
   ✅ Vote(s) créé(s)
   ✅ Argent réellement débité!

Si utilisateur annule ou timeout:
   → MTN marque comme "FAILED"
   → Status reste "pending" ou passe à "failed"
   ❌ Pas de vote créé
```

## 🎯 Solutions

### Option 1: Passer en Production (RECOMMANDÉ)

Pour avoir de vrais paiements:

1. **Obtenir des clés MTN Production**
   - https://momodeveloper.mtn.com
   - Créer une subscription "Production"
   - Obtenir nouvelles Primary/Secondary keys

2. **Créer API User Production**
   ```bash
   curl -X POST https://proxy.momoapi.mtn.com/v1_0/apiuser \
     -H "X-Reference-Id: <uuid>" \
     -H "Ocp-Apim-Subscription-Key: <production-key>"
   ```

3. **Mettre à jour .env**
   ```env
   MTN_MOMO_ENVIRONMENT=production
   MTN_MOMO_API_USER=<nouveau-uuid-prod>
   MTN_MOMO_API_KEY=<nouvelle-key-prod>
   MTN_MOMO_SUBSCRIPTION_KEY=<production-primary-key>
   ```

4. **Tester avec de vrais numéros MTN**
   - Currency: XOF (pas EUR)
   - Numéros: Vrais numéros MTN Bénin (229...)
   - Popup USSD apparaît sur le téléphone
   - Débit réel du compte

### Option 2: Désactiver Auto-Confirmation en Sandbox (TEMPORAIRE)

Si vous voulez continuer les tests sans créer de votes:

**Modifier**: `app/Http/Controllers/SandboxPaymentController.php`

```php
// AVANT (ligne 109)
if (isset($apiStatus['status']) && $apiStatus['status'] === 'successful') {
    $this->confirmPayment($payment);
    $payment->refresh();
}

// APRÈS - Seulement en production
if (env('MTN_MOMO_ENVIRONMENT') === 'production' && 
    isset($apiStatus['status']) && $apiStatus['status'] === 'successful') {
    $this->confirmPayment($payment);
    $payment->refresh();
}
```

**⚠️ Limitation**: En sandbox, les paiements resteront "pending" indéfiniment.

### Option 3: Mode Sandbox avec Confirmation Manuelle

Ajouter un endpoint admin pour confirmer manuellement les paiements sandbox:

```php
// routes/api.php (admin only)
Route::post('/admin/confirm-payment/{reference}', function($reference) {
    $payment = PaymentSandbox::where('reference', $reference)->first();
    if ($payment && $payment->status === 'pending') {
        // Confirmer manuellement
        app(SandboxPaymentController::class)->confirmPayment($payment);
        return response()->json(['success' => true]);
    }
    return response()->json(['error' => 'Not found'], 404);
});
```

## 📊 Comparaison

| Aspect | Sandbox (Actuel) | Production (Souhaité) |
|--------|------------------|----------------------|
| **Paiements** | Simulés | Réels |
| **Confirmation** | Automatique (3-5s) | Manuelle (USSD) |
| **Argent** | Pas débité | Débité du compte MTN |
| **Currency** | EUR | XOF |
| **Popup USSD** | Non | Oui |
| **Numéros** | Test (46733123453) | Vrais MTN Bénin |
| **Validation MTN** | Auto | Utilisateur doit entrer PIN |
| **Votes créés** | ✅ Même sans payer | ✅ Seulement si payé |

## 🎯 Recommandation

**Pour le lancement en production:**

1. ✅ Obtenir clés MTN Production
2. ✅ Changer `MTN_MOMO_ENVIRONMENT=production`
3. ✅ Tester avec un vrai numéro MTN (le vôtre)
4. ✅ Vérifier que le popup USSD apparaît
5. ✅ Confirmer que l'argent est débité
6. ✅ Vérifier que le vote est créé

**En attendant (sandbox):**
- Les paiements s'auto-confirment
- Utilisez pour tester l'interface uniquement
- Ne comptez PAS sur les votes créés en sandbox

---

**État actuel**: Sandbox avec auto-confirmation  
**État souhaité**: Production avec vrais paiements USSD
