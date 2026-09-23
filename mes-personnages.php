<?php

session_start();
require_once 'config.php';


/* ==================================================
   UTILISATEUR CONNECTÉ OBLIGATOIRE
================================================== */

if (!isset($_SESSION['user_id'])) {

    header('Location: connexion.php');
    exit;
}


/* ==================================================
   DUPLIQUER UN PERSONNAGE
================================================== */

$messageDuplication = '';
$erreurDuplication = '';

if (isset($_POST['dupliquer'])) {

    $idPersonnage = (int)($_POST['personnage_id'] ?? 0);
    $nouveauNom = trim($_POST['nouveau_nom'] ?? '');

    if ($nouveauNom === '') {
        $erreurDuplication = 'Le nouveau nom est obligatoire.';
    } elseif (mb_strlen($nouveauNom, 'UTF-8') > 100) {
        $erreurDuplication = 'Le nom ne peut pas dépasser 100 caractères.';
    } else {

        $verificationNom = $pdo->prepare(
            "SELECT id FROM personnages WHERE LOWER(nom) = LOWER(:nom) LIMIT 1"
        );
        $verificationNom->execute(['nom' => $nouveauNom]);

        if ($verificationNom->fetch()) {
            $erreurDuplication = 'Ce nom est déjà utilisé. Choisissez un autre nom.';
        } else {

            $requeteOriginal = $pdo->prepare(
                "SELECT * FROM personnages
                 WHERE id = :id
                 AND utilisateur_id = :utilisateur_id
                 LIMIT 1"
            );
            $requeteOriginal->execute([
                'id' => $idPersonnage,
                'utilisateur_id' => $_SESSION['user_id']
            ]);

            $original = $requeteOriginal->fetch(PDO::FETCH_ASSOC);

            if (!$original) {
                $erreurDuplication = 'Personnage introuvable.';
            } else {

                try {
                    $pdo->beginTransaction();

                    $requeteDuplication = $pdo->prepare(
                        "INSERT INTO personnages
                        (utilisateur_id, nom, genre, modele_visuel, image, visage,
                         couleur_cheveux, couleur_yeux, coiffure, forme_yeux,
                         statut_nom, partage, motif_refus)
                         VALUES
                        (:utilisateur_id, :nom, :genre, :modele_visuel, :image, :visage,
                         :couleur_cheveux, :couleur_yeux, :coiffure, :forme_yeux,
                         'en_attente', 0, NULL)"
                    );

                    $requeteDuplication->execute([
                        'utilisateur_id' => $_SESSION['user_id'],
                        'nom' => $nouveauNom,
                        'genre' => $original['genre'],
                        'modele_visuel' => $original['modele_visuel'],
                        'image' => $original['image'],
                        'visage' => $original['visage'],
                        'couleur_cheveux' => $original['couleur_cheveux'],
                        'couleur_yeux' => $original['couleur_yeux'],
                        'coiffure' => $original['coiffure'],
                        'forme_yeux' => $original['forme_yeux']
                    ]);

                    $nouvelId = (int)$pdo->lastInsertId();

                    $copieElements = $pdo->prepare(
                        "INSERT INTO personnage_elements (personnage_id, element_id)
                         SELECT :nouvel_id, element_id
                         FROM personnage_elements
                         WHERE personnage_id = :ancien_id"
                    );
                    $copieElements->execute([
                        'nouvel_id' => $nouvelId,
                        'ancien_id' => $idPersonnage
                    ]);

                    $pdo->commit();

                    header('Location: mes-personnages.php?duplication=ok');
                    exit;

                } catch (Throwable $e) {
                    if ($pdo->inTransaction()) {
                        $pdo->rollBack();
                    }
                    $erreurDuplication = 'Une erreur est survenue pendant la duplication.';
                }
            }
        }
    }
}

if (isset($_GET['duplication']) && $_GET['duplication'] === 'ok') {
    $messageDuplication = 'Personnage dupliqué ! Son nouveau nom est maintenant en attente de validation.';
}


/* ==================================================
   PARTAGER UN PERSONNAGE
================================================== */

if (isset($_POST['partager'])) {

    $idPersonnage = (int)($_POST['personnage_id'] ?? 0);

    $requetePartage = $pdo->prepare(
        "UPDATE personnages
         SET partage = 1
         WHERE id = :id
         AND utilisateur_id = :utilisateur_id
         AND statut_nom = 'valide'"
    );

    $requetePartage->execute([
        'id' => $idPersonnage,
        'utilisateur_id' => $_SESSION['user_id']
    ]);

    header('Location: mes-personnages.php');
    exit;
}


/* ==================================================
   RETIRER DU PARTAGE
================================================== */

if (isset($_POST['depublier'])) {

    $idPersonnage = (int)($_POST['personnage_id'] ?? 0);

    $requeteDepublication = $pdo->prepare(
        "UPDATE personnages
         SET partage = 0
         WHERE id = :id
         AND utilisateur_id = :utilisateur_id"
    );

    $requeteDepublication->execute([
        'id' => $idPersonnage,
        'utilisateur_id' => $_SESSION['user_id']
    ]);

    header('Location: mes-personnages.php');
    exit;
}


/* ==================================================
   SUPPRIMER UN PERSONNAGE
================================================== */

if (isset($_POST['supprimer'])) {

    $idPersonnage = (int)($_POST['personnage_id'] ?? 0);

    $requeteSuppression = $pdo->prepare(
        "DELETE FROM personnages
         WHERE id = :id
         AND utilisateur_id = :utilisateur_id"
    );

    $requeteSuppression->execute([
        'id' => $idPersonnage,
        'utilisateur_id' => $_SESSION['user_id']
    ]);

    header('Location: mes-personnages.php');
    exit;
}


/* ==================================================
   RÉCUPÉRATION DES PERSONNAGES DU COMPTE CONNECTÉ
================================================== */

$requete = $pdo->prepare(
    "SELECT *
     FROM personnages
     WHERE utilisateur_id = :utilisateur_id
     ORDER BY date_creation DESC"
);

$requete->execute([
    'utilisateur_id' => $_SESSION['user_id']
]);

$personnages = $requete->fetchAll(PDO::FETCH_ASSOC);


/* ==================================================
   IMAGE DU PERSONNAGE
================================================== */

function imagePersonnage($nom)
{
    $nomMinuscule = mb_strtolower($nom, 'UTF-8');

    if (str_contains($nomMinuscule, 'mage noire')) {
        return 'assets/images/mage_noir.png';
    }

    if (str_contains($nomMinuscule, 'guerrière') ||
        str_contains($nomMinuscule, 'guerrier')) {
        return 'assets/images/guerrier.png';
    }

    if (str_contains($nomMinuscule, 'archère') ||
        str_contains($nomMinuscule, 'archer')) {
        return 'assets/images/archer.png';
    }

    if (str_contains($nomMinuscule, 'sorcière')) {
        return 'assets/images/sorciere.png';
    }

    /*
       Pour les personnages créés par les utilisateurs,
       on utilise une image fantasy par défaut.
    */
    return 'assets/images/mage_noir.png';
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
        Mes personnages - FantasyRealm
    </title>

    <link
        rel="stylesheet"
        href="assets/CSS/style.css"
    >


    <style>
        .duplication-message {
            max-width: 1100px;
            margin: 0 auto 20px;
            padding: 14px 18px;
            border: 1px solid #C6A15B;
            border-radius: 8px;
            background: rgba(41, 31, 47, 0.95);
            color: #F5F0F6;
        }

        .duplication-error {
            border-color: #d46a6a;
        }

        .duplicate-character-form {
            margin-top: 14px;
        }

        .duplicate-character-form label {
            display: block;
            margin-bottom: 7px;
            font-size: 0.85rem;
        }

        .duplicate-character-row {
            display: flex;
            gap: 8px;
            align-items: stretch;
        }

        .duplicate-character-row input {
            min-width: 0;
            flex: 1;
            padding: 10px 12px;
            border: 1px solid #C6A15B;
            border-radius: 6px;
            background: #F5F0F6;
            color: #291F2F;
        }

        @media (max-width: 600px) {
            .duplicate-character-row {
                flex-direction: column;
            }
        }
    </style>

</head>


<body>


<?php include 'navbar.php'; ?>


<main class="my-characters-page">

    <?php if ($messageDuplication !== '') { ?>
        <div class="duplication-message duplication-success">
            <?php echo htmlspecialchars($messageDuplication); ?>
        </div>
    <?php } ?>

    <?php if ($erreurDuplication !== '') { ?>
        <div class="duplication-message duplication-error">
            <?php echo htmlspecialchars($erreurDuplication); ?>
        </div>
    <?php } ?>


    <section class="my-characters-header">

        <div>

            <p class="my-characters-eyebrow">
                VOTRE COLLECTION
            </p>

            <h1>
                MES PERSONNAGES
            </h1>

            <p class="my-characters-intro">
                Retrouvez vos héros, personnalisez-les
                et choisissez ceux que vous souhaitez
                partager avec la communauté.
            </p>

        </div>


        <a
            href="creer-personnage.php"
            class="create-character-button"
        >
            + CRÉER UN PERSONNAGE
        </a>

    </section>



    <?php if (empty($personnages)) { ?>

        <section class="empty-characters">

            <h2>
                Aucun personnage
            </h2>

            <p>
                Vous n'avez pas encore créé de personnage.
                Commencez votre aventure dans FantasyRealm.
            </p>

            <a href="creer-personnage.php">
                CRÉER MON PREMIER PERSONNAGE
            </a>

        </section>

    <?php } else { ?>


        <section class="my-characters-grid">


            <?php foreach ($personnages as $personnage) { ?>


                <?php

                $image = imagePersonnage($personnage['nom']);

                $statut = $personnage['statut_nom'];

                ?>


                <article class="my-character-card">


                    <div class="my-character-image-wrapper">


                        <img
                            src="<?php echo htmlspecialchars($image); ?>"
                            alt="<?php echo htmlspecialchars($personnage['nom']); ?>"
                            class="my-character-image"
                        >


                        <span
                            class="character-status
                            <?php echo htmlspecialchars($statut); ?>"
                        >

                            <?php if ($statut === 'valide') { ?>

                                NOM VALIDÉ

                            <?php } elseif ($statut === 'refuse') { ?>

                                NOM REFUSÉ

                            <?php } else { ?>

                                EN ATTENTE

                            <?php } ?>

                        </span>


                        <span class="sharing-status">

                            <?php if ((int)$personnage['partage'] === 1) { ?>

                                PUBLIC

                            <?php } else { ?>

                                PRIVÉ

                            <?php } ?>

                        </span>


                    </div>



                    <div class="my-character-content">


                        <p class="character-type">
                            PERSONNAGE
                        </p>


                        <h2>
                            <?php
                            echo htmlspecialchars(
                                ucfirst($personnage['nom'])
                            );
                            ?>
                        </h2>


                        <div class="my-character-details">


                            <p>

                                <span>Genre</span>

                                <?php
                                echo htmlspecialchars(
                                    $personnage['genre']
                                );
                                ?>

                            </p>


                            <p>

                                <span>Créé le</span>

                                <?php
                                echo date(
                                    'd/m/Y',
                                    strtotime(
                                        $personnage['date_creation']
                                    )
                                );
                                ?>

                            </p>


                        </div>



                        <?php if ($statut === 'en_attente') { ?>

                            <div class="waiting-validation">

                                Le nom de votre personnage
                                attend la validation d'un employé.

                            </div>

                        <?php } ?>



                        <?php if ($statut === 'valide') { ?>


                            <div class="my-character-actions">


                                <a
                                    href="modifier-personnage.php?id=<?php echo (int)$personnage['id']; ?>"
                                    class="character-main-action"
                                >
                                    PERSONNALISER
                                </a>



                                <?php if ((int)$personnage['partage'] === 0) { ?>

                                    <form
                                        method="POST"
                                        action=""
                                    >

                                        <input
                                            type="hidden"
                                            name="personnage_id"
                                            value="<?php echo (int)$personnage['id']; ?>"
                                        >

                                        <button
                                            type="submit"
                                            name="partager"
                                            class="character-secondary-action"
                                        >
                                            PARTAGER
                                        </button>

                                    </form>

                                <?php } else { ?>

                                    <form
                                        method="POST"
                                        action=""
                                    >

                                        <input
                                            type="hidden"
                                            name="personnage_id"
                                            value="<?php echo (int)$personnage['id']; ?>"
                                        >

                                        <button
                                            type="submit"
                                            name="depublier"
                                            class="character-secondary-action"
                                        >
                                            RENDRE PRIVÉ
                                        </button>

                                    </form>

                                <?php } ?>


                            </div>


                        <?php } ?>



                   <form
    method="POST"
    action=""
    class="duplicate-character-form"
>

    <input
        type="hidden"
        name="personnage_id"
        value="<?php echo (int)$personnage['id']; ?>"
    >

    <h3 class="duplicate-character-title">
        DUPLIQUER VOTRE PERSONNAGE
    </h3>

    <input
        type="text"
        id="nouveau_nom_<?php echo (int)$personnage['id']; ?>"
        name="nouveau_nom"
        maxlength="100"
        required
        placeholder="Nouveau nom unique"
        aria-label="Nouveau nom du personnage dupliqué"
    >

    <button
        type="submit"
        name="dupliquer"
        class="duplicate-character-button"
    >
        DUPLIQUER
    </button>

</form>


                        <form
                            method="POST"
                            action=""
                            class="delete-character-form"
                            onsubmit="return confirm('Voulez-vous vraiment supprimer ce personnage ? Cette action est définitive.');"
                        >

                            <input
                                type="hidden"
                                name="personnage_id"
                                value="<?php echo (int)$personnage['id']; ?>"
                            >

                            <button
                                type="submit"
                                name="supprimer"
                                class="delete-character-button"
                            >
                                SUPPRIMER LE PERSONNAGE
                            </button>

                        </form>


                    </div>


                </article>


            <?php } ?>


        </section>


    <?php } ?>


</main>


</body>

</html>