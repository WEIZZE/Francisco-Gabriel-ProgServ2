<?php

declare(strict_types=1);

session_start();

/*
 * À adapter à ta page de connexion.
 * Après une connexion réussie, elle doit définir :
 * $_SESSION['id_utilisateur'] = (int) $utilisateur['id'];
 */
$idUtilisateur = filter_var(
    $_SESSION['id_utilisateur'] ?? null,
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);

if ($idUtilisateur === false || $idUtilisateur === null) {
    $idUtilisateur = null;
}

$estConnecte = $idUtilisateur !== null;

// Configuration identique à celle du script de synchronisation.
// À centraliser ensuite dans un fichier de configuration.
$host = 'mariadb';
$base = 'my_database';
$utilisateurDB = 'username';
$motDePasseDB = 'password';

$utc = new DateTimeZone('UTC');
$fuseauAffichage = new DateTimeZone('Europe/Zurich');

function e(mixed $valeur): string {
    return htmlspecialchars(
        (string) $valeur,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}

function champPost(string $nom): string {
    $valeur = $_POST[$nom] ?? '';

    if (!is_string($valeur)) {
        throw new InvalidArgumentException('Formulaire invalide.');
    }

    return trim($valeur);
}

function lireScore(string $valeur): ?int {
    if ($valeur === '') {
        return null;
    }

    // Limite applicative : entre 0 et 99.
    if (!preg_match('/\A[0-9]{1,2}\z/', $valeur)) {
        throw new InvalidArgumentException(
            'Chaque score doit être un entier entre 0 et 99.'
        );
    }

    return (int) $valeur;
}

function dateMatch(
    array $match,
    DateTimeZone $utc,
    DateTimeZone $fuseau
): DateTimeImmutable {
    return (new DateTimeImmutable(
        $match['date'] . ' ' . $match['heure_debut'],
        $utc
    ))->setTimezone($fuseau);
}

try {
    $pdo = new PDO(
        "mysql:host=$host;port=3306;dbname=$base;charset=utf8mb4",
        $utilisateurDB,
        $motDePasseDB,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );

    $pdo->exec("SET time_zone = '+00:00'");

    // Vérifier le compte uniquement si la session contient un identifiant.
    if ($estConnecte) {
        $requete = $pdo->prepare(
            'SELECT id FROM utilisateur WHERE id = ?'
        );
        $requete->execute([$idUtilisateur]);

        if ($requete->fetchColumn() === false) {
            // Le compte n’existe plus : afficher la page en visiteur.
            unset($_SESSION['id_utilisateur']);

            $idUtilisateur = null;
            $estConnecte = false;
        }
    }
} catch (PDOException $erreur) {
    error_log((string) $erreur);
    http_response_code(500);
    exit('Connexion à la base de données impossible.');
}

if (
    !isset($_SESSION['csrf_pronostics'])
    || !is_string($_SESSION['csrf_pronostics'])
) {
    $_SESSION['csrf_pronostics'] = bin2hex(random_bytes(32));
}

$erreurFormulaire = '';
$message = $_SESSION['message_pronostics'] ?? '';
unset($_SESSION['message_pronostics']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Contrôle obligatoire avant toute écriture en base.
        if (!$estConnecte) {
            http_response_code(403);

            throw new InvalidArgumentException(
                'Tu dois être connecté pour enregistrer un pronostic.'
            );
        }

        $csrf = champPost('csrf');

        if (!hash_equals($_SESSION['csrf_pronostics'], $csrf)) {
            throw new InvalidArgumentException(
                'Formulaire expiré ou invalide. Recharge la page.'
            );
        }

        $idMatch = filter_var(
            champPost('id_match'),
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );

        if ($idMatch === false) {
            throw new InvalidArgumentException('Match invalide.');
        }

        $prediction = champPost('prediction');

        if (!in_array($prediction, ['1', 'N', '2'], true)) {
            throw new InvalidArgumentException(
                'Choisis une victoire à domicile, un nul ou une victoire à l’extérieur.'
            );
        }

        $scoreDomicile = lireScore(champPost('score_domicile'));
        $scoreExterieur = lireScore(champPost('score_exterieur'));

        // Le score exact est facultatif, mais exige deux valeurs.
        if (($scoreDomicile === null) !== ($scoreExterieur === null)) {
            throw new InvalidArgumentException(
                'Renseigne les deux scores ou laisse les deux champs vides.'
            );
        }

        // Le score exact doit correspondre au résultat choisi.
        if ($scoreDomicile !== null && $scoreExterieur !== null) {
            $resultatScore = $scoreDomicile > $scoreExterieur
                ? '1'
                : ($scoreDomicile < $scoreExterieur ? '2' : 'N');

            if ($prediction !== $resultatScore) {
                throw new InvalidArgumentException(
                    'Le score exact ne correspond pas au résultat choisi.'
                );
            }
        }

        $pdo->beginTransaction();

        /*
         * Verrouiller le match pendant la validation et l’écriture.
         * Deux soumissions sur ce match sont ainsi sérialisées.
         */
        $requete = $pdo->prepare(
            'SELECT id
             FROM matchs
             WHERE id = ?
             FOR UPDATE'
        );
        $requete->execute([$idMatch]);

        if ($requete->fetchColumn() === false) {
            throw new InvalidArgumentException('Ce match n’existe pas.');
        }

        $requete = $pdo->prepare(
            'SELECT id
             FROM statut_pronostic
             WHERE libelle = ?'
        );
        $requete->execute(['en attente']);
        $idStatutAttente = $requete->fetchColumn();

        if ($idStatutAttente === false) {
            throw new RuntimeException(
                'Le statut de pronostic « en attente » est absent.'
            );
        }

        // Ne pas modifier un pronostic déjà réglé.
        $requete = $pdo->prepare(
            'SELECT id_statut_pronostic
             FROM pronostic
             WHERE id_utilisateur = ? AND id_match = ?
             FOR UPDATE'
        );
        $requete->execute([$idUtilisateur, $idMatch]);
        $statutExistant = $requete->fetchColumn();

        if (
            $statutExistant !== false
            && (int) $statutExistant !== (int) $idStatutAttente
        ) {
            throw new InvalidArgumentException(
                'Ce pronostic a déjà été traité et ne peut plus être modifié.'
            );
        }

        /*
         * Vérification de la clôture directement dans l’écriture SQL.
         * Même un formulaire ouvert depuis longtemps est refusé
         * s’il est envoyé après la limite.
         *
         * Les points restent à 0 : le barème sera ajouté séparément.
         */
        $requete = $pdo->prepare(
            "INSERT INTO pronostic (
                prediction,
                score_prediction_domicile,
                score_prediction_exterieur,
                points_a_gagner,
                id_statut_pronostic,
                id_utilisateur,
                id_match
             )
             SELECT ?, ?, ?, 0, ?, ?, m.id
             FROM matchs AS m
             JOIN statut_match AS s ON s.id = m.id_statut_match
             WHERE m.id = ?
               AND s.libelle = 'a venir'
               AND TIMESTAMP(m.date, m.heure_debut)
                   > DATE_ADD(UTC_TIMESTAMP(), INTERVAL 5 MINUTE)
             ON DUPLICATE KEY UPDATE
                prediction = VALUES(prediction),
                score_prediction_domicile =
                    VALUES(score_prediction_domicile),
                score_prediction_exterieur =
                    VALUES(score_prediction_exterieur)"
        );

        $requete->execute([
            $prediction,
            $scoreDomicile,
            $scoreExterieur,
            (int) $idStatutAttente,
            $idUtilisateur,
            $idMatch,
        ]);

        if ($requete->rowCount() === 0) {
            throw new InvalidArgumentException(
                'Les pronostics sont fermés pour ce match.'
            );
        }

        $pdo->commit();

        $_SESSION['message_pronostics'] =
            'Ton pronostic a bien été enregistré.';

        // Évite une nouvelle soumission au rafraîchissement.
        header('Location: pronostics.php', true, 303);
        exit;
    } catch (InvalidArgumentException $erreur) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        $erreurFormulaire = $erreur->getMessage();
    } catch (Throwable $erreur) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        error_log((string) $erreur);
        $erreurFormulaire =
            'Enregistrement impossible. Réessaie dans quelques instants.';
    }
}

// Journée actuelle à l’heure suisse.
$debutJourLocal = new DateTimeImmutable(
    'today',
    new DateTimeZone('Europe/Zurich')
);

$finJourLocal = $debutJourLocal->modify('+1 day');

// Les dates des matchs sont enregistrées en UTC.
$debutJourUTC = $debutJourLocal
    ->setTimezone(new DateTimeZone('UTC'))
    ->format('Y-m-d H:i:s');

$finJourUTC = $finJourLocal
    ->setTimezone(new DateTimeZone('UTC'))
    ->format('Y-m-d H:i:s');

// Liste des matchs encore ouverts avec le pronostic de l’utilisateur.
try {
    $requete = $pdo->prepare(
        "SELECT
            m.id,
            m.id_competition,
            m.date,
            m.heure_debut,
            c.nom AS competition,
            d.nom AS domicile,
            e.nom AS exterieur,
            p.prediction,
            p.score_prediction_domicile,
            p.score_prediction_exterieur
         FROM matchs AS m
         JOIN competition AS c ON c.id = m.id_competition
         JOIN equipes AS d ON d.id = m.id_equipe_d
         JOIN equipes AS e ON e.id = m.id_equipe_e
         JOIN statut_match AS s ON s.id = m.id_statut_match
         LEFT JOIN pronostic AS p
            ON p.id_match = m.id
           AND p.id_utilisateur = ?
         WHERE s.libelle = 'a venir'
           AND TIMESTAMP(m.date, m.heure_debut) >= ?
           AND TIMESTAMP(m.date, m.heure_debut) < ?
           AND TIMESTAMP(m.date, m.heure_debut)
               > DATE_ADD(UTC_TIMESTAMP(), INTERVAL 5 MINUTE)
         ORDER BY m.date, m.heure_debut, m.id"
    );

    $requete->execute([
        $idUtilisateur,
        $debutJourUTC,
        $finJourUTC,
    ]);

    $matchs = $requete->fetchAll();
} catch (PDOException $erreur) {
    error_log((string) $erreur);
    http_response_code(500);
    exit('Impossible de charger les matchs du jour.');
}

// Sections toujours affichées, même sans match.
$sectionsCompetitions = [
    2001 => [
        'titre' => 'Champions League (CL)',
        'matchs' => [],
    ],
    2021 => [
        'titre' => 'Premier League (PL)',
        'matchs' => [],
    ],
    2014 => [
        'titre' => 'LaLiga — Primera Division (PD)',
        'matchs' => [],
    ],
];

// Répartir les matchs du jour dans leurs sections.
foreach ($matchs as $match) {
    $idCompetition = (int) $match['id_competition'];

    if (isset($sectionsCompetitions[$idCompetition])) {
        $sectionsCompetitions[$idCompetition]['matchs'][] = $match;
    }
}
?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mes pronostics</title>

    <style>
        body {
            margin: 0;
            background: #f3f4f6;
            color: #17202a;
            font-family: system-ui, sans-serif;
        }

        main {
            max-width: 850px;
            margin: 35px auto;
            padding: 0 20px;
        }

        article {
            margin: 20px 0;
            padding: 22px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 12px;
        }

        .succes,
        .erreur {
            padding: 14px;
            border-radius: 8px;
        }

        .succes {
            background: #dcfce7;
        }

        .erreur {
            background: #fee2e2;
        }

        fieldset {
            margin: 16px 0;
            padding: 14px;
            border: 1px solid #ccc;
        }

        .choix,
        .scores {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
        }

        input[type="number"] {
            display: block;
            width: 90px;
            margin-top: 6px;
            padding: 8px;
        }

        button {
            padding: 11px 18px;
            border: 0;
            border-radius: 6px;
            background: #166534;
            color: white;
            cursor: pointer;
        }

        small {
            color: #555;
        }
    </style>
</head>

<body>
    <main>
        <h1>Mes pronostics</h1>

        <p>
            Choisis le résultat et, si tu le souhaites, le score exact.
            Tu peux modifier ton pronostic jusqu’à cinq minutes
            avant le coup d’envoi.
        </p>

        <p><small>Horaires affichés : Europe/Zurich.</small></p>

        <?php if (is_string($message) && $message !== ''): ?>
            <p class="succes" role="status"><?= e($message) ?></p>
        <?php endif; ?>

        <?php if ($erreurFormulaire !== ''): ?>
            <p class="erreur" role="alert">
                <?= e($erreurFormulaire) ?>
            </p>
        <?php endif; ?>

        <?php foreach ($sectionsCompetitions as $section): ?>
            <section>
                <h2><?= e($section['titre']) ?></h2>

                <?php if ($section['matchs'] === []): ?>
                    <p>Il n'y a pas de matchs aujourd'hui</p>
                <?php else: ?>

                    <?php foreach ($section['matchs'] as $match): ?>
                        <?php
                        $debut = dateMatch($match, $utc, $fuseauAffichage);
                        $cloture = $debut->modify('-5 minutes');
                        $dejaPronostique = $match['prediction'] !== null;
                        ?>

                        <article>
                            <p><?= e($match['competition']) ?></p>

                            <h3>
                                <?= e($match['domicile']) ?>
                                — <?= e($match['exterieur']) ?>
                            </h3>

                            <p>
                                Coup d’envoi :
                                <?= e($debut->format('d/m/Y à H:i')) ?>
                                <br>
                                Clôture :
                                <?= e($cloture->format('d/m/Y à H:i')) ?>
                            </p>

                            <?php if ($dejaPronostique): ?>
                                <p>Tu as déjà un pronostic pour ce match.</p>
                            <?php endif; ?>
                            <?php if ($estConnecte): ?>
                                <form method="post" action="pronostics.php">
                                    <input
                                        type="hidden"
                                        name="csrf"
                                        value="<?= e($_SESSION['csrf_pronostics']) ?>">

                                    <input
                                        type="hidden"
                                        name="id_match"
                                        value="<?= e($match['id']) ?>">

                                    <fieldset>
                                        <legend>Résultat du match</legend>

                                        <div class="choix">
                                            <?php
                                            $choix = [
                                                '1' => $match['domicile'],
                                                'N' => 'Match nul',
                                                '2' => $match['exterieur'],
                                            ];
                                            ?>

                                            <?php foreach ($choix as $valeur => $libelle): ?>
                                                <label>
                                                    <input
                                                        type="radio"
                                                        name="prediction"
                                                        value="<?= e($valeur) ?>"
                                                        required
                                                        <?= $match['prediction'] === (string) $valeur
                                                            ? 'checked'
                                                            : '' ?>>
                                                    <?= e($libelle) ?>
                                                </label>
                                            <?php endforeach; ?>
                                        </div>
                                    </fieldset>

                                    <fieldset>
                                        <legend>Score exact — facultatif</legend>

                                        <div class="scores">
                                            <label>
                                                <?= e($match['domicile']) ?>
                                                <input
                                                    type="number"
                                                    name="score_domicile"
                                                    min="0"
                                                    max="99"
                                                    step="1"
                                                    value="<?= e(
                                                                $match['score_prediction_domicile']
                                                            ) ?>">
                                            </label>

                                            <label>
                                                <?= e($match['exterieur']) ?>
                                                <input
                                                    type="number"
                                                    name="score_exterieur"
                                                    min="0"
                                                    max="99"
                                                    step="1"
                                                    value="<?= e(
                                                                $match['score_prediction_exterieur']
                                                            ) ?>">
                                            </label>
                                        </div>
                                    </fieldset>

                                    <button type="submit">
                                        <?= $dejaPronostique
                                            ? 'Modifier mon pronostic'
                                            : 'Enregistrer mon pronostic' ?>
                                    </button>
                                </form>
                            <?php else: ?>
                                <p>
                                    Connecte-toi pour faire un pronostic sur ce match.
                                </p>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>

                <?php endif; ?>
            </section>
        <?php endforeach; ?>
    </main>
</body>

</html>
