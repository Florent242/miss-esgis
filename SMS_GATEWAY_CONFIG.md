# 📲 GUIDE DE CONFIGURATION SMS GATEWAY API

## 🎯 SERVICES RECOMMANDÉS

### 1. SMS Gateway API (Android App) - ⭐ RECOMMANDÉ

**Avantages** :
- ✅ Gratuit
- ✅ Utilise votre téléphone Android
- ✅ Pas d'abonnement
- ✅ Webhook instantané

**Installation** :
1. Télécharger "SMS Gateway API" sur Google Play Store
2. Installer l'application sur un téléphone Android
3. Créer un compte et obtenir votre API Key
4. Configurer le webhook

**Configuration** :
```
Webhook URL : https://votre-domaine.com/api/webhook/sms
Method      : POST
Headers     : X-API-Key: votre_cle_api_secrete
Format      : JSON
```

**Format du webhook** :
```json
{
  "from": "22991234567",
  "message": "Vous avez recu 500 FCFA de 91234567...",
  "timestamp": "2025-11-25T10:30:00Z",
  "device": "Device Name"
}
```

---

### 2. Twilio

**Avantages** :
- ✅ Très fiable
- ✅ API robuste
- ❌ Payant ($)

**Configuration** :
```php
// Dans .env
TWILIO_SID=your_sid
TWILIO_AUTH_TOKEN=your_token
TWILIO_PHONE_NUMBER=+1234567890
```

**Webhook** :
```
URL : https://votre-domaine.com/api/webhook/sms
```

---

### 3. Africa's Talking

**Avantages** :
- ✅ Spécialisé Afrique
- ✅ Bons tarifs
- ✅ Support local

**Configuration** :
```php
// Dans .env
AFRICASTALKING_USERNAME=sandbox
AFRICASTALKING_API_KEY=your_key
```

---

## 🔧 CONFIGURATION DANS VOTRE PROJET

### 1. Fichier .env

```env
# SMS Gateway API Configuration
SMS_GATEWAY_API_KEY=votre_cle_secrete_ici_changez_moi
SMS_GATEWAY_WEBHOOK_URL=https://votre-domaine.com/api/webhook/sms

# Numéros MoMo de réception (VOS numéros personnels)
MOMO_MTN_NUMBER=91234567
MOMO_MOOV_NUMBER=97234567
MOMO_CELTIIS_NUMBER=99234567
```

### 2. Dans SMS Gateway API App

**Étapes** :
1. Ouvrir l'app SMS Gateway API
2. Aller dans "Settings"
3. Section "Webhooks"
4. Ajouter un nouveau webhook :
   - Name : Miss ESGIS Payments
   - URL : `https://votre-domaine.com/api/webhook/sms`
   - Method : POST
   - Event : Message Received
   - Add Header : 
     * Key : `X-API-Key`
     * Value : `votre_cle_secrete_ici_changez_moi`

5. Activer le webhook
6. Tester avec "Send Test Webhook"

---

## 🧪 TESTER LE WEBHOOK

### Test manuel avec cURL :

```bash
curl -X POST https://votre-domaine.com/api/webhook/sms \
  -H "Content-Type: application/json" \
  -H "X-API-Key: votre_cle_secrete" \
  -d '{
    "from": "22991234567",
    "message": "Vous avez recu 500 FCFA de 91234567. Ref: TEST123. Solde: 5000 FCFA"
  }'
```

**Réponse attendue** :
```json
{
  "message": "Payment processed successfully"
}
```

### Test avec Postman :

1. Créer une requête POST
2. URL : `https://votre-domaine.com/api/webhook/sms`
3. Headers :
   - `Content-Type: application/json`
   - `X-API-Key: votre_cle_secrete`
4. Body (JSON) :
```json
{
  "from": "22991234567",
  "message": "Vous avez recu 500 FCFA de 91234567. Ref: ABC123"
}
```

---

## 📱 FORMAT DES SMS MOBILE MONEY

### MTN Mobile Money :
```
Vous avez recu 500 FCFA de 91234567. 
Ref: ABC123XYZ. 
Solde: 5000 FCFA. 
Date: 25/11/2025 10:30
```

### Moov Money (Flooz) :
```
Reception de 500F. 
Expediteur: 97234567. 
Ref: XYZ789. 
Nouveau solde: 5000F
```

### Pattern de détection :
Le système cherche :
1. Un montant : `500`, `500F`, `500 FCFA`, `500 CFA`
2. Un numéro : `91234567`, `+22991234567`, `229 91 23 45 67`

---

## 🔍 DEBUGGING

### Vérifier les logs Laravel :
```bash
tail -f storage/logs/laravel.log | grep -i sms
```

### Voir tous les paiements en attente :
```bash
php artisan payments:monitor
```

### Tester le parsing d'un SMS :
```bash
php artisan tinker
>>> $sms = "Vous avez recu 500 FCFA de 91234567. Ref: ABC123";
>>> preg_match('/(\d+)\s*(?:F|FCFA|CFA).*?(\d{8,})/i', $sms, $matches);
>>> print_r($matches);
```

---

## 🚨 PROBLÈMES COURANTS

### 1. Webhook ne fonctionne pas

**Vérifications** :
```bash
# Test direct du webhook
curl -X POST http://127.0.0.1:8000/api/webhook/sms \
  -H "Content-Type: application/json" \
  -H "X-API-Key: test" \
  -d '{"from":"91234567","message":"Test"}'

# Vérifier les routes
php artisan route:list | grep webhook

# Vérifier les logs
tail storage/logs/laravel.log
```

**Solutions** :
- Vérifier que l'API Key est correcte
- Vérifier que le webhook n'est pas bloqué par firewall
- Vérifier que HTTPS est configuré

### 2. SMS non détecté

**Vérifications** :
- Le format du SMS correspond-il au pattern ?
- Le montant est-il correct ?
- Le numéro correspond-il ?

**Solution** :
Ajuster le pattern dans `SmsWebhookController.php`

### 3. Paiement expire avant confirmation

**Solution** :
Augmenter le délai dans `SandboxPaymentController.php` :
```php
'expires_at' => now()->addMinutes(15) // Au lieu de 10
```

---

## 🔐 SÉCURITÉ

### Protection du webhook :

1. **API Key obligatoire**
   ```php
   if ($apiKey !== env('SMS_GATEWAY_API_KEY')) {
       return response()->json(['error' => 'Unauthorized'], 401);
   }
   ```

2. **HTTPS recommandé**
   - Utiliser SSL/TLS pour le webhook
   - Let's Encrypt gratuit

3. **Rate limiting**
   - Limiter les appels au webhook
   - Bloquer les IPs suspectes

### Générer une API Key sécurisée :
```bash
php artisan tinker
>>> Str::random(64)
```

---

## 📊 MONITORING

### Commande de monitoring :
```bash
# Voir les paiements en attente
php artisan payments:monitor

# Expirer les vieux paiements
php artisan payments:expire
```

### Automatisation (Cron) :

Dans `/etc/crontab` ou Laravel Scheduler :
```
*/5 * * * * cd /path/to/project && php artisan payments:expire
```

---

## 🎯 CHECKLIST DE DÉPLOIEMENT

- [ ] SMS Gateway API installée sur téléphone Android
- [ ] Téléphone connecté au réseau
- [ ] Webhook configuré dans l'app
- [ ] API Key générée et sécurisée
- [ ] Numéros MoMo ajoutés au .env
- [ ] Migration exécutée
- [ ] Webhook testé manuellement
- [ ] Test de paiement complet effectué
- [ ] Monitoring activé

---

## 💡 CONSEILS

1. **Téléphone dédié** : Utilisez un téléphone Android dédié pour SMS Gateway API
2. **Batterie** : Gardez le téléphone chargé en permanence
3. **Réseau** : Assurez une connexion internet stable (WiFi + Data)
4. **Backup** : Configurez 2 téléphones pour la redondance
5. **Logs** : Consultez régulièrement les logs

---

## 🔄 RETOUR À KKIAPAY

Si vous voulez revenir à KKiaPay :

```bash
# Restaurer la vue originale
mv resources/views/vote/show.kkiapay.backup resources/views/vote/show.blade.php

# Ou garder les deux et switcher via .env
PAYMENT_METHOD=kkiapay  # ou 'sandbox'
```

---

**Documentation SMS Gateway API** : https://smsgateway.me/  
**Support** : Consultez SANDBOX_MOMO_GUIDE.md
