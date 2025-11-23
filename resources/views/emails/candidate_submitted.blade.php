<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Candidature reçue - Reine ESGIS</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #fce7f3 0%, #fff7ed 50%, #fef3c7 100%);
            padding: 20px;
            line-height: 1.7;
        }
        .email-wrapper {
            max-width: 650px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(236, 72, 153, 0.15);
        }
        .header {
            background: linear-gradient(135deg, #ec4899 0%, #f97316 100%);
            padding: 30px 25px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .logo-sparkle {
            font-size: 50px;
            margin-bottom: 15px;
            animation: sparkle 2s ease-in-out infinite;
        }
        @keyframes sparkle {
            0%, 100% { transform: scale(1) rotate(0deg); }
            50% { transform: scale(1.1) rotate(5deg); }
        }
        .header h1 {
            color: white;
            font-size: 22px;
            font-weight: 700;
            margin: 10px 0;
            text-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .header p {
            color: rgba(255,255,255,0.95);
            font-size: 14px;
            font-weight: 500;
        }
        .content {
            padding: 30px 20px;
            background: white;
        }
        .greeting {
            font-size: 16px;
            color: #111827;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .intro-text {
            font-size: 14px;
            color: #374151;
            margin-bottom: 25px;
            line-height: 1.6;
        }
        .status-card {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 2px solid #f59e0b;
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        .status-badge {
            display: inline-block;
            background: white;
            color: #92400e;
            padding: 8px 18px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 13px;
            margin-bottom: 12px;
            box-shadow: 0 4px 10px rgba(245, 158, 11, 0.2);
        }
        .status-card p {
            color: #78350f;
            font-size: 13px;
            line-height: 1.6;
        }
        .info-box {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            border-left: 4px solid #3b82f6;
            padding: 18px;
            border-radius: 10px;
            margin: 20px 0;
        }
        .info-box-title {
            color: #1e40af;
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .info-box p {
            color: #1e3a8a;
            font-size: 13px;
            line-height: 1.6;
        }
        .steps-title {
            color: #111827;
            font-size: 15px;
            font-weight: 700;
            margin: 25px 0 15px 0;
        }
        .steps-list {
            background: #f9fafb;
            border-radius: 10px;
            padding: 18px 18px 18px 35px;
            margin: 15px 0;
        }
        .steps-list li {
            color: #6b7280;
            font-size: 13px;
            padding: 6px 0;
            line-height: 1.6;
        }
        .closing {
            margin-top: 25px;
            color: #374151;
            font-size: 14px;
            line-height: 1.6;
        }
        .signature {
            margin-top: 20px;
            color: #6b7280;
            font-size: 13px;
        }
        .signature strong {
            color: #111827;
            display: block;
            margin-top: 5px;
        }
        .footer {
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
            padding: 25px 20px;
            text-align: center;
            color: #9ca3af;
        }
        .footer p {
            margin: 6px 0;
            font-size: 11px;
            line-height: 1.5;
        }
        .footer-logo {
            font-size: 30px;
            margin-bottom: 12px;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="header">
            <div class="logo-sparkle">✨</div>
            <h1>Candidature Reçue</h1>
            <p>Reine ESGIS {{ date('Y') }}</p>
        </div>

        <div class="content">
            <div class="greeting">
                Bonjour {{ $prenom }} {{ $nom }},
            </div>

            <div class="intro-text">
                Nous avons bien reçu votre candidature à l'élection <strong>Reine ESGIS {{ date('Y') }}</strong> ! 🎉<br>
                Merci pour votre confiance et votre intérêt.
            </div>

            <div class="status-card">
                <div class="status-badge">⏳ En cours de validation</div>
                <p style="margin-top: 15px;">
                    Votre dossier est actuellement en cours d'examen par notre équipe. 
                    Cette étape peut prendre quelques jours ouvrables.
                </p>
            </div>

            <div class="info-box">
                <div class="info-box-title">📧 Que faire maintenant ?</div>
                <p>
                    Vous n'avez rien à faire pour le moment. Restez attentive à vos emails ! 
                    Nous vous notifierons dès que votre candidature aura été examinée.
                </p>
            </div>

            <div class="steps-title">
                📋 Prochaines étapes :
            </div>
            
            <ul class="steps-list">
                <li>✅ <strong>Candidature reçue</strong> (étape actuelle)</li>
                <li>🔍 Examen par l'équipe d'organisation</li>
                <li>📨 Notification par email de la décision</li>
                <li>🎯 Accès à votre espace personnel (si approuvée)</li>
            </ul>

            <div class="closing">
                <p>
                    Merci pour votre participation et <strong>bonne chance</strong> pour la suite ! 🍀✨
                </p>
                <p style="margin-top: 15px;">
                    Nous sommes impatients de découvrir votre profil !
                </p>
            </div>

            <div class="signature">
                Cordialement,<br>
                <strong>L'équipe Reine ESGIS-Bénin</strong>
            </div>
        </div>

        <div class="footer">
            <div class="footer-logo">👑</div>
            <p>Ceci est un message automatique, merci de ne pas y répondre directement.</p>
            <p>Pour toute question, contactez-nous à {{ env('MAIL_FROM_ADDRESS', 'contact@missesgis.com') }}</p>
            <p>© {{ date('Y') }} Reine ESGIS-Bénin. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>
