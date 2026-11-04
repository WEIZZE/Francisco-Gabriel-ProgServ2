<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/accueil.css">
    <meta name="description" content="Fais tes pronostics de football et défie tes amis sur FOOTIX.">
    <title>FOOTIX — Accueil</title>
</head>

<body class="accueil">

    <!-- En-tête -->
    <header class="header">
        <a href="index.php" class="logo">
            <img src="../images/Logo_trophee.png" alt="FOOTIX">
        </a>

        <nav>
            <ul>
                <li><a href="./pronostics.php">Pronostics</a></li>
                <li><a href="./classements.php">Classements</a></li>
                <li><a href="./reglement.php">Règlement</a></li>
                <li><a href="./battles.php">Battles</a></li>
            </ul>
        </nav>

        <a href="connexion.php">Connexion</a>
    </header>

    <main>

        <!-- Présentation -->
        <section class="hero" aria-labelledby="titre-accueil">

            <img src="../images/Accueil_hero.jpg" alt="" class="hero-image">

            <p class="hero-slogan">Pronostique. Vibre. Domine.</p>

            <div class="hero-contenu">
                <h1 id="titre-accueil">
                    Le foot se joue <span>avant le coup d’envoi</span>
                </h1>

                <div class="hero-presentation">
                    <p>
                        Fais tes pronostics, gagne des points,
                        et deviens une légende des championnats.
                    </p>

                    <div class="hero-actions">
                        <a href="inscription.php">Inscription</a>
                        <a href="connexion.php">Connexion</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Match à la une -->
        <section class="match-section">
            <header>
                <h2 id="titre-match">À toi de jouer.</h2>

                <p>Fais tes pronostics, gagne des points, et deviens une légende des championnats.</p>
            </header>

            <article class="match">
                <header>
                    <h3>FC Porto contre FC Barcelone</h3>
                    <p>Mardi 18 mars</p>
                    <p>21:00</p>
                </header>

                <div class="match-equipes">
                    <div class="equipe">
                        <img src="../images/FCPorto.webp" alt="Écusson du FC Porto">
                        <p>FC Porto</p>
                    </div>

                    <p class="match-score">
                        <span>-</span>
                        <span>VS</span>
                        <span>-</span>
                    </p>

                    <div class="equipe">
                        <img src="../images/FC_Barcelona.svg.webp" alt="Écusson du FC Barcelone">
                        <p>FC Barcelone</p>
                    </div>
                </div>

                <ul class="match-issues">
                    <li>1 — Victoire du FC Porto</li>
                    <li>N — Match nul</li>
                    <li>2 — Victoire du FC Barcelone</li>
                </ul>

                <a href="pronostics.php">Pronostique maintenant</a>
            </article>
        </section>

        <!-- Aperçu du classement -->
        <section class="classement-section">
            <div class="classement-presentation">
                <h2 id="titre-classement">Qui lit le mieux le jeu ?</h2>

                <p>
                    Chaque bon choix te rapproche du sommet.
                    Les scores exacts font la différence entre
                    les bons et les légendes.
                </p>

                <a href="classements.php">
                    Voir le classement complet →
                </a>
            </div>

            <table class="classement-tableau">
                <caption>Classement global — aperçu</caption>

                <thead>
                    <tr>
                        <th scope="col">Position</th>
                        <th scope="col">Joueur</th>
                        <th scope="col">Points</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td>1</td>
                        <th scope="row">Nico10</th>
                        <td>5 420</td>
                    </tr>

                    <tr>
                        <td>2</td>
                        <th scope="row">TheCoach</th>
                        <td>4 980</td>
                    </tr>

                    <tr>
                        <td>3</td>
                        <th scope="row">MayaGoal</th>
                        <td>4 615</td>
                    </tr>

                    <tr>
                        <td>28</td>
                        <th scope="row">Vous</th>
                        <td>1 840</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <!-- Fonctionnement -->
        <section class="fonctionnement">
            <h2 id="titre-fonctionnement">
                Simple à jouer.<br>
                Dur à maîtriser.
            </h2>

            <ol class="fonctionnement-etapes">
                <li>
                    <p>1 N 2</p>
                    <h3>Choisis l’issue</h3>
                    <p>Victoire à domicile, match nul ou victoire à l’extérieur.</p>
                </li>

                <li>
                    <p>3 - 1</p>
                    <h3>Vise le score</h3>
                    <p>Affûte ton pronostic avec un score exact pour remporter un maximum de points.</p>
                </li>

                <li>
                    <img src="../images/Winner.png" id="Winner">
                    <h3>Grimpe au sommet</h3>
                    <p>Accumule des points et débloque des titres.</p>
                </li>
            </ol>
        </section>
    </main>

    <!-- Pied de page -->
    <footer class="footer">
        <a href="index.php">
            <img src="../images/Logo_trophee.png" class="logo">
        </a>

        <nav>
            <ul>
                <li><a href="reglement.php">Règlement</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="connexion.php">Connexion</a></li>
            </ul>
        </nav>
    </footer>

</body>

</html>
