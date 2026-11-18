<?php

namespace FootX;

class Equipe {
    private int $id;
    private string $nom;
    private string $logo;

    public function __construct(int $id, string $nom, string $logo) {
        $this->id = $id;
        $this->nom = $nom;
        $this->logo = $logo;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getNom(): string {
        return $this->nom;
    }

    public function getLogo(): string {
        return $this->logo;
    }
}
