# Guide Complet: Obtenir les Clés MTN MoMo Production

**Date**: 25 Novembre 2025  
**Objectif**: Passer du mode SANDBOX au mode PRODUCTION avec de vrais paiements

---

## 🎯 Prérequis

Avant de commencer, vous devez avoir:
- ✅ Un compte sur https://momodeveloper.mtn.com (celui que vous avez déjà)
- ✅ Les informations de votre entreprise (nom, adresse, contact)
- ✅ Un numéro MTN Mobile Money actif pour les tests
- ✅ Éventuellement: Registre de commerce (selon le pays)

---

## 📋 Étapes Détaillées

### Étape 1: Se Connecter au Portail MTN

1. Allez sur **https://momodeveloper.mtn.com**
2. Cliquez sur **"Login"** en haut à droite
3. Connectez-vous avec vos identifiants (ceux que vous avez déjà)

### Étape 2: Accéder aux Products

1. Une fois connecté, allez dans le menu **"Products"**
2. Vous verrez plusieurs produits:
   - **Collections** (pour recevoir des paiements) ← C'est celui-ci!
   - Disbursements (pour envoyer de l'argent)
   - Remittances (transferts internationaux)

### Étape 3: Subscribe to Collections - Production

#### Option A: Depuis le Dashboard

1. Dans **"Products" → "Collections"**
2. Cherchez l'option **"Subscribe"** ou **"Production"**
3. Vous devriez voir:
   - **Sandbox Subscription** (que vous avez déjà) ✅
   - **Production Subscription** (à créer) ⬅️

#### Option B: Créer une Nouvelle Subscription

1. Cliquez sur **"Create Subscription"** ou **"New Subscription"**
2. Remplissez le formulaire:
   ```
   Product: Collections
   Environment: Production (pas Sandbox!)
   Subscription Name: reine-esgis-production
   ```

### Étape 4: Remplir le Formulaire de Production

MTN va vous demander des informations additionnelles:

#### Informations Entreprise
```
Company Name: [Votre nom d'entreprise]
Business Type: E-commerce / Voting Platform
Country: Bénin
Address: [Votre adresse]
Contact Person: [Votre nom]
Email: [Votre email]
Phone: [Votre numéro]
```

#### Détails Techniques
```
Use Case: Vote en ligne pour concours Miss ESGIS
Expected Monthly Volume: [Ex: 1000-5000 transactions]
Average Transaction Amount: 100-500 FCFA
Website URL: https://reine-esgis.com
Callback URL: https://reine-esgis.com/api/webhook/momo
```

#### Documents Requis (possibles)
- Copie du registre de commerce
- Pièce d'identité du propriétaire
- Preuve d'adresse
- Business plan (optionnel)

### Étape 5: Attendre la Validation MTN

⏰ **Délai**: 2-10 jours ouvrables

MTN va:
1. Vérifier vos informations
2. Valider votre use case
3. Effectuer des vérifications de sécurité
4. Vous contacter si besoin de documents additionnels

**Email de confirmation**: Vous recevrez un email quand c'est approuvé

### Étape 6: Obtenir les Clés de Production

Une fois approuvé:

1. Retournez sur **https://momodeveloper.mtn.com**
2. Allez dans **"Subscriptions"**
3. Cliquez sur votre subscription **"Production - Collections"**
4. Vous verrez vos clés:

```
Primary Key:   xxxxxxxxxxxxxxxxxxxxxxxxxxxx
Secondary Key: xxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

⚠️ **IMPORTANT**: Ces clés sont DIFFÉRENTES de vos clés sandbox!

### Étape 7: Créer un API User pour la Production

Contrairement au sandbox, vous devez faire une demande officielle:

#### Méthode 1: Via le Portail (Recommandé)

1. Dans votre subscription Production
2. Cherchez **"API Users"** ou **"Create API User"**
3. Cliquez sur **"Create"**
4. MTN génère automatiquement:
   - API User ID (UUID)
   - API Key

#### Méthode 2: Via API (Si disponible)

```bash
# Générer un UUID
UUID=$(uuidgen | tr '[:upper:]' '[:lower:]')

# Créer l'API User
curl -X POST https://proxy.momoapi.mtn.com/v1_0/apiuser \
  -H "X-Reference-Id: $UUID" \
  -H "Ocp-Apim-Subscription-Key: VOTRE_PRODUCTION_PRIMARY_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "providerCallbackHost": "reine-esgis.com"
  }'

# Créer l'API Key
curl -X POST https://proxy.momoapi.mtn.com/v1_0/apiuser/$UUID/apikey \
  -H "Ocp-Apim-Subscription-Key: VOTRE_PRODUCTION_PRIMARY_KEY"
```

### Étape 8: Tester les Clés de Production

Avant de mettre en production, testez avec votre propre numéro:

```bash
# 1. Obtenir un token
curl -X POST https://proxy.momoapi.mtn.com/collection/token/ \
  -u "API_USER_ID:API_KEY" \
  -H "Ocp-Apim-Subscription-Key: PRODUCTION_PRIMARY_KEY" \
  -H "Content-Length: 0"

# 2. Faire un test de paiement (avec VOTRE numéro!)
REF=$(uuidgen | tr '[:upper:]' '[:lower:]')

curl -X POST https://proxy.momoapi.mtn.com/collection/v1_0/requesttopay \
  -H "Authorization: Bearer TOKEN_OBTENU" \
  -H "X-Reference-Id: $REF" \
  -H "X-Target-Environment: mtncameroon" \
  -H "Ocp-Apim-Subscription-Key: PRODUCTION_PRIMARY_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "amount": "100",
    "currency": "XOF",
    "externalId": "'$REF'",
    "payer": {
      "partyIdType": "MSISDN",
      "partyId": "22961234567"
    },
    "payerMessage": "Test production",
    "payeeNote": "Test"
  }'
```

**Vous devriez recevoir un popup USSD sur votre téléphone!**

---

## 🔧 Configuration dans Votre Application

Une fois les clés obtenues, mettez à jour votre `.env`:

```env
# MTN MoMo Production
MTN_MOMO_ENVIRONMENT=production
MTN_MOMO_API_USER=xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
MTN_MOMO_API_KEY=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
MTN_MOMO_SUBSCRIPTION_KEY=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
MTN_MOMO_SECONDARY_KEY=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

Puis:
```bash
cd /var/www/miss-esgis
php artisan config:clear
php artisan cache:clear
```

---

## 📞 Contacts MTN Support

Si vous rencontrez des problèmes:

### Support Général
- **Email**: momo@mtn.com
- **Portal**: https://momodeveloper.mtn.com/support

### Support par Pays

**Bénin**:
- Email: momo.benin@mtn.com
- Téléphone: [Vérifier sur le site MTN Bénin]

**Côte d'Ivoire**:
- Email: momo.ci@mtn.com

**Cameroun**:
- Email: momo.cm@mtn.com

### Questions Fréquentes à Poser

1. "J'ai besoin d'accès à l'API Collections en Production"
2. "Combien de temps prend l'approbation?"
3. "Quels documents sont requis pour le Bénin?"
4. "Comment tester l'API Production avant le go-live?"

---

## ⚠️ Points Importants

### Différences Sandbox vs Production

| Aspect | Sandbox | Production |
|--------|---------|------------|
| **Clés** | Gratuites, instantanées | Nécessitent validation |
| **Approbation** | Immédiate | 2-10 jours |
| **Documents** | Aucun | Registre commerce, ID |
| **URL API** | sandbox.momodeveloper.mtn.com | proxy.momoapi.mtn.com |
| **Currency** | EUR | XOF (Bénin) |
| **Paiements** | Simulés | Réels |
| **Frais** | Aucun | Oui (3-5% selon accord) |

### Frais de Transaction

MTN prélève des frais sur chaque transaction:
- **Standard**: ~3.5% par transaction
- **Négociable**: Si volume élevé, contactez MTN pour un accord commercial

Exemple:
- Client paie: 100 FCFA
- MTN prélève: ~3.5 FCFA
- Vous recevez: ~96.5 FCFA

### Limites de Transaction

- **Minimum**: Généralement 50-100 FCFA
- **Maximum**: Dépend du compte MTN de l'utilisateur
- **Quotidien**: Variable selon le niveau de vérification KYC

---

## 🎯 Checklist Complète

### Préparation
- [ ] Compte MTN Developer créé
- [ ] Documents entreprise prêts
- [ ] Use case clairement défini
- [ ] Numéro MTN pour tests prêt

### Obtention des Clés
- [ ] Subscription Production créée
- [ ] Formulaire rempli et soumis
- [ ] Documents uploadés
- [ ] Approbation MTN reçue
- [ ] Primary/Secondary Keys copiées

### Configuration API
- [ ] API User créé
- [ ] API Key générée
- [ ] Credentials testés avec curl
- [ ] Popup USSD reçu sur téléphone test

### Mise en Production
- [ ] .env mis à jour avec clés production
- [ ] MTN_MOMO_ENVIRONMENT=production
- [ ] Cache Laravel vidé
- [ ] Test avec vraie transaction (petite somme)
- [ ] Vérification que l'argent est débité
- [ ] Vérification que le vote est créé

### Go Live
- [ ] Tests complets effectués
- [ ] Monitoring des logs activé
- [ ] Support MTN contacté (si besoin)
- [ ] Lancement public ✅

---

## 🚀 Timeline Estimée

```
Jour 1:     Soumission de la demande
            └─ Formulaire + Documents

Jour 1-3:   Vérification initiale par MTN
            └─ Validation des informations

Jour 3-7:   Review approfondie
            └─ Vérification sécurité/compliance

Jour 7-10:  Approbation et activation
            └─ Réception des clés

Jour 10:    Configuration et tests
            └─ Intégration dans l'application

Jour 11:    GO LIVE! 🎉
```

**Durée totale**: 7-14 jours en moyenne

---

## 💡 Conseils Pro

1. **Préparez tout avant**: Documents, informations, use case clair
2. **Soyez patient**: La validation prend du temps
3. **Testez d'abord**: Toujours tester en sandbox avant production
4. **Contactez le support**: N'hésitez pas à appeler MTN si ça bloque
5. **Documentez tout**: Gardez une trace de toutes vos communications
6. **Commencez petit**: Faites un soft launch avant le grand public

---

**Prochaine étape**: Allez sur https://momodeveloper.mtn.com et créez votre subscription Production! 🚀
