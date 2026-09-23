<?php

session_start();
require_once 'config.php';
require_once 'mongodb.php';

/* ==================================================
   ACCÈS ADMINISTRATEUR UNIQUEMENT
================================================== */

if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'admin'
) {
    header('Location: connexion.php');
    exit;
}

$erreur = '';
$succes = '';

/* ==================================================
   CRÉATION D'UN EMPLOYÉ
================================================== */

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['creer_employe'])
) {
    $pseudo = trim($_POST['pseudo'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $motDePasse = $_POST['mot_de_passe'] ?? '';

    if ($pseudo === '' || $email === '' || $motDePasse === '') {
        $erreur = 'Veuillez remplir tous les champs.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = 'Veuillez entrer une adresse e-mail valide.';
    } elseif (
        strlen($motDePasse) < 8 ||
        !preg_match('/[A-Z]/', $motDePasse) ||
        !preg_match('/[a-z]/', $motDePasse) ||
        !preg_match('/[0-9]/', $motDePasse) ||
        !preg_match('/[^A-Za-z0-9]/', $motDePasse)
    ) {
        $erreur = 'Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.';
    } else {
        $verification = $pdo->prepare(
            "SELECT id
             FROM utilisateurs
             WHERE pseudo = :pseudo
                OR email = :email
             LIMIT 1"
        );
        $verification->execute([
            'pseudo' => $pseudo,
            'email' => $email
        ]);

        if ($verification->fetch(PDO::FETCH_ASSOC)) {
            $erreur = 'Ce pseudo ou cette adresse e-mail est déjà utilisé.';
        } else {
            $creation = $pdo->prepare(
                "INSERT INTO utilisateurs
                    (pseudo, email, mot_de_passe, role, suspendu)
                 VALUES
                    (:pseudo, :email, :mot_de_passe, 'employe', 0)"
            );

            $creation->execute([
                'pseudo' => $pseudo,
                'email' => $email,
                'mot_de_passe' => password_hash(
                    $motDePasse,
                    PASSWORD_DEFAULT
                )
            ]);

            $nouvelEmployeId = (int) $pdo->lastInsertId();

            enregistrerLogMongoDB(
                'CREATION_EMPLOYE',
                'Création du compte employé "' . $pseudo . '" (ID ' . $nouvelEmployeId . ').',
                (int) $_SESSION['user_id']
            );

            header('Location: administration.php?succes=creation');
            exit;
        }
    }
}

/* ==================================================
   MODIFICATION DU MOT DE PASSE
================================================== */

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['modifier_mot_de_passe'])
) {
    $employeId = (int) ($_POST['employe_id'] ?? 0);
    $nouveauMotDePasse = $_POST['nouveau_mot_de_passe'] ?? '';

    if (
        strlen($nouveauMotDePasse) < 8 ||
        !preg_match('/[A-Z]/', $nouveauMotDePasse) ||
        !preg_match('/[a-z]/', $nouveauMotDePasse) ||
        !preg_match('/[0-9]/', $nouveauMotDePasse) ||
        !preg_match('/[^A-Za-z0-9]/', $nouveauMotDePasse)
    ) {
        $erreur = 'Le nouveau mot de passe ne respecte pas les règles de sécurité.';
    } else {
        $requete = $pdo->prepare(
            "UPDATE utilisateurs
             SET mot_de_passe = :mot_de_passe
             WHERE id = :id
               AND role = 'employe'"
        );

        $employeCible = $pdo->prepare(
            "SELECT pseudo
             FROM utilisateurs
             WHERE id = :id
               AND role = 'employe'
             LIMIT 1"
        );
        $employeCible->execute(['id' => $employeId]);
        $employeCible = $employeCible->fetch(PDO::FETCH_ASSOC);

        $requete->execute([
            'mot_de_passe' => password_hash(
                $nouveauMotDePasse,
                PASSWORD_DEFAULT
            ),
            'id' => $employeId
        ]);

        if ($requete->rowCount() > 0 && $employeCible) {
            enregistrerLogMongoDB(
                'MODIFICATION_MOT_DE_PASSE_EMPLOYE',
                'Modification du mot de passe de l’employé "' . $employeCible['pseudo'] . '" (ID ' . $employeId . ').',
                (int) $_SESSION['user_id']
            );
        }

        header('Location: administration.php?succes=motdepasse');
        exit;
    }
}

/* ==================================================
   SUSPENSION / RÉACTIVATION
================================================== */

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['changer_suspension'])
) {
    $employeId = (int) ($_POST['employe_id'] ?? 0);

    $employeCible = $pdo->prepare(
        "SELECT pseudo, suspendu
         FROM utilisateurs
         WHERE id = :id
           AND role = 'employe'
         LIMIT 1"
    );
    $employeCible->execute(['id' => $employeId]);
    $employeCible = $employeCible->fetch(PDO::FETCH_ASSOC);

    $requete = $pdo->prepare(
        "UPDATE utilisateurs
         SET suspendu = CASE
             WHEN suspendu = 1 THEN 0
             ELSE 1
         END
         WHERE id = :id
           AND role = 'employe'"
    );

    $requete->execute(['id' => $employeId]);

    if ($requete->rowCount() > 0 && $employeCible) {
        $actionSuspension =
            (int) $employeCible['suspendu'] === 1
                ? 'REACTIVATION_EMPLOYE'
                : 'SUSPENSION_EMPLOYE';

        $verbeSuspension =
            (int) $employeCible['suspendu'] === 1
                ? 'Réactivation'
                : 'Suspension';

        enregistrerLogMongoDB(
            $actionSuspension,
            $verbeSuspension . ' du compte employé "' . $employeCible['pseudo'] . '" (ID ' . $employeId . ').',
            (int) $_SESSION['user_id']
        );
    }

    header('Location: administration.php?succes=suspension');
    exit;
}

/* ==================================================
   SUPPRESSION D'UN EMPLOYÉ
================================================== */

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['supprimer_employe'])
) {
    $employeId = (int) ($_POST['employe_id'] ?? 0);

    $employeCible = $pdo->prepare(
        "SELECT pseudo
         FROM utilisateurs
         WHERE id = :id
           AND role = 'employe'
         LIMIT 1"
    );
    $employeCible->execute(['id' => $employeId]);
    $employeCible = $employeCible->fetch(PDO::FETCH_ASSOC);

    $requete = $pdo->prepare(
        "DELETE FROM utilisateurs
         WHERE id = :id
           AND role = 'employe'"
    );

    $requete->execute(['id' => $employeId]);

    if ($requete->rowCount() > 0 && $employeCible) {
        enregistrerLogMongoDB(
            'SUPPRESSION_EMPLOYE',
            'Suppression du compte employé "' . $employeCible['pseudo'] . '" (ancien ID ' . $employeId . ').',
            (int) $_SESSION['user_id']
        );
    }

    header('Location: administration.php?succes=suppression');
    exit;
}

/* ==================================================
   MESSAGES
================================================== */

if (isset($_GET['succes'])) {
    switch ($_GET['succes']) {
        case 'creation':
            $succes = 'Le compte employé a bien été créé.';
            break;

        case 'motdepasse':
            $succes = 'Le mot de passe de l’employé a bien été modifié.';
            break;

        case 'suspension':
            $succes = 'Le statut de l’employé a bien été modifié.';
            break;

        case 'suppression':
            $succes = 'Le compte employé a bien été supprimé.';
            break;
    }
}

/* ==================================================
   LISTE DES EMPLOYÉS
================================================== */

$requeteEmployes = $pdo->query(
    "SELECT id, pseudo, email, suspendu
     FROM utilisateurs
     WHERE role = 'employe'
     ORDER BY pseudo ASC"
);

$employes = $requeteEmployes->fetchAll(PDO::FETCH_ASSOC);

/* ==================================================
   JOURNAL D'ACTIVITÉ MONGODB
================================================== */

$logsAdministration = [];
$erreurLogs = '';

try {
    $managerMongo = new MongoDB\Driver\Manager('mongodb://localhost:27017');

    $optionsLogs = [
        'sort' => ['date' => -1],
        'limit' => 100
    ];

    $requeteLogs = new MongoDB\Driver\Query([], $optionsLogs);

    $curseurLogs = $managerMongo->executeQuery(
        'fantasyrealm_logs.logs',
        $requeteLogs
    );

    foreach ($curseurLogs as $log) {
        $logsAdministration[] = $log;
    }
} catch (Throwable $exception) {
    $erreurLogs = 'Le journal d’activité est temporairement indisponible.';
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
        Administration - FantasyRealm Online
    </title>

    <link
        rel="stylesheet"
        href="assets/CSS/style.css"
    >
</head>

<body class="administration-page">

<?php include 'navbar.php'; ?>

<main class="admin-dashboard">

    <section class="admin-heading">

        <p class="admin-small-title">
            PIXELVERSE STUDIOS
        </p>

        <h1>
            ADMINISTRATION
        </h1>

        <p>
            Gestion des comptes employés de FantasyRealm Online.
        </p>

    </section>

    <?php if ($erreur !== '') { ?>

        <div class="error-message">
            <?php echo htmlspecialchars($erreur); ?>
        </div>

    <?php } ?>

    <?php if ($succes !== '') { ?>

        <div class="success-message">
            <?php echo htmlspecialchars($succes); ?>
        </div>

    <?php } ?>

    <section class="admin-section">

        <div class="admin-section-title">
            <div>
                <span>NOUVEAU MEMBRE</span>
                <h2>CRÉER UN EMPLOYÉ</h2>
            </div>
        </div>

        <form
            method="POST"
            class="admin-create-form"
        >

            <div>
                <label for="pseudo">
                    Pseudo
                </label>

                <input
                    type="text"
                    id="pseudo"
                    name="pseudo"
                    maxlength="50"
                    required
                >
            </div>

            <div>
                <label for="email">
                    Adresse e-mail
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    required
                >
            </div>

            <div>
                <label for="mot_de_passe">
                    Mot de passe
                </label>

                <input
                    type="password"
                    id="mot_de_passe"
                    name="mot_de_passe"
                    minlength="8"
                    required
                >
            </div>

            <button
                type="submit"
                name="creer_employe"
            >
                CRÉER L'EMPLOYÉ
            </button>

        </form>

    </section>

    <section class="admin-section">

        <div class="admin-section-title">

            <div>
                <span>ÉQUIPE PIXELVERSE</span>
                <h2>COMPTES EMPLOYÉS</h2>
            </div>

            <input
                type="search"
                id="employee-search"
                placeholder="Rechercher un employé..."
            >

        </div>

        <div
            class="admin-employees"
            id="employee-list"
        >

            <?php if (count($employes) === 0) { ?>

                <p class="admin-empty">
                    Aucun compte employé pour le moment.
                </p>

            <?php } ?>

            <?php foreach ($employes as $employe) { ?>

                <article
                    class="admin-employee-card"
                    data-search="<?php
                        echo htmlspecialchars(
                            mb_strtolower(
                                $employe['pseudo']
                                . ' '
                                . $employe['email']
                            )
                        );
                    ?>"
                >

                    <div class="admin-employee-info">

                        <div>
                            <span class="admin-role">
                                EMPLOYÉ
                            </span>

                            <h3>
                                <?php
                                echo htmlspecialchars(
                                    $employe['pseudo']
                                );
                                ?>
                            </h3>

                            <p>
                                <?php
                                echo htmlspecialchars(
                                    $employe['email']
                                );
                                ?>
                            </p>
                        </div>

                        <span class="<?php
                            echo (int) $employe['suspendu'] === 1
                                ? 'admin-status suspended'
                                : 'admin-status active';
                        ?>">
                            <?php
                            echo (int) $employe['suspendu'] === 1
                                ? 'SUSPENDU'
                                : 'ACTIF';
                            ?>
                        </span>

                    </div>

                    <div class="admin-employee-actions">

                        <form method="POST">

                            <input
                                type="hidden"
                                name="employe_id"
                                value="<?php
                                    echo (int) $employe['id'];
                                ?>"
                            >

                            <label>
                                Nouveau mot de passe
                            </label>

                            <div class="admin-password-row">

                                <input
                                    type="password"
                                    name="nouveau_mot_de_passe"
                                    minlength="8"
                                    placeholder="Nouveau mot de passe"
                                    required
                                >

                                <button
                                    type="submit"
                                    name="modifier_mot_de_passe"
                                >
                                    MODIFIER
                                </button>

                            </div>

                        </form>

                        <div class="admin-action-row">

                            <form method="POST">

                                <input
                                    type="hidden"
                                    name="employe_id"
                                    value="<?php
                                        echo (int) $employe['id'];
                                    ?>"
                                >

                                <button
                                    type="submit"
                                    name="changer_suspension"
                                    class="admin-secondary-button"
                                >
                                    <?php
                                    echo (int) $employe['suspendu'] === 1
                                        ? 'RÉACTIVER'
                                        : 'SUSPENDRE';
                                    ?>
                                </button>

                            </form>

                            <form
                                method="POST"
                                class="admin-delete-form"
                                data-employee="<?php
                                    echo htmlspecialchars(
                                        $employe['pseudo']
                                    );
                                ?>"
                            >

                                <input
                                    type="hidden"
                                    name="employe_id"
                                    value="<?php
                                        echo (int) $employe['id'];
                                    ?>"
                                >

                                <button
                                    type="submit"
                                    name="supprimer_employe"
                                    class="admin-delete-button"
                                >
                                    SUPPRIMER
                                </button>

                            </form>

                        </div>

                    </div>

                </article>

            <?php } ?>

        </div>

    </section>


    <section class="admin-section admin-logs-section">

        <div class="admin-section-title">
            <div>
                <span>TRAÇABILITÉ</span>
                <h2>JOURNAL D'ACTIVITÉ</h2>
            </div>

            <input
                type="search"
                id="log-search"
                placeholder="Rechercher dans les logs..."
            >
        </div>

        <p class="admin-log-intro">
            Les 100 actions les plus récentes enregistrées dans MongoDB.
        </p>

        <?php if ($erreurLogs !== '') { ?>
            <div class="error-message">
                <?php echo htmlspecialchars($erreurLogs); ?>
            </div>
        <?php } elseif (count($logsAdministration) === 0) { ?>
            <p class="admin-empty">
                Aucune action enregistrée pour le moment.
            </p>
        <?php } else { ?>

            <div class="admin-logs-list" id="admin-logs-list">

                <?php foreach ($logsAdministration as $log) { ?>

                    <?php
                    $actionLog = isset($log->action)
                        ? (string) $log->action
                        : 'ACTION';

                    $detailsLog = isset($log->details)
                        ? (string) $log->details
                        : 'Aucun détail disponible.';

                    $auteurLog = isset($log->utilisateur_id)
                        && $log->utilisateur_id !== null
                            ? '#' . (int) $log->utilisateur_id
                            : 'SYSTÈME';

                    $dateLog = 'Date inconnue';

                    if (
                        isset($log->date) &&
                        $log->date instanceof MongoDB\BSON\UTCDateTime
                    ) {
                        $dateObjet = $log->date->toDateTime();
                        $dateObjet->setTimezone(
                            new DateTimeZone('Europe/Paris')
                        );
                        $dateLog = $dateObjet->format('d/m/Y à H:i:s');
                    }

                    $rechercheLog = mb_strtolower(
                        $actionLog
                        . ' '
                        . $detailsLog
                        . ' '
                        . $auteurLog
                        . ' '
                        . $dateLog
                    );
                    ?>

                    <article
                        class="admin-log-card"
                        data-search="<?php
                            echo htmlspecialchars(
                                $rechercheLog,
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                    >
                        <div class="admin-log-top">
                            <span class="admin-log-action">
                                <?php
                                echo htmlspecialchars(
                                    str_replace('_', ' ', $actionLog)
                                );
                                ?>
                            </span>

                            <time>
                                <?php echo htmlspecialchars($dateLog); ?>
                            </time>
                        </div>

                        <p>
                            <?php echo htmlspecialchars($detailsLog); ?>
                        </p>

                        <span class="admin-log-author">
                            AUTEUR : <?php echo htmlspecialchars($auteurLog); ?>
                        </span>
                    </article>

                <?php } ?>

            </div>

            <p
                class="admin-empty"
                id="admin-log-no-result"
                style="display: none;"
            >
                Aucun log ne correspond à cette recherche.
            </p>

        <?php } ?>

    </section>

</main>

<!-- MODAL DE CONFIRMATION -->

<div
    class="admin-confirm-overlay"
    id="admin-confirm-overlay"
    aria-hidden="true"
>

    <div
        class="admin-confirm-modal"
        role="dialog"
        aria-modal="true"
        aria-labelledby="admin-confirm-title"
    >

        <span class="admin-modal-small">
            ACTION DÉFINITIVE
        </span>

        <h2 id="admin-confirm-title">
            SUPPRIMER CET EMPLOYÉ ?
        </h2>

        <p id="admin-confirm-text">
            Cette action est irréversible.
        </p>

        <div class="admin-confirm-actions">

            <button
                type="button"
                id="admin-cancel-delete"
                class="admin-secondary-button"
            >
                ANNULER
            </button>

            <button
                type="button"
                id="admin-confirm-delete"
                class="admin-delete-button"
            >
                SUPPRIMER
            </button>

        </div>

    </div>

</div>

<script>

/* RECHERCHE */

const employeeSearch =
    document.querySelector('#employee-search');

const employeeCards =
    document.querySelectorAll('.admin-employee-card');

if (employeeSearch) {

    employeeSearch.addEventListener(
        'input',
        function () {

            const search =
                employeeSearch.value
                    .toLowerCase()
                    .trim();

            employeeCards.forEach(
                function (card) {

                    const content =
                        card.dataset.search || '';

                    card.style.display =
                        content.includes(search)
                            ? ''
                            : 'none';
                }
            );
        }
    );
}


/* RECHERCHE DANS LES LOGS */

const logSearch =
    document.querySelector('#log-search');

const logCards =
    document.querySelectorAll('.admin-log-card');

const logNoResult =
    document.querySelector('#admin-log-no-result');

if (logSearch) {

    logSearch.addEventListener(
        'input',
        function () {

            const search =
                logSearch.value
                    .toLowerCase()
                    .trim();

            let visibleLogs = 0;

            logCards.forEach(
                function (card) {

                    const content =
                        card.dataset.search || '';

                    const visible =
                        content.includes(search);

                    card.style.display =
                        visible ? '' : 'none';

                    if (visible) {
                        visibleLogs++;
                    }
                }
            );

            if (logNoResult) {
                logNoResult.style.display =
                    visibleLogs === 0
                        ? ''
                        : 'none';
            }
        }
    );
}


/* MODAL DE SUPPRESSION */

const adminDeleteForms =
    document.querySelectorAll('.admin-delete-form');

const adminOverlay =
    document.querySelector('#admin-confirm-overlay');

const adminCancelDelete =
    document.querySelector('#admin-cancel-delete');

const adminConfirmDelete =
    document.querySelector('#admin-confirm-delete');

const adminConfirmText =
    document.querySelector('#admin-confirm-text');

let pendingAdminDeleteForm = null;
let pendingAdminDeleteButton = null;

adminDeleteForms.forEach(
    function (form) {

        form.addEventListener(
            'submit',
            function (event) {

                event.preventDefault();

                pendingAdminDeleteForm = form;
                pendingAdminDeleteButton = event.submitter;

                const employeeName =
                    form.dataset.employee || 'cet employé';

                adminConfirmText.textContent =
                    'Le compte de '
                    + employeeName
                    + ' sera définitivement supprimé.';

                adminOverlay.classList.add('open');
                adminOverlay.setAttribute(
                    'aria-hidden',
                    'false'
                );
            }
        );
    }
);

adminCancelDelete.addEventListener(
    'click',
    function () {

        pendingAdminDeleteForm = null;
        pendingAdminDeleteButton = null;

        adminOverlay.classList.remove('open');
        adminOverlay.setAttribute(
            'aria-hidden',
            'true'
        );
    }
);

adminConfirmDelete.addEventListener(
    'click',
    function () {

        if (
            pendingAdminDeleteForm &&
            pendingAdminDeleteButton
        ) {
            const actionField =
                document.createElement('input');

            actionField.type = 'hidden';
            actionField.name =
                pendingAdminDeleteButton.name;

            actionField.value =
                pendingAdminDeleteButton.value || '1';

            pendingAdminDeleteForm.appendChild(
                actionField
            );

            pendingAdminDeleteForm.submit();
        }
    }
);

</script>

</body>
</html>
