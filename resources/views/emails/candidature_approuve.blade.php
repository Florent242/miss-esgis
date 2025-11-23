<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidature approuvée - Reine ESGIS</title>
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
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }
        .header h1 {
            margin: 0 0 10px 0;
            font-size: 28px;
            font-weight: 700;
        }
        .success-icon {
            font-size: 64px;
            margin-bottom: 15px;
        }
        .content {
            padding: 40px 30px;
        }
        .card {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            border-left: 4px solid #10b981;
            border-radius: 12px;
            padding: 25px;
            margin: 25px 0;
        }
        .features {
            background: #f9fafb;
            border-radius: 12px;
            padding: 25px;
            margin: 25px 0;
        }
        .feature-item {
            display: flex;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .feature-item:last-child {
            border-bottom: none;
        }
        .feature-icon {
            font-size: 24px;
            margin-right: 15px;
            min-width: 30px;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #ec4899 0%, #f97316 100%);
            color: white !important;
            padding: 15px 40px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            font-size: 16px;
            margin: 25px 0;
            box-shadow: 0 4px 15px rgba(236, 72, 153, 0.3);
            transition: transform 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .credentials-box {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 20px;
            margin: 25px 0;
            border-radius: 8px;
        }
        .tip-box {
            background: #dbeafe;
            border-left: 4px solid #3b82f6;
            padding: 20px;
            margin: 25px 0;
            border-radius: 8px;
        }
        .footer {
            background: #f9fafb;
            padding: 25px;
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
            <div class="success-icon">🎉</div>
            <h1>FÉLICITATIONS !</h1>
            <p style="margin: 0; font-size: 18px; opacity: 0.95;">{{ $candidate->prenom }} {{ $candidate->nom }}</p>
        </div>

        <div class="content">
            <div class="card">
                <h2 style="color: #065f46; margin: 0 0 15px 0; font-size: 20px;">
                    ✨ Votre candidature a été approuvée !
                </h2>
                <p style="margin: 0; color: #047857; font-size: 16px;">
                    Nous avons le plaisir de vous informer que votre candidature à l'élection <strong>Reine ESGIS {{ date('Y') }}</strong> a été validée avec succès !
                </p>
            </div>

            <div class="features">
                <h3 style="margin: 0 0 20px 0; color: #111827; font-size: 18px;">
                    🎯 Votre espace personnel vous permet de :
                </h3>
                
                <div class="feature-item">
                    <span class="feature-icon">📸</span>
                    <span style="color: #374151;">Gérer vos photos et vidéos de présentation</span>
                </div>
                
                <div class="feature-item">
                    <span class="feature-icon">📊</span>
                    <span style="color: #374151;">Suivre vos votes en temps réel</span>
                </div>
                
                <div class="feature-item">
                    <span class="feature-icon">✏️</span>
                    <span style="color: #374151;">Modifier vos informations personnelles</span>
                </div>
                
                <div class="feature-item">
                    <span class="feature-icon">🏆</span>
                    <span style="color: #374151;">Consulter votre classement</span>
                </div>
            </div>

            <div style="text-align: center;">
                <a href="{{ url('/connexion') }}" class="btn">
                    🔐 Accéder à mon espace
                </a>
            </div>

            <div class="credentials-box">
                <p style="margin: 0 0 15px 0; font-weight: 700; color: #92400e;">
                    🔑 Vos identifiants de connexion :
                </p>
                <p style="margin: 5px 0; color: #78350f;">
                    <strong>Email :</strong> {{ $candidate->email }}
                </p>
                <p style="margin: 5px 0; color: #78350f;">
                    <strong>Mot de passe :</strong> Celui que vous avez choisi lors de l'inscription
                </p>
            </div>

            <div class="tip-box">
                <p style="margin: 0; color: #1e40af;">
                    <strong>💡 Conseil important :</strong> Pensez à ajouter des photos de qualité et une vidéo de présentation pour maximiser vos chances de remporter le titre !
                </p>
            </div>

            <p style="margin-top: 30px; color: #374151; font-size: 16px;">
                Nous vous souhaitons une excellente participation et beaucoup de succès dans cette aventure ! 🌟
            </p>

            <p style="margin-top: 25px; color: #6b7280; font-size: 14px;">
                Cordialement,<br>
                <strong style="color: #111827;">L'équipe Reine ESGIS-Bénin</strong>
            </p>
        </div>

        <div class="footer">
            <p style="margin: 5px 0;">Ceci est un message automatique, merci de ne pas y répondre directement.</p>
            <p style="margin: 5px 0;">Pour toute question, contactez-nous à {{ env('MAIL_FROM_ADDRESS', 'contact@missesgis.com') }}</p>
            <p style="margin: 5px 0;">© {{ date('Y') }} Reine ESGIS-Bénin. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>
