<?php

namespace FootX;

use Exception;

class UtilisateurManager {
    private Database $database;

    public function __construct() {
        $this->database = new Database();
    }

    // Vérifie si un pseudo existe déjà.

    public function pseudoExiste(string $pseudo): bool {
        $sql = "SELECT id FROM utilisateur WHERE pseudo = :pseudo";

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->bindValue(':pseudo', $pseudo);
        $stmt->execute();

        return $stmt->fetch() !== false;
    }

    // Vérifie si un email existe déjà.

    public function emailExiste(string $email): bool {
        $sql = "SELECT id FROM utilisateur WHERE email = :email";

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->execute();

        return $stmt->fetch() !== false;
    }

    // Validation de l'inscription.

    public function validerInscription(
        string $pseudo,
        string $email,
        string $motDePasse
    ): void {
        if (strlen($pseudo) < 3 || strlen($pseudo) > 30) {
            throw new Exception('Pseudo invalide');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Email invalide');
        }

        if (strlen($motDePasse) < 8) {
            throw new Exception(
                'Le mot de passe doit contenir au moins 8 caractères'
            );
        }

        if ($this->pseudoExiste($pseudo)) {
            throw new Exception('Pseudo déjà utilisé');
        }

        if ($this->emailExiste($email)) {
            throw new Exception('Email déjà utilisé');
        }
    }

    // Ajoute un utilisateur.

    public function addUtilisateur(
        string $pseudo,
        string $email,
        string $motDePasseHache,
        ?string $photoDeProfil = null
    ): void {
        $sql = "
            INSERT INTO utilisateur
            (
                pseudo,
                email,
                mot_de_passe,
                photo_de_profil,
                points,
                id_rang,
                id_equipe
            )
            VALUES
            (
                :pseudo,
                :email,
                :mot_de_passe,
                :photo_de_profil,
                0,
                1,
                NULL
            )
        ";

        $stmt = $this->database->getPdo()->prepare($sql);

        $stmt->bindValue(':pseudo', $pseudo);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':mot_de_passe', $motDePasseHache);
        $stmt->bindValue(':photo_de_profil', $photoDeProfil);

        $stmt->execute();
    }

    // Recherche un utilisateur par email.

    public function getUtilisateurParEmail(string $email): ?Utilisateur {
        $sql = "SELECT * FROM utilisateur WHERE email = :email";

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->execute();

        $ligne = $stmt->fetch();

        if ($ligne === false) {
            return null;
        }

        return new Utilisateur(
            (int)$ligne['id'],
            $ligne['pseudo'],
            $ligne['email'],
            $ligne['mot_de_passe'],
            $ligne['photo_de_profil'],
            (int)$ligne['points'],
            (int)$ligne['id_rang'],
            $ligne['id_equipe'] !== null
                ? (int)$ligne['id_equipe']
                : null
        );
    }

    // Recherche un utilisateur par son id.

    public function getUtilisateurParId(int $id): ?Utilisateur {
        $sql = "SELECT * FROM utilisateur WHERE id = :id";

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        $ligne = $stmt->fetch();

        if ($ligne === false) {
            return null;
        }

        return new Utilisateur(
            (int)$ligne['id'],
            $ligne['pseudo'],
            $ligne['email'],
            $ligne['mot_de_passe'],
            $ligne['photo_de_profil'],
            (int)$ligne['points'],
            (int)$ligne['id_rang'],
            $ligne['id_equipe'] !== null
                ? (int)$ligne['id_equipe']
                : null
        );
    }


    // Retourne tous les utilisateurs.

    public function getUtilisateurs(): array {
        $sql = "SELECT * FROM utilisateur";

        $stmt = $this->database->getPdo()->query($sql);

        $utilisateurs = [];

        while ($ligne = $stmt->fetch()) {
            $utilisateurs[] = new Utilisateur(
                (int)$ligne['id'],
                $ligne['pseudo'],
                $ligne['email'],
                $ligne['mot_de_passe'],
                $ligne['photo_de_profil'],
                (int)$ligne['points'],
                (int)$ligne['id_rang'],
                $ligne['id_equipe'] !== null
                    ? (int)$ligne['id_equipe']
                    : null
            );
        }

        return $utilisateurs;
    }

    // Supprime un utilisateur.
    public function deleteUtilisateur(int $id): void {
        $sql = "DELETE FROM utilisateur WHERE id = :id";

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
    }
}
