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

$modelesAutorises = ['guerriere', 'mage', 'archere', 'sorciere', 'tieffelin'];

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

$elementsChoisis = $requeteChoix->fetchAll(
    PDO::FETCH_COLUMN
);

$elementsChoisis = array_map(
    'intval',
    $elementsChoisis
);


/* ==================================================
   VALEURS ACTUELLES DE PERSONNALISATION
================================================== */

$modeleActuel = in_array(($personnage['modele_visuel'] ?? ''), $modelesAutorises, true)
    ? $personnage['modele_visuel']
    : 'tieffelin';

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

    $modeleVisuel = trim($_POST['modele_visuel'] ?? 'tieffelin');

    $visage = trim($_POST['visage'] ?? '');

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
       VÉRIFIER LE VISAGE
    ================================================== */

    if (!in_array($modeleVisuel, $modelesAutorises, true)) {

        $erreur = 'Le modèle sélectionné est invalide.';

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

                'modele_visuel' => $modeleVisuel,

                'visage' => $visage,

                'coiffure' => $coiffure,

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
       CONSERVER LES VALEURS DU FORMULAIRE EN CAS D'ERREUR
    ================================================== */

    $modeleActuel = $modeleVisuel;
    $visageActuel = $visage;

    $coiffureActuelle = $coiffure;

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
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Personnaliser <?php echo htmlspecialchars($personnage['nom']); ?></title>
<link rel="stylesheet" href="assets/CSS/style.css">
<style>
/* PAGE CRÉATEUR - isolée pour ne pas casser le reste du site */
.stardoll-page{min-height:100vh;background:#171119;color:#f5f0f6;padding:42px 4% 70px;font-family:"Poppins",sans-serif}
.stardoll-title{max-width:1450px;margin:0 auto 24px}
.stardoll-title h1{margin:0;color:#e0c7ea;font-family:"Cinzel",serif;font-size:clamp(30px,4vw,52px);font-weight:500}
.stardoll-title p{margin:7px 0 0;color:#b9a7bd;font-family:Georgia,serif;font-size:20px;font-style:italic}
.stardoll-form{max-width:1450px;margin:auto;display:grid;grid-template-columns:minmax(360px,.9fr) minmax(560px,1.25fr);gap:22px}
.stardoll-portrait,.stardoll-panel{border:1px solid rgba(198,161,91,.55);background:#110d13;box-shadow:0 15px 45px rgba(0,0,0,.3)}
.stardoll-portrait{padding:14px;align-self:start}
.portrait-frame{height:830px;border:1px solid rgba(198,161,91,.45);overflow:hidden;background:radial-gradient(circle at 50% 25%,#35253c,#120e15 70%);position:relative}
.portrait-frame img{width:100%;height:100%;object-fit:cover;object-position:center top;display:block}
.portrait-name{text-align:center;padding:18px 8px 8px;color:#e0c27a;font-family:"Cinzel",serif;font-size:28px}
.portrait-quote{text-align:center;margin:0 0 14px;color:#bba9bf;font-family:Georgia,serif;font-size:17px;font-style:italic}
.creator-tabs{display:grid;grid-template-columns:repeat(4,1fr);border-bottom:1px solid rgba(198,161,91,.45)}
.creator-tab{padding:18px 8px;border:0;border-right:1px solid rgba(198,161,91,.28);background:#151018;color:#d8c8dd;font-family:"Cinzel",serif;font-size:15px;cursor:pointer}
.creator-tab.active{background:linear-gradient(#53365f,#34233d);color:#fff;border-top:2px solid #e0c27a}
.tab-content{display:none;padding:26px}.tab-content.active{display:block}
.creator-section{padding:0 0 23px;margin:0 0 23px;border-bottom:1px solid rgba(198,161,91,.5)}
.creator-section h2{margin:0 0 4px;font-family:"Cinzel",serif;font-size:18px;color:#f5f0f6}
.creator-section>p{margin:0 0 15px;color:#a998ad;font-size:13px}
.visual-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:13px}
.visual-option{display:block;cursor:pointer;text-align:center}
.visual-option input{position:absolute;opacity:0;pointer-events:none}
.visual-card{height:145px;border:1px solid #4d4050;background:#0d0a0f;overflow:hidden;transition:.18s}
.visual-card img{width:100%;height:100%;object-fit:cover;display:block}
.visual-option span:last-child{display:block;margin-top:7px;color:#d9cadc;font-family:"Cinzel",serif;font-size:14px}
.visual-option input:checked+.visual-card{border:3px solid #e0c27a;box-shadow:0 0 0 1px #7f6133,0 0 15px rgba(198,161,91,.16)}
.color-row{display:flex;align-items:center;gap:16px;flex-wrap:wrap}
.color-row input[type=color]{width:68px;height:45px;padding:3px;background:#110d13;border:1px solid #c6a15b;border-radius:4px;cursor:pointer}
.color-preview{width:34px;height:34px;border-radius:50%;border:2px solid #e0c27a;box-shadow:0 0 0 2px #251b29}
.creator-elements{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
.element-card{display:flex;gap:12px;padding:15px;border:1px solid #4c3c50;background:#19131c;cursor:pointer}
.element-card:has(input:checked){border-color:#c6a15b;background:#241a29}
.element-card input{accent-color:#c6a15b}
.element-card strong{display:block;color:#e0c27a;font-family:"Cinzel",serif;font-size:13px}
.element-card small{display:block;color:#b8a9bb;margin-top:4px;line-height:1.4}
.save-row{display:flex;gap:14px;justify-content:flex-end;padding-top:5px}
.save-row a,.save-row button{min-width:180px;padding:15px 20px;text-align:center;border:1px solid #6b586f;font-family:"Cinzel",serif;font-size:14px;text-decoration:none;cursor:pointer}
.save-row a{color:#c7b8ca;background:#100c12}
.save-row button{color:white;border-color:#c6a15b;background:linear-gradient(135deg,#3d2a48,#674174)}
.error-message{max-width:1450px;margin:0 auto 18px;padding:12px 16px;border:1px solid #a45c66;background:#351d25;color:#ffdce1}
.apercu-info{padding:18px;border:1px solid #49384d;background:#19131c;line-height:1.8}
@media(max-width:1050px){.stardoll-form{grid-template-columns:1fr}.portrait-frame{height:650px}.stardoll-portrait{max-width:620px;width:100%;margin:auto}}
@media(max-width:650px){.stardoll-page{padding:24px 14px}.creator-tabs{grid-template-columns:1fr 1fr}.tab-content{padding:18px}.visual-card{height:110px}.creator-elements{grid-template-columns:1fr}.portrait-frame{height:520px}.save-row{flex-direction:column}.save-row a,.save-row button{width:100%}}
</style>

<style>
/* VERSION STABLE ECF : aucune dépendance aux anciens faux calques */
.visual-option .visual-card img[src*="assets/images/creator/"] { display:none !important; }
.visual-option .visual-card:has(img[src*="assets/images/creator/"]) { display:none !important; }

/* Les 5 modèles propres restent visibles */
.visual-option .visual-card img[src^="assets/images/"] {
    width:100%;
    height:150px;
    object-fit:cover;
    display:block;
}
.model-options {
    grid-template-columns:repeat(auto-fit,minmax(105px,1fr)) !important;
}
#mainHeroine {
    width:100%;
    height:100%;
    object-fit:cover;
    object-position:center top;
}
</style>

</head>
<body>
<?php
$imagesModeles = [
    'guerriere' => 'guerrier.png',
    'mage' => 'mage_noir.png',
    'archere' => 'archer.png',
    'sorciere' => 'sorciere.png',
    'tieffelin' => 'tieffelin.png'
];
$imageApercu = $imagesModeles[$modeleActuel] ?? 'tieffelin.png';
?>
<?php include 'navbar.php'; ?>

<main class="stardoll-page">
    <div class="stardoll-title">
        <h1>Créer / Modifier un personnage</h1>
        <p>Façonne l'héroïne qui écrira ton histoire...</p>
    </div>

    <?php if ($erreur !== '') { ?>
        <p class="error-message"><?php echo htmlspecialchars($erreur); ?></p>
    <?php } ?>

    <form method="POST" class="stardoll-form">
        <section class="stardoll-portrait">
            <div class="portrait-frame">
                <img id="mainHeroine" src="assets/images/tieffelin.png" alt="Aperçu de Nyxaria">
            </div>
            <div class="portrait-name"><?php echo htmlspecialchars($personnage['nom']); ?></div>
            <p class="portrait-quote">« Même dans l'ombre, je brille. »</p>
        </section>

        <section class="stardoll-panel">
            <div class="creator-tabs">
                <button type="button" class="creator-tab active" data-tab="apparence">Apparence</button>
                <button type="button" class="creator-tab" data-tab="equipements">Équipements</button>
                <button type="button" class="creator-tab" data-tab="pouvoirs">Pouvoirs</button>
                <button type="button" class="creator-tab" data-tab="apercu">Aperçu</button>
            </div>

            <div class="tab-content active" id="tab-apparence">
                <div class="creator-section">
                    <h2>Modèle de base</h2>
                    <p>Choisis une héroïne de départ. Tu pourras la personnaliser ensuite.</p>
                    <div class="visual-grid">
                        <label class="visual-option"><input type="radio" name="modele_visuel" value="guerriere" checked><span class="visual-card"><img src="assets/images/creator/modele-guerriere.png" alt=""></span><span>Guerrière</span></label>
                        <label class="visual-option"><input type="radio" name="modele_visuel" value="mage"><span class="visual-card"><img src="assets/images/creator/modele-mage.png" alt=""></span><span>Mage</span></label>
                        <label class="visual-option"><input type="radio" name="modele_visuel" value="archere"><span class="visual-card"><img src="assets/images/creator/modele-archere.png" alt=""></span><span>Archère</span></label>
                    </div>
                </div>

                <div class="creator-section">
                    <h2>Forme du visage</h2>
                    <div class="visual-grid">
                        <?php
                        $faces = [
                            ['Rond','visage-rond.png'],
                            ['Normal','visage-normal.png'],
                            ['Allongé','visage-allonge.png']
                        ];
                        foreach ($faces as $f) { ?>
                            <label class="visual-option">
                                <input type="radio" name="visage" value="<?php echo $f[0]; ?>" <?php echo $visageActuel === $f[0] ? 'checked' : ''; ?>>
                                <span class="visual-card"><img src="assets/images/creator/<?php echo $f[1]; ?>" alt=""></span>
                                <span><?php echo $f[0]; ?></span>
                            </label>
                        <?php } ?>
                    </div>
                </div>

                <div class="creator-section">
                    <h2>Coiffure</h2>
                    <div class="visual-grid">
                        <?php
                        $hairs = [
                            ['Court','coiffure-court.png'],
                            ['Long','coiffure-long.png'],
                            ['Tresse','coiffure-tresse.png']
                        ];
                        foreach ($hairs as $h) { ?>
                            <label class="visual-option">
                                <input type="radio" name="coiffure" value="<?php echo $h[0]; ?>" <?php echo $coiffureActuelle === $h[0] ? 'checked' : ''; ?>>
                                <span class="visual-card"><img src="assets/images/creator/<?php echo $h[1]; ?>" alt=""></span>
                                <span><?php echo $h[0]; ?></span>
                            </label>
                        <?php } ?>
                    </div>
                </div>

                <div class="creator-section">
                    <h2>Couleur des cheveux</h2>
                    <div class="color-row">
                        <input type="color" id="couleur_cheveux" name="couleur_cheveux" value="<?php echo htmlspecialchars($couleurCheveuxActuelle); ?>">
                        <span class="color-preview" id="hairColorPreview"></span>
                    </div>
                </div>

                <div class="creator-section">
                    <h2>Forme des yeux</h2>
                    <div class="visual-grid">
                        <?php
                        $eyes = [
                            ['Fins','yeux-fins.png'],
                            ['Ronds','yeux-ronds.png'],
                            ['Etroits','yeux-etroits.png']
                        ];
                        foreach ($eyes as $e) { ?>
                            <label class="visual-option">
                                <input type="radio" name="forme_yeux" value="<?php echo $e[0]; ?>" <?php echo $formeYeuxActuelle === $e[0] ? 'checked' : ''; ?>>
                                <span class="visual-card"><img src="assets/images/creator/<?php echo $e[1]; ?>" alt=""></span>
                                <span><?php echo $e[0] === 'Etroits' ? 'Étroits' : $e[0]; ?></span>
                            </label>
                        <?php } ?>
                    </div>
                </div>

                <div class="creator-section">
                    <h2>Couleur des yeux</h2>
                    <div class="color-row">
                        <input type="color" id="couleur_yeux" name="couleur_yeux" value="<?php echo htmlspecialchars($couleurYeuxActuelle); ?>">
                        <span class="color-preview" id="eyeColorPreview"></span>
                    </div>
                </div>
            </div>

            <div class="tab-content" id="tab-equipements">
                <div class="creator-section">
                    <h2>Équipements</h2>
                    <p>Choisis l'équipement de ton personnage.</p>
                    <div class="creator-elements">
                        <?php if (empty($equipements)) { ?><p>Aucun équipement disponible.</p><?php } ?>
                        <?php foreach ($equipements as $equipement) { ?>
                            <label class="element-card">
                                <input type="checkbox" name="elements[]" value="<?php echo (int)$equipement['id']; ?>" <?php echo in_array((int)$equipement['id'],$elementsChoisis,true) ? 'checked' : ''; ?>>
                                <span><strong><?php echo htmlspecialchars($equipement['nom']); ?></strong><small><?php echo htmlspecialchars($equipement['description'] ?? ''); ?></small></span>
                            </label>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div class="tab-content" id="tab-pouvoirs">
                <div class="creator-section">
                    <h2>Pouvoirs</h2>
                    <p>Sélectionne les capacités de ton personnage.</p>
                    <div class="creator-elements">
                        <?php if (empty($pouvoirs)) { ?><p>Aucun pouvoir disponible.</p><?php } ?>
                        <?php foreach ($pouvoirs as $pouvoir) { ?>
                            <label class="element-card">
                                <input type="checkbox" name="elements[]" value="<?php echo (int)$pouvoir['id']; ?>" <?php echo in_array((int)$pouvoir['id'],$elementsChoisis,true) ? 'checked' : ''; ?>>
                                <span><strong><?php echo htmlspecialchars($pouvoir['nom']); ?></strong><small><?php echo htmlspecialchars($pouvoir['description'] ?? ''); ?></small></span>
                            </label>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div class="tab-content" id="tab-apercu">
                <div class="creator-section">
                    <h2>Aperçu de <?php echo htmlspecialchars($personnage['nom']); ?></h2>
                    <div class="apercu-info">
                        <strong>Genre :</strong> <?php echo htmlspecialchars($personnage['genre']); ?><br>
                        <strong>Statut :</strong> personnage validé<br>
                        <strong>Personnalisation :</strong> les choix seront enregistrés dans ton personnage.
                    </div>
                </div>
            </div>

            <div class="tab-content active" style="display:block;padding-top:0">
                <div class="save-row">
                    <a href="mes-personnages.php">✕ Annuler</a>
                    <button type="submit">▣ Enregistrer</button>
                </div>
            </div>
        </section>
    </form>
</main>

<script>
const tabs = document.querySelectorAll('.creator-tab');
const contents = document.querySelectorAll('.stardoll-panel > .tab-content:not(:last-child)');

tabs.forEach(tab => {
    tab.addEventListener('click', () => {
        tabs.forEach(t => t.classList.remove('active'));
        contents.forEach(c => c.classList.remove('active'));
        tab.classList.add('active');
        document.getElementById('tab-' + tab.dataset.tab).classList.add('active');
    });
});

const hairInput = document.getElementById('couleur_cheveux');
const eyeInput = document.getElementById('couleur_yeux');
const hairPreview = document.getElementById('hairColorPreview');
const eyePreview = document.getElementById('eyeColorPreview');

function refreshColors() {
    hairPreview.style.background = hairInput.value;
    eyePreview.style.background = eyeInput.value;
}
hairInput.addEventListener('input', refreshColors);
eyeInput.addEventListener('input', refreshColors);
refreshColors();
/* =========================================
   CHANGER LE MODÈLE PRINCIPAL
========================================= */

const mainHeroine = document.getElementById('mainHeroine');

const modelImages = {
    guerriere: 'assets/images/guerrier.png',
    mage: 'assets/images/mage_noir.png',
    archere: 'assets/images/archer.png',
    sorciere: 'assets/images/sorciere.png',
    tieffelin: 'assets/images/tieffelin.png'
};

document
    .querySelectorAll('input[name="modele_visuel"]')
    .forEach(function (option) {

        option.addEventListener('change', function () {

            if (modelImages[this.value]) {
                mainHeroine.src = modelImages[this.value];
            }

        });

    });
</script>
</body>
</html>
