<?php

namespace FootX;

class Utilisateur {
    private int $id;
    private string $pseudo;
    private string $email;
    private string $motDePasse;
    private ?string $photoDeProfil;
    private int $points;
    private int $idRang;
    private ?int $idEquipe;

    public function __construct(int $id, string $pseudo, string $email, string $motDePasse, ?string $photoDeProfil, int $points, int $idRang, ?int $idEquipe) {
        $this->id = $id;
        $this->pseudo = $pseudo;
        $this->email = $email;
        $this->motDePasse = $motDePasse;
        $this->photoDeProfil = $photoDeProfil;
        $this->points = $points;
        $this->idRang = $idRang;
        $this->idEquipe = $idEquipe;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getPseudo(): string {
        return $this->pseudo;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getMotDePasse(): string {
        return $this->motDePasse;
    }

    public function getPhotoDeProfil(): ?string {
        return $this->photoDeProfil;
    }

    public function getPoints(): int {
        return $this->points;
    }

    public function getIdRang(): int {
        return $this->idRang;
    }

    public function getIdEquipe(): ?int {
        return $this->idEquipe;
    }
}
