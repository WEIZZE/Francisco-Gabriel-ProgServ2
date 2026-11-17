<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/classement.css">

    <meta
        name="description"
        content="Consulte le classement des meilleurs pronostiqueurs sur FOOTIX.">

    <title>FOOTIX — Classement</title>
</head>

<body class="classements">

    <!-- En-tête -->
    <header class="header">
        <a href="index.php" class="logo">
            <img src="../images/Logo_trophee.png" alt="FOOTIX">
        </a>

        <nav>
            <ul>
                <li><a href="./index.php">Accueil</a></li>
                <li><a href="pronostics.php">Pronostics</a></li>
                <li><a href="reglement.php">Règlement</a></li>
            </ul>
        </nav>

        <a href="connexion.php">Connexion</a>
    </header>

    <main>

        <!-- Présentation -->
        <section class="hero">
            <img
                src="../images/Podium.avif"
                alt=""
                class="hero-image">

            <p class="hero-slogan">Pronostique. Vibre. Domine.</p>

            <div class="hero-contenu">
                <h1>Classement</h1>

                <p>
                    Qui sont les vrais experts ? Consultez le
                    classement officiel des meilleurs pronostiqueurs.
                </p>
            </div>
        </section>

        <!-- Classement des joueurs -->
        <div class="classements-contenu">

            <!-- Filtres -->
            <div class="classements-filtres">
                <button type="button" class="actif">Global</button>
                <button type="button">Champions League</button>
                <button type="button">Premier League</button>
                <button type="button">La Liga</button>
            </div>

            <!-- Tableau -->
            <table class="classements-tableau">
                <thead>
                    <tr>
                        <th scope="col">N°</th>
                        <th scope="col">Profil</th>
                        <th scope="col">Points</th>
                        <th scope="col">Rang</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td>01</td>
                        <td>
                            <div class="classements-profil">
                                <span class="profil-avatar">P</span>
                                <span>ProProno_77</span>
                            </div>
                        </td>
                        <td>9'784</td>
                        <td>
                            <img src="../images/RankChallenger.webp" alt="Rang 1">
                        </td>
                    </tr>

                    <tr>
                        <td>02</td>
                        <td>
                            <div class="classements-profil">
                                <span class="profil-avatar">B</span>
                                <span>BetMaster_CH</span>
                            </div>
                        </td>
                        <td>9'327</td>
                        <td>
                            <img src="../images/RankChallenger.webp" alt="Rang 1">
                        </td>
                    </tr>

                    <tr>
                        <td>03</td>
                        <td>
                            <div class="classements-profil">
                                <span class="profil-avatar">W</span>
                                <span>WinStreak_23</span>
                            </div>
                        </td>
                        <td>8'583</td>
                        <td>
                            <img src="../images/RankMaster.webp" alt="Rang 2">
                        </td>
                    </tr>

                    <tr>
                        <td>04</td>
                        <td>
                            <div class="classements-profil">
                                <span class="profil-avatar">O</span>
                                <span>OddHunter</span>
                            </div>
                        </td>
                        <td>8'351</td>
                        <td>
                            <img src="../images/RankMaster.webp" alt="Rang 2">
                        </td>
                    </tr>

                    <tr>
                        <td>05</td>
                        <td>
                            <div class="classements-profil">
                                <span class="profil-avatar">G</span>
                                <span>GoalPredictor</span>
                            </div>
                        </td>
                        <td>7'980</td>
                        <td>
                            <img src="../images/RankGold.webp" alt="Rang 3">
                        </td>
                    </tr>

                    <tr>
                        <td>06</td>
                        <td>
                            <div class="classements-profil">
                                <span class="profil-avatar">L</span>
                                <span>LuckyStrike_99</span>
                            </div>
                        </td>
                        <td>7'593</td>
                        <td>
                            <img src="../images/RankGold.webp" alt="Rang 3">
                        </td>
                    </tr>

                    <tr>
                        <td>07</td>
                        <td>
                            <div class="classements-profil">
                                <span class="profil-avatar">S</span>
                                <span>SmartBet_Alex</span>
                            </div>
                        </td>
                        <td>7'443</td>
                        <td>
                            <img src="../images/RankSilver.webp" alt="Rang 3">
                        </td>
                    </tr>

                    <tr>
                        <td>08</td>
                        <td>
                            <div class="classements-profil">
                                <span class="profil-avatar">T</span>
                                <span>TopTipster</span>
                            </div>
                        </td>
                        <td>6'826</td>
                        <td>
                            <img src="../images/RankSilver.webp" alt="Rang 4">
                        </td>
                    </tr>

                    <tr>
                        <td>09</td>
                        <td>
                            <div class="classements-profil">
                                <span class="profil-avatar">G</span>
                                <span>GameAnalyst_42</span>
                            </div>
                        </td>
                        <td>6'535</td>
                        <td>
                            <img src="../images/RankIronwebp.webp" alt="Rang 4">
                        </td>
                    </tr>

                    <tr>
                        <td>10</td>
                        <td>
                            <div class="classements-profil">
                                <span class="profil-avatar">V</span>
                                <span>VictoryVault</span>
                            </div>
                        </td>
                        <td>6'323</td>
                        <td>
                            <img src="../images/RankIronwebp.webp" alt="Rang 4">
                        </td>
                    </tr>
                </tbody>
            </table>

        </div>
    </main>

    <!-- Pied de page -->
    <footer class="footer">
        <a href="index.php" class="logo">
            <img src="../images/Logo_trophee.png" alt="FOOTIX">
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
