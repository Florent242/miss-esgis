<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidature - Reine ESGIS</title>
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
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }
        .header h1 {
            margin: 0 0 10px 0;
            font-size: 28px;
            font-weight: 700;
        }
        .icon {
            font-size: 64px;
            margin-bottom: 15px;
        }
        .content {
            padding: 40px 30px;
        }
        .message-box {
            background: #f9fafb;
            border-left: 4px solid #6b7280;
            border-radius: 12px;
            padding: 25px;
            margin: 25px 0;
        }
        .info-box {
            background: #dbeafe;
            border-left: 4px solid #3b82f6;
            padding: 20px;
            margin: 25px 0;
            border-radius: 8px;
        }
        .contact-box {
            background: #f0fdf4;
            border-left: 4px solid #10b981;
            padding: 20px;
            margin: 25px 0;
            border-radius: 8px;
        }
        .encouragement-box {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-radius: 12px;
            padding: 25px;
            margin: 25px 0;
            text-align: center;
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
            <div class="icon">💌</div>
            <h1>Réponse à votre candidature</h1>
            <p style="margin: 0; font-size: 18px; opacity: 0.95;">{{ $candidate->prenom }} {{ $candidate->nom }}</p>
        </div>

        <div class="content">
            <h2 style="color: #111827; margin: 0 0 20px 0; font-size: 22px;">
                Bonjour {{ $candidate->prenom }},
            </h2>

            <p style="font-size: 16px; color: #374151; margin-bottom: 25px;">
                Nous vous remercions sincèrement pour l'intérêt que vous avez porté à l'élection <strong>Reine ESGIS {{ date('Y') }}</strong> et pour le temps consacré à votre candidature.
            </p>

            <div class="message-box">
                <p style="margin: 0; color: #374151; font-size: 16px;">
                    Après un examen attentif de votre dossier, nous avons le regret de vous informer que nous ne pouvons pas donner une suite favorable à votre candidature cette année.
                </p>
            </div>

            <div class="info-box">
                <p style="margin: 0 0 10px 0; font-weight: 700; color: #1e40af;">
                    ℹ️ Important à savoir
                </p>
                <p style="margin: 0; color: #1e3a8a; font-size: 15px;">
                    Cette décision ne remet en aucun cas en cause vos qualités personnelles. Le nombre de places disponibles étant limité, nous avons dû faire des choix difficiles parmi de nombreuses excellentes candidatures.
                </p>
            </div>

            <div class="contact-box">
                <p style="margin: 0 0 15px 0; font-weight: 700; color: #065f46;">
                    📞 Besoin d'éclaircissements ?
                </p>
                <p style="margin: 0 0 10px 0; color: #047857; font-size: 15px;">
                    Si vous pensez qu'il s'agit d'une erreur ou si vous souhaitez obtenir plus d'informations, n'hésitez pas à contacter le comité d'organisation :
                </p>
                <p style="margin: 5px 0; color: #047857;">
                    <strong>📧 Email :</strong> {{ env('MAIL_FROM_ADDRESS', 'contact@missesgis.com') }}
                </p>
                <p style="margin: 5px 0; color: #047857;">
                    <strong>📱 Contact direct :</strong> Rapprochez-vous de l'équipe organisatrice
                </p>
            </div>

            <div class="encouragement-box">
                <p style="margin: 0; font-size: 20px; font-weight: 700; color: #92400e;">
                    💪 Ne baissez pas les bras !
                </p>
                <p style="margin: 15px 0 0 0; color: #78350f; font-size: 16px;">
                    Nous vous encourageons vivement à retenter votre chance l'année prochaine. Chaque nouvelle édition apporte de nouvelles opportunités !
                </p>
            </div>

            <p style="margin-top: 30px; color: #374151; font-size: 16px;">
                Nous vous souhaitons le meilleur pour vos projets futurs et espérons vous revoir bientôt ! 🌟
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
