<?php
// Démarre la session
session_start();

$_SESSION = [];
//Détruit la session
session_destroy();

header('Location: connexion.php');
exit();
