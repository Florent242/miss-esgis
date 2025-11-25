# Configuration MTN MoMo pour Production

## ⚠️ IMPORTANT: Clés Actuelles = SANDBOX SEULEMENT

Vos clés `reine-esgis` sont configurées pour le **sandbox** uniquement:
- Primary key: `aa3d492186e2441fbfaeb684b09e02e8`
- Elles fonctionnent UNIQUEMENT avec `MTN_MOMO_ENVIRONMENT=sandbox`

## 📋 Pour Passer en Production

### Option 1: Utiliser le Sandbox (RECOMMANDÉ pour les tests)

Dans `.env`:
```env
MTN_MOMO_API_USER=9ac129dd-f753-4eac-b515-13da14e32534
MTN_MOMO_API_KEY=e44e106fcd4c43b09c7049c587f325a2
MTN_MOMO_SUBSCRIPTION_KEY=aa3d492186e2441fbfaeb684b09e02e8
MTN_MOMO_ENVIRONMENT=sandbox
```

**Caractéristiques**:
- ✅ Fonctionne avec vos clés actuelles
- ✅ Numéro de test: `46733123453`
- ⚠️ Currency: **EUR** (pas XOF)
- ⚠️ Paiements simulés (pas de vrais débits)

### Option 2: Activer la Production

Vous devez aller sur https://momodeveloper.mtn.com et:

1. **Créer une nouvelle Product Subscription** pour "Production"
2. Activer "Collections" en mode Production
3. Obtenir de nouvelles clés (Primary/Secondary)
4. Créer un nouvel API User pour la production
5. Faire une demande de "Go Live" auprès de MTN

Puis dans `.env`:
```env
MTN_MOMO_API_USER=<nouveau-uuid-production>
MTN_MOMO_API_KEY=<nouvelle-api-key-production>
MTN_MOMO_SUBSCRIPTION_KEY=<nouvelle-primary-key-production>
MTN_MOMO_ENVIRONMENT=production
```

**Caractéristiques**:
- ✅ Vrais paiements avec vrais numéros MTN Bénin
- ✅ Currency: **XOF**
- ⚠️ Nécessite validation MTN
- ⚠️ Frais de transaction applicables

## 🧪 Tests Disponibles

### Test Sandbox (EUR)
```bash
/var/www/miss-esgis/tests/mtn_sandbox_test.sh
```

### Test Production (XOF) - Quand vous aurez les clés
```bash
/var/www/miss-esgis/tests/mtn_production_test.sh
```

## 📞 Contact MTN Support

Pour obtenir des clés production:
- Email: momo@mtn.com
- Portal: https://momodeveloper.mtn.com
- Documentation: https://momodeveloper.mtn.com/api-documentation

## 🔧 Configuration Actuelle

Actuellement configuré en: **SANDBOX**
- URL: `https://sandbox.momodeveloper.mtn.com`
- Currency: `EUR`
- Numéro test: `46733123453`
