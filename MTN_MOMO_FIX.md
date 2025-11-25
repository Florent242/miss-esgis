# Fix MTN MoMo API - 25 Nov 2025

## Problème Initial
Erreur **400 Bad Request** sur `/api/sandbox/initiate` lors des paiements MTN MoMo.

## Causes Identifiées

### 1. Header `Content-Length` manquant pour `/collection/token/`
L'API MTN exige un header `Content-Length: 0` pour la requête POST du token.

### 2. UUID en majuscules
Le `X-Reference-Id` doit être un UUID en **lowercase**, pas uppercase.
- ❌ `strtoupper(Str::uuid())` → Génère `F10CD6AA-8B55-...`
- ✅ `strtolower(Str::uuid())` → Génère `f10cd6aa-8b55-...`

### 3. Currency incorrecte
- **Sandbox MTN**: utilise `EUR`
- **Production MTN**: utilise `XOF`

## Modifications Apportées

### 1. `app/Services/MoMoPaymentService.php`
```php
// Ajout header Content-Length pour token
$tokenResponse = Http::withHeaders([
    'Ocp-Apim-Subscription-Key' => $subscriptionKey,
    'Content-Length' => '0',  // ← AJOUTÉ
])->withBasicAuth($apiUser, $apiKey)
  ->post($baseUrl . '/collection/token/');

// Currency dynamique selon environnement
$currency = $environment === 'sandbox' ? 'EUR' : 'XOF';  // ← AJOUTÉ
```

### 2. `app/Http/Controllers/SandboxPaymentController.php`
```php
// UUID en lowercase
$reference = strtolower(Str::uuid()->toString());  // ← MODIFIÉ
```

### 3. `.env`
```env
MTN_MOMO_SUBSCRIPTION_KEY=aa3d492186e2441fbfaeb684b09e02e8
MTN_MOMO_SECONDARY_KEY=039f3ed61e734aedae7ebbe5cc157fab  # ← AJOUTÉ
```

## Résultat

✅ **L'API fonctionne correctement**

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

# Réponse:
{
  "success": true,
  "reference": "f10cd6aa-8b55-442b-b468-0cfc015c8cb0",
  "operator": "mtn",
  "amount": 100,
  "message": "Vérifiez votre téléphone pour confirmer le paiement"
}
```

## Numéro de Test Sandbox
- **Numéro**: `46733123453`
- **Currency**: `EUR`
- **Environment**: `sandbox`

## Pour Passer en Production

1. Changer dans `.env`:
   ```env
   MTN_MOMO_ENVIRONMENT=production
   ```

2. Utiliser vos vraies credentials de production MTN

3. La currency passera automatiquement à `XOF`

4. Utiliser de vrais numéros MTN Mobile Money Bénin (229...)
