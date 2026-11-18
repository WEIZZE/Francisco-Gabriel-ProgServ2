<?php

namespace FootX;

use PDO;
use PDOException;

class Database implements DatabaseInterface {
    private PDO $pdo;

    public function __construct() {
        try {
            $this->pdo = new PDO(
                'mysql:host=mariadb;dbname=my_database;charset=utf8mb4',
                'username',
                'password'
            );

            $this->pdo->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );

            $this->pdo->setAttribute(
                PDO::ATTR_DEFAULT_FETCH_MODE,
                PDO::FETCH_ASSOC
            );
        } catch (PDOException $e) {

            die('Erreur de connexion à la base de données : '
                . $e->getMessage());
        }
    }

    public function getPdo(): PDO {
        return $this->pdo;
    }
}
