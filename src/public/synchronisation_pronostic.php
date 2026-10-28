<?php

declare(strict_types=1);

// Ce script modifie la DB : exécution depuis le terminal uniquement.
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Exécute ce script depuis le terminal.');
}

// Configuration
$token = getenv('FOOTBALL_DATA_TOKEN') ?: '';

$host = 'mariadb';
$base = 'my_database';
$utilisateur = 'username';
$motDePasse = 'password';

// Premier test : uniquement la Premier League.
$competitions = ['CL', 'PL', 'PD'];

/**
 * Récupère une réponse JSON de l’API.
 */
function appelerApi(string $chemin, string $token): array {
    $curl = curl_init('https://api.football-data.org/v4/' . $chemin);

    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ['X-Auth-Token: ' . $token],
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 30,
    ]);

    $reponse = curl_exec($curl);

    if ($reponse === false) {
        throw new RuntimeException('Erreur cURL : ' . curl_error($curl));
    }

    $codeHttp = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    if ($codeHttp !== 200) {
        throw new RuntimeException("L’API a répondu avec le code HTTP $codeHttp.");
    }

    return json_decode($reponse, true, 512, JSON_THROW_ON_ERROR);
}

/**
 * Ajoute une ligne ou actualise celle ayant le même identifiant.
 * Les noms de table et de colonnes viennent uniquement de notre code.
 */
function enregistrer(PDO $pdo, string $table, int $id, array $donnees): void {
    if (!in_array($table, ['competition', 'equipes', 'matchs'], true)) {
        throw new RuntimeException('Table non autorisée.');
    }

    $recherche = $pdo->prepare("SELECT id FROM `$table` WHERE id = ?");
    $recherche->execute([$id]);

    if ($recherche->fetchColumn() !== false) {
        $colonnes = array_map(
            fn($colonne) => "`$colonne` = ?",
            array_keys($donnees)
        );

        $sql = "UPDATE `$table` SET "
            . implode(', ', $colonnes)
            . ' WHERE id = ?';

        $valeurs = [...array_values($donnees), $id];
    } else {
        $donnees = ['id' => $id] + $donnees;

        $colonnes = '`' . implode('`, `', array_keys($donnees)) . '`';
        $parametres = implode(', ', array_fill(0, count($donnees), '?'));

        $sql = "INSERT INTO `$table` ($colonnes) VALUES ($parametres)";
        $valeurs = array_values($donnees);
    }

    $pdo->prepare($sql)->execute($valeurs);
}

$pdo = null;
$verrouPris = false;

try {
    if ($token === '') {
        throw new RuntimeException('La variable FOOTBALL_DATA_TOKEN est manquante.');
    }

    $pdo = new PDO(
        "mysql:host=$host;port=3306;dbname=$base;charset=utf8mb4",
        $utilisateur,
        $motDePasse,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );

    // Empêche deux synchronisations simultanées.
    $verrouPris = (int) $pdo
        ->query("SELECT GET_LOCK('footx_synchronisation', 0)")
        ->fetchColumn() === 1;

    if (!$verrouPris) {
        throw new RuntimeException('Une synchronisation est déjà en cours.');
    }

    // On récupère les identifiants des statuts sans supposer leur numéro.
    $statuts = $pdo
        ->query('SELECT libelle, id FROM statut_match')
        ->fetchAll(PDO::FETCH_KEY_PAIR);

    $correspondanceStatuts = [
        'SCHEDULED' => 'a venir',
        'TIMED' => 'a venir',
        'IN_PLAY' => 'en cours',
        'PAUSED' => 'en cours',
        'EXTRA_TIME' => 'en cours',
        'PENALTY_SHOOTOUT' => 'en cours',
        'FINISHED' => 'termine',
        'POSTPONED' => 'reporte',
        'SUSPENDED' => 'reporte',
        'CANCELLED' => 'annule',
        'AWARDED' => 'annule',
    ];

    foreach ($competitions as $code) {
        // Les appels réseau se font avant la transaction.
        $competition = appelerApi("competitions/$code", $token);
        $saison = $competition['currentSeason'];

        if (empty($saison['startDate']) || empty($saison['endDate'])) {
            throw new RuntimeException("Dates de saison indisponibles pour $code.");
        }

        $annee = (int) substr($saison['startDate'], 0, 4);

        $reponse = appelerApi(
            "competitions/$code/matches?season=$annee",
            $token
        );

        if (!isset($reponse['matches']) || !is_array($reponse['matches'])) {
            throw new RuntimeException("Liste de matchs absente pour $code.");
        }

        $pdo->beginTransaction();

        // Votre modèle actuel conserve une seule édition par identifiant API.
        $recherche = $pdo->prepare('SELECT annee FROM competition WHERE id = ?');
        $recherche->execute([$competition['id']]);
        $ancienneAnnee = $recherche->fetchColumn();

        if ($ancienneAnnee !== false && (int) $ancienneAnnee !== $annee) {
            throw new RuntimeException(
                'Nouvelle saison détectée : import arrêté pour préserver l’historique.'
            );
        }

        enregistrer($pdo, 'competition', (int) $competition['id'], [
            'nom' => $competition['name'],
            'logo' => $competition['emblem']
                ?? 'logo-indisponible-competition-' . $competition['id'],
            'annee' => $annee,
            'date_de_debut' => $saison['startDate'],
            'date_de_fin' => $saison['endDate'],
        ]);

        $nombreMatchs = 0;
        $nombreIgnores = 0;

        foreach ($reponse['matches'] as $match) {
            // Certaines rencontres n’ont pas encore leurs équipes déterminées.
            if (
                empty($match['homeTeam']['id'])
                || empty($match['awayTeam']['id'])
                || empty($match['utcDate'])
            ) {
                $nombreIgnores++;
                continue;
            }

            foreach ([$match['homeTeam'], $match['awayTeam']] as $equipe) {
                enregistrer($pdo, 'equipes', (int) $equipe['id'], [
                    'nom' => $equipe['name'],
                    'logo' => $equipe['crest']
                        ?? 'logo-indisponible-equipe-' . $equipe['id'],
                ]);
            }

            $libelle = $correspondanceStatuts[$match['status']] ?? null;

            if ($libelle === null || !isset($statuts[$libelle])) {
                throw new RuntimeException(
                    'Statut inconnu ou absent de la DB : ' . $match['status']
                );
            }

            $date = (new DateTimeImmutable($match['utcDate']))
                ->setTimezone(new DateTimeZone('UTC'));

            // On conserve le score à 90 minutes, pas celui après prolongations.
            $score = ($match['score']['duration'] ?? '') === 'REGULAR'
                ? ($match['score']['fullTime'] ?? [])
                : ($match['score']['regularTime'] ?? []);

            enregistrer($pdo, 'matchs', (int) $match['id'], [
                'date' => $date->format('Y-m-d'),
                'heure_debut' => $date->format('H:i:s'),

                // Valeur technique : votre colonne est obligatoire.
                // Ce n’est PAS une heure réelle de fin.
                'heure_fin' => '00:00:00',

                'score_domicile' => $score['home'] ?? $score['homeTeam'] ?? null,
                'score_exterieur' => $score['away'] ?? $score['awayTeam'] ?? null,
                'id_statut_match' => (int) $statuts[$libelle],
                'id_competition' => (int) $competition['id'],
                'id_equipe_d' => (int) $match['homeTeam']['id'],
                'id_equipe_e' => (int) $match['awayTeam']['id'],
            ]);

            $nombreMatchs++;
        }

        $pdo->commit();

        echo "$code : $nombreMatchs matchs traités, $nombreIgnores ignorés.\n";
    }

    echo "Synchronisation terminée.\n";
} catch (Throwable $erreur) {
    if ($pdo !== null && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    // Affichage réservé au terminal, pas aux visiteurs.
    fwrite(STDERR, 'Erreur : ' . $erreur->getMessage() . "\n");
    exit(1);
} finally {
    if ($pdo !== null && $verrouPris) {
        $pdo->query("SELECT RELEASE_LOCK('footx_synchronisation')");
    }
}
