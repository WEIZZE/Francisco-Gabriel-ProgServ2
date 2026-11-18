<?php

session_start();

require_once __DIR__ . '/../classes/DatabaseInterface.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Utilisateur/Utilisateur.php';
require_once __DIR__ . '/../classes/Utilisateur/UtilisateurManager.php';

use FootX\UtilisateurManager;

$erreurs = [];
$succes = '';

$pseudo = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $pseudo = trim($_POST['pseudo']);
    $email = trim($_POST['email']);
    $motDePasse = $_POST['mot_de_passe'];

    try {

        $utilisateurManager = new UtilisateurManager();

        $utilisateurManager->validerInscription(
            $pseudo,
            $email,
            $motDePasse
        );

        $motDePasseHache = password_hash(
            $motDePasse,
            PASSWORD_DEFAULT
        );

        $utilisateurManager->addUtilisateur(
            $pseudo,
            $email,
            $motDePasseHache
        );

        $succes = "Inscription réussie.";

        $pseudo = '';
        $email = '';
    } catch (Exception $e) {

        $erreurs[] = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
</head>

<body>

    <h1>Inscription</h1>

    <?php foreach ($erreurs as $erreur): ?>
        <p><?= htmlspecialchars($erreur) ?></p>
    <?php endforeach; ?>

    <?php if ($succes): ?>
        <p><?= htmlspecialchars($succes) ?></p>
    <?php endif; ?>

    <form method="POST">

        <label>Pseudo</label><br>
        <input
            type="text"
            name="pseudo"
            value="<?= htmlspecialchars($pseudo) ?>"
            required>

        <br><br>

        <label>Mot de passe</label><br>
        <input
            type="password"
            name="mot_de_passe"
            required>

        <br><br>

        <label>Adresse mail</label><br>
        <input
            type="email"
            name="email"
            value="<?= htmlspecialchars($email) ?>"
            required>

        <br><br>

        <label>Photo de profil</label><br>
        <input
            type="file"
            name="photo">

        <br><br>

        <button type="submit">
            Inscription
        </button>

    </form>

    <p>
        Déjà inscrit ?
        <a href="connexion.php">Connexion</a>
    </p>

</body>

</html>
