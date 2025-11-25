# Fix Status Endpoint 500 Error

**Date**: 25 Novembre 2025  
**Problème**: POST `/api/sandbox/status` retournait 500 Internal Server Error

## ❌ Erreur

```
POST https://reine-esgis.com/api/sandbox/status 500 (Internal Server Error)
Uncaught (in promise) SyntaxError: Unexpected token '<', "<!DOCTYPE "... is not valid JSON
```

## 🔍 Diagnostic

L'erreur venait de la ligne 137 dans `SandboxPaymentController.php`:

```php
$payment->sms_received_at = now();  // ❌ Colonne inexistante
$payment->save();
```

### Stack trace
```
PDOException: SQLSTATE[42S22]: Column not found: 1054 Unknown column 'sms_received_at'
```

## ✅ Solution

Suppression de la ligne inutile dans `app/Http/Controllers/SandboxPaymentController.php`:

```php
// AVANT
$payment->status = 'confirmed';
$payment->sms_received_at = now();  // ❌ ERREUR
$payment->save();

// APRÈS
$payment->status = 'confirmed';
$payment->save();  // ✅ OK
```

## 🧪 Test de Vérification

```bash
# Créer un paiement
curl -X POST https://reine-esgis.com/api/sandbox/initiate \
  -H "Content-Type: application/json" \
  -d '{
    "miss_id": 1,
    "operator": "mtn",
    "phone_number": "46733123453",
    "amount": 100,
    "vote_count": 1
  }'

# Réponse
{
  "success": true,
  "reference": "4b148635-f333-4947-8fdb-a989bcfdae4d",
  ...
}

# Vérifier le statut
curl -X POST https://reine-esgis.com/api/sandbox/status \
  -H "Content-Type: application/json" \
  -d '{"reference":"4b148635-f333-4947-8fdb-a989bcfdae4d"}'

# Réponse ✅
{
  "status": "pending",
  "reference": "4b148635-f333-4947-8fdb-a989bcfdae4d"
}
```

## 📋 Structure Table Correcte

La table `payment_sandbox` contient les colonnes suivantes:

```
- id
- reference
- external_reference
- miss_id
- operator
- phone_number
- amount
- vote_count
- status (enum: pending, confirmed, failed, expired)
- provider_response
- ip_address
- user_agent
- expires_at
- created_at
- updated_at
```

**Note**: Pas de colonne `sms_received_at` (elle était peut-être prévue mais jamais créée dans la migration).

## ✅ Résultat

L'endpoint `/api/sandbox/status` fonctionne maintenant correctement et retourne du JSON valide.
