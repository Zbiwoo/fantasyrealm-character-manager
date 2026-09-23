<?php

session_start();

require_once 'config.php';
require_once 'mail-config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'PHPMailer/src/Exception.php';
require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';


$erreur = '';
$succes = '';
$email = '';


/* ==================================================
   DEMANDE DE RÉINITIALISATION
================================================== */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');


    if ($email === '') {

        $erreur =
            'Veuillez entrer votre adresse e-mail.';


    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $erreur =
            'Veuillez entrer une adresse e-mail valide.';


    } else {


        /* ==================================================
           RECHERCHE DE L'UTILISATEUR
        ================================================== */

        $requete = $pdo->prepare(
            "SELECT id, pseudo, email
             FROM utilisateurs
             WHERE email = :email"
        );

        $requete->execute([
            'email' => $email
        ]);

        $utilisateur =
            $requete->fetch(PDO::FETCH_ASSOC);


        if (!$utilisateur) {

            $erreur =
                'Aucun compte ne correspond à cette adresse e-mail.';


        } else {


            /* ==================================================
               CRÉATION D'UN TOKEN
            ================================================== */

            $token =
                bin2hex(random_bytes(32));


            $expiration =
                date(
                    'Y-m-d H:i:s',
                    strtotime('+1 hour')
                );


            /* ==================================================
               ENREGISTREMENT DU TOKEN
            ================================================== */

            $requeteToken = $pdo->prepare(
                "UPDATE utilisateurs
                 SET reset_token = :token,
                     reset_token_expiration = :expiration
                 WHERE id = :id"
            );


            $requeteToken->execute([
                'token' => $token,
                'expiration' => $expiration,
                'id' => $utilisateur['id']
            ]);


            /* ==================================================
               LIEN DE RÉINITIALISATION
            ================================================== */

            $lien =
                'http://localhost/fantasyrealm-character-manager/reinitialiser-mot-de-passe.php?token='
                . urlencode($token);


            /* ==================================================
               ENVOI DU MAIL
            ================================================== */

            $mail = new PHPMailer(true);


            try {

                $mail->isSMTP();

                $mail->Host =
                    'smtp.gmail.com';

                $mail->SMTPAuth =
                    true;

                $mail->Username =
                    MAIL_USERNAME;

                $mail->Password =
                    MAIL_PASSWORD;

                $mail->SMTPSecure =
                    PHPMailer::ENCRYPTION_STARTTLS;

                $mail->Port =
                    587;

                $mail->CharSet =
                    'UTF-8';


                $mail->setFrom(
                    MAIL_USERNAME,
                    'FantasyRealm Online'
                );


                $mail->addAddress(
                    $utilisateur['email'],
                    $utilisateur['pseudo']
                );


                $mail->isHTML(true);


                $mail->Subject =
                    'Réinitialisation de votre mot de passe - FantasyRealm';


                $pseudoSecurise =
                    htmlspecialchars(
                        $utilisateur['pseudo'],
                        ENT_QUOTES,
                        'UTF-8'
                    );


                $lienSecurise =
                    htmlspecialchars(
                        $lien,
                        ENT_QUOTES,
                        'UTF-8'
                    );


                $mail->Body = "

                    <h2>
                        FantasyRealm Online
                    </h2>

                    <p>
                        Bonjour {$pseudoSecurise},
                    </p>

                    <p>
                        Une demande de réinitialisation
                        de votre mot de passe a été effectuée.
                    </p>

                    <p>
                        Cliquez sur le lien suivant :
                    </p>

                    <p>
                        <a href=\"{$lienSecurise}\">
                            Réinitialiser mon mot de passe
                        </a>
                    </p>

                    <p>
                        Ce lien est valable pendant une heure.
                    </p>

                ";


                $mail->AltBody =
                    "Bonjour "
                    . $utilisateur['pseudo']
                    . ",\n\n"
                    . "Voici votre lien de réinitialisation :\n"
                    . $lien
                    . "\n\nCe lien est valable pendant une heure.";


                $mail->send();


                $succes =
                    'Un e-mail de réinitialisation vous a été envoyé.';


                $email = '';


            } catch (Exception $e) {

                $erreur =
                    "Une erreur est survenue pendant l'envoi de l'e-mail.";
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
        Mot de passe oublié - FantasyRealm Online
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
            MOT DE PASSE OUBLIÉ
        </h1>


        <p>
            Entrez l'adresse e-mail associée à votre compte.
        </p>


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


            <button type="submit">
                ENVOYER LE LIEN
            </button>


        </form>


        <a href="connexion.php">
            Retour à la connexion
        </a>


    </section>


</main>


</body>

</html>