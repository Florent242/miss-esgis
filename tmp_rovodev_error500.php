<?php
/**
 * FICHIER TEMPORAIRE POUR GÉNÉRER UNE ERREUR 500
 * 
 * Pour activer l'erreur 500 :
 * 1. Renommer ce fichier en "enable_error500.php" 
 * 2. Le placer dans le dossier public/
 * 
 * Pour désactiver l'erreur 500 :
 * 1. Supprimer le fichier "enable_error500.php" du dossier public/
 * 
 * L'erreur ne s'activera que si le fichier est présent et accessible.
 */

// Vérifier si le fichier d'activation existe dans public/
if (file_exists(__DIR__ . '/../public/enable_error500.php')) {
    // Forcer une erreur 500
    http_response_code(500);
    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>Erreur 500 - Service Temporairement Indisponible</title>
        <style>
            body { font-family: Arial, sans-serif; text-align: center; margin-top: 100px; }
            h1 { color: #e74c3c; }
            p { color: #666; }
        </style>
    </head>
    <body>
        <h1>Service Temporairement Indisponible</h1>
        <p>Le site est actuellement en maintenance. Veuillez réessayer plus tard.</p>
        <p><small>Erreur 500 - Internal Server Error</small></p>
    </body>
    </html>";
    exit;
}
?>