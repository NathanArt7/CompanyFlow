<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Réinitialisation du mot de passe</title>
</head>

<body style="font-family: Arial, Helvetica, sans-serif; background:#f4f6f9; padding:40px;">

    <table
        style="max-width:600px; margin:auto; background:white; border-radius:10px; padding:40px;">

        <tr>
            <td>

                <h2 style="color:#2d3748;">
                    Bonjour {{ $user->prenom }} {{ $user->nom }},
                </h2>

                <p style="font-size:16px; color:#4a5568; line-height:1.6;">
                    Nous avons reçu une demande de réinitialisation de votre mot de passe.
                </p>

                <p style="font-size:16px; color:#4a5568; line-height:1.6;">
                    Cliquez sur le bouton ci-dessous pour définir un nouveau mot de passe.
                </p>

                <div style="text-align:center; margin:40px 0;">

                    <a href="{{ $resetLink }}"
                        style="
                            background:#2563eb;
                            color:white;
                            text-decoration:none;
                            padding:15px 30px;
                            border-radius:8px;
                            display:inline-block;
                            font-size:16px;
                            font-weight:bold;
                        ">
                        Réinitialiser mon mot de passe
                    </a>

                </div>

                <p style="font-size:15px; color:#4a5568;">
                    Ce lien est valable pendant
                    <strong>24 heures</strong>.
                </p>

                <hr style="margin:35px 0;">

                <p style="font-size:14px; color:#718096;">
                    Si vous n'êtes pas à l'origine de cette demande,
                    vous pouvez ignorer cet e-mail en toute sécurité.
                    Votre mot de passe ne sera pas modifié tant que vous
                    n'aurez pas utilisé ce lien.
                </p>

                <p style="margin-top:40px; color:#4a5568;">
                    À bientôt,<br>

                    <strong>L'équipe AppReservation</strong>
                </p>

            </td>
        </tr>

    </table>

</body>

</html>