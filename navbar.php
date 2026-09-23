<header class="home-header">

    <a href="index.php" class="logo">
        PIXELUNIVERSE
    </a>

    <nav>

        <a href="index.php">
            ACCUEIL
        </a>

        <a href="personnages.php">
            PERSONNAGES
        </a>

        <a href="contact.php">
            CONTACT
        </a>

        <?php if (isset($_SESSION['user_id'])) { ?>

            <?php if (
                isset($_SESSION['role']) &&
                $_SESSION['role'] === 'utilisateur'
            ) { ?>

                <a href="mes-personnages.php">
                    MES PERSONNAGES
                </a>

            <?php } ?>

            <a
                href="profil.php?id=<?php echo $_SESSION['user_id']; ?>"
                class="user-name"
            >
                <?php echo htmlspecialchars($_SESSION['pseudo']); ?>
            </a>

            <?php if (
                isset($_SESSION['role']) &&
                (
                    $_SESSION['role'] === 'employe' ||
                    $_SESSION['role'] === 'admin'
                )
            ) { ?>

                <a href="validation-personnages.php">
                    ESPACE EMPLOYÉ
                </a>

            <?php } ?>

            <?php if (
                isset($_SESSION['role']) &&
                $_SESSION['role'] === 'admin'
            ) { ?>

                <a href="administration.php">
                    ADMINISTRATION
                </a>

            <?php } ?>

            <a href="deconnexion.php">
                DÉCONNEXION
            </a>

        <?php } else { ?>

            <a href="connexion.php">
                CONNEXION
            </a>

        <?php } ?>

    </nav>

</header>

