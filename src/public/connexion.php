<?php

session_start();

require_once __DIR__ . '/../classes/DatabaseInterface.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Utilisateur/Utilisateur.php';
require_once __DIR__ . '/../classes/Utilisateur/UtilisateurManager.php';

use FootX\UtilisateurManager;

$erreurs = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email']);
    $motDePasse = $_POST['mot_de_passe'];

    $utilisateurManager = new UtilisateurManager();

    $utilisateur = $utilisateurManager->getUtilisateurParEmail($email);

    if (
        $utilisateur && password_verify(
            $motDePasse,
            $utilisateur->getMotDePasse()
        )
    ) {

        $_SESSION['user_id'] = $utilisateur->getId();
        $_SESSION['username'] = $utilisateur->getPseudo();

        header('Location: index.php');
        exit();
    } else {

        $erreurs[] =
            "Email ou mot de passe incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
</head>

<body>

    <h1>Connexion</h1>

    <?php foreach ($erreurs as $erreur): ?>
        <p><?= htmlspecialchars($erreur) ?></p>
    <?php endforeach; ?>

    <form method="POST">

        <label for="email">
            Adresse mail
        </label>
        <br>

        <input
            type="email"
            id="email"
            name="email"
            value="<?= htmlspecialchars($email) ?>"
            required>

        <br><br>

        <label for="mot_de_passe">
            Mot de passe
        </label>
        <br>

        <input
            type="password"
            id="mot_de_passe"
            name="mot_de_passe"
            required>

        <br><br>

        <button type="submit">
            Connexion
        </button>

    </form>

    <p>
        Tu n'as pas de compte ?
        <a href="inscription.php">
            Inscris-toi
        </a>
    </p>

</body>

</html>
