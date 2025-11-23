<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compte réactivé - Reine ESGIS</title>
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
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            padding: 30px 20px;
            text-align: center;
        }
        .logo-icon {
            font-size: 45px;
            margin-bottom: 12px;
            animation: bounce 1.5s infinite;
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
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
        .success-box {
            background: #d1fae5;
            border: 2px solid #10b981;
            border-radius: 10px;
            padding: 15px;
            margin: 18px 0;
        }
        .success-badge {
            display: inline-block;
            background: #10b981;
            color: white;
            padding: 6px 14px;
            border-radius: 15px;
            font-weight: 700;
            font-size: 11px;
            margin-bottom: 10px;
        }
        .success-box p {
            color: #065f46;
            font-size: 13px;
            line-height: 1.6;
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
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #ec4899 0%, #be185d 100%);
            color: white;
            padding: 14px 28px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 700;
            font-size: 13px;
            margin: 20px 0;
            box-shadow: 0 6px 15px rgba(236, 72, 153, 0.3);
            transition: transform 0.2s;
        }
        .cta-button:hover {
            transform: translateY(-2px);
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
            <div class="logo-icon">✅</div>
            <h1>Compte réactivé avec succès !</h1>
            <p>Reine ESGIS {{ date('Y') }}</p>
        </div>

        <div class="content">
            <p class="greeting">
                Bonjour {{ $prenom }} {{ $nom }},
            </p>

            <div class="intro-text">
                Nous sommes heureux de vous informer que <strong>votre accès à votre espace candidat Reine ESGIS {{ date('Y') }} a été rétabli</strong>.
            </div>

            <div class="success-box">
                <div class="success-badge">✨ COMPTE ACTIF</div>
                <p>
                    Vous pouvez désormais vous reconnecter à votre espace personnel et continuer à participer au concours.
                </p>
            </div>

            <div class="info-box">
                <div class="info-box-title">🎯 Vous pouvez maintenant</div>
                <p>
                    • Accéder à votre espace personnel<br>
                    • Modifier vos informations et médias<br>
                    • Collecter des votes de nouveau<br>
                    • Participer pleinement au concours
                </p>
            </div>

            <div style="text-align: center; margin: 25px 0;">
                <a href="{{ url('/connexion') }}" class="cta-button">
                    🔐 Se connecter maintenant
                </a>
            </div>

            <div class="intro-text" style="margin-top: 20px; background: #fef3c7; padding: 12px; border-radius: 8px; border-left: 4px solid #f59e0b;">
                <strong style="color: #78350f;">⚠️ Rappel important :</strong><br>
                <span style="color: #92400e; font-size: 12px;">
                    Nous vous rappelons de respecter le règlement du concours pour garantir une compétition équitable et éviter toute nouvelle restriction.
                </span>
            </div>

            <div style="margin-top: 18px; color: #6b7280; font-size: 12px;">
                Bonne chance pour la suite du concours !<br><br>
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
