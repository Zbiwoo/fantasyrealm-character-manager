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
   VÉRIFIER QU'UN PERSONNAGE EST DEMANDÉ
================================================== */

if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    header('Location: mes-personnages.php');
    exit;
}

$idPersonnage = (int) $_GET['id'];


/* ==================================================
   RÉCUPÉRER LE PERSONNAGE DE L'UTILISATEUR
================================================== */

$requete = $pdo->prepare(
    "SELECT *
     FROM personnages
     WHERE id = :id
     AND utilisateur_id = :utilisateur_id
     AND statut_nom = 'valide'"
);

$requete->execute([
    'id' => $idPersonnage,
    'utilisateur_id' => $_SESSION['user_id']
]);

$personnage = $requete->fetch(PDO::FETCH_ASSOC);


/* ==================================================
   PERSONNAGE INTROUVABLE OU NON AUTORISÉ
================================================== */

if (!$personnage) {
    header('Location: mes-personnages.php');
    exit;
}


/* ==================================================
   VALEURS AUTORISÉES
================================================== */

$modelesAutorises = [
    'guerriere',
    'mage',
    'archere'
];

$visagesAutorises = [
    'Rond',
    'Normal',
    'Allongé'
];

$coiffuresAutorisees = [
    'Court',
    'Long',
    'Tresse'
];

$yeuxAutorises = [
    'Fins',
    'Ronds',
    'Etroits'
];


/* ==================================================
   RÉCUPÉRER LES ÉQUIPEMENTS ET POUVOIRS ACTIFS
================================================== */

$requeteElements = $pdo->query(
    "SELECT id, nom, type, description
     FROM elements_personnalisation
     WHERE actif = 1
     ORDER BY type ASC, nom ASC"
);

$elements = $requeteElements->fetchAll(PDO::FETCH_ASSOC);

$equipements = [];
$pouvoirs = [];

foreach ($elements as $element) {

    if ($element['type'] === 'equipement') {
        $equipements[] = $element;
    }

    if ($element['type'] === 'pouvoir') {
        $pouvoirs[] = $element;
    }
}


/* ==================================================
   RÉCUPÉRER LES ÉLÉMENTS DÉJÀ CHOISIS
================================================== */

$requeteChoix = $pdo->prepare(
    "SELECT element_id
     FROM personnage_elements
     WHERE personnage_id = :personnage_id"
);

$requeteChoix->execute([
    'personnage_id' => $idPersonnage
]);

$elementsChoisis = $requeteChoix->fetchAll(PDO::FETCH_COLUMN);

$elementsChoisis = array_map(
    'intval',
    $elementsChoisis
);


/* ==================================================
   VALEURS ACTUELLES DE PERSONNALISATION
================================================== */

$modeleActuel =
    $personnage['modele_visuel'] ?: 'guerriere';

$visageActuel =
    $personnage['visage'] ?: 'Normal';

$coiffureActuelle =
    $personnage['coiffure'] ?: 'Court';

$couleurCheveuxActuelle =
    $personnage['couleur_cheveux'] ?: '#291F2F';

$formeYeuxActuelle =
    $personnage['forme_yeux'] ?: 'Ronds';

$couleurYeuxActuelle =
    $personnage['couleur_yeux'] ?: '#C6A15B';


/* ==================================================
   ENREGISTRER LA PERSONNALISATION
================================================== */

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $modeleVisuel = trim(
        $_POST['modele_visuel'] ?? ''
    );

    $visage = trim(
        $_POST['visage'] ?? ''
    );

    $coiffure = trim(
        $_POST['coiffure'] ?? ''
    );

    $couleurCheveux = trim(
        $_POST['couleur_cheveux'] ?? ''
    );

    $formeYeux = trim(
        $_POST['forme_yeux'] ?? ''
    );

    $couleurYeux = trim(
        $_POST['couleur_yeux'] ?? ''
    );


    /* ==================================================
       RÉCUPÉRER LES ÉQUIPEMENTS / POUVOIRS CHOISIS
    ================================================== */

    $elementsSelectionnes =
        $_POST['elements'] ?? [];

    if (!is_array($elementsSelectionnes)) {
        $elementsSelectionnes = [];
    }

    $elementsSelectionnes = array_map(
        'intval',
        $elementsSelectionnes
    );

    $elementsSelectionnes = array_values(
        array_unique($elementsSelectionnes)
    );


    /* ==================================================
       VÉRIFIER LE MODÈLE
    ================================================== */

    if (
        !in_array(
            $modeleVisuel,
            $modelesAutorises,
            true
        )
    ) {

        $erreur =
            'Le modèle sélectionné est invalide.';


    /* ==================================================
       VÉRIFIER LE VISAGE
    ================================================== */

    } elseif (
        !in_array(
            $visage,
            $visagesAutorises,
            true
        )
    ) {

        $erreur =
            'Le visage sélectionné est invalide.';


    /* ==================================================
       VÉRIFIER LA COIFFURE
    ================================================== */

    } elseif (
        !in_array(
            $coiffure,
            $coiffuresAutorisees,
            true
        )
    ) {

        $erreur =
            'La coiffure sélectionnée est invalide.';


    /* ==================================================
       VÉRIFIER LA FORME DES YEUX
    ================================================== */

    } elseif (
        !in_array(
            $formeYeux,
            $yeuxAutorises,
            true
        )
    ) {

        $erreur =
            'La forme des yeux sélectionnée est invalide.';


    /* ==================================================
       VÉRIFIER LA COULEUR DES CHEVEUX
    ================================================== */

    } elseif (
        !preg_match(
            '/^#[0-9A-Fa-f]{6}$/',
            $couleurCheveux
        )
    ) {

        $erreur =
            'La couleur des cheveux est invalide.';


    /* ==================================================
       VÉRIFIER LA COULEUR DES YEUX
    ================================================== */

    } elseif (
        !preg_match(
            '/^#[0-9A-Fa-f]{6}$/',
            $couleurYeux
        )
    ) {

        $erreur =
            'La couleur des yeux est invalide.';

    } else {


        /* ==================================================
           VÉRIFIER LES ÉLÉMENTS EN BASE
        ================================================== */

        $idsActifs = [];

        foreach ($elements as $element) {
            $idsActifs[] = (int) $element['id'];
        }

        $elementsValides = [];

        foreach ($elementsSelectionnes as $idElement) {

            if (
                in_array(
                    $idElement,
                    $idsActifs,
                    true
                )
            ) {

                $elementsValides[] =
                    $idElement;
            }
        }


        /* ==================================================
           ENREGISTREMENT EN BASE DE DONNÉES
        ================================================== */

        try {

            $pdo->beginTransaction();


            /* ==============================================
               MODIFIER L'APPARENCE
            ============================================== */

            $requeteModification = $pdo->prepare(
                "UPDATE personnages

                 SET modele_visuel = :modele_visuel,
                     visage = :visage,
                     coiffure = :coiffure,
                     couleur_cheveux = :couleur_cheveux,
                     forme_yeux = :forme_yeux,
                     couleur_yeux = :couleur_yeux

                 WHERE id = :id
                 AND utilisateur_id = :utilisateur_id
                 AND statut_nom = 'valide'"
            );

            $requeteModification->execute([

                'modele_visuel' =>
                    $modeleVisuel,

                'visage' =>
                    $visage,

                'coiffure' =>
                    $coiffure,

                'couleur_cheveux' =>
                    $couleurCheveux,

                'forme_yeux' =>
                    $formeYeux,

                'couleur_yeux' =>
                    $couleurYeux,

                'id' =>
                    $idPersonnage,

                'utilisateur_id' =>
                    $_SESSION['user_id']
            ]);


            /* ==============================================
               SUPPRIMER LES ANCIENS CHOIX
            ============================================== */

            $requeteSuppression = $pdo->prepare(
                "DELETE FROM personnage_elements
                 WHERE personnage_id = :personnage_id"
            );

            $requeteSuppression->execute([
                'personnage_id' =>
                    $idPersonnage
            ]);


            /* ==============================================
               ENREGISTRER LES NOUVEAUX CHOIX
            ============================================== */

            if (!empty($elementsValides)) {

                $requeteAjout = $pdo->prepare(
                    "INSERT INTO personnage_elements
                     (personnage_id, element_id)

                     VALUES
                     (:personnage_id, :element_id)"
                );

                foreach (
                    $elementsValides
                    as $idElement
                ) {

                    $requeteAjout->execute([

                        'personnage_id' =>
                            $idPersonnage,

                        'element_id' =>
                            $idElement
                    ]);
                }
            }


            /* ==============================================
               VALIDER TOUTES LES MODIFICATIONS
            ============================================== */

            $pdo->commit();

            header(
                'Location: mes-personnages.php?modification=succes'
            );

            exit;

        } catch (PDOException $e) {

            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $erreur =
                'Une erreur est survenue pendant l\'enregistrement.';
        }
    }


    /* ==================================================
       CONSERVER LES VALEURS EN CAS D'ERREUR
    ================================================== */

    $modeleActuel =
        $modeleVisuel;

    $visageActuel =
        $visage;

    $coiffureActuelle =
        $coiffure;

    $couleurCheveuxActuelle =
        $couleurCheveux;

    $formeYeuxActuelle =
        $formeYeux;

    $couleurYeuxActuelle =
        $couleurYeux;

    $elementsChoisis =
        $elementsSelectionnes;
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
    Personnaliser <?php echo htmlspecialchars($personnage['nom']); ?>
</title>

<link
    rel="stylesheet"
    href="assets/CSS/style.css"
>

</head>

<body>

<?php include 'navbar.php'; ?>


<main class="stardoll-page">

    <div class="stardoll-title">

        <h1>
            Créer / Modifier un personnage
        </h1>

        <p>
            Façonne l'héroïne qui écrira ton histoire...
        </p>

    </div>


    <?php if ($erreur !== '') { ?>

        <p class="error-message">
            <?php echo htmlspecialchars($erreur); ?>
        </p>

    <?php } ?>


    <form
        method="POST"
        class="stardoll-form"
    >


        <!-- ==========================================
             PORTRAIT
        =========================================== -->

        <section class="stardoll-portrait">

            <div
                class="portrait-frame"
                id="portraitFrame"
            >

                <div
                    class="heroine-live"
                    id="heroineLive"
                >

                    <img
                        id="mainHeroine"

                        src="<?php

                        if ($modeleActuel === 'mage') {

                            echo 'assets/images/creator/modele-mage.png';

                        } elseif ($modeleActuel === 'archere') {

                            echo 'assets/images/creator/modele-archere.png';

                        } else {

                            echo 'assets/images/creator/heroine-guerriere.png';
                        }

                        ?>"

                        alt="Aperçu du personnage"
                    >

                </div>

            </div>


            <div class="portrait-name">

                <?php
                echo htmlspecialchars(
                    $personnage['nom']
                );
                ?>

            </div>


            <p class="portrait-quote">
                « Même dans l'ombre, je brille. »
            </p>

        </section>


        <!-- ==========================================
             PANNEAU DE PERSONNALISATION
        =========================================== -->

        <section class="stardoll-panel">


            <!-- ======================================
                 ONGLETS
            ======================================= -->

            <div class="creator-tabs">

                <button
                    type="button"
                    class="creator-tab active"
                    data-tab="apparence"
                >
                    Apparence
                </button>

                <button
                    type="button"
                    class="creator-tab"
                    data-tab="equipements"
                >
                    Équipements
                </button>

                <button
                    type="button"
                    class="creator-tab"
                    data-tab="pouvoirs"
                >
                    Pouvoirs
                </button>

                <button
                    type="button"
                    class="creator-tab"
                    data-tab="apercu"
                >
                    Aperçu
                </button>

            </div>


            <!-- ======================================
                 APPARENCE
            ======================================= -->

            <div
                class="tab-content active"
                id="tab-apparence"
            >


                <!-- MODÈLE DE BASE -->

                <div class="creator-section">

                    <h2>Modèle de base</h2>

                    <p>
                        Choisis une héroïne de départ.
                    </p>


                    <div class="visual-grid">


                        <label class="visual-option">

                            <input
                                type="radio"
                                name="modele_visuel"
                                value="guerriere"

                                <?php
                                echo $modeleActuel === 'guerriere'
                                    ? 'checked'
                                    : '';
                                ?>
                            >

                            <span class="visual-card">

                                <img
                                    src="assets/images/creator/modele-guerriere.png"
                                    alt="Guerrière"
                                >

                            </span>

                            <span>Guerrière</span>

                        </label>


                        <label class="visual-option">

                            <input
                                type="radio"
                                name="modele_visuel"
                                value="mage"

                                <?php
                                echo $modeleActuel === 'mage'
                                    ? 'checked'
                                    : '';
                                ?>
                            >

                            <span class="visual-card">

                                <img
                                    src="assets/images/creator/modele-mage.png"
                                    alt="Mage"
                                >

                            </span>

                            <span>Mage</span>

                        </label>


                        <label class="visual-option">

                            <input
                                type="radio"
                                name="modele_visuel"
                                value="archere"

                                <?php
                                echo $modeleActuel === 'archere'
                                    ? 'checked'
                                    : '';
                                ?>
                            >

                            <span class="visual-card">

                                <img
                                    src="assets/images/creator/modele-archere.png"
                                    alt="Archère"
                                >

                            </span>

                            <span>Archère</span>

                        </label>

                    </div>

                </div>


                <!-- VISAGE -->

                <div class="creator-section">

                    <h2>Forme du visage</h2>

                    <div class="visual-grid">

                        <?php

                        $faces = [
                            ['Rond', 'visage-rond.png'],
                            ['Normal', 'visage-normal.png'],
                            ['Allongé', 'visage-allonge.png']
                        ];

                        foreach ($faces as $f) {

                        ?>

                            <label class="visual-option">

                                <input
                                    type="radio"
                                    name="visage"
                                    value="<?php echo $f[0]; ?>"

                                    <?php
                                    echo $visageActuel === $f[0]
                                        ? 'checked'
                                        : '';
                                    ?>
                                >

                                <span class="visual-card">

                                    <img
                                        src="assets/images/creator/<?php echo $f[1]; ?>"
                                        alt=""
                                    >

                                </span>

                                <span>
                                    <?php echo $f[0]; ?>
                                </span>

                            </label>

                        <?php } ?>

                    </div>

                </div>


                <!-- COIFFURE -->

                <div class="creator-section">

                    <h2>Coiffure</h2>

                    <div class="visual-grid">

                        <?php

                        $hairs = [
                            ['Court', 'coiffure-court.png'],
                            ['Long', 'coiffure-long.png'],
                            ['Tresse', 'coiffure-tresse.png']
                        ];

                        foreach ($hairs as $h) {

                        ?>

                            <label class="visual-option">

                                <input
                                    type="radio"
                                    name="coiffure"
                                    value="<?php echo $h[0]; ?>"

                                    <?php
                                    echo $coiffureActuelle === $h[0]
                                        ? 'checked'
                                        : '';
                                    ?>
                                >

                                <span class="visual-card">

                                    <img
                                        src="assets/images/creator/<?php echo $h[1]; ?>"
                                        alt=""
                                    >

                                </span>

                                <span>
                                    <?php echo $h[0]; ?>
                                </span>

                            </label>

                        <?php } ?>

                    </div>

                </div>


                <!-- COULEUR CHEVEUX -->

                <div class="creator-section">

                    <h2>Couleur des cheveux</h2>

                    <div class="color-row">

                        <input
                            type="color"
                            id="couleur_cheveux"
                            name="couleur_cheveux"
                            value="<?php echo htmlspecialchars($couleurCheveuxActuelle); ?>"
                        >

                        <span
                            class="color-preview"
                            id="hairColorPreview"
                        ></span>

                    </div>

                </div>


                <!-- YEUX -->

                <div class="creator-section">

                    <h2>Forme des yeux</h2>

                    <div class="visual-grid">

                        <?php

                        $eyes = [
                            ['Fins', 'yeux-fins.png'],
                            ['Ronds', 'yeux-ronds.png'],
                            ['Etroits', 'yeux-etroits.png']
                        ];

                        foreach ($eyes as $e) {

                        ?>

                            <label class="visual-option">

                                <input
                                    type="radio"
                                    name="forme_yeux"
                                    value="<?php echo $e[0]; ?>"

                                    <?php
                                    echo $formeYeuxActuelle === $e[0]
                                        ? 'checked'
                                        : '';
                                    ?>
                                >

                                <span class="visual-card">

                                    <img
                                        src="assets/images/creator/<?php echo $e[1]; ?>"
                                        alt=""
                                    >

                                </span>

                                <span>

                                    <?php

                                    echo $e[0] === 'Etroits'
                                        ? 'Étroits'
                                        : $e[0];

                                    ?>

                                </span>

                            </label>

                        <?php } ?>

                    </div>

                </div>


                <!-- COULEUR YEUX -->

                <div class="creator-section">

                    <h2>Couleur des yeux</h2>

                    <div class="color-row">

                        <input
                            type="color"
                            id="couleur_yeux"
                            name="couleur_yeux"
                            value="<?php echo htmlspecialchars($couleurYeuxActuelle); ?>"
                        >

                        <span
                            class="color-preview"
                            id="eyeColorPreview"
                        ></span>

                    </div>

                </div>

            </div>


            <!-- ======================================
                 ÉQUIPEMENTS
            ======================================= -->

            <div
                class="tab-content"
                id="tab-equipements"
            >

                <div class="creator-section">

                    <h2>Équipements</h2>

                    <p>
                        Choisis l'équipement de ton personnage.
                    </p>

                    <div class="creator-elements">

                        <?php if (empty($equipements)) { ?>

                            <p>
                                Aucun équipement disponible.
                            </p>

                        <?php } ?>


                        <?php foreach ($equipements as $equipement) { ?>

                            <label class="element-card">

                                <input
                                    type="checkbox"
                                    name="elements[]"
                                    value="<?php echo (int) $equipement['id']; ?>"

                                    <?php
                                    echo in_array(
                                        (int) $equipement['id'],
                                        $elementsChoisis,
                                        true
                                    )
                                        ? 'checked'
                                        : '';
                                    ?>
                                >

                                <span>

                                    <strong>
                                        <?php echo htmlspecialchars($equipement['nom']); ?>
                                    </strong>

                                    <small>
                                        <?php echo htmlspecialchars($equipement['description'] ?? ''); ?>
                                    </small>

                                </span>

                            </label>

                        <?php } ?>

                    </div>

                </div>

            </div>


            <!-- ======================================
                 POUVOIRS
            ======================================= -->

            <div
                class="tab-content"
                id="tab-pouvoirs"
            >

                <div class="creator-section">

                    <h2>Pouvoirs</h2>

                    <p>
                        Sélectionne les capacités de ton personnage.
                    </p>

                    <div class="creator-elements">

                        <?php if (empty($pouvoirs)) { ?>

                            <p>
                                Aucun pouvoir disponible.
                            </p>

                        <?php } ?>


                        <?php foreach ($pouvoirs as $pouvoir) { ?>

                            <label class="element-card">

                                <input
                                    type="checkbox"
                                    name="elements[]"
                                    value="<?php echo (int) $pouvoir['id']; ?>"

                                    <?php
                                    echo in_array(
                                        (int) $pouvoir['id'],
                                        $elementsChoisis,
                                        true
                                    )
                                        ? 'checked'
                                        : '';
                                    ?>
                                >

                                <span>

                                    <strong>
                                        <?php echo htmlspecialchars($pouvoir['nom']); ?>
                                    </strong>

                                    <small>
                                        <?php echo htmlspecialchars($pouvoir['description'] ?? ''); ?>
                                    </small>

                                </span>

                            </label>

                        <?php } ?>

                    </div>

                </div>

            </div>


            <!-- ======================================
                 APERÇU
            ======================================= -->

            <div
                class="tab-content"
                id="tab-apercu"
            >

                <div class="creator-section">

                    <h2>
                        Aperçu de
                        <?php echo htmlspecialchars($personnage['nom']); ?>
                    </h2>

                    <div class="apercu-info">

                        <strong>Genre :</strong>

                        <?php
                        echo htmlspecialchars(
                            $personnage['genre']
                        );
                        ?>

                        <br>

                        <strong>Statut :</strong>
                        personnage validé

                        <br>

                        <strong>Modèle :</strong>
                        <span id="resumeModele"></span>

                        <br>

                        <strong>Visage :</strong>
                        <span id="resumeVisage"></span>

                        <br>

                        <strong>Coiffure :</strong>
                        <span id="resumeCoiffure"></span>

                        <br>

                        <strong>Forme des yeux :</strong>
                        <span id="resumeYeux"></span>

                        <br>

                        <strong>Couleur des cheveux :</strong>
                        <span id="resumeCouleurCheveux"></span>

                        <br>

                        <strong>Couleur des yeux :</strong>
                        <span id="resumeCouleurYeux"></span>

                    </div>

                </div>

            </div>


            <!-- ======================================
                 BOUTONS
            ======================================= -->

            <div
                class="tab-content active"
                style="display:block;padding-top:0"
            >

                <div class="save-row">

                    <a href="mes-personnages.php">
                        ✕ Annuler
                    </a>

                    <button type="submit">
                        ▣ Enregistrer
                    </button>

                </div>

            </div>

        </section>

    </form>

</main>


<script src="assets/JS/modifier-personnage.js"></script>

</body>

</html>