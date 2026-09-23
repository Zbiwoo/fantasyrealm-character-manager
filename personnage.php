<?php

session_start();
require_once 'config.php';
require_once 'mots-interdits.php';


/* ==================================================
   RÉCUPÉRATION DU PERSONNAGE
================================================== */

if (!isset($_GET['id'])) {
    header('Location: personnages.php');
    exit;
}

$idPersonnage = (int) $_GET['id'];


$requete = $pdo->prepare(
    "SELECT personnages.*, utilisateurs.pseudo
     FROM personnages
     INNER JOIN utilisateurs
        ON personnages.utilisateur_id = utilisateurs.id
     WHERE personnages.id = :id
       AND personnages.statut_nom = 'valide'
       AND personnages.partage = 1"
);

$requete->execute([
    'id' => $idPersonnage
]);

$personnage = $requete->fetch(PDO::FETCH_ASSOC);


if (!$personnage) {
    header('Location: personnages.php');
    exit;
}


/* ==================================================
   AJOUT D'UN COMMENTAIRE
================================================== */

$messageCommentaire = '';
$erreurCommentaire = '';


if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['envoyer_commentaire'])
) {

    if (!isset($_SESSION['user_id'])) {

        $erreurCommentaire =
            'Vous devez être connecté pour laisser un commentaire.';

    } else {

        $commentaire = trim($_POST['commentaire'] ?? '');
        $note = (int) ($_POST['note'] ?? 0);


        if ($commentaire === '') {

            $erreurCommentaire =
                'Veuillez écrire un commentaire.';

        } elseif (contientTermeInterdit($commentaire)) {

            $erreurCommentaire =
                'Ce commentaire ne peut pas être envoyé car il contient un terme interdit.';

        } elseif ($note < 1 || $note > 5) {

            $erreurCommentaire =
                'Veuillez choisir une note entre 1 et 5.';

        } else {

            $requeteCommentaire = $pdo->prepare(
                "INSERT INTO commentaires
                (
                    personnage_id,
                    utilisateur_id,
                    commentaire,
                    note,
                    statut
                )
                VALUES
                (
                    :personnage_id,
                    :utilisateur_id,
                    :commentaire,
                    :note,
                    'en_attente'
                )"
            );

            $requeteCommentaire->execute([
                'personnage_id' => $idPersonnage,
                'utilisateur_id' => $_SESSION['user_id'],
                'commentaire' => $commentaire,
                'note' => $note
            ]);

            $messageCommentaire =
                'Votre commentaire a bien été envoyé. Il sera visible après validation par un employé.';
        }
    }
}


/* ==================================================
   RÉCUPÉRATION DES COMMENTAIRES VALIDÉS
================================================== */

$requeteCommentaires = $pdo->prepare(
    "SELECT
        commentaires.*,
        utilisateurs.pseudo
     FROM commentaires
     INNER JOIN utilisateurs
        ON commentaires.utilisateur_id = utilisateurs.id
     WHERE commentaires.personnage_id = :personnage_id
       AND commentaires.statut = 'valide'
     ORDER BY commentaires.date_creation DESC"
);

$requeteCommentaires->execute([
    'personnage_id' => $idPersonnage
]);

$commentaires = $requeteCommentaires->fetchAll(PDO::FETCH_ASSOC);


/* ==================================================
   CALCUL DE LA NOTE MOYENNE
================================================== */

$noteMoyenne = null;

if (count($commentaires) > 0) {

    $totalNotes = 0;

    foreach ($commentaires as $commentaireValide) {
        $totalNotes += (int) $commentaireValide['note'];
    }

    $noteMoyenne =
        $totalNotes / count($commentaires);
}


/* ==================================================
   APPARENCE DU PERSONNAGE
================================================== */

$nomPersonnage = mb_strtolower($personnage['nom']);


/* Valeurs par défaut */

$imagePersonnage = 'mage_noir.png';

$accessoires = [
    [
        'icone' => '⚔',
        'nom' => 'Épée des Ombres',
        'description' => 'Lame imprégnée d’une ancienne magie obscure.'
    ],
    [
        'icone' => '◈',
        'nom' => 'Armure obscure',
        'description' => 'Protection forgée dans les terres sombres.'
    ],
    [
        'icone' => '✦',
        'nom' => 'Anneau mystique',
        'description' => 'Amplifie la puissance de la magie des ombres.'
    ]
];


/* ==================================================
   MAGE NOIRE
================================================== */

if ($nomPersonnage === 'mage noire') {

    $imagePersonnage = 'mage_noir.png';

    $accessoires = [
        [
            'icone' => '⚔',
            'nom' => 'Épée des Ombres',
            'description' =>
                'Lame imprégnée d’une ancienne magie obscure.'
        ],
        [
            'icone' => '◈',
            'nom' => 'Armure obscure',
            'description' =>
                'Protection forgée dans les terres sombres.'
        ],
        [
            'icone' => '✦',
            'nom' => 'Anneau mystique',
            'description' =>
                'Amplifie la puissance de la magie des ombres.'
        ]
    ];


/* ==================================================
   GUERRIÈRE
================================================== */

} elseif (
    $nomPersonnage === 'guerrière'
    || $nomPersonnage === 'guerriere'
) {

    $imagePersonnage = 'guerrier.png';

    $accessoires = [
        [
            'icone' => '⚔',
            'nom' => 'Grande épée runique',
            'description' =>
                'Une lourde lame gravée de runes anciennes.'
        ],
        [
            'icone' => '◈',
            'nom' => 'Armure du Dragon',
            'description' =>
                'Une armure résistante inspirée des dragons légendaires.'
        ],
        [
            'icone' => '◆',
            'nom' => 'Bouclier ancestral',
            'description' =>
                'Un bouclier ancien capable de résister aux attaques les plus puissantes.'
        ]
    ];


/* ==================================================
   ARCHÈRE
================================================== */

} elseif (
    $nomPersonnage === 'archère'
    || $nomPersonnage === 'archere'
) {

    $imagePersonnage = 'archer.png';

    $accessoires = [
        [
            'icone' => '➶',
            'nom' => 'Arc sylvestre',
            'description' =>
                'Un arc léger façonné dans le bois des forêts anciennes.'
        ],
        [
            'icone' => '✦',
            'nom' => 'Carquois enchanté',
            'description' =>
                'Un carquois magique qui protège et renforce ses flèches.'
        ],
        [
            'icone' => '◇',
            'nom' => 'Cape du rôdeur',
            'description' =>
                'Une cape discrète permettant de se fondre dans les environnements sauvages.'
        ]
    ];


/* ==================================================
   SORCIÈRE
================================================== */

} elseif (
    $nomPersonnage === 'sorcière'
    || $nomPersonnage === 'sorciere'
) {

    $imagePersonnage = 'sorciere.png';

    $accessoires = [
        [
            'icone' => '✦',
            'nom' => 'Baguette maudite',
            'description' =>
                'Une baguette ancienne utilisée pour lancer de puissantes malédictions.'
        ],
        [
            'icone' => '◈',
            'nom' => 'Grimoire des Arcanes',
            'description' =>
                'Un ouvrage interdit renfermant des sorts oubliés et une magie dangereuse.'
        ],
        [
            'icone' => '◆',
            'nom' => 'Amulette des Âmes',
            'description' =>
                'Une relique mystique qui augmente la puissance des sortilèges.'
        ]
    ];
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
        <?php echo htmlspecialchars($personnage['nom']); ?>
        - FantasyRealm Online
    </title>

    <link
        rel="stylesheet"
        href="assets/CSS/style.css"
    >

</head>


<body class="character-detail-page">


<?php include 'navbar.php'; ?>


<main class="character-detail">


    <!-- ==================================================
         PROFIL DU PERSONNAGE
    ================================================== -->

    <section class="character-profile">


        <div class="detail-image">

            <img
                src="assets/images/<?php echo $imagePersonnage; ?>"
                alt="<?php echo htmlspecialchars($personnage['nom']); ?>"
                id="character-image"
            >

        </div>


        <div class="detail-info">


            <p class="character-category">
                FANTASYREALM ONLINE
            </p>


            <h1>
                <?php echo htmlspecialchars($personnage['nom']); ?>
            </h1>


            <p class="character-creator-name">

                Créé par

                <strong>
                    @<?php echo htmlspecialchars($personnage['pseudo']); ?>
                </strong>

            </p>


            <div class="character-divider"></div>


            <h2>
                INFORMATIONS
            </h2>


            <div class="character-information-grid">


                <div class="character-information">

                    <span>
                        GENRE
                    </span>

                    <strong>
                        <?php echo htmlspecialchars($personnage['genre']); ?>
                    </strong>

                </div>


                <div class="character-information">

                    <span>
                        CRÉATION
                    </span>

                    <strong>

                        <?php
                        echo date(
                            'd/m/Y',
                            strtotime($personnage['date_creation'])
                        );
                        ?>

                    </strong>

                </div>


                <div class="character-information">

                    <span>
                        STATUT
                    </span>

                    <strong>
                        VALIDÉ
                    </strong>

                </div>


                <div class="character-information">

                    <span>
                        PARTAGE
                    </span>

                    <strong>
                        PUBLIC
                    </strong>

                </div>


            </div>


            <div class="character-divider"></div>


            <h2>
                ACCESSOIRES & POUVOIRS
            </h2>


            <div class="character-accessories">


                <?php foreach ($accessoires as $accessoire) { ?>


                    <div class="character-accessory">

                        <span class="accessory-icon">
                            <?php echo $accessoire['icone']; ?>
                        </span>


                        <div>

                            <strong>
                                <?php
                                echo htmlspecialchars(
                                    $accessoire['nom']
                                );
                                ?>
                            </strong>

                            <p>
                                <?php
                                echo htmlspecialchars(
                                    $accessoire['description']
                                );
                                ?>
                            </p>

                        </div>


                    </div>


                <?php } ?>


            </div>


        </div>


    </section>



    <!-- ==================================================
         COMMENTAIRES
    ================================================== -->

    <section class="comments-section">


        <p class="comments-small-title">
            COMMUNAUTÉ
        </p>


        <h2>
            AVIS DE LA COMMUNAUTÉ
        </h2>



        <!-- NOTE MOYENNE -->

        <div class="rating">


            <?php if ($noteMoyenne !== null) { ?>


                <span class="rating-stars">

                    <?php

                    $noteArrondie = round($noteMoyenne);

                    for ($i = 1; $i <= 5; $i++) {

                        if ($i <= $noteArrondie) {
                            echo '★';
                        } else {
                            echo '☆';
                        }
                    }

                    ?>

                </span>


                <strong>

                    <?php
                    echo number_format(
                        $noteMoyenne,
                        1,
                        ',',
                        ' '
                    );
                    ?>

                    / 5

                </strong>


                <p>

                    <?php echo count($commentaires); ?>

                    avis validé<?php
                    if (count($commentaires) > 1) {
                        echo 's';
                    }
                    ?>

                </p>


            <?php } else { ?>


                <span class="rating-stars">
                    ☆☆☆☆☆
                </span>

                <strong>
                    Aucune note
                </strong>

                <p>
                    Soyez le premier à donner votre avis.
                </p>


            <?php } ?>


        </div>



        <!-- MESSAGES -->

        <?php if ($messageCommentaire !== '') { ?>

            <div class="comment-success">

                <?php
                echo htmlspecialchars(
                    $messageCommentaire
                );
                ?>

            </div>

        <?php } ?>


        <?php if ($erreurCommentaire !== '') { ?>

            <div class="comment-error">

                <?php
                echo htmlspecialchars(
                    $erreurCommentaire
                );
                ?>

            </div>

        <?php } ?>



        <!-- LISTE DES COMMENTAIRES VALIDÉS -->

        <div class="comments-list">


            <?php if (count($commentaires) > 0) { ?>


                <?php foreach ($commentaires as $avis) { ?>


                    <article class="comment">


                        <div class="comment-header">


                            <strong>

                                @<?php
                                echo htmlspecialchars(
                                    $avis['pseudo']
                                );
                                ?>

                            </strong>


                            <span>

                                <?php

                                for ($i = 1; $i <= 5; $i++) {

                                    if ($i <= (int) $avis['note']) {
                                        echo '★';
                                    } else {
                                        echo '☆';
                                    }
                                }

                                ?>

                            </span>


                        </div>


                        <p>

                            <?php
                            echo nl2br(
                                htmlspecialchars(
                                    $avis['commentaire']
                                )
                            );
                            ?>

                        </p>


                    </article>


                <?php } ?>


            <?php } else { ?>


                <p class="no-comments">
                    Aucun commentaire validé pour le moment.
                </p>


            <?php } ?>


        </div>



        <!-- ==================================================
             FORMULAIRE UTILISATEUR CONNECTÉ
        ================================================== -->

        <?php if (isset($_SESSION['user_id'])) { ?>


            <form
                class="comment-form"
                method="POST"
                action=""
            >


<label>
    VOTRE NOTE
</label>

<div class="star-rating">

    <input type="radio" id="star5" name="note" value="5" required>
    <label for="star5" title="5 étoiles">★</label>

    <input type="radio" id="star4" name="note" value="4">
    <label for="star4" title="4 étoiles">★</label>

    <input type="radio" id="star3" name="note" value="3">
    <label for="star3" title="3 étoiles">★</label>

    <input type="radio" id="star2" name="note" value="2">
    <label for="star2" title="2 étoiles">★</label>

    <input type="radio" id="star1" name="note" value="1">
    <label for="star1" title="1 étoile">★</label>

</div>



                <label for="commentaire">
                    AJOUTER UN COMMENTAIRE
                </label>


                <textarea
                    id="commentaire"
                    name="commentaire"
                    rows="4"
                    maxlength="1000"
                    placeholder="Partagez votre avis sur ce personnage..."
                    required
                ></textarea>


                <button
                    type="submit"
                    name="envoyer_commentaire"
                >
                    ENVOYER
                </button>


            </form>


        <?php } else { ?>


            <div class="comment-login-message">

                <p>
                    Connectez-vous pour laisser un commentaire.
                </p>

                <a href="connexion.php">
                    SE CONNECTER
                </a>

            </div>


        <?php } ?>


    </section>


</main>



<!-- ==================================================
     ZOOM QUI SUIT LA SOURIS
================================================== -->

<script>

const imageContainer =
    document.querySelector('.detail-image');

const characterImage =
    document.querySelector('#character-image');


if (imageContainer && characterImage) {


    imageContainer.addEventListener(
        'mouseenter',
        function () {

            characterImage.style.transition =
                'transform 0.25s ease';

            characterImage.style.transform =
                'scale(2.2)';
        }
    );


    imageContainer.addEventListener(
        'mousemove',
        function (event) {

            const rectangle =
                imageContainer.getBoundingClientRect();

            const mouseX =
                (
                    (event.clientX - rectangle.left)
                    / rectangle.width
                ) * 100;

            const mouseY =
                (
                    (event.clientY - rectangle.top)
                    / rectangle.height
                ) * 100;


            characterImage.style.transformOrigin =
                mouseX + '% ' + mouseY + '%';
        }
    );


    imageContainer.addEventListener(
        'mouseleave',
        function () {

            characterImage.style.transform =
                'scale(1)';

            characterImage.style.transformOrigin =
                'center center';
        }
    );

}

</script>


</body>

</html>