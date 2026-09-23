<?php

session_start();

require_once 'config.php';
require_once 'mail-config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'PHPMailer/src/Exception.php';
require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';


/* ==================================================
   VALEURS PAR DÉFAUT
================================================== */

$email = '';
$pseudo = '';
$message = '';

$messageSucces = '';
$messageErreur = '';


/* ==================================================
   PRÉREMPLISSAGE SI UTILISATEUR CONNECTÉ
================================================== */

if (isset($_SESSION['user_id'])) {

    $requeteUtilisateur = $pdo->prepare(
        "SELECT pseudo, email
         FROM utilisateurs
         WHERE id = :id"
    );

    $requeteUtilisateur->execute([
        'id' => $_SESSION['user_id']
    ]);

    $utilisateur =
        $requeteUtilisateur->fetch(PDO::FETCH_ASSOC);

    if ($utilisateur) {

        $email = $utilisateur['email'];
        $pseudo = $utilisateur['pseudo'];
    }
}


/* ==================================================
   TRAITEMENT DU FORMULAIRE
================================================== */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $pseudo = trim($_POST['pseudo'] ?? '');
    $message = trim($_POST['message'] ?? '');


    /* Vérification des champs */

    if (
        $email === ''
        || $pseudo === ''
        || $message === ''
    ) {

        $messageErreur =
            'Veuillez remplir tous les champs.';


    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $messageErreur =
            'Veuillez entrer une adresse e-mail valide.';


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


        if (!$pseudoExiste) {

            $messageErreur =
                'Ce pseudo ne correspond à aucun utilisateur.';


        } else {


            /* ==================================================
               ENVOI DU MAIL AVEC PHPMAILER
            ================================================== */

            $mail = new PHPMailer(true);

            try {

                /* Configuration SMTP */

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


                /* Encodage */

                $mail->CharSet =
                    'UTF-8';


                /* Expéditeur */

                $mail->setFrom(
                    MAIL_USERNAME,
                    'FantasyRealm Online'
                );


                /* Destination */

                $mail->addAddress(
                    MAIL_USERNAME
                );


                /*
                    Quand l'équipe répond au mail,
                    la réponse sera envoyée à
                    l'adresse indiquée dans le formulaire.
                */

                $mail->addReplyTo(
                    $email,
                    $pseudo
                );


                /* Contenu */

                $mail->isHTML(true);

                $mail->Subject =
                    'Nouvelle demande de contact - FantasyRealm';


                $pseudoSecurise =
                    htmlspecialchars(
                        $pseudo,
                        ENT_QUOTES,
                        'UTF-8'
                    );

                $emailSecurise =
                    htmlspecialchars(
                        $email,
                        ENT_QUOTES,
                        'UTF-8'
                    );

                $messageSecurise =
                    nl2br(
                        htmlspecialchars(
                            $message,
                            ENT_QUOTES,
                            'UTF-8'
                        )
                    );


                $mail->Body = "

                    <h2>
                        Nouvelle demande FantasyRealm
                    </h2>

                    <p>
                        <strong>Pseudo :</strong>
                        {$pseudoSecurise}
                    </p>

                    <p>
                        <strong>Adresse e-mail :</strong>
                        {$emailSecurise}
                    </p>

                    <p>
                        <strong>Demande :</strong>
                    </p>

                    <p>
                        {$messageSecurise}
                    </p>

                ";


                /* Version texte */

                $mail->AltBody =
                    "Nouvelle demande FantasyRealm\n\n"
                    . "Pseudo : " . $pseudo . "\n"
                    . "Adresse e-mail : " . $email . "\n\n"
                    . "Demande :\n"
                    . $message;


                /* ENVOI */

                $mail->send();


                $messageSucces =
                    'Votre demande a bien été envoyée.';


                /* On vide uniquement le message */

                $message = '';


            } catch (Exception $e) {

                $messageErreur =
                    "Une erreur est survenue pendant l'envoi du message.";
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
        Contact - FantasyRealm Online
    </title>

    <link
        rel="stylesheet"
        href="assets/CSS/style.css"
    >

</head>


<body>


<?php include 'navbar.php'; ?>


<main class="contact-page">


    <section class="contact-box">


        <h1>
            CONTACTEZ-NOUS
        </h1>


        <p>
            Une question ou un problème ?
            Envoyez votre demande à l'équipe PixelVerse Studios.
        </p>



        <?php if ($messageSucces !== '') { ?>

            <div class="contact-success">

                <?php
                echo htmlspecialchars($messageSucces);
                ?>

            </div>

        <?php } ?>



        <?php if ($messageErreur !== '') { ?>

            <div class="contact-error">

                <?php
                echo htmlspecialchars($messageErreur);
                ?>

            </div>

        <?php } ?>



        <form
            method="POST"
            action=""
        >


            <label for="contact-email">
                Adresse e-mail
            </label>


            <input
                type="email"
                id="contact-email"
                name="email"
                value="<?php echo htmlspecialchars($email); ?>"
                required
            >



            <label for="contact-pseudo">
                Pseudo
            </label>


            <input
                type="text"
                id="contact-pseudo"
                name="pseudo"
                value="<?php echo htmlspecialchars($pseudo); ?>"
                required
            >



            <label for="contact-message">
                Détail de votre demande
            </label>


            <textarea
                id="contact-message"
                name="message"
                rows="6"
                required
            ><?php echo htmlspecialchars($message); ?></textarea>



            <button type="submit">
                ENVOYER
            </button>


        </form>


    </section>


</main>


</body>

</html>