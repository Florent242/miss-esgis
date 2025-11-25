# Migration Sandbox → Production - MTN MoMo

**Date**: 25 Novembre 2025  
**Problème**: Le site en production utilisait les endpoints `/api/sandbox/*`

## 🐛 Problème

L'application appelait `/api/sandbox/status` même en production, ce qui causait des erreurs car le code était lié au mode sandbox.

```javascript
// ❌ AVANT - Hardcodé "sandbox"
fetch('/api/sandbox/initiate', {...})
fetch('/api/sandbox/status', {...})
```

## ✅ Solution

### 1. Création des Routes de Production

**Fichier**: `routes/api.php`

```php
// Routes de PRODUCTION
Route::prefix('payment')->group(function () {
    Route::post('/initiate', [SandboxPaymentController::class, 'initiate']);
    Route::post('/status', [SandboxPaymentController::class, 'checkStatus']);
    Route::get('/operators', [SandboxPaymentController::class, 'getOperators']);
});

// Routes SANDBOX (conservées pour compatibilité/tests)
Route::prefix('sandbox')->group(function () {
    Route::post('/initiate', [SandboxPaymentController::class, 'initiate']);
    Route::post('/status', [SandboxPaymentController::class, 'checkStatus']);
    Route::get('/operators', [SandboxPaymentController::class, 'getOperators']);
});
```

### 2. Création du Composant de Production

**Fichier créé**: `resources/views/components/payment-modal.blade.php`

Copié depuis `components/sandbox/payment-modal.blade.php` avec modifications:

```javascript
// ✅ APRÈS - Utilise /api/payment/
fetch('/api/payment/initiate', {...})
fetch('/api/payment/status', {...})
```

### 3. Mise à Jour de la Vue

**Fichier**: `resources/views/vote/show.blade.php`

```php
// ❌ AVANT
@include('components.sandbox.payment-modal')

// ✅ APRÈS  
@include('components.payment-modal')
```

## 🧪 Tests de Validation

### Endpoints Production

```bash
# Initiate
curl -X POST https://reine-esgis.com/api/payment/initiate \
  -H "Content-Type: application/json" \
  -d '{
    "miss_id": 1,
    "operator": "mtn",
    "phone_number": "46733123453",
    "amount": 100,
    "vote_count": 1
  }'

# Status
curl -X POST https://reine-esgis.com/api/payment/status \
  -H "Content-Type: application/json" \
  -d '{"reference": "cbc92552-e3f0-4afa-8260-b54d9a75ca7b"}'

# Operators
curl https://reine-esgis.com/api/payment/operators
```

### Résultats

✅ **Tous les endpoints fonctionnent**:
- `/api/payment/initiate` → 200 OK
- `/api/payment/status` → 200 OK  
- `/api/payment/operators` → 200 OK

## 📊 Architecture Finale

```
┌─────────────────────────────────────────┐
│         Frontend (Blade)                │
│  resources/views/components/            │
│                                         │
│  ┌─────────────────────────────────┐   │
│  │ payment-modal.blade.php         │   │
│  │ (PRODUCTION)                    │   │
│  │  → /api/payment/initiate        │   │
│  │  → /api/payment/status          │   │
│  └─────────────────────────────────┘   │
│                                         │
│  ┌─────────────────────────────────┐   │
│  │ sandbox/payment-modal.blade.php │   │
│  │ (TESTS)                         │   │
│  │  → /api/sandbox/initiate        │   │
│  │  → /api/sandbox/status          │   │
│  └─────────────────────────────────┘   │
└─────────────────────────────────────────┘
                   │
                   ▼
┌─────────────────────────────────────────┐
│         Backend (Laravel)               │
│  routes/api.php                         │
│                                         │
│  /api/payment/*  ──────┐                │
│                        │                │
│  /api/sandbox/*  ──────┤                │
│                        │                │
│                        ▼                │
│         SandboxPaymentController        │
│         (gère sandbox ET production)    │
│                        │                │
│                        ▼                │
│            MoMoPaymentService           │
│         (détecte env automatiquement)   │
└─────────────────────────────────────────┘
```

## 🎯 Avantages

1. **Flexibilité**: Routes séparées pour prod et sandbox
2. **Compatibilité**: Ancien code sandbox continue de fonctionner
3. **Maintenabilité**: Un seul contrôleur gère les deux
4. **Clarté**: Séparation claire prod vs sandbox dans le frontend

## 📝 Notes Importantes

- Le **même contrôleur** `SandboxPaymentController` gère prod ET sandbox
- L'environnement (sandbox/production) est déterminé par `MTN_MOMO_ENVIRONMENT` dans `.env`
- Le contrôleur adapte automatiquement la currency (EUR/XOF) selon l'environnement
- Les routes `/api/sandbox/*` sont conservées pour les tests

## ✅ Checklist Migration

- [x] Créer routes `/api/payment/*`
- [x] Créer composant `payment-modal.blade.php`
- [x] Mettre à jour `vote/show.blade.php`
- [x] Tester les 3 endpoints de production
- [x] Vérifier que le frontend utilise les bonnes routes
- [x] Documenter les changements

---

**Résultat**: L'application fonctionne maintenant correctement en **production** avec les endpoints appropriés `/api/payment/*`
