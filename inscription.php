<?php

session_start();
require_once 'config.php';


$erreur = '';
$succes = '';

$pseudo = '';
$email = '';


/* ==================================================
   TRAITEMENT DE L'INSCRIPTION
================================================== */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $pseudo = trim($_POST['pseudo'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm-password'] ?? '';


    /* ==================================================
       VÉRIFICATION DES CHAMPS
    ================================================== */

    if (
        $pseudo === ''
        || $email === ''
        || $password === ''
        || $confirmPassword === ''
    ) {

        $erreur =
            'Veuillez remplir tous les champs.';


    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $erreur =
            'Veuillez entrer une adresse e-mail valide.';


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
           VÉRIFICATION DU PSEUDO
        ================================================== */

        $requetePseudo = $pdo->prepare(
            "SELECT id
             FROM utilisateurs
             WHERE pseudo = :pseudo"
        );

        $requetePseudo->execute([
            'pseudo' => $pseudo
        ]);

        $pseudoExiste =
            $requetePseudo->fetch(PDO::FETCH_ASSOC);


        if ($pseudoExiste) {

            $erreur =
                'Ce pseudo est déjà utilisé.';


        } else {


            /* ==================================================
               VÉRIFICATION DE L'ADRESSE E-MAIL
            ================================================== */

            $requeteEmail = $pdo->prepare(
                "SELECT id
                 FROM utilisateurs
                 WHERE email = :email"
            );

            $requeteEmail->execute([
                'email' => $email
            ]);

            $emailExiste =
                $requeteEmail->fetch(PDO::FETCH_ASSOC);


            if ($emailExiste) {

                $erreur =
                    'Cette adresse e-mail est déjà utilisée.';


            } else {


                /* ==================================================
                   CRÉATION DU COMPTE
                ================================================== */

                $motDePasseHash =
                    password_hash(
                        $password,
                        PASSWORD_DEFAULT
                    );


                $requeteCreation = $pdo->prepare(
                    "INSERT INTO utilisateurs
                    (
                        pseudo,
                        email,
                        mot_de_passe
                    )
                    VALUES
                    (
                        :pseudo,
                        :email,
                        :mot_de_passe
                    )"
                );


                $requeteCreation->execute([
                    'pseudo' => $pseudo,
                    'email' => $email,
                    'mot_de_passe' => $motDePasseHash
                ]);


                $succes =
                    'Votre compte a bien été créé !';


                /* On vide le formulaire */

                $pseudo = '';
                $email = '';
            }
        }
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
        Inscription - FantasyRealm Online
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
            INSCRIPTION
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

        <?php } ?>



        <form
            method="POST"
            action=""
        >


            <label for="pseudo">
                Pseudo
            </label>


            <input
                type="text"
                id="pseudo"
                name="pseudo"
                value="<?php echo htmlspecialchars($pseudo); ?>"
                required
            >



            <label for="email">
                Adresse e-mail
            </label>


            <input
                type="email"
                id="email"
                name="email"
                value="<?php echo htmlspecialchars($email); ?>"
                required
            >



            <label for="password">
                Mot de passe
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
                S'INSCRIRE
            </button>


        </form>


        <a href="connexion.php">
            Déjà un compte ? Se connecter
        </a>


    </section>


</main>


</body>

</html>