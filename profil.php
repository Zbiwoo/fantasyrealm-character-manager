<?php

session_start();
require_once 'config.php';


/* ==================================================
   VÉRIFIER QU'UN UTILISATEUR EST DEMANDÉ
================================================== */

if (!isset($_GET['id']) || !ctype_digit((string) $_GET['id'])) {
    header('Location: index.php');
    exit;
}

$idUtilisateur = (int) $_GET['id'];


/* ==================================================
   VÉRIFIER SI C'EST MON PROPRE PROFIL
================================================== */

$estMonProfil =
    isset($_SESSION['user_id'])
    && (int) $_SESSION['user_id'] === $idUtilisateur;


/* ==================================================
   MESSAGES
================================================== */

$erreurProfil = '';
$succesProfil = '';

$erreurMotDePasse = '';
$succesMotDePasse = '';


/* ==================================================
   MODIFIER LE PSEUDO ET L'EMAIL
================================================== */

if (
    $estMonProfil
    && $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['modifier_profil'])
) {

    $nouveauPseudo = trim($_POST['pseudo'] ?? '');
    $nouvelEmail = trim($_POST['email'] ?? '');


    if ($nouveauPseudo === '' || $nouvelEmail === '') {

        $erreurProfil =
            'Le pseudo et l\'adresse e-mail sont obligatoires.';

    } elseif (!filter_var($nouvelEmail, FILTER_VALIDATE_EMAIL)) {

        $erreurProfil =
            'L\'adresse e-mail n\'est pas valide.';

    } elseif (
        mb_strlen($nouveauPseudo) < 3
        || mb_strlen($nouveauPseudo) > 30
    ) {

        $erreurProfil =
            'Le pseudo doit contenir entre 3 et 30 caractères.';

    } else {

        /*
         * Vérifier qu'aucun AUTRE utilisateur
         * ne possède déjà ce pseudo ou cet e-mail.
         */
        $requeteVerification = $pdo->prepare(
            "SELECT id
             FROM utilisateurs
             WHERE (pseudo = :pseudo OR email = :email)
             AND id != :id
             LIMIT 1"
        );

        $requeteVerification->execute([
            'pseudo' => $nouveauPseudo,
            'email' => $nouvelEmail,
            'id' => $idUtilisateur
        ]);

        $utilisateurExistant =
            $requeteVerification->fetch(PDO::FETCH_ASSOC);


        if ($utilisateurExistant) {

            $erreurProfil =
                'Ce pseudo ou cette adresse e-mail est déjà utilisé.';

        } else {

            $requeteModification = $pdo->prepare(
                "UPDATE utilisateurs
                 SET pseudo = :pseudo,
                     email = :email
                 WHERE id = :id"
            );

            $requeteModification->execute([
                'pseudo' => $nouveauPseudo,
                'email' => $nouvelEmail,
                'id' => $idUtilisateur
            ]);


            /*
             * Mettre également le pseudo à jour
             * dans la session pour la navbar.
             */
            $_SESSION['pseudo'] = $nouveauPseudo;

            $succesProfil =
                'Tes informations ont bien été modifiées.';
        }
    }
}


/* ==================================================
   MODIFIER LE MOT DE PASSE
================================================== */

if (
    $estMonProfil
    && $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['modifier_mot_de_passe'])
) {

    $ancienMotDePasse =
        $_POST['ancien_mot_de_passe'] ?? '';

    $nouveauMotDePasse =
        $_POST['nouveau_mot_de_passe'] ?? '';

    $confirmationMotDePasse =
        $_POST['confirmation_mot_de_passe'] ?? '';


    /*
     * Récupérer le mot de passe actuel
     */
    $requeteMotDePasse = $pdo->prepare(
        "SELECT mot_de_passe
         FROM utilisateurs
         WHERE id = :id"
    );

    $requeteMotDePasse->execute([
        'id' => $idUtilisateur
    ]);

    $utilisateurMotDePasse =
        $requeteMotDePasse->fetch(PDO::FETCH_ASSOC);


    if (
        !$utilisateurMotDePasse
        || !password_verify(
            $ancienMotDePasse,
            $utilisateurMotDePasse['mot_de_passe']
        )
    ) {

        $erreurMotDePasse =
            'Ton mot de passe actuel est incorrect.';

    } elseif (
        $nouveauMotDePasse !== $confirmationMotDePasse
    ) {

        $erreurMotDePasse =
            'Les nouveaux mots de passe ne correspondent pas.';

    } elseif (
        !preg_match('/[A-Z]/', $nouveauMotDePasse)
        || !preg_match('/[a-z]/', $nouveauMotDePasse)
        || !preg_match('/[0-9]/', $nouveauMotDePasse)
        || !preg_match('/[^a-zA-Z0-9]/', $nouveauMotDePasse)
    ) {

        $erreurMotDePasse =
            'Le nouveau mot de passe doit contenir une majuscule, une minuscule, un chiffre et un caractère spécial.';

    } else {

        $nouveauHash = password_hash(
            $nouveauMotDePasse,
            PASSWORD_DEFAULT
        );

        $requeteModificationMotDePasse = $pdo->prepare(
            "UPDATE utilisateurs
             SET mot_de_passe = :mot_de_passe
             WHERE id = :id"
        );

        $requeteModificationMotDePasse->execute([
            'mot_de_passe' => $nouveauHash,
            'id' => $idUtilisateur
        ]);

        $succesMotDePasse =
            'Ton mot de passe a bien été modifié.';
    }
}


/* ==================================================
   RÉCUPÉRER LE PROFIL
================================================== */

/*
 * L'e-mail est récupéré uniquement pour pouvoir
 * l'afficher au propriétaire du compte.
 * Il ne sera jamais affiché sur un profil public.
 */
$requeteUtilisateur = $pdo->prepare(
    "SELECT id, pseudo, email
     FROM utilisateurs
     WHERE id = :id"
);

$requeteUtilisateur->execute([
    'id' => $idUtilisateur
]);

$profil =
    $requeteUtilisateur->fetch(PDO::FETCH_ASSOC);


/* ==================================================
   SI L'UTILISATEUR N'EXISTE PAS
================================================== */

if (!$profil) {
    header('Location: index.php');
    exit;
}


/* ==================================================
   RÉCUPÉRER LES PERSONNAGES PUBLICS
================================================== */

$requetePersonnages = $pdo->prepare(
    "SELECT *
     FROM personnages
     WHERE utilisateur_id = :utilisateur_id
     AND statut_nom = 'valide'
     AND partage = 1
     ORDER BY date_creation DESC"
);

$requetePersonnages->execute([
    'utilisateur_id' => $idUtilisateur
]);

$personnages =
    $requetePersonnages->fetchAll(PDO::FETCH_ASSOC);

$nombrePersonnages =
    count($personnages);


/* ==================================================
   INITIALE POUR L'AVATAR
================================================== */

$initiale = mb_strtoupper(
    mb_substr($profil['pseudo'], 0, 1)
);


/* ==================================================
   IMAGE DU PERSONNAGE
================================================== */

function imageProfilPersonnage($personnage)
{
    $modele =
        $personnage['modele_visuel']
        ?? 'guerriere';

    $images = [
        'guerriere' =>
            'assets/images/creator/heroine-guerriere.png',

        'mage' =>
            'assets/images/creator/modele-mage.png',

        'archere' =>
            'assets/images/creator/modele-archere.png'
    ];

    return $images[$modele]
        ?? 'assets/images/creator/heroine-guerriere.png';
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
        Profil de
        <?php echo htmlspecialchars($profil['pseudo']); ?>
        - FantasyRealm
    </title>

    <link
        rel="stylesheet"
        href="assets/CSS/style.css"
    >

</head>


<body class="profile-page">


<?php include 'navbar.php'; ?>


<main class="fantasy-profile">


    <!-- ==================================================
         BANNIÈRE
    ================================================== -->

    <section class="profile-hero">

        <div class="profile-hero-overlay"></div>


        <div class="profile-hero-content">


            <div class="profile-avatar">

                <span>
                    <?php echo htmlspecialchars($initiale); ?>
                </span>

            </div>


            <div class="profile-identity">

                <p class="profile-eyebrow">
                    JOUEUR DE FANTASYREALM
                </p>

                <h1>
                    @<?php echo htmlspecialchars($profil['pseudo']); ?>
                </h1>

                <p class="profile-description">

                    Aventurier du royaume et créateur
                    de personnages FantasyRealm Online.

                </p>

            </div>


            <div class="profile-stat">

                <strong>
                    <?php echo $nombrePersonnages; ?>
                </strong>

                <span>

                    <?php

                    echo $nombrePersonnages > 1
                        ? 'PERSONNAGES PARTAGÉS'
                        : 'PERSONNAGE PARTAGÉ';

                    ?>

                </span>

            </div>


        </div>

    </section>


    <!-- ==================================================
         BARRE PROFIL
    ================================================== -->

    <section class="profile-navigation">

        <div>

            <span class="profile-navigation-title">
                COLLECTION
            </span>

            <span class="profile-navigation-subtitle">
                Personnages publics
            </span>

        </div>


        <?php if ($estMonProfil) { ?>

            <a
                href="mes-personnages.php"
                class="profile-manage-button"
            >
                MES PERSONNAGES
            </a>

        <?php } ?>

    </section>


    <!-- ==================================================
         PARAMÈTRES DU COMPTE
         UNIQUEMENT SUR SON PROPRE PROFIL
    ================================================== -->

    <?php if ($estMonProfil) { ?>


        <section class="profile-settings">


            <div class="profile-section-heading">

                <div>

                    <p>
                        MON COMPTE
                    </p>

                    <h2>
                        PARAMÈTRES DU PROFIL
                    </h2>

                </div>

            </div>


            <div class="profile-settings-grid">


                <!-- ======================================
                     INFORMATIONS DU COMPTE
                ======================================= -->

                <article class="profile-settings-card">


                    <div class="profile-settings-card-title">

                        <span>
                            01
                        </span>

                        <div>

                            <p>
                                IDENTITÉ
                            </p>

                            <h3>
                                INFORMATIONS DU COMPTE
                            </h3>

                        </div>

                    </div>


                    <p class="profile-settings-description">

                        Modifie les informations utilisées
                        sur ton profil FantasyRealm.

                    </p>


                    <?php if ($erreurProfil !== '') { ?>

                        <div class="profile-message profile-message-error">

                            <?php
                            echo htmlspecialchars($erreurProfil);
                            ?>

                        </div>

                    <?php } ?>


                    <?php if ($succesProfil !== '') { ?>

                        <div class="profile-message profile-message-success">

                            <?php
                            echo htmlspecialchars($succesProfil);
                            ?>

                        </div>

                    <?php } ?>


                    <form
                        method="POST"
                        action=""
                        class="profile-settings-form"
                    >


                        <label for="pseudo">
                            PSEUDO
                        </label>

                        <input
                            type="text"
                            id="pseudo"
                            name="pseudo"
                            value="<?php echo htmlspecialchars($profil['pseudo']); ?>"
                            minlength="3"
                            maxlength="30"
                            required
                        >


                        <label for="email">
                            ADRESSE E-MAIL
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="<?php echo htmlspecialchars($profil['email']); ?>"
                            required
                        >


                        <button
                            type="submit"
                            name="modifier_profil"
                        >
                            ENREGISTRER LES MODIFICATIONS
                        </button>


                    </form>


                </article>


                <!-- ======================================
                     MOT DE PASSE
                ======================================= -->

                <article class="profile-settings-card">


                    <div class="profile-settings-card-title">

                        <span>
                            02
                        </span>

                        <div>

                            <p>
                                SÉCURITÉ
                            </p>

                            <h3>
                                MOT DE PASSE
                            </h3>

                        </div>

                    </div>


                    <p class="profile-settings-description">

                        Pour protéger ton compte,
                        confirme ton mot de passe actuel
                        avant d'en choisir un nouveau.

                    </p>


                    <?php if ($erreurMotDePasse !== '') { ?>

                        <div class="profile-message profile-message-error">

                            <?php
                            echo htmlspecialchars($erreurMotDePasse);
                            ?>

                        </div>

                    <?php } ?>


                    <?php if ($succesMotDePasse !== '') { ?>

                        <div class="profile-message profile-message-success">

                            <?php
                            echo htmlspecialchars($succesMotDePasse);
                            ?>

                        </div>

                    <?php } ?>


                    <form
                        method="POST"
                        action=""
                        class="profile-settings-form"
                    >


                        <label for="ancien_mot_de_passe">
                            MOT DE PASSE ACTUEL
                        </label>

                        <div class="profile-password-field">

                            <input
                                type="password"
                                id="ancien_mot_de_passe"
                                name="ancien_mot_de_passe"
                                autocomplete="current-password"
                                required
                            >

                            <button
                                type="button"
                                class="password-toggle"
                                data-target="ancien_mot_de_passe"
                                aria-label="Afficher le mot de passe"
                            >
                                VOIR
                            </button>

                        </div>


                        <label for="nouveau_mot_de_passe">
                            NOUVEAU MOT DE PASSE
                        </label>

                        <div class="profile-password-field">

                            <input
                                type="password"
                                id="nouveau_mot_de_passe"
                                name="nouveau_mot_de_passe"
                                autocomplete="new-password"
                                required
                            >

                            <button
                                type="button"
                                class="password-toggle"
                                data-target="nouveau_mot_de_passe"
                                aria-label="Afficher le mot de passe"
                            >
                                VOIR
                            </button>

                        </div>


                        <p class="profile-password-help">
                            Majuscule, minuscule, chiffre
                            et caractère spécial obligatoires.
                        </p>


                        <label for="confirmation_mot_de_passe">
                            CONFIRMER LE NOUVEAU MOT DE PASSE
                        </label>

                        <div class="profile-password-field">

                            <input
                                type="password"
                                id="confirmation_mot_de_passe"
                                name="confirmation_mot_de_passe"
                                autocomplete="new-password"
                                required
                            >

                            <button
                                type="button"
                                class="password-toggle"
                                data-target="confirmation_mot_de_passe"
                                aria-label="Afficher le mot de passe"
                            >
                                VOIR
                            </button>

                        </div>


                        <button
                            type="submit"
                            name="modifier_mot_de_passe"
                        >
                            MODIFIER LE MOT DE PASSE
                        </button>


                    </form>


                </article>


            </div>


        </section>


    <?php } ?>


    <!-- ==================================================
         PERSONNAGES PARTAGÉS
    ================================================== -->

    <section class="profile-collection">


        <div class="profile-section-heading">

            <div>

                <p>
                    CRÉATIONS
                </p>

                <h2>
                    PERSONNAGES PARTAGÉS
                </h2>

            </div>


            <span class="profile-character-count">

                <?php echo $nombrePersonnages; ?>

                <?php

                echo $nombrePersonnages > 1
                    ? 'personnages'
                    : 'personnage';

                ?>

            </span>

        </div>


        <?php if ($nombrePersonnages > 0) { ?>


            <div class="profile-character-grid">


                <?php foreach ($personnages as $personnage) { ?>


                    <article class="profile-character-card">


                        <a
                            href="personnage.php?id=<?php echo (int) $personnage['id']; ?>"
                            class="profile-character-image"
                        >

                            <img
                                src="<?php echo htmlspecialchars(imageProfilPersonnage($personnage)); ?>"
                                alt="Personnage <?php echo htmlspecialchars($personnage['nom']); ?>"
                            >


                            <div class="profile-character-image-overlay">

                                <span>
                                    VOIR LE PERSONNAGE
                                </span>

                            </div>

                        </a>


                        <div class="profile-character-content">


                            <div class="profile-character-top">

                                <div>

                                    <span class="profile-character-label">
                                        PERSONNAGE
                                    </span>

                                    <h3>
                                        <?php echo htmlspecialchars($personnage['nom']); ?>
                                    </h3>

                                </div>


                                <span class="profile-character-status">
                                    PUBLIC
                                </span>

                            </div>


                            <div class="profile-character-details">


                                <div>

                                    <span>
                                        GENRE
                                    </span>

                                    <strong>
                                        <?php echo htmlspecialchars($personnage['genre']); ?>
                                    </strong>

                                </div>


                                <div>

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


                            </div>


                            <a
                                href="personnage.php?id=<?php echo (int) $personnage['id']; ?>"
                                class="profile-character-button"
                            >
                                DÉCOUVRIR
                            </a>


                        </div>


                    </article>


                <?php } ?>


            </div>


        <?php } else { ?>


            <div class="profile-empty">

                <div class="profile-empty-symbol">
                    ✦
                </div>

                <h3>
                    AUCUN PERSONNAGE PARTAGÉ
                </h3>

                <p>

                    @<?php echo htmlspecialchars($profil['pseudo']); ?>
                    n'a pas encore partagé de personnage avec le royaume.

                </p>


                <?php if ($estMonProfil) { ?>

                    <a href="mes-personnages.php">
                        GÉRER MES PERSONNAGES
                    </a>

                <?php } ?>


            </div>


        <?php } ?>


    </section>


</main>

<script>
document.querySelectorAll('.password-toggle').forEach(function (button) {

    button.addEventListener('click', function () {

        const inputId = this.getAttribute('data-target');
        const input = document.getElementById(inputId);

        if (input.type === 'password') {
            input.type = 'text';
            this.textContent = 'MASQUER';
            this.setAttribute('aria-label', 'Masquer le mot de passe');
        } else {
            input.type = 'password';
            this.textContent = 'VOIR';
            this.setAttribute('aria-label', 'Afficher le mot de passe');
        }

    });

});
</script>

</body>

</html>