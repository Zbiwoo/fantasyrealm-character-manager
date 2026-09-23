<?php

session_start();
require_once 'config.php';


$erreur = '';
$succes = '';
$tokenValide = false;
$utilisateur = null;


/* ==================================================
   RÉCUPÉRATION DU TOKEN
================================================== */

$token = trim($_GET['token'] ?? '');


if ($token === '') {

    $erreur =
        'Ce lien de réinitialisation est invalide.';

} else {


    /* ==================================================
       RECHERCHE DU TOKEN DANS LA BDD
    ================================================== */

    $requeteToken = $pdo->prepare(
        "SELECT id, pseudo, reset_token_expiration
         FROM utilisateurs
         WHERE reset_token = :token"
    );

    $requeteToken->execute([
        'token' => $token
    ]);

    $utilisateur =
        $requeteToken->fetch(PDO::FETCH_ASSOC);


    if (!$utilisateur) {

        $erreur =
            'Ce lien de réinitialisation est invalide ou a déjà été utilisé.';


    } elseif (
        empty($utilisateur['reset_token_expiration'])
        || strtotime($utilisateur['reset_token_expiration']) < time()
    ) {

        $erreur =
            'Ce lien de réinitialisation a expiré.';


    } else {

        $tokenValide = true;
    }
}


/* ==================================================
   NOUVEAU MOT DE PASSE
================================================== */

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && $tokenValide
) {

    $password =
        $_POST['password'] ?? '';

    $confirmPassword =
        $_POST['confirm-password'] ?? '';


    /* ==================================================
       VÉRIFICATIONS
    ================================================== */

    if (
        $password === ''
        || $confirmPassword === ''
    ) {

        $erreur =
            'Veuillez remplir tous les champs.';


    } elseif ($password !== $confirmPassword) {

        $erreur =
            'Les mots de passe ne correspondent pas.';


    } elseif (
        !preg_match('/[A-Z]/', $password)
        || !preg_match('/[a-z]/', $password)
        || !preg_match('/[0-9]/', $password)
        || !preg_match('/[^a-zA-Z0-9]/', $password)
    ) {

        $erreur =
            'Le mot de passe doit contenir une majuscule, une minuscule, un chiffre et un caractère spécial.';


    } else {


        /* ==================================================
           HASH DU NOUVEAU MOT DE PASSE
        ================================================== */

        $nouveauHash =
            password_hash(
                $password,
                PASSWORD_DEFAULT
            );


        /* ==================================================
           MODIFICATION DANS LA BDD
        ================================================== */

        $requeteModification = $pdo->prepare(
            "UPDATE utilisateurs
             SET mot_de_passe = :mot_de_passe,
                 reset_token = NULL,
                 reset_token_expiration = NULL
             WHERE id = :id"
        );


        $requeteModification->execute([
            'mot_de_passe' => $nouveauHash,
            'id' => $utilisateur['id']
        ]);


        $succes =
            'Votre mot de passe a bien été modifié.';


        /*
            Le token vient d'être supprimé.
            Le formulaire ne doit donc plus être affiché.
        */

        $tokenValide = false;
    }
}

?>


<!DOCTYPE html>

<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Nouveau mot de passe - FantasyRealm Online
    </title>

    <link
        rel="stylesheet"
        href="assets/CSS/style.css"
    >

</head>


<body>


<?php include 'navbar.php'; ?>


<main class="auth-page">


    <section class="login-box">


        <h1>
            NOUVEAU MOT DE PASSE
        </h1>


        <?php if ($erreur !== '') { ?>

            <p class="error-message">

                <?php
                echo htmlspecialchars($erreur);
                ?>

            </p>

        <?php } ?>


        <?php if ($succes !== '') { ?>

            <p class="success-message">

                <?php
                echo htmlspecialchars($succes);
                ?>

            </p>


            <a href="connexion.php">
                SE CONNECTER
            </a>

        <?php } ?>



        <?php if ($tokenValide) { ?>


            <p>
                Choisissez votre nouveau mot de passe.
            </p>


            <form
                method="POST"
                action="?token=<?php echo urlencode($token); ?>"
            >


                <label for="password">
                    Nouveau mot de passe
                </label>


                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                >


                <p class="password-help">
                    Majuscule, minuscule, chiffre et caractère spécial.
                </p>


                <label for="confirm-password">
                    Confirmer le mot de passe
                </label>


                <input
                    type="password"
                    id="confirm-password"
                    name="confirm-password"
                    required
                >


                <button type="submit">
                    MODIFIER LE MOT DE PASSE
                </button>


            </form>


        <?php } elseif ($succes === '') { ?>


            <a href="mot-de-passe-oublie.php">
                DEMANDER UN NOUVEAU LIEN
            </a>


        <?php } ?>


    </section>


</main>


</body>

</html>