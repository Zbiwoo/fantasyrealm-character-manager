<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>FantasyRealm Online</title>

    <link
        rel="stylesheet"
        href="assets/CSS/style.css"
    >

</head>


<body class="home-page">

<?php include 'navbar.php'; ?>

    <main>


        <!-- HERO -->

        <section class="home-hero">


            <div class="home-hero-content">


                <h1>
                    FANTASYREALM ONLINE
                </h1>


                <p>
                    Créez votre légende. Façonnez votre héros.
                    Entrez dans le royaume.
                </p>


                <div class="home-hero-buttons">


                    <a href="creer-personnage.php">
                        COMMENCER
                    </a>


                    <a href="personnages.php">
                        PERSONNAGES
                    </a>


                </div>


            </div>


        </section>


        <!-- PRESENTATION -->

        <section class="home-presentation">


            <h2>
                PIXELVERSE STUDIOS
            </h2>


            <p>
                PixelVerse Studios est un studio indépendant spécialisé
                dans la création d'expériences immersives et communautaires.
            </p>


            <p>
                Avec FantasyRealm Online, notre ambition est de proposer
                un univers riche où chaque joueur peut créer, personnaliser
                et partager son propre héros.
            </p>


        </section>


        <!-- POURQUOI NOUS CHOISIR -->

        <section class="home-advantages">


            <h2>
                POURQUOI NOUS CHOISIR ?
            </h2>


            <div class="home-advantages-container">


                <!-- CRÉEZ -->

                <article class="home-advantage">


                    <div class="home-advantage-icon">
                        ⚔
                    </div>


                    <h3>
                        CRÉEZ
                    </h3>


                    <p>
                        Donnez vie à votre héros et façonnez son identité.
                    </p>


                    <p class="advantage-more">
                        Choisissez son apparence, son genre et donnez-lui
                        un nom qui lui correspond.
                    </p>


                </article>


                <!-- PERSONNALISEZ -->

                <article class="home-advantage">


                    <div class="home-advantage-icon">
                        ✨
                    </div>


                    <h3>
                        PERSONNALISEZ
                    </h3>


                    <p>
                        Modifiez chaque détail pour créer un personnage unique.
                    </p>


                    <p class="advantage-more">
                        Modifiez son visage, sa coiffure, ses yeux et ses
                        couleurs pour créer un personnage vraiment unique.
                    </p>


                </article>


                <!-- PARTAGEZ -->

                <article class="home-advantage">


                    <div class="home-advantage-icon">
                        ♙
                    </div>


                    <h3>
                        PARTAGEZ
                    </h3>


                    <p>
                        Présentez vos créations à toute la communauté.
                    </p>


                    <p class="advantage-more">
                        Publiez votre création et présentez votre personnage
                        à toute la communauté FantasyRealm.
                    </p>


                </article>


            </div>


        </section>


    </main>


    <!-- FOOTER -->

    <footer>


        <p>
            © 2026 PixelVerse Studios
        </p>


       <div class="footer-links">

    <a href="mentions-legales.php">
        Mentions légales
    </a>

    <a href="cgv.php">
        CGV
    </a>

</div>

    </footer>


</body>

</html>
