#!/bin/bash
# Vérification automatique des paiements FedaPay toutes les 5 minutes

cd /var/www/miss-esgis
php check_and_update_fedapay.php >> storage/logs/fedapay_cron.log 2>&1
