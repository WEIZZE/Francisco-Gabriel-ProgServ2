<?php

declare(strict_types=1);

// Ce traitement modifie les points : terminal uniquement.
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Exécute ce script depuis le terminal.');
}

// Barème provisoire.
const POINTS_BON_RESULTAT = 3;
const BONUS_SCORE_EXACT = 2;
const POINTS_MAUVAIS_RESULTAT = -1;

// Configuration : à centraliser avec celle des autres fichiers.
$host = 'mariadb';
$base = 'my_database';
$utilisateurDB = 'username';
$motDePasseDB = 'password';

$pdo = null;
$verrouPris = false;
$codeSortie = 0;

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

    /*
     * Même verrou que dans synchronisation_pronostic.php :
     * empêcher une synchronisation ou une autre correction simultanée.
     */
    $verrouPris = (int) $pdo
        ->query("SELECT GET_LOCK('footx_synchronisation', 0)")
        ->fetchColumn() === 1;

    if (!$verrouPris) {
        throw new RuntimeException(
            'Une synchronisation ou une correction est déjà en cours.'
        );
    }

    // Ne pas supposer les identifiants numériques des statuts.
    $statutsPronostic = $pdo
        ->query('SELECT libelle, id FROM statut_pronostic')
        ->fetchAll(PDO::FETCH_KEY_PAIR);

    foreach (['en attente', 'gagne', 'perdu', 'annule'] as $libelle) {
        if (!isset($statutsPronostic[$libelle])) {
            throw new RuntimeException(
                "Statut de pronostic absent : $libelle."
            );
        }
    }

    $statutsMatch = $pdo
        ->query('SELECT libelle, id FROM statut_match')
        ->fetchAll(PDO::FETCH_KEY_PAIR);

    foreach (['termine', 'annule'] as $libelle) {
        if (!isset($statutsMatch[$libelle])) {
            throw new RuntimeException(
                "Statut de match absent : $libelle."
            );
        }
    }

    $idAttente = (int) $statutsPronostic['en attente'];
    $idGagne = (int) $statutsPronostic['gagne'];
    $idPerdu = (int) $statutsPronostic['perdu'];
    $idAnnule = (int) $statutsPronostic['annule'];

    $idMatchTermine = (int) $statutsMatch['termine'];
    $idMatchAnnule = (int) $statutsMatch['annule'];

    $pdo->beginTransaction();

    /*
     * Traiter uniquement les pronostics encore en attente.
     * Un match terminé sans scores complets reste en attente.
     */
    $requete = $pdo->prepare(
        'SELECT
            p.id,
            p.id_utilisateur,
            p.prediction,
            p.score_prediction_domicile,
            p.score_prediction_exterieur,
            m.id_statut_match,
            m.score_domicile,
            m.score_exterieur
         FROM pronostic AS p
         JOIN matchs AS m ON m.id = p.id_match
         WHERE p.id_statut_pronostic = ?
           AND m.id_statut_match IN (?, ?)
         ORDER BY p.id_utilisateur, p.id
         FOR UPDATE'
    );

    $requete->execute([
        $idAttente,
        $idMatchTermine,
        $idMatchAnnule,
    ]);

    $pronostics = $requete->fetchAll();

    $corrigerPronostic = $pdo->prepare(
        'UPDATE pronostic
         SET
            points_obtenus = ?,
            id_statut_pronostic = ?
         WHERE id = ?
           AND id_statut_pronostic = ?'
    );

    $ajouterPoints = $pdo->prepare(
        'UPDATE utilisateur
         SET points = points + ?
         WHERE id = ?'
    );

    $nombreGagnes = 0;
    $nombrePerdus = 0;
    $nombreAnnules = 0;
    $nombreSansScore = 0;

    $variationsUtilisateurs = [];

    foreach ($pronostics as $pronostic) {
        $idPronostic = (int) $pronostic['id'];
        $idUtilisateur = (int) $pronostic['id_utilisateur'];
        $statutMatch = (int) $pronostic['id_statut_match'];

        $points = 0;

        if ($statutMatch === $idMatchAnnule) {
            $nouveauStatut = $idAnnule;
        } else {
            // Ne jamais interpréter un score NULL comme zéro.
            if (
                $pronostic['score_domicile'] === null
                || $pronostic['score_exterieur'] === null
            ) {
                $nombreSansScore++;
                continue;
            }

            $scoreDomicile = (int) $pronostic['score_domicile'];
            $scoreExterieur = (int) $pronostic['score_exterieur'];

            if ($scoreDomicile < 0 || $scoreExterieur < 0) {
                throw new RuntimeException(
                    "Score réel invalide pour le pronostic $idPronostic."
                );
            }

            if (!in_array(
                $pronostic['prediction'],
                ['1', 'N', '2'],
                true
            )) {
                throw new RuntimeException(
                    "Prédiction invalide pour le pronostic $idPronostic."
                );
            }

            $resultatReel = $scoreDomicile > $scoreExterieur
                ? '1'
                : ($scoreDomicile < $scoreExterieur ? '2' : 'N');

            if ($pronostic['prediction'] === $resultatReel) {
                $points = POINTS_BON_RESULTAT;
                $nouveauStatut = $idGagne;

                $scoreExact =
                    $pronostic['score_prediction_domicile'] !== null
                    && $pronostic['score_prediction_exterieur'] !== null
                    && (int) $pronostic['score_prediction_domicile']
                    === $scoreDomicile
                    && (int) $pronostic['score_prediction_exterieur']
                    === $scoreExterieur;

                if ($scoreExact) {
                    $points += BONUS_SCORE_EXACT;
                }
            } else {
                $points = POINTS_MAUVAIS_RESULTAT;
                $nouveauStatut = $idPerdu;
            }
        }

        $corrigerPronostic->execute([
            $points,
            $nouveauStatut,
            $idPronostic,
            $idAttente,
        ]);

        if ($corrigerPronostic->rowCount() !== 1) {
            throw new RuntimeException(
                "Le pronostic $idPronostic n’a pas pu être corrigé."
            );
        }

        if ($nouveauStatut === $idGagne) {
            $nombreGagnes++;
        } elseif ($nouveauStatut === $idPerdu) {
            $nombrePerdus++;
        } else {
            $nombreAnnules++;
        }

        // Additionner les variations avant de mettre à jour les comptes.
        if (!isset($variationsUtilisateurs[$idUtilisateur])) {
            $variationsUtilisateurs[$idUtilisateur] = 0;
        }

        $variationsUtilisateurs[$idUtilisateur] += $points;
    }

    /*
     * Verrouiller et actualiser les utilisateurs dans un ordre stable.
     * La modification utilise points + delta, sans écraser leur total.
     */
    ksort($variationsUtilisateurs, SORT_NUMERIC);

    $lireUtilisateur = $pdo->prepare(
        'SELECT points FROM utilisateur WHERE id = ? FOR UPDATE'
    );

    /*
     * Choisir le rang dont le seuil minimal est le plus élevé
     * parmi ceux atteints.
     *
     * Le dernier rang reste acquis au-delà de son seuil maximal.
     */
    $chercherRang = $pdo->prepare(
        'SELECT id
         FROM rang
         WHERE seuil_min <= ?
         ORDER BY seuil_min DESC
         LIMIT 1'
    );

    $mettreAJourRang = $pdo->prepare(
        'UPDATE utilisateur SET id_rang = ? WHERE id = ?'
    );

    foreach ($variationsUtilisateurs as $idUtilisateur => $variation) {
        $lireUtilisateur->execute([$idUtilisateur]);
        $anciensPoints = $lireUtilisateur->fetchColumn();

        if ($anciensPoints === false) {
            throw new RuntimeException(
                "Utilisateur introuvable : $idUtilisateur."
            );
        }

        $nouveauxPoints = (int) $anciensPoints + $variation;

        if ($variation !== 0) {
            $ajouterPoints->execute([$variation, $idUtilisateur]);
        }

        // Un total négatif conserve le rang de base.
        $chercherRang->execute([max(0, $nouveauxPoints)]);
        $idRang = $chercherRang->fetchColumn();

        if ($idRang === false) {
            throw new RuntimeException(
                'Aucun rang adapté. Vérifie les données de la table rang.'
            );
        }

        $mettreAJourRang->execute([
            (int) $idRang,
            $idUtilisateur,
        ]);
    }
    // Recalculer les rangs de tous les utilisateurs,
    // même sans nouveau pronostic corrigé.
    $pdo->exec(
        'UPDATE utilisateur AS u
     JOIN rang AS r
        ON r.seuil_min = (
            SELECT MAX(r2.seuil_min)
            FROM rang AS r2
            WHERE r2.seuil_min <= GREATEST(u.points, 0)
        )
     SET u.id_rang = r.id'
    );

    $pdo->commit();

    echo "Correction terminée.\n";
    echo "Pronostics gagnés : $nombreGagnes\n";
    echo "Pronostics perdus : $nombrePerdus\n";
    echo "Pronostics annulés : $nombreAnnules\n";
    echo "En attente de scores complets : $nombreSansScore\n";
    echo 'Utilisateurs traités : '
        . count($variationsUtilisateurs)
        . "\n";
} catch (Throwable $erreur) {
    if ($pdo !== null && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    fwrite(STDERR, 'Erreur : ' . $erreur->getMessage() . "\n");
    $codeSortie = 1;
} finally {
    if ($pdo !== null && $verrouPris) {
        try {
            $pdo->query(
                "SELECT RELEASE_LOCK('footx_synchronisation')"
            );
        } catch (Throwable $erreurVerrou) {
            fwrite(
                STDERR,
                'Erreur de libération du verrou : '
                    . $erreurVerrou->getMessage()
                    . "\n"
            );
            $codeSortie = 1;
        }
    }
}

exit($codeSortie);
