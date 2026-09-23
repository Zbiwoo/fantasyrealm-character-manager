<?php

session_start();
require_once 'config.php';
require_once 'mongodb.php';
require_once 'mail-config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'PHPMailer/src/Exception.php';
require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';




/* ==================================================
   ENVOI DES NOTIFICATIONS PAR E-MAIL
================================================== */

function creerTemplateMailFantasyRealm(
    string $pseudo,
    string $badge,
    string $titre,
    string $message,
    string $detail = '',
    string $boutonTexte = '',
    string $boutonLien = ''
): string {
    $pseudoSecurise = htmlspecialchars($pseudo, ENT_QUOTES, 'UTF-8');
    $badgeSecurise = htmlspecialchars($badge, ENT_QUOTES, 'UTF-8');
    $titreSecurise = htmlspecialchars($titre, ENT_QUOTES, 'UTF-8');

    $blocDetail = '';

    if ($detail !== '') {
        $blocDetail = '
            <div style="
                margin:26px 0;
                padding:18px 20px;
                background:#211824;
                border:1px solid #8A6A2F;
                border-left:4px solid #C6A15B;
                border-radius:8px;
                color:#F5F0F6;
                font-size:14px;
                line-height:1.7;
            ">
                ' . $detail . '
            </div>
        ';
    }

    $blocBouton = '';

    if ($boutonTexte !== '' && $boutonLien !== '') {
        $boutonTexteSecurise = htmlspecialchars($boutonTexte, ENT_QUOTES, 'UTF-8');
        $boutonLienSecurise = htmlspecialchars($boutonLien, ENT_QUOTES, 'UTF-8');

        $blocBouton = '
            <div style="text-align:center; margin:30px 0 8px;">
                <a href="' . $boutonLienSecurise . '" style="
                    display:inline-block;
                    padding:13px 24px;
                    background:#C6A15B;
                    color:#291F2F;
                    text-decoration:none;
                    font-weight:bold;
                    letter-spacing:1px;
                    border-radius:5px;
                ">
                    ' . $boutonTexteSecurise . '
                </a>
            </div>
        ';
    }

    return '
    <!DOCTYPE html>
    <html lang="fr">
    <body style="
        margin:0;
        padding:0;
      background:#F5F0F6;
    
        font-family:Arial,Helvetica,sans-serif;
    ">
        <div style="padding:38px 12px;">
            <div style="
                max-width:620px;
                margin:0 auto;
                overflow:hidden;
                background:#291F2F;
                border:1px solid #8A6A2F;
                border-radius:12px;
                box-shadow:0 12px 35px rgba(0,0,0,.35);
            ">
                <div style="
                    padding:28px 24px 22px;
                    text-align:center;
                    background:#3D2A48;
                    border-bottom:1px solid #8A6A2F;
                ">
                    <div style="
                        color:#E0C27A;
                        font-family:Georgia,Times New Roman,serif;
                        font-size:25px;
                        font-weight:bold;
                        letter-spacing:3px;
                    ">
                        PIXELUNIVERSE
                    </div>

                    <div style="
                        margin-top:7px;
                        color:#C6A15B;
                        font-size:11px;
                        letter-spacing:4px;
                    ">
                        FANTASYREALM ONLINE
                    </div>
                </div>

                <div style="padding:34px 34px 30px;">
                    <div style="text-align:center;">
                        <span style="
                            display:inline-block;
                            padding:7px 14px;
                            border:1px solid #C6A15B;
                            border-radius:20px;
                            color:#E0C27A;
                            font-size:11px;
                            font-weight:bold;
                            letter-spacing:2px;
                        ">
                            ' . $badgeSecurise . '
                        </span>

                        <h1 style="
                            margin:18px 0 26px;
                            color:#E0C27A;
                            font-family:Georgia,Times New Roman,serif;
                            font-size:27px;
                            letter-spacing:1px;
                        ">
                            ' . $titreSecurise . '
                        </h1>
                    </div>

                    <p style="
                        margin:0 0 18px;
                        color:#F5F0F6;
                        font-size:16px;
                        line-height:1.7;
                    ">
                        Bonjour <strong style="color:#E0C27A;">' . $pseudoSecurise . '</strong>,
                    </p>

                    <div style="
                        color:#E9E1EB;
                        font-size:15px;
                        line-height:1.8;
                    ">
                        ' . $message . '
                    </div>

                    ' . $blocDetail . '
                    ' . $blocBouton . '

                    <p style="
                        margin:30px 0 0;
                        padding-top:22px;
                        border-top:1px solid #513B58;
                        color:#B9AEBB;
                        text-align:center;
                        font-size:12px;
                        line-height:1.6;
                    ">
                        Que votre légende continue de s&apos;écrire.<br>
                        <strong style="color:#C6A15B;">L&apos;équipe FantasyRealm Online</strong>
                    </p>
                </div>
            </div>
        </div>
    </body>
    </html>';
}


function envoyerNotificationFantasyRealm($email, $pseudo, $sujet, $contenuHtml, $contenuTexte)
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = MAIL_USERNAME;
        $mail->Password = MAIL_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';

        $mail->setFrom(
            MAIL_USERNAME,
            'FantasyRealm Online'
        );

        $mail->addAddress(
            $email,
            $pseudo
        );

        $mail->isHTML(true);
        $mail->Subject = $sujet;
        $mail->Body = $contenuHtml;
        $mail->AltBody = $contenuTexte;

        $mail->send();

        return true;
    } catch (Exception $e) {
        return false;
    }
}


/* ==================================================
   SÉCURITÉ : EMPLOYÉ OU ADMIN UNIQUEMENT
================================================== */

if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit;
}

if (
    $_SESSION['role'] !== 'employe'
    && $_SESSION['role'] !== 'admin'
) {
    header('Location: index.php');
    exit;
}


/* ==================================================
   VALIDATION D'UN PERSONNAGE
================================================== */

if (isset($_POST['valider_personnage'])) {

    $idPersonnage = (int) $_POST['personnage_id'];

    $requeteProprietaire = $pdo->prepare(
        "SELECT personnages.nom, utilisateurs.pseudo, utilisateurs.email
         FROM personnages
         INNER JOIN utilisateurs
            ON personnages.utilisateur_id = utilisateurs.id
         WHERE personnages.id = :id"
    );
    $requeteProprietaire->execute(['id' => $idPersonnage]);
    $proprietaire = $requeteProprietaire->fetch(PDO::FETCH_ASSOC);

    if ($proprietaire) {
        $requeteValidation = $pdo->prepare(
            "UPDATE personnages
             SET statut_nom = 'valide'
             WHERE id = :id"
        );
        $requeteValidation->execute(['id' => $idPersonnage]);

        enregistrerLogMongoDB(
            'VALIDATION_PERSONNAGE',
            'Validation du personnage "' . $proprietaire['nom'] . '" (ID ' . $idPersonnage . ').',
            (int) $_SESSION['user_id']
        );

        $pseudoSecurise = htmlspecialchars($proprietaire['pseudo'], ENT_QUOTES, 'UTF-8');
        $nomSecurise = htmlspecialchars($proprietaire['nom'], ENT_QUOTES, 'UTF-8');

        envoyerNotificationFantasyRealm(
            $proprietaire['email'],
            $proprietaire['pseudo'],
            'Personnage validé - FantasyRealm',
            creerTemplateMailFantasyRealm(
                $proprietaire['pseudo'],
                'PERSONNAGE VALIDÉ',
                'Votre héros rejoint le royaume',
                "<p>Bonne nouvelle ! Le nom de votre personnage <strong style=\"color:#E0C27A;\">{$nomSecurise}</strong> a été validé par notre équipe.</p>
                 <p>Vous pouvez maintenant poursuivre sa personnalisation et préparer son aventure dans FantasyRealm.</p>"
            ),
            "Bonjour " . $proprietaire['pseudo']
            . ",\n\nLe nom de votre personnage "
            . $proprietaire['nom']
            . " a été validé.\nVous pouvez maintenant poursuivre sa personnalisation."
        );
    }

    header('Location: validation-personnages.php?tab=moderation');
    exit;
}


/* ==================================================
   REFUS D'UN PERSONNAGE
================================================== */

if (isset($_POST['refuser_personnage'])) {

    $idPersonnage = (int) $_POST['personnage_id'];
    $motifRefus = trim($_POST['motif_refus'] ?? '');

    if ($motifRefus === '') {
        $motifRefus = 'Le nom proposé ne respecte pas les règles de FantasyRealm.';
    }

    $requeteProprietaire = $pdo->prepare(
        "SELECT personnages.nom, utilisateurs.pseudo, utilisateurs.email
         FROM personnages
         INNER JOIN utilisateurs
            ON personnages.utilisateur_id = utilisateurs.id
         WHERE personnages.id = :id"
    );
    $requeteProprietaire->execute(['id' => $idPersonnage]);
    $proprietaire = $requeteProprietaire->fetch(PDO::FETCH_ASSOC);

    if ($proprietaire) {
        $pseudoSecurise = htmlspecialchars($proprietaire['pseudo'], ENT_QUOTES, 'UTF-8');
        $nomSecurise = htmlspecialchars($proprietaire['nom'], ENT_QUOTES, 'UTF-8');
        $motifSecurise = htmlspecialchars($motifRefus, ENT_QUOTES, 'UTF-8');

        envoyerNotificationFantasyRealm(
            $proprietaire['email'],
            $proprietaire['pseudo'],
            'Personnage refusé - FantasyRealm',
            creerTemplateMailFantasyRealm(
                $proprietaire['pseudo'],
                'PERSONNAGE REFUSÉ',
                'Une nouvelle identité est nécessaire',
                "<p>Le nom proposé pour votre personnage <strong style=\"color:#E0C27A;\">{$nomSecurise}</strong> n’a pas été accepté par notre équipe.</p>
                 <p>Le personnage refusé a été supprimé. Vous pouvez en créer un nouveau avec un autre nom.</p>",
                "<strong style=\"color:#E0C27A;\">MOTIF DU REFUS</strong><br>{$motifSecurise}"
            ),
            "Bonjour " . $proprietaire['pseudo']
            . ",\n\nLe nom proposé pour votre personnage "
            . $proprietaire['nom']
            . " a été refusé.\nMotif : "
            . $motifRefus
            . "\n\nLe personnage refusé a été supprimé."
        );

        $requeteRefus = $pdo->prepare(
            "DELETE FROM personnages
             WHERE id = :id"
        );
        $requeteRefus->execute(['id' => $idPersonnage]);

        enregistrerLogMongoDB(
            'REFUS_PERSONNAGE',
            'Refus et suppression du personnage "' . $proprietaire['nom'] . '" (ancien ID ' . $idPersonnage . '). Motif : ' . $motifRefus,
            (int) $_SESSION['user_id']
        );
    }

    header('Location: validation-personnages.php?tab=moderation');
    exit;
}


/* ==================================================
   VALIDATION D'UN COMMENTAIRE
================================================== */

if (isset($_POST['valider_commentaire'])) {

    $idCommentaire = (int) $_POST['commentaire_id'];

    $requeteProprietaireCommentaire = $pdo->prepare(
        "SELECT
            personnages.nom AS personnage_nom,
            proprietaire.pseudo,
            proprietaire.email
         FROM commentaires
         INNER JOIN personnages
            ON commentaires.personnage_id = personnages.id
         INNER JOIN utilisateurs AS proprietaire
            ON personnages.utilisateur_id = proprietaire.id
         WHERE commentaires.id = :id"
    );
    $requeteProprietaireCommentaire->execute(['id' => $idCommentaire]);
    $proprietaire = $requeteProprietaireCommentaire->fetch(PDO::FETCH_ASSOC);

    $requeteValidationCommentaire = $pdo->prepare(
        "UPDATE commentaires
         SET statut = 'valide'
         WHERE id = :id"
    );
    $requeteValidationCommentaire->execute(['id' => $idCommentaire]);

    enregistrerLogMongoDB(
        'VALIDATION_COMMENTAIRE',
        'Validation du commentaire ID ' . $idCommentaire
        . ($proprietaire ? ' sur le personnage "' . $proprietaire['personnage_nom'] . '".' : '.'),
        (int) $_SESSION['user_id']
    );

    if ($proprietaire) {
        $pseudoSecurise = htmlspecialchars($proprietaire['pseudo'], ENT_QUOTES, 'UTF-8');
        $nomSecurise = htmlspecialchars($proprietaire['personnage_nom'], ENT_QUOTES, 'UTF-8');

        envoyerNotificationFantasyRealm(
            $proprietaire['email'],
            $proprietaire['pseudo'],
            'Nouveau commentaire publié - FantasyRealm',
            creerTemplateMailFantasyRealm(
                $proprietaire['pseudo'],
                'NOUVEL AVIS',
                'Votre personnage fait parler de lui',
                "<p>Un nouveau commentaire a été validé et publié sur votre personnage <strong style=\"color:#E0C27A;\">{$nomSecurise}</strong>.</p>
                 <p>La communauté FantasyRealm vient de laisser une nouvelle trace sur son aventure.</p>"
            ),
            "Bonjour " . $proprietaire['pseudo']
            . ",\n\nUn nouveau commentaire a été validé et publié sur votre personnage "
            . $proprietaire['personnage_nom']
            . "."
        );
    }

    header('Location: validation-personnages.php?tab=moderation');
    exit;
}


/* ==================================================
   REFUS D'UN COMMENTAIRE
================================================== */

if (isset($_POST['refuser_commentaire'])) {

    $idCommentaire = (int) $_POST['commentaire_id'];

    $requeteRefusCommentaire = $pdo->prepare(
        "DELETE FROM commentaires
         WHERE id = :id"
    );

    $requeteRefusCommentaire->execute([
        'id' => $idCommentaire
    ]);

    enregistrerLogMongoDB(
        'REFUS_COMMENTAIRE',
        'Refus et suppression du commentaire ID ' . $idCommentaire . '.',
        (int) $_SESSION['user_id']
    );

    header('Location: validation-personnages.php?tab=moderation');
    exit;
}



/* ==================================================
   GESTION DES ÉQUIPEMENTS ET POUVOIRS
================================================== */

if (isset($_POST['ajouter_element'])) {
    $nomElement = trim($_POST['nom_element'] ?? '');
    $typeElement = $_POST['type_element'] ?? '';
    $descriptionElement = trim($_POST['description_element'] ?? '');

    if ($nomElement !== '' && in_array($typeElement, ['equipement', 'pouvoir'], true)) {
        try {
            $requeteAjoutElement = $pdo->prepare(
                "INSERT INTO elements_personnalisation (nom, type, description, actif)
                 VALUES (:nom, :type, :description, 1)"
            );
            $requeteAjoutElement->execute([
                'nom' => $nomElement,
                'type' => $typeElement,
                'description' => $descriptionElement !== '' ? $descriptionElement : null
            ]);

            enregistrerLogMongoDB(
                'AJOUT_ELEMENT',
                'Ajout de l’élément "' . $nomElement . '" (' . $typeElement . ').',
                (int) $_SESSION['user_id']
            );
        } catch (PDOException $e) {
            // Le nom est UNIQUE en base : on évite de casser la page si un doublon est envoyé.
        }
    }

    header('Location: validation-personnages.php?tab=elements');
    exit;
}

if (isset($_POST['basculer_element'])) {
    $idElement = (int) ($_POST['element_id'] ?? 0);

    $requeteElement = $pdo->prepare(
        "SELECT nom, type, actif
         FROM elements_personnalisation
         WHERE id = :id"
    );
    $requeteElement->execute(['id' => $idElement]);
    $elementAvantAction = $requeteElement->fetch(PDO::FETCH_ASSOC);

    $requeteBasculerElement = $pdo->prepare(
        "UPDATE elements_personnalisation
         SET actif = IF(actif = 1, 0, 1)
         WHERE id = :id"
    );
    $requeteBasculerElement->execute(['id' => $idElement]);

    if ($elementAvantAction) {
        $actionElement = (int) $elementAvantAction['actif'] === 1
            ? 'DESACTIVATION_ELEMENT'
            : 'REACTIVATION_ELEMENT';

        $verbeElement = (int) $elementAvantAction['actif'] === 1
            ? 'Désactivation'
            : 'Réactivation';

        enregistrerLogMongoDB(
            $actionElement,
            $verbeElement . ' de l’élément "' . $elementAvantAction['nom']
            . '" (' . $elementAvantAction['type'] . ', ID ' . $idElement . ').',
            (int) $_SESSION['user_id']
        );
    }

    header('Location: validation-personnages.php?tab=elements');
    exit;
}

if (isset($_POST['supprimer_element'])) {
    $idElement = (int) ($_POST['element_id'] ?? 0);

    $requeteElement = $pdo->prepare(
        "SELECT nom, type
         FROM elements_personnalisation
         WHERE id = :id"
    );
    $requeteElement->execute(['id' => $idElement]);
    $elementSupprime = $requeteElement->fetch(PDO::FETCH_ASSOC);

    $requeteSupprimerElement = $pdo->prepare(
        "DELETE FROM elements_personnalisation WHERE id = :id"
    );
    $requeteSupprimerElement->execute(['id' => $idElement]);

    if ($elementSupprime) {
        enregistrerLogMongoDB(
            'SUPPRESSION_ELEMENT',
            'Suppression de l’élément "' . $elementSupprime['nom']
            . '" (' . $elementSupprime['type'] . ', ancien ID ' . $idElement . ').',
            (int) $_SESSION['user_id']
        );
    }

    header('Location: validation-personnages.php?tab=elements');
    exit;
}


/* ==================================================
   GESTION DES PERSONNAGES
================================================== */

if (isset($_POST['supprimer_personnage'])) {
    $idPersonnage = (int) ($_POST['personnage_id'] ?? 0);

    $requetePersonnageSupprime = $pdo->prepare(
        "SELECT personnages.nom, utilisateurs.pseudo
         FROM personnages
         INNER JOIN utilisateurs ON personnages.utilisateur_id = utilisateurs.id
         WHERE personnages.id = :id"
    );
    $requetePersonnageSupprime->execute(['id' => $idPersonnage]);
    $personnageSupprime = $requetePersonnageSupprime->fetch(PDO::FETCH_ASSOC);

    $requeteSuppressionPersonnage = $pdo->prepare(
        "DELETE FROM personnages WHERE id = :id"
    );
    $requeteSuppressionPersonnage->execute(['id' => $idPersonnage]);

    if ($personnageSupprime) {
        enregistrerLogMongoDB(
            'SUPPRESSION_PERSONNAGE',
            'Suppression du personnage "' . $personnageSupprime['nom']
            . '" de @' . $personnageSupprime['pseudo']
            . ' (ancien ID ' . $idPersonnage . ').',
            (int) $_SESSION['user_id']
        );
    }

    header('Location: validation-personnages.php?tab=characters');
    exit;
}


/* ==================================================
   GESTION DES UTILISATEURS
================================================== */

if (isset($_POST['basculer_suspension_utilisateur'])) {
    $idUtilisateur = (int) ($_POST['utilisateur_id'] ?? 0);

    $requeteUtilisateur = $pdo->prepare(
        "SELECT pseudo, suspendu
         FROM utilisateurs
         WHERE id = :id AND role = 'utilisateur'"
    );
    $requeteUtilisateur->execute(['id' => $idUtilisateur]);
    $utilisateurAvantAction = $requeteUtilisateur->fetch(PDO::FETCH_ASSOC);

    // Un employé ne peut agir ici que sur les comptes utilisateur.
    $requeteSuspensionUtilisateur = $pdo->prepare(
        "UPDATE utilisateurs
         SET suspendu = IF(suspendu = 1, 0, 1)
         WHERE id = :id AND role = 'utilisateur'"
    );
    $requeteSuspensionUtilisateur->execute(['id' => $idUtilisateur]);

    if ($utilisateurAvantAction) {
        $actionUtilisateur = (int) $utilisateurAvantAction['suspendu'] === 1
            ? 'REACTIVATION_UTILISATEUR'
            : 'SUSPENSION_UTILISATEUR';

        $verbeUtilisateur = (int) $utilisateurAvantAction['suspendu'] === 1
            ? 'Réactivation'
            : 'Suspension';

        enregistrerLogMongoDB(
            $actionUtilisateur,
            $verbeUtilisateur . ' du compte @' . $utilisateurAvantAction['pseudo']
            . ' (ID ' . $idUtilisateur . ').',
            (int) $_SESSION['user_id']
        );
    }

    header('Location: validation-personnages.php?tab=users');
    exit;
}

if (isset($_POST['supprimer_utilisateur'])) {
    $idUtilisateur = (int) ($_POST['utilisateur_id'] ?? 0);

    $requeteUtilisateurSupprime = $pdo->prepare(
        "SELECT pseudo
         FROM utilisateurs
         WHERE id = :id AND role = 'utilisateur'"
    );
    $requeteUtilisateurSupprime->execute(['id' => $idUtilisateur]);
    $utilisateurSupprime = $requeteUtilisateurSupprime->fetch(PDO::FETCH_ASSOC);

    // Protection : cette action ne supprime ni employé ni administrateur.
    $requeteSuppressionUtilisateur = $pdo->prepare(
        "DELETE FROM utilisateurs
         WHERE id = :id AND role = 'utilisateur'"
    );
    $requeteSuppressionUtilisateur->execute(['id' => $idUtilisateur]);

    if ($utilisateurSupprime) {
        enregistrerLogMongoDB(
            'SUPPRESSION_UTILISATEUR',
            'Suppression du compte utilisateur @' . $utilisateurSupprime['pseudo']
            . ' (ancien ID ' . $idUtilisateur . ').',
            (int) $_SESSION['user_id']
        );
    }

    header('Location: validation-personnages.php?tab=users');
    exit;
}


/* ==================================================
   RÉCUPÉRATION DES PERSONNAGES EN ATTENTE
================================================== */

$requetePersonnages = $pdo->prepare(
    "SELECT
        personnages.*,
        utilisateurs.pseudo
     FROM personnages
     INNER JOIN utilisateurs
        ON personnages.utilisateur_id = utilisateurs.id
     WHERE personnages.statut_nom = 'en_attente'
     ORDER BY personnages.date_creation ASC"
);

$requetePersonnages->execute();

$personnages =
    $requetePersonnages->fetchAll(PDO::FETCH_ASSOC);


/* ==================================================
   RÉCUPÉRATION DES COMMENTAIRES EN ATTENTE
================================================== */

$requeteCommentaires = $pdo->prepare(
    "SELECT
        commentaires.*,
        utilisateurs.pseudo,
        personnages.nom AS personnage_nom
     FROM commentaires
     INNER JOIN utilisateurs
        ON commentaires.utilisateur_id = utilisateurs.id
     INNER JOIN personnages
        ON commentaires.personnage_id = personnages.id
     WHERE commentaires.statut = 'en_attente'
     ORDER BY commentaires.date_creation ASC"
);

$requeteCommentaires->execute();

$commentaires =
    $requeteCommentaires->fetchAll(PDO::FETCH_ASSOC);


/* ==================================================
   DONNÉES DE GESTION EMPLOYÉ
================================================== */

$requeteElements = $pdo->query(
    "SELECT * FROM elements_personnalisation
     ORDER BY type ASC, nom ASC"
);
$elementsPersonnalisation = $requeteElements->fetchAll(PDO::FETCH_ASSOC);

$requeteTousPersonnages = $pdo->query(
    "SELECT personnages.*, utilisateurs.pseudo
     FROM personnages
     INNER JOIN utilisateurs
        ON personnages.utilisateur_id = utilisateurs.id
     ORDER BY personnages.date_creation DESC"
);
$tousPersonnages = $requeteTousPersonnages->fetchAll(PDO::FETCH_ASSOC);

$requeteUtilisateurs = $pdo->query(
    "SELECT id, pseudo, email, suspendu
     FROM utilisateurs
     WHERE role = 'utilisateur'
     ORDER BY pseudo ASC"
);
$utilisateurs = $requeteUtilisateurs->fetchAll(PDO::FETCH_ASSOC);


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
        Espace employé - FantasyRealm
    </title>

    <link
        rel="stylesheet"
        href="assets/CSS/style.css"
    >

</head>


<body class="employee-validation-page">


<?php include 'navbar.php'; ?>


<main class="validation-page">

    <section class="employee-dashboard-head">
        <p class="validation-small-title">PIXELVERSE STUDIOS</p>
        <h1>ESPACE EMPLOYÉ</h1>
        <p>Modération et gestion de FantasyRealm.</p>

        <div class="employee-tabs" role="tablist" aria-label="Sections de l'espace employé">
            <button type="button" class="employee-tab" data-target="moderation">VALIDATIONS</button>
            <button type="button" class="employee-tab" data-target="elements">ÉQUIPEMENTS & POUVOIRS</button>
            <button type="button" class="employee-tab" data-target="characters">PERSONNAGES</button>
            <button type="button" class="employee-tab" data-target="users">UTILISATEURS</button>
        </div>
    </section>

    <div class="employee-panel" data-panel="moderation">


    <!-- ==================================================
         SECTION 1 : PERSONNAGES
    ================================================== -->

    <section class="validation-section">


        <div class="validation-section-header">

            <p class="validation-small-title">
                MODÉRATION
            </p>

            <h1>
                VALIDATION DES PERSONNAGES
            </h1>

            <p>
                Vérifiez les noms proposés par les joueurs
                avant leur validation.
            </p>

        </div>


        <div class="validation-list">


            <?php if (count($personnages) > 0) { ?>


                <?php foreach ($personnages as $personnage) { ?>


                    <article class="validation-card">


                        <h2>
                            <?php
                            echo htmlspecialchars(
                                $personnage['nom']
                            );
                            ?>
                        </h2>


                        <p>

                            Créé par

                            <strong>
                                @<?php
                                echo htmlspecialchars(
                                    $personnage['pseudo']
                                );
                                ?>
                            </strong>

                        </p>


                        <p>

                            Genre :

                            <strong>
                                <?php
                                echo htmlspecialchars(
                                    $personnage['genre']
                                );
                                ?>
                            </strong>

                        </p>


                        <div class="validation-actions">


                            <form
                                method="POST"
                                action=""
                            >

                                <input
                                    type="hidden"
                                    name="personnage_id"
                                    value="<?php echo $personnage['id']; ?>"
                                >

                                <button
                                    type="submit"
                                    name="valider_personnage"
                                    class="validation-accept"
                                >
                                    VALIDER
                                </button>

                            </form>


                            <form
                                method="POST"
                                action=""
                                class="validation-refuse-form"
                            >

                                <input
                                    type="hidden"
                                    name="personnage_id"
                                    value="<?php echo $personnage['id']; ?>"
                                >


                                <label
                                    for="motif_<?php echo $personnage['id']; ?>"
                                >
                                    Motif du refus
                                </label>


                                <input
                                    type="text"
                                    id="motif_<?php echo $personnage['id']; ?>"
                                    name="motif_refus"
                                    placeholder="Ex : nom inapproprié"
                                    required
                                >


                                <button
                                    type="submit"
                                    name="refuser_personnage"
                                    class="validation-refuse"
                                >
                                    REFUSER
                                </button>

                            </form>


                        </div>


                    </article>


                <?php } ?>


            <?php } else { ?>


                <div class="validation-empty">

                    <p>
                        Aucun personnage en attente de validation.
                    </p>

                </div>


            <?php } ?>


        </div>


    </section>



    <!-- ==================================================
         SÉPARATION
    ================================================== -->

    <div class="validation-big-divider"></div>



    <!-- ==================================================
         SECTION 2 : COMMENTAIRES
    ================================================== -->

    <section class="validation-section comments-validation-section">


        <div class="validation-section-header">

            <p class="validation-small-title">
                COMMUNAUTÉ
            </p>

            <h1>
                VALIDATION DES COMMENTAIRES
            </h1>

            <p>
                Vérifiez les avis envoyés par les joueurs
                avant leur publication.
            </p>

        </div>


        <div class="validation-list">


            <?php if (count($commentaires) > 0) { ?>


                <?php foreach ($commentaires as $commentaire) { ?>


                    <article class="validation-card comment-validation-card">


                        <div class="comment-validation-top">


                            <div>

                                <span class="comment-validation-label">
                                    PERSONNAGE
                                </span>

                                <h2>
                                    <?php
                                    echo htmlspecialchars(
                                        $commentaire['personnage_nom']
                                    );
                                    ?>
                                </h2>

                            </div>


                            <div class="comment-validation-stars">

                                <?php

                                for ($i = 1; $i <= 5; $i++) {

                                    if (
                                        $i <=
                                        (int) $commentaire['note']
                                    ) {
                                        echo '★';
                                    } else {
                                        echo '☆';
                                    }
                                }

                                ?>

                            </div>


                        </div>


                        <p class="comment-validation-author">

                            Envoyé par

                            <strong>
                                @<?php
                                echo htmlspecialchars(
                                    $commentaire['pseudo']
                                );
                                ?>
                            </strong>

                        </p>


                        <div class="comment-validation-text">

                            <?php
                            echo nl2br(
                                htmlspecialchars(
                                    $commentaire['commentaire']
                                )
                            );
                            ?>

                        </div>


                        <div class="comment-validation-buttons">


                            <form
                                method="POST"
                                action=""
                            >

                                <input
                                    type="hidden"
                                    name="commentaire_id"
                                    value="<?php echo $commentaire['id']; ?>"
                                >

                                <button
                                    type="submit"
                                    name="valider_commentaire"
                                    class="validation-accept"
                                >
                                    VALIDER
                                </button>

                            </form>


                            <form
                                method="POST"
                                action=""
                            >

                                <input
                                    type="hidden"
                                    name="commentaire_id"
                                    value="<?php echo $commentaire['id']; ?>"
                                >

                                <button
                                    type="submit"
                                    name="refuser_commentaire"
                                    class="validation-refuse"
                                >
                                    REFUSER
                                </button>

                            </form>


                        </div>


                    </article>


                <?php } ?>


            <?php } else { ?>


                <div class="validation-empty">

                    <p>
                        Aucun commentaire en attente de validation.
                    </p>

                </div>


            <?php } ?>


        </div>


    </section>



    </div>

    <div class="employee-panel" data-panel="elements">

    <!-- ==================================================
         SECTION 3 : ÉQUIPEMENTS ET POUVOIRS
    ================================================== -->

    <section class="validation-section employee-management-section">

        <div class="validation-section-header">
            <p class="validation-small-title">PERSONNALISATION</p>
            <h1>ÉQUIPEMENTS & POUVOIRS</h1>
            <p>Ajoutez, désactivez ou supprimez les éléments disponibles pour les personnages.</p>
        </div>

        <div class="employee-tools">
            <input type="search" class="employee-search" data-search="elements" placeholder="Rechercher un équipement ou un pouvoir..." aria-label="Rechercher un équipement ou un pouvoir">
            <div class="employee-filter-buttons">
                <button type="button" class="employee-filter active" data-filter="all">TOUS</button>
                <button type="button" class="employee-filter" data-filter="equipement">ÉQUIPEMENTS</button>
                <button type="button" class="employee-filter" data-filter="pouvoir">POUVOIRS</button>
            </div>
        </div>

        <div class="validation-card employee-add-card">
            <h2>AJOUTER UN ÉLÉMENT</h2>

            <form method="POST" action="" class="employee-management-form">
                <label for="nom_element">Nom</label>
                <input type="text" id="nom_element" name="nom_element" maxlength="100" required>

                <label for="type_element">Type</label>
                <select id="type_element" name="type_element" required>
                    <option value="equipement">Équipement</option>
                    <option value="pouvoir">Pouvoir</option>
                </select>

                <label for="description_element">Description</label>
                <input type="text" id="description_element" name="description_element" maxlength="255">

                <button type="submit" name="ajouter_element" class="validation-accept">
                    AJOUTER
                </button>
            </form>
        </div>

        <div class="validation-list">
            <?php if (count($elementsPersonnalisation) > 0) { ?>
                <?php foreach ($elementsPersonnalisation as $element) { ?>
                    <article class="validation-card employee-search-card" data-kind="<?php echo htmlspecialchars($element['type']); ?>" data-search-text="<?php echo htmlspecialchars(strtolower($element['nom'] . ' ' . ($element['description'] ?? ''))); ?>">
                        <p class="validation-small-title">
                            <?php echo $element['type'] === 'equipement' ? 'ÉQUIPEMENT' : 'POUVOIR'; ?>
                        </p>

                        <h2><?php echo htmlspecialchars($element['nom']); ?></h2>

                        <?php if (!empty($element['description'])) { ?>
                            <p><?php echo htmlspecialchars($element['description']); ?></p>
                        <?php } ?>

                        <p>
                            Statut :
                            <strong><?php echo (int) $element['actif'] === 1 ? 'ACTIF' : 'DÉSACTIVÉ'; ?></strong>
                        </p>

                        <div class="comment-validation-buttons">
                            <form method="POST" action="">
                                <input type="hidden" name="element_id" value="<?php echo $element['id']; ?>">
                                <button type="submit" name="basculer_element" class="validation-accept">
                                    <?php echo (int) $element['actif'] === 1 ? 'DÉSACTIVER' : 'RÉACTIVER'; ?>
                                </button>
                            </form>

                            <form method="POST" action="" class="fantasy-delete-form" data-delete-label="<?php echo htmlspecialchars($element['nom']); ?>">
                                <input type="hidden" name="element_id" value="<?php echo $element['id']; ?>">
                                <button type="submit" name="supprimer_element" class="validation-refuse">
                                    SUPPRIMER
                                </button>
                            </form>
                        </div>
                    </article>
                <?php } ?>
            <?php } else { ?>
                <div class="validation-empty">
                    <p>Aucun équipement ou pouvoir enregistré.</p>
                </div>
            <?php } ?>
        </div>

    </section>


    </div>

    <div class="employee-panel" data-panel="characters">

    <!-- ==================================================
         SECTION 4 : GESTION DES PERSONNAGES
    ================================================== -->

    <section class="validation-section employee-management-section">

        <div class="validation-section-header">
            <p class="validation-small-title">MODÉRATION</p>
            <h1>GESTION DES PERSONNAGES</h1>
            <p>L'employé peut consulter et supprimer un personnage si nécessaire.</p>
        </div>

        <div class="employee-tools">
            <input type="search" class="employee-search" data-search="characters" placeholder="Rechercher un personnage ou un propriétaire..." aria-label="Rechercher un personnage">
        </div>

        <div class="validation-list">
            <?php if (count($tousPersonnages) > 0) { ?>
                <?php foreach ($tousPersonnages as $personnage) { ?>
                    <article class="validation-card employee-search-card"
                             data-search-text="<?php echo htmlspecialchars(strtolower($personnage['nom'] . ' ' . $personnage['pseudo'] . ' ' . $personnage['statut_nom'])); ?>">
                        <h2><?php echo htmlspecialchars($personnage['nom']); ?></h2>

                        <p>
                            Propriétaire :
                            <strong>@<?php echo htmlspecialchars($personnage['pseudo']); ?></strong>
                        </p>

                        <p>
                            Statut :
                            <strong><?php echo htmlspecialchars($personnage['statut_nom']); ?></strong>
                        </p>

                        <p>
                            Partagé :
                            <strong><?php echo (int) $personnage['partage'] === 1 ? 'OUI' : 'NON'; ?></strong>
                        </p>

                        <form method="POST" action="" class="fantasy-delete-form" data-delete-label="<?php echo htmlspecialchars($personnage['nom']); ?>">
                            <input type="hidden" name="personnage_id" value="<?php echo $personnage['id']; ?>">
                            <button type="submit" name="supprimer_personnage" class="validation-refuse">
                                SUPPRIMER LE PERSONNAGE
                            </button>
                        </form>
                    </article>
                <?php } ?>
            <?php } else { ?>
                <div class="validation-empty">
                    <p>Aucun personnage enregistré.</p>
                </div>
            <?php } ?>
        </div>

    </section>


    </div>

    <div class="employee-panel" data-panel="users">

    <!-- ==================================================
         SECTION 5 : GESTION DES UTILISATEURS
    ================================================== -->

    <section class="validation-section employee-management-section">

        <div class="validation-section-header">
            <p class="validation-small-title">COMPTES</p>
            <h1>GESTION DES UTILISATEURS</h1>
            <p>Suspendez, réactivez ou supprimez les comptes utilisateurs.</p>
        </div>

        <div class="employee-tools">
            <input type="search" class="employee-search" data-search="users" placeholder="Rechercher un pseudo ou un email..." aria-label="Rechercher un utilisateur">
        </div>

        <div class="validation-list">
            <?php if (count($utilisateurs) > 0) { ?>
                <?php foreach ($utilisateurs as $utilisateur) { ?>
                    <article class="validation-card employee-search-card"
                             data-search-text="<?php echo htmlspecialchars(strtolower($utilisateur['pseudo'] . ' ' . $utilisateur['email'])); ?>">
                        <h2>@<?php echo htmlspecialchars($utilisateur['pseudo']); ?></h2>

                        <p>
                            Email :
                            <strong><?php echo htmlspecialchars($utilisateur['email']); ?></strong>
                        </p>

                        <p>
                            Statut :
                            <strong>
                                <?php echo (int) $utilisateur['suspendu'] === 1 ? 'SUSPENDU' : 'ACTIF'; ?>
                            </strong>
                        </p>

                        <div class="comment-validation-buttons">
                            <form method="POST" action="">
                                <input type="hidden" name="utilisateur_id" value="<?php echo $utilisateur['id']; ?>">
                                <button type="submit" name="basculer_suspension_utilisateur" class="validation-accept">
                                    <?php echo (int) $utilisateur['suspendu'] === 1 ? 'RÉACTIVER' : 'SUSPENDRE'; ?>
                                </button>
                            </form>

                            <form method="POST" action="" class="fantasy-delete-form" data-delete-label="@<?php echo htmlspecialchars($utilisateur['pseudo']); ?>">
                                <input type="hidden" name="utilisateur_id" value="<?php echo $utilisateur['id']; ?>">
                                <button type="submit" name="supprimer_utilisateur" class="validation-refuse">
                                    SUPPRIMER
                                </button>
                            </form>
                        </div>
                    </article>
                <?php } ?>
            <?php } else { ?>
                <div class="validation-empty">
                    <p>Aucun compte utilisateur enregistré.</p>
                </div>
            <?php } ?>
        </div>

    </section>

    </div>

</main>


<div class="fantasy-confirm-overlay" id="fantasyConfirmOverlay" aria-hidden="true">
    <div class="fantasy-confirm-modal" role="dialog" aria-modal="true" aria-labelledby="fantasyConfirmTitle">
        <p class="validation-small-title">CONFIRMATION</p>
        <h2 id="fantasyConfirmTitle">SUPPRIMER CET ÉLÉMENT ?</h2>
        <p id="fantasyConfirmText">
            Cette action est définitive.
        </p>

        <div class="fantasy-confirm-actions">
            <button type="button" class="fantasy-confirm-cancel" id="fantasyConfirmCancel">
                ANNULER
            </button>

            <button type="button" class="fantasy-confirm-delete" id="fantasyConfirmDelete">
                SUPPRIMER
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabs = document.querySelectorAll('.employee-tab');
    const panels = document.querySelectorAll('.employee-panel');

    const params = new URLSearchParams(window.location.search);
    const allowedTabs = ['moderation', 'elements', 'characters', 'users'];
    let currentTab = params.get('tab');

    if (!allowedTabs.includes(currentTab)) {
        currentTab = 'moderation';
    }

    const activateTab = function (targetName) {
        tabs.forEach(function (item) {
            item.classList.toggle('active', item.dataset.target === targetName);
        });

        panels.forEach(function (panel) {
            panel.classList.toggle('active', panel.dataset.panel === targetName);
        });
    };

    activateTab(currentTab);

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            tabs.forEach(function (item) {
                item.classList.remove('active');
            });

            panels.forEach(function (panel) {
                panel.classList.remove('active');
            });

            tab.classList.add('active');

            const target = document.querySelector(
                '.employee-panel[data-panel="' + tab.dataset.target + '"]'
            );

            if (target) {
                target.classList.add('active');

                const url = new URL(window.location.href);
                url.searchParams.set('tab', tab.dataset.target);
                window.history.replaceState({}, '', url);
            }
        });
    });

    const normalize = function (value) {
        return value
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '');
    };

    document.querySelectorAll('.employee-search').forEach(function (input) {
        input.addEventListener('input', function () {
            const panel = input.closest('.employee-panel');
            const query = normalize(input.value.trim());

            panel.querySelectorAll('.employee-search-card').forEach(function (card) {
                const text = normalize(card.dataset.searchText || card.textContent);
                card.style.display = text.includes(query) ? '' : 'none';
            });
        });
    });

    const elementPanel = document.querySelector('.employee-panel[data-panel="elements"]');

    // Fenêtre de confirmation FantasyRealm pour toutes les suppressions.
    const confirmOverlay = document.getElementById('fantasyConfirmOverlay');
    const confirmCancel = document.getElementById('fantasyConfirmCancel');
    const confirmDelete = document.getElementById('fantasyConfirmDelete');
    const confirmText = document.getElementById('fantasyConfirmText');
    let pendingDeleteForm = null;
    let pendingDeleteButton = null;

    document.querySelectorAll('.fantasy-delete-form').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            event.preventDefault();
            pendingDeleteForm = form;
            pendingDeleteButton = event.submitter;

            const label = form.dataset.deleteLabel || 'cet élément';
            confirmText.textContent = 'Voulez-vous vraiment supprimer « ' + label + ' » ? Cette action est définitive.';

            confirmOverlay.classList.add('open');
            confirmOverlay.setAttribute('aria-hidden', 'false');
            confirmCancel.focus();
        });
    });

    const closeConfirmModal = function () {
        confirmOverlay.classList.remove('open');
        confirmOverlay.setAttribute('aria-hidden', 'true');
        pendingDeleteForm = null;
        pendingDeleteButton = null;
    };

    confirmCancel.addEventListener('click', closeConfirmModal);

    confirmDelete.addEventListener('click', function () {
        if (pendingDeleteForm && pendingDeleteButton) {
            // form.submit() seul n'envoie pas le name du bouton cliqué.
            // On le recrée en champ caché pour que PHP reçoive bien
            // supprimer_element / supprimer_personnage / supprimer_utilisateur.
            const actionField = document.createElement('input');
            actionField.type = 'hidden';
            actionField.name = pendingDeleteButton.name;
            actionField.value = pendingDeleteButton.value || '1';
            pendingDeleteForm.appendChild(actionField);

            pendingDeleteForm.submit();
        }
    });

    confirmOverlay.addEventListener('click', function (event) {
        if (event.target === confirmOverlay) {
            closeConfirmModal();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && confirmOverlay.classList.contains('open')) {
            closeConfirmModal();
        }
    });

    if (elementPanel) {
        const filterButtons = elementPanel.querySelectorAll('.employee-filter');

        filterButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                filterButtons.forEach(function (item) {
                    item.classList.remove('active');
                });

                button.classList.add('active');

                const filter = button.dataset.filter;

                elementPanel.querySelectorAll('.employee-search-card[data-kind]').forEach(function (card) {
                    const matchesType = filter === 'all' || card.dataset.kind === filter;
                    card.dataset.filterVisible = matchesType ? '1' : '0';
                    card.style.display = matchesType ? '' : 'none';
                });

                const search = elementPanel.querySelector('.employee-search');
                if (search && search.value !== '') {
                    search.dispatchEvent(new Event('input'));
                }
            });
        });

        const search = elementPanel.querySelector('.employee-search');
        if (search) {
            search.addEventListener('input', function () {
                const query = normalize(search.value.trim());
                const activeFilter = elementPanel.querySelector('.employee-filter.active');
                const filter = activeFilter ? activeFilter.dataset.filter : 'all';

                elementPanel.querySelectorAll('.employee-search-card[data-kind]').forEach(function (card) {
                    const text = normalize(card.dataset.searchText || card.textContent);
                    const matchesText = text.includes(query);
                    const matchesType = filter === 'all' || card.dataset.kind === filter;

                    card.style.display = matchesText && matchesType ? '' : 'none';
                });
            });
        }
    }
});
</script>

</body>

</html>