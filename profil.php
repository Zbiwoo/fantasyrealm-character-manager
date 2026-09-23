<?php

session_start();
require_once 'config.php';

/* Vérifier qu'un utilisateur est demandé */
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$idUtilisateur = $_GET['id'];


/* Récupérer l'utilisateur */
$requeteUtilisateur = $pdo->prepare(
    "SELECT id, pseudo
     FROM utilisateurs
     WHERE id = :id"
);

$requeteUtilisateur->execute([
    'id' => $idUtilisateur
]);

$profil = $requeteUtilisateur->fetch(PDO::FETCH_ASSOC);


/* Si l'utilisateur n'existe pas */
if (!$profil) {
    header('Location: index.php');
    exit;
}


/* Récupérer ses personnages publics */
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

$personnages = $requetePersonnages->fetchAll(PDO::FETCH_ASSOC);

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
        Profil de <?php echo htmlspecialchars($profil['pseudo']); ?> - FantasyRealm
    </title>

    <link
        rel="stylesheet"
        href="assets/CSS/style.css"
    >

</head>

<body>

<?php include 'navbar.php'; ?>

<main class="my-characters-page">

    <h1>
        PROFIL DE
        <?php echo htmlspecialchars($profil['pseudo']); ?>
    </h1>

    <p>
        Personnages partagés par
        <strong>
            @<?php echo htmlspecialchars($profil['pseudo']); ?>
        </strong>
    </p>


    <section class="characters-grid">

        <?php if (count($personnages) > 0) { ?>


            <?php foreach ($personnages as $personnage) { ?>

                <article class="character-card">

                    <div class="character-image">
                        IMAGE DU PERSONNAGE
                    </div>

                    <div class="character-info">

                        <h2>
                            <?php echo htmlspecialchars($personnage['nom']); ?>
                        </h2>

                        <p>
                            Genre :
                            <?php echo htmlspecialchars($personnage['genre']); ?>
                        </p>

                        <a href="personnage.php?id=<?php echo $personnage['id']; ?>">
                            VOIR LE PERSONNAGE
                        </a>

                    </div>

                </article>

            <?php } ?>


        <?php } else { ?>

            <p>
                Cet utilisateur n'a aucun personnage partagé.
            </p>

        <?php } ?>

    </section>

</main>


</body>

</html>
