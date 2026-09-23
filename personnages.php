<?php

session_start();
require_once 'config.php';


/* ==================================================
   RÉCUPÉRER ET NETTOYER LES FILTRES
================================================== */

$genre = trim($_GET['genre'] ?? '');
$dateDebut = trim($_GET['date_debut'] ?? '');
$dateFin = trim($_GET['date_fin'] ?? '');
$createur = trim($_GET['createur'] ?? '');


/* ==================================================
   VALEURS AUTORISÉES POUR LE GENRE
================================================== */

$genresAutorises = [
    'Femme',
    'Homme',
    'Autre'
];

if (
    $genre !== '' &&
    !in_array($genre, $genresAutorises, true)
) {
    $genre = '';
}


/* ==================================================
   REQUÊTE DE BASE

   Seuls les personnages :
   - validés
   - partagés

   peuvent apparaître publiquement.
================================================== */

$sql = "
    SELECT
        personnages.*,
        utilisateurs.pseudo

    FROM personnages

    INNER JOIN utilisateurs
        ON personnages.utilisateur_id = utilisateurs.id

    WHERE personnages.partage = 1
    AND personnages.statut_nom = 'valide'
";

$parametres = [];


/* ==================================================
   FILTRE PAR GENRE
================================================== */

if ($genre !== '') {

    $sql .= "
        AND personnages.genre = :genre
    ";

    $parametres['genre'] = $genre;
}


/* ==================================================
   FILTRE PAR DATE DE DÉBUT
================================================== */

if ($dateDebut !== '') {

    $sql .= "
        AND DATE(personnages.date_creation) >= :date_debut
    ";

    $parametres['date_debut'] = $dateDebut;
}


/* ==================================================
   FILTRE PAR DATE DE FIN
================================================== */

if ($dateFin !== '') {

    $sql .= "
        AND DATE(personnages.date_creation) <= :date_fin
    ";

    $parametres['date_fin'] = $dateFin;
}


/* ==================================================
   FILTRE PAR PSEUDO DU CRÉATEUR
================================================== */

if ($createur !== '') {

    $sql .= "
        AND utilisateurs.pseudo LIKE :createur
    ";

    $parametres['createur'] =
        '%' . $createur . '%';
}


/* ==================================================
   PLUS RÉCENTS EN PREMIER
================================================== */

$sql .= "
    ORDER BY personnages.date_creation DESC
";


/* ==================================================
   EXÉCUTER LA REQUÊTE
================================================== */

$requete = $pdo->prepare($sql);

$requete->execute($parametres);

$personnages =
    $requete->fetchAll(PDO::FETCH_ASSOC);


/* ==================================================
   IMAGE DU PERSONNAGE

   Les personnages de démonstration possèdent
   leur image.

   Les personnages créés par les utilisateurs
   utilisent une image fantasy par défaut.
================================================== */

function obtenirImagePersonnage($nom)
{
    $nomImage = mb_strtolower(
        trim($nom),
        'UTF-8'
    );


    if ($nomImage === 'mage noire') {

        return 'assets/images/mage_noir.png';
    }


    if (
        $nomImage === 'guerrière' ||
        $nomImage === 'guerriere'
    ) {

        return 'assets/images/guerrier.png';
    }


    if (
        $nomImage === 'archère' ||
        $nomImage === 'archere'
    ) {

        return 'assets/images/archer.png';
    }


    if (
        $nomImage === 'sorcière' ||
        $nomImage === 'sorciere'
    ) {

        return 'assets/images/sorciere.png';
    }


    /*
       Image utilisée par défaut pour
       un personnage créé par un joueur.
    */

    return 'assets/images/mage_noir.png';
}


/* ==================================================
   METTRE LA PREMIÈRE LETTRE EN MAJUSCULE
================================================== */

function formaterNomPersonnage($nom)
{
    $nom = trim($nom);

    if ($nom === '') {
        return '';
    }

    return
        mb_strtoupper(
            mb_substr($nom, 0, 1, 'UTF-8'),
            'UTF-8'
        )
        .
        mb_substr(
            $nom,
            1,
            null,
            'UTF-8'
        );
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
        Personnages - FantasyRealm Online
    </title>

    <link
        rel="stylesheet"
        href="assets/CSS/style.css"
    >

</head>


<body class="characters-list-page">


<?php include 'navbar.php'; ?>


<main class="characters-page">


    <!-- ==========================================
         TITRE
    =========================================== -->


    <section class="characters-header">


        <h1>
            PERSONNAGES DE LA COMMUNAUTÉ
        </h1>


        <p>
            Découvrez les créations partagées
            par les joueurs de FantasyRealm Online.
        </p>


    </section>



    <!-- ==========================================
         FILTRES
    =========================================== -->


    <form
        class="filters"
        method="GET"
        action=""
    >


        <select
            name="genre"
            aria-label="Filtrer par genre"
        >


            <option value="">
                Tous les genres
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



        <label>

            Du

            <input
                type="date"
                name="date_debut"
                value="<?php
                echo htmlspecialchars($dateDebut);
                ?>"
            >

        </label>



        <label>

            Au

            <input
                type="date"
                name="date_fin"
                value="<?php
                echo htmlspecialchars($dateFin);
                ?>"
            >

        </label>



        <input
            type="text"
            name="createur"
            placeholder="Pseudo du créateur"
            value="<?php
            echo htmlspecialchars($createur);
            ?>"
            aria-label="Pseudo du créateur"
        >



        <button type="submit">

            FILTRER

        </button>


    </form>



    <!-- ==========================================
         PERSONNAGES
    =========================================== -->


    <section class="characters-grid">


        <?php if (!empty($personnages)) { ?>


            <?php foreach ($personnages as $personnage) { ?>


                <?php

                $imagePersonnage =
                    obtenirImagePersonnage(
                        $personnage['nom']
                    );

                $nomPersonnage =
                    formaterNomPersonnage(
                        $personnage['nom']
                    );

                ?>


                <article class="character-card">


                    <!-- IMAGE -->


                    <a
                        class="character-image"
                        href="personnage.php?id=<?php
                        echo (int)$personnage['id'];
                        ?>"
                    >


                        <img
                            src="<?php
                            echo htmlspecialchars(
                                $imagePersonnage
                            );
                            ?>"
                            alt="<?php
                            echo htmlspecialchars(
                                $nomPersonnage
                            );
                            ?>"
                        >


                    </a>



                    <!-- INFORMATIONS -->


                    <div class="character-info">


                        <h2>

                            <?php
                            echo htmlspecialchars(
                                $nomPersonnage
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



                        <a
                            href="personnage.php?id=<?php
                            echo (int)$personnage['id'];
                            ?>"
                        >

                            VOIR LE PERSONNAGE

                        </a>


                    </div>


                </article>


            <?php } ?>


        <?php } else { ?>


            <p class="no-characters-message">

                Aucun personnage ne correspond
                à votre recherche.

            </p>


        <?php } ?>


    </section>


</main>


</body>

</html>