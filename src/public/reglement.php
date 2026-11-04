<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/reglement.css">

    <meta
        name="description"
        content="Découvre les règles de FOOTIX : pronostique les résultats, vise le score exact et défie tes amis.">

    <title>FOOTIX — Règlement</title>
</head>

<body class="reglement">

    <!-- En-tête -->
    <header class="header">
        <a href="index.php" class="logo">
            <img src="../images/Logo_trophee.png" alt="FOOTIX">
        </a>

        <nav>
            <ul>
                <li>
                    <a href="./pronostics.php">Pronostics</a>
                </li>
                <li>
                    <a href="./classements.php">Classements</a>
                </li>
                <li>
                    <a href="./reglement.php">Règlement</a>
                </li>
                <li>
                    <a href="./battles.php">Battles</a>
                </li>
            </ul>
        </nav>

        <a href="connexion.php">Connexion</a>
    </header>

    <main>

        <!-- Présentation -->
        <section class="hero">
            <img
                src="../images/arbitre.webp" alt="" class="hero-image">

            <div class="hero-contenu">
                <p>Pronostique. Vibre. Domine.</p>

                <h1 id="titre-reglement">Règlement</h1>

                <p>
                    Maîtrisez les règles du jeu et transformez vos
                    pronostics en victoires.
                </p>
            </div>
        </section>

        <!-- Règles du jeu -->
        <div class="reglement-contenu">

            <!-- Choisir l'issue du match -->
            <section class="reglement-etape">
                <h2 id="titre-issue">Choisis l’issue</h2>

                <div class="reglement-etape-contenu">
                    <p class="reglement-exemple">1 N 2</p>

                    <p class="reglement-description">
                        Victoire à domicile, match nul ou victoire à
                        l’extérieur. Fait ton choix !
                    </p>
                </div>
            </section>

            <!-- Pronostiquer le score exact -->
            <section
                class="reglement-etape reglement-etape-inversee">
                <h2 id="titre-score">Vise le score</h2>

                <div class="reglement-etape-contenu">
                    <p class="reglement-description">
                        Pour aller plus loin prédis le score exact et
                        remporte des points supplémentaires
                    </p>

                    <p class="reglement-exemple">3 - 1</p>
                </div>
            </section>

            <!-- Gagner des points et progresser -->
            <section
                class="reglement-etape">
                <h2 id="titre-progression">Grimpe au sommet</h2>

                <div class="reglement-etape-contenu">
                    <img
                        src="../images/Winner.png"
                        alt=""
                        class="reglement-illustration">

                    <p class="reglement-description">
                        Accumule des points, débloque des titres et
                        défie tes amis en battle
                    </p>
                </div>
            </section>

        </div>
    </main>

    <!-- Pied de page -->
    <footer class="footer">
        <a href="index.php" class="logo">
            <img src="../images/Logo_trophee.png" alt="FOOTIX">
        </a>

        <nav>
            <ul>
                <li>
                    <a href="reglement.php">
                        Règlement
                    </a>
                </li>
                <li>
                    <a href="contact.php">Contact</a>
                </li>
                <li>
                    <a href="connexion.php">Connexion</a>
                </li>
            </ul>
        </nav>
    </footer>

</body>

</html>
