<?php

session_start();
require_once 'config.php';


$erreur = '';
$email = '';


/* ==================================================
   TRAITEMENT DE LA CONNEXION
================================================== */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';


    /* ==================================================
       VÉRIFICATION DES CHAMPS
    ================================================== */

    if ($email === '' || $password === '') {

        $erreur =
            'Veuillez remplir tous les champs.';


    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $erreur =
            'Veuillez entrer une adresse e-mail valide.';


    } else {


        /* ==================================================
           RECHERCHE DE L'UTILISATEUR
        ================================================== */

        $requete = $pdo->prepare(
            "SELECT *
             FROM utilisateurs
             WHERE email = :email"
        );

        $requete->execute([
            'email' => $email
        ]);

        $utilisateur =
            $requete->fetch(PDO::FETCH_ASSOC);


        /* ==================================================
           VÉRIFICATION DU MOT DE PASSE
        ================================================== */

        if (
            !$utilisateur
            || !password_verify(
                $password,
                $utilisateur['mot_de_passe']
            )
        ) {

            $erreur =
                'Adresse e-mail ou mot de passe incorrect.';


        /* ==================================================
           VÉRIFICATION DE LA SUSPENSION
        ================================================== */

        } elseif ((int) $utilisateur['suspendu'] === 1) {

            $erreur =
                'Ce compte est actuellement suspendu.';


        /* ==================================================
           CONNEXION RÉUSSIE
        ================================================== */

        } else {

            session_regenerate_id(true);

            $_SESSION['user_id'] =
                $utilisateur['id'];

            $_SESSION['pseudo'] =
                $utilisateur['pseudo'];

            $_SESSION['role'] =
                $utilisateur['role'];


            header('Location: index.php');
            exit;
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
        Connexion - FantasyRealm Online
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
            CONNEXION
        </h1>


        <?php if ($erreur !== '') { ?>

            <p class="error-message">

                <?php
                echo htmlspecialchars($erreur);
                ?>

            </p>

        <?php } ?>


        <form
            method="POST"
            action=""
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


            <button type="submit">
                SE CONNECTER
            </button>


        </form>


        <a href="mot-de-passe-oublie.php">
            Mot de passe oublié ?
        </a>


        <p class="register-link">

            Pas encore de compte ?

            <a href="inscription.php">
                Créer un compte
            </a>

        </p>


    </section>


</main>


</body>

</html>
