<?php
/**
 * SCRIPT POUR FORCER UNE ERREUR 500 SUR LE SITE
 * 
 * UTILISATION :
 * 1. Pour activer l'erreur 500 : php tmp_rovodev_trigger_error500.php enable
 * 2. Pour désactiver l'erreur 500 : php tmp_rovodev_trigger_error500.php disable
 */

if ($argc < 2) {
    echo "Usage: php tmp_rovodev_trigger_error500.php [enable|disable]\n";
    exit(1);
}

$action = $argv[1];
$indexPath = __DIR__ . '/public/index.php';
$backupPath = __DIR__ . '/public/index.php.backup';

if ($action === 'enable') {
    // Sauvegarder le fichier original
    if (!file_exists($backupPath)) {
        copy($indexPath, $backupPath);
        echo "✅ Sauvegarde créée : index.php.backup\n";
    }
    
    // Créer un nouveau index.php qui cause une erreur 500
    $errorCode = '<?php
http_response_code(500);
exit;
?>';

    file_put_contents($indexPath, $errorCode);
    echo "🚨 ERREUR 500 ACTIVÉE ! Le site affiche maintenant une erreur 500.\n";
    echo "💡 Pour désactiver : php tmp_rovodev_trigger_error500.php disable\n";

} elseif ($action === 'disable') {
    // Restaurer le fichier original
    if (file_exists($backupPath)) {
        copy($backupPath, $indexPath);
        unlink($backupPath);
        echo "✅ ERREUR 500 DÉSACTIVÉE ! Le site fonctionne normalement.\n";
        echo "🗑️ Sauvegarde supprimée.\n";
    } else {
        echo "❌ Erreur : Aucune sauvegarde trouvée (index.php.backup)\n";
    }
} else {
    echo "❌ Action invalide. Utilisez 'enable' ou 'disable'\n";
}
?>