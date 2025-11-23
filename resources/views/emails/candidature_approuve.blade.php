<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidature approuvée - Reine ESGIS</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #fce7f3 0%, #fff7ed 100%);
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
        .logo-crown {
            font-size: 45px;
            margin-bottom: 12px;
            animation: bounce 2s ease-in-out infinite;
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }
        .header h1 {
            color: white;
            font-size: 20px;
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
            background: white;
        }
        .success-banner {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            border: 2px solid #10b981;
            border-radius: 12px;
            padding: 18px;
            text-align: center;
            margin-bottom: 20px;
        }
        .success-badge {
            display: inline-block;
            background: white;
            color: #065f46;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 13px;
            margin-bottom: 10px;
        }
        .success-banner h2 {
            color: #065f46;
            font-size: 16px;
            margin: 8px 0;
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
            margin-bottom: 20px;
            line-height: 1.6;
        }
        .credentials-box {
            background: #fef3c7;
            border: 2px solid #f59e0b;
            border-radius: 10px;
            padding: 18px;
            margin: 20px 0;
        }
        .credentials-box h3 {
            color: #92400e;
            font-size: 14px;
            margin-bottom: 12px;
            font-weight: 700;
        }
        .credential-item {
            background: white;
            padding: 10px;
            border-radius: 6px;
            margin: 8px 0;
        }
        .credential-label {
            font-size: 11px;
            color: #6b7280;
            text-transform: uppercase;
            font-weight: 600;
        }
        .credential-value {
            font-size: 14px;
            color: #111827;
            font-weight: 600;
            margin-top: 4px;
        }
        .cta-button {
            display: block;
            background: linear-gradient(135deg, #ec4899 0%, #f97316 100%);
            color: white !important;
            text-align: center;
            padding: 14px 24px;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            margin: 20px 0;
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
            <div class="logo-crown">👑</div>
            <h1>Félicitations !</h1>
            <p>Reine ESGIS {{ date('Y') }}</p>
        </div>

        <div class="content">
            <div class="success-banner">
                <div class="success-badge">✓ APPROUVÉE</div>
                <h2>Votre candidature est validée !</h2>
            </div>

            <p class="greeting">
                Bonjour {{ $prenom }} {{ $nom }},
            </p>

            <div class="intro-text">
                Nous sommes ravis de vous annoncer que votre candidature au concours <strong>Reine ESGIS {{ date('Y') }}</strong> a été <strong>approuvée</strong> ! 🎉
            </div>

            <div class="credentials-box">
                <h3>🔐 Vos identifiants de connexion</h3>
                <div class="credential-item">
                    <div class="credential-label">Email</div>
                    <div class="credential-value">{{ $email }}</div>
                </div>
                <div class="credential-item">
                    <div class="credential-label">Mot de passe</div>
                    <div class="credential-value">{{ $password }}</div>
                </div>
            </div>

            <a href="{{ url('/connexion') }}" class="cta-button">
                🚀 Accéder à mon espace
            </a>

            <div class="info-box">
                <div class="info-box-title">📱 Prochaines étapes</div>
                <p>
                    • Connectez-vous à votre espace<br>
                    • Complétez votre profil<br>
                    • Ajoutez vos photos et vidéo<br>
                    • Partagez votre page pour collecter des votes
                </p>
            </div>

            <div class="intro-text" style="margin-top: 20px;">
                <strong>Important :</strong> Changez votre mot de passe après votre première connexion pour sécuriser votre compte.
            </div>

            <p style="font-size: 13px; color: #374151; margin-top: 20px;">
                Bonne chance pour le concours ! 🍀✨
            </p>

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
