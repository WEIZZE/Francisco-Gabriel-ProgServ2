<?php

declare(strict_types=1);

const DATABASE_CONFIGURATION_FILE = __DIR__ . '/../config/database.ini';

$config = parse_ini_file(DATABASE_CONFIGURATION_FILE, true);

if (!$config) {
    throw new Exception("Erreur lors de la lecture du fichier de configuration : " . DATABASE_CONFIGURATION_FILE);
}

$host = $config['host'];
$port = $config['port'];
$database = $config['database'];
$username = $config['username'];
$password = $config['password'];

$pdo = new PDO("mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4", $username, $password);

$pdo->exec("SET time_zone = '+00:00'");


// Échapper les valeurs affichées dans le HTML.
function e(mixed $valeur): string {
    return htmlspecialchars(
        (string) $valeur,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}

// Définir la journée actuelle à l’heure suisse.
$utc = new DateTimeZone('UTC');
$fuseauSuisse = new DateTimeZone('Europe/Zurich');

$debutJour = new DateTimeImmutable('today', $fuseauSuisse);
$finJour = $debutJour->modify('+1 day');

// Convertir les limites en UTC pour les comparer aux données.
$debutJourUTC = $debutJour
    ->setTimezone($utc)
    ->format('Y-m-d H:i:s');

$finJourUTC = $finJour
    ->setTimezone($utc)
    ->format('Y-m-d H:i:s');

// Charger les matchs du jour encore ouverts aux pronostics.
$requete = $pdo->prepare(
    "SELECT
        m.id,
        m.id_competition,
        m.date,
        m.heure_debut,
        d.nom AS domicile,
        e.nom AS exterieur
     FROM matchs AS m
     JOIN equipes AS d ON d.id = m.id_equipe_d
     JOIN equipes AS e ON e.id = m.id_equipe_e
     JOIN statut_match AS s ON s.id = m.id_statut_match
     WHERE m.id_competition IN (2001, 2021, 2014)
       AND s.libelle = 'a venir'
       AND TIMESTAMP(m.date, m.heure_debut) >= ?
       AND TIMESTAMP(m.date, m.heure_debut) < ?
       AND TIMESTAMP(m.date, m.heure_debut)
           > DATE_ADD(UTC_TIMESTAMP(), INTERVAL 5 MINUTE)
     ORDER BY m.date, m.heure_debut, m.id"
);

$requete->execute([
    $debutJourUTC,
    $finJourUTC,
]);

$matchs = $requete->fetchAll();

// Préparer les trois sections.
$competitions = [
    2001 => [
        'nom' => 'Champions League',
        'matchs' => [],
    ],
    2021 => [
        'nom' => 'Premier League',
        'matchs' => [],
    ],
    2014 => [
        'nom' => 'LaLiga — Primera Division',
        'matchs' => [],
    ],
];

// Répartir les matchs dans leur compétition.
foreach ($matchs as $match) {
    $idCompetition = (int) $match['id_competition'];
    $competitions[$idCompetition]['matchs'][] = $match;
}

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mes pronostics</title>
</head>

<body>
    <main>
        <h1>
            Matchs du <?= e($debutJour->format('d/m/Y')) ?>
        </h1>

        <p>Horaires affichés à l’heure suisse.</p>
        <p>Seuls les matchs encore ouverts aux pronostics sont affichés.</p>

        <?php foreach ($competitions as $competition): ?>
            <section>
                <h2><?= e($competition['nom']) ?></h2>

                <?php if ($competition['matchs'] === []): ?>
                    <p>Il n'y a pas de matchs aujourd'hui</p>
                <?php else: ?>
                    <?php foreach ($competition['matchs'] as $match): ?>
                        <?php
                        $debut = (new DateTimeImmutable(
                            $match['date'] . ' ' . $match['heure_debut'],
                            $utc
                        ))->setTimezone($fuseauSuisse);

                        $cloture = $debut->modify('-5 minutes');
                        ?>

                        <article>
                            <h3>
                                <?= e($match['domicile']) ?>
                                — <?= e($match['exterieur']) ?>
                            </h3>

                            <p>
                                Coup d’envoi :
                                <?= e($debut->format('d/m/Y à H:i')) ?>
                            </p>

                            <p>
                                Clôture des pronostics :
                                <?= e($cloture->format('H:i')) ?>
                            </p>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </section>
        <?php endforeach; ?>
    </main>
</body>

</html>
