<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Candidature reçue - Miss ESGIS</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
            color: #111827;
            background-color: #f9fafb;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #ec4899 0%, #f97316 100%);
            padding: 30px;
            text-align: center;
            color: white;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }
        .content {
            padding: 30px;
        }
        .card {
            background: #f9fafb;
            border-left: 4px solid #ec4899;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .status-badge {
            display: inline-block;
            background: #fef3c7;
            color: #92400e;
            padding: 6px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
        }
        .info-box {
            background: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .footer {
            background: #f9fafb;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✨ Candidature Reçue ✨</h1>
            <p style="margin: 10px 0 0 0; opacity: 0.9;">Miss ESGIS {{ date('Y') }}</p>
        </div>

        <div class="content">
            <h2 style="color: #111827; margin-bottom: 10px;">
                Bonjour {{ $miss->prenom }} {{ $miss->nom }},
            </h2>
            
            <p style="font-size: 16px; color: #374151;">
                Nous avons bien reçu votre candidature à l'élection <strong>Miss ESGIS {{ date('Y') }}</strong> ! 🎉
            </p>

            <div class="card">
                <p style="margin: 0 0 10px 0;">
                    <strong>Statut actuel :</strong> 
                    <span class="status-badge">⏳ En cours de validation</span>
                </p>
                <p style="margin: 10px 0;">
                    Votre dossier est actuellement en cours d'examen par notre équipe. Cette étape peut prendre quelques jours.
                </p>
            </div>

            <div class="info-box">
                <p style="margin: 0; font-size: 14px;">
                    <strong>📧 Que faire maintenant ?</strong><br>
                    Vous n'avez rien à faire pour le moment. Nous vous enverrons un email dès que votre candidature sera examinée.
                </p>
            </div>

            <p style="margin-top: 20px; color: #374151;">
                <strong>Prochaines étapes :</strong>
            </p>
            <ul style="color: #6b7280; line-height: 1.8;">
                <li>✅ Candidature reçue (actuel)</li>
                <li>🔍 Examen par l'équipe d'organisation</li>
                <li>📨 Notification par email de la décision</li>
                <li>🎯 Accès à votre espace personnel (si approuvée)</li>
            </ul>

            <p style="margin-top: 25px; color: #374151;">
                Merci pour votre participation et <strong>bonne chance</strong> ! 🍀
            </p>

            <p style="margin-top: 20px; color: #6b7280; font-size: 14px;">
                Cordialement,<br>
                <strong>L'équipe Miss ESGIS-Bénin</strong>
            </p>
        </div>

        <div class="footer">
            <p style="margin: 5px 0;">Ceci est un message automatique, merci de ne pas y répondre directement.</p>
            <p style="margin: 5px 0;">© {{ date('Y') }} Miss ESGIS-Bénin. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>
