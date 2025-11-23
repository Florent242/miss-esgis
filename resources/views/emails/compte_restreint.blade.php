<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accès restreint - Reine ESGIS</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #ffeef8 0%, #fff5f8 100%);
            padding: 15px;
            line-height: 1.5;
        }
        .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(236, 72, 153, 0.12);
        }
        .header {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            padding: 30px 20px;
            text-align: center;
        }
        .logo-icon {
            font-size: 45px;
            margin-bottom: 12px;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.08); }
        }
        .header h1 {
            color: white;
            font-size: 18px;
            font-weight: 700;
            margin: 8px 0;
        }
        .header p {
            color: rgba(255,255,255,0.95);
            font-size: 13px;
            font-weight: 500;
        }
        .content {
            padding: 25px 20px;
        }
        .greeting {
            font-size: 15px;
            color: #111827;
            margin-bottom: 15px;
            font-weight: 600;
        }
        .intro-text {
            font-size: 13px;
            color: #374151;
            margin-bottom: 18px;
            line-height: 1.6;
        }
        .warning-box {
            background: #fef3c7;
            border: 2px solid #f59e0b;
            border-radius: 10px;
            padding: 15px;
            margin: 18px 0;
        }
        .warning-badge {
            display: inline-block;
            background: #f59e0b;
            color: white;
            padding: 6px 14px;
            border-radius: 15px;
            font-weight: 700;
            font-size: 11px;
            margin-bottom: 10px;
        }
        .warning-box p {
            color: #78350f;
            font-size: 13px;
            line-height: 1.6;
        }
        .reason-box {
            background: #fee2e2;
            border-left: 4px solid #ef4444;
            padding: 15px;
            border-radius: 8px;
            margin: 18px 0;
        }
        .reason-box h3 {
            color: #991b1b;
            font-size: 14px;
            margin-bottom: 8px;
            font-weight: 700;
        }
        .reason-box p {
            color: #7f1d1d;
            font-size: 12px;
            line-height: 1.5;
        }
        .info-box {
            background: #dbeafe;
            border-left: 4px solid #3b82f6;
            padding: 15px;
            border-radius: 8px;
            margin: 18px 0;
        }
        .info-box-title {
            color: #1e40af;
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .info-box p {
            color: #1e3a8a;
            font-size: 12px;
            line-height: 1.5;
        }
        .contact-box {
            background: #f0fdf4;
            border-left: 4px solid #10b981;
            padding: 15px;
            border-radius: 8px;
            margin: 18px 0;
        }
        .contact-box h3 {
            color: #065f46;
            font-size: 14px;
            margin-bottom: 8px;
            font-weight: 700;
        }
        .contact-box p {
            color: #064e3b;
            font-size: 12px;
            line-height: 1.5;
        }
        .footer {
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
            padding: 20px 15px;
            text-align: center;
            color: #9ca3af;
        }
        .footer p {
            margin: 5px 0;
            font-size: 10px;
            line-height: 1.4;
        }
        .footer-logo {
            font-size: 28px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="header">
            <div class="logo-icon">⚠️</div>
            <h1>Accès temporairement restreint</h1>
            <p>Reine ESGIS {{ date('Y') }}</p>
        </div>

        <div class="content">
            <p class="greeting">
                Bonjour {{ $prenom }} {{ $nom }},
            </p>

            <div class="intro-text">
                Nous vous informons que votre accès à votre espace candidat <strong>Reine ESGIS {{ date('Y') }}</strong> a été temporairement restreint.
            </div>

            <div class="warning-box">
                <div class="warning-badge">⛔ COMPTE RESTREINT</div>
                <p>
                    Vous ne pouvez plus vous connecter à votre espace personnel pour le moment.
                </p>
            </div>

            <div class="reason-box">
                <h3>📌 Raison</h3>
                <p>{{ $raison ?? 'Non-respect du règlement du concours ou comportement inapproprié.' }}</p>
            </div>

            <div class="info-box">
                <div class="info-box-title">ℹ️ Ce que cela signifie</div>
                <p>
                    • Votre profil n'est plus visible publiquement<br>
                    • Vous ne pouvez plus collecter de votes<br>
                    • Accès à votre espace bloqué temporairement<br>
                    • Situation réévaluée selon le règlement
                </p>
            </div>

            <div class="contact-box">
                <h3>💬 Besoin d'informations ?</h3>
                <p>
                    Si vous pensez qu'il s'agit d'une erreur ou souhaitez obtenir des clarifications, 
                    n'hésitez pas à nous contacter à {{ env('MAIL_FROM_ADDRESS', 'contact@reine-esgis.com') }}
                </p>
            </div>

            <div class="intro-text" style="margin-top: 20px;">
                Nous vous rappelons l'importance de respecter le règlement du concours pour garantir une compétition équitable.
            </div>

            <div style="margin-top: 18px; color: #6b7280; font-size: 12px;">
                Cordialement,<br>
                <strong style="color: #111827;">L'équipe Reine ESGIS-Bénin</strong>
            </div>
        </div>

        <div class="footer">
            <div class="footer-logo">👑</div>
            <p>Message automatique - Ne pas répondre</p>
            <p>Pour toute question : {{ env('MAIL_FROM_ADDRESS', 'contact@reine-esgis.com') }}</p>
            <p>© {{ date('Y') }} Reine ESGIS-Bénin</p>
        </div>
    </div>
</body>
</html>
