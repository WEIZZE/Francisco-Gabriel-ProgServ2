<?php

session_start();

require_once __DIR__ . '/../Database.php';
require_once __DIR__ . '/../DatabaseInterface.php';
require_once __DIR__ . '/../Utilisateur.php';
require_once __DIR__ . '/../UtilisateurManager.php';

use FootX\UtilisateurManager;

$erreurs = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email']);
    $motDePasse = $_POST['mot_de_passe'];

    try {

        $utilisateurManager = new UtilisateurManager();

        $utilisateur =
            $utilisateurManager->getUtilisateurParEmail($email);

        if (
            $utilisateur === null ||
            !password_verify(
                $motDePasse,
                $utilisateur->getMotDePasse()
            )
        ) {
            throw new Exception(
                'Email ou mot de passe incorrect.'
            );
        }

        $_SESSION['id'] =
            $utilisateur->getId();

        $_SESSION['pseudo'] =
            $utilisateur->getPseudo();

        header('Location: index.php');
        exit();
    } catch (Exception $e) {

        $erreurs[] = $e->getMessage();
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

        <label>Adresse mail</label><br>
        <input
            type="email"
            name="email"
            value="<?= htmlspecialchars($email) ?>"
            required>

        <br><br>

        <label>Mot de passe</label><br>
        <input
            type="password"
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
