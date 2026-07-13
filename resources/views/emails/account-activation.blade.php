<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Activation du compte</title>
</head>

<body style="font-family: Arial, sans-serif; background:#f4f4f4; padding:40px;">

<div style="max-width:650px;margin:auto;background:white;padding:40px;border-radius:10px;">

    <h2>
        Bienvenue {{ $user->nom }} {{ $user->prenom }}
    </h2>

    <p>
        Votre compte a été créé avec succès.
    </p>

    <p>
        Cliquez sur le bouton ci-dessous pour définir votre mot de passe.
    </p>

    <p style="text-align:center;margin:40px 0;">

        <a href="{{ $activationLink }}"
           style="
                background:#2563eb;
                color:white;
                padding:15px 25px;
                text-decoration:none;
                border-radius:8px;
                font-weight:bold;
           ">

            Activer mon compte

        </a>

    </p>

    <p>
        Ce lien expirera dans 24 heures.
    </p>

    <hr>

    <small>
        Si vous n'êtes pas concerné par cet e-mail, vous pouvez simplement l'ignorer.
    </small>

</div>

</body>

</html>