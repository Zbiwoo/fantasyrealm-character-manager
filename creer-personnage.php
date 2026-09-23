<?php

session_start();
require_once 'config.php';
require_once 'mots-interdits.php';


/* ==================================================
   UTILISATEUR CONNECTÉ OBLIGATOIRE
================================================== */

if (!isset($_SESSION['user_id'])) {

    header('Location: connexion.php');
    exit;
}


$erreur = '';

$nom = '';
$genre = '';


/* ==================================================
   CRÉATION DU PERSONNAGE
================================================== */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nom = trim($_POST['nom'] ?? '');
    $genre = trim($_POST['genre'] ?? '');


    /* ==================================================
       VÉRIFICATION DES CHAMPS
    ================================================== */

    if ($nom === '' || $genre === '') {

        $erreur =
            'Veuillez remplir tous les champs.';


    } elseif (mb_strlen($nom) < 2) {

        $erreur =
            'Le nom du personnage doit contenir au moins 2 caractères.';


    } elseif (mb_strlen($nom) > 50) {

        $erreur =
            'Le nom du personnage ne peut pas dépasser 50 caractères.';


    } elseif (contientTermeInterdit($nom)) {

        $erreur =
            'Ce nom ne peut pas être utilisé car il contient un terme interdit.';

    } elseif (
        !in_array(
            $genre,
            ['Homme', 'Femme', 'Autre'],
            true
        )
    ) {

        $erreur =
            'Le genre sélectionné est invalide.';


    } else {


        /* ==================================================
           VÉRIFICATION DU NOM
        ================================================== */

        $requeteNom = $pdo->prepare(
            "SELECT id
             FROM personnages
             WHERE LOWER(nom) = LOWER(:nom)"
        );

        $requeteNom->execute([
            'nom' => $nom
        ]);

        $nomExiste =
            $requeteNom->fetch(PDO::FETCH_ASSOC);


        if ($nomExiste) {

            $erreur =
                'Ce nom de personnage est déjà utilisé.';


        } else {


            /* ==================================================
               ENREGISTREMENT DU PERSONNAGE
            ================================================== */

            $requeteCreation = $pdo->prepare(
                "INSERT INTO personnages
                (
                    utilisateur_id,
                    nom,
                    genre,
                    statut_nom,
                    partage
                )
                VALUES
                (
                    :utilisateur_id,
                    :nom,
                    :genre,
                    'en_attente',
                    0
                )"
            );


            $requeteCreation->execute([
                'utilisateur_id' =>
                    $_SESSION['user_id'],

                'nom' =>
                    $nom,

                'genre' =>
                    $genre
            ]);


            header(
                'Location: creer-personnage.php?succes=1'
            );

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
        Créer un personnage - FantasyRealm
    </title>

    <link
        rel="stylesheet"
        href="assets/CSS/style.css"
    >

</head>


<body>


<?php include 'navbar.php'; ?>


<main class="create-character-page">


    <section class="create-character-box">


        <h1>
            CRÉER UN PERSONNAGE
        </h1>



        <?php if (isset($_GET['succes'])) { ?>

            <p class="success-message">

                Votre personnage a bien été créé
                et attend la validation de son nom.

            </p>

        <?php } ?>



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


            <label for="nom">
                Nom du personnage
            </label>


            <input
                type="text"
                id="nom"
                name="nom"
                maxlength="50"
                value="<?php echo htmlspecialchars($nom); ?>"
                required
            >



            <label for="genre">
                Genre
            </label>


            <select
                id="genre"
                name="genre"
                required
            >


                <option value="">
                    Choisir un genre
                </option>


                <option
                    value="Homme"
                    <?php
                    if ($genre === 'Homme') {
                        echo 'selected';
                    }
                    ?>
                >
                    Homme
                </option>


                <option
                    value="Femme"
                    <?php
                    if ($genre === 'Femme') {
                        echo 'selected';
                    }
                    ?>
                >
                    Femme
                </option>


                <option
                    value="Autre"
                    <?php
                    if ($genre === 'Autre') {
                        echo 'selected';
                    }
                    ?>
                >
                    Autre
                </option>


            </select>



            <button type="submit">
                CRÉER
            </button>


        </form>


    </section>


</main>


</body>

</html>