<?php

namespace FootX;

class Rang {
    private int $id;
    private string $nom;
    private int $seuilMin;
    private int $seuilMax;

    public function __construct(int $id, string $nom, int $seuilMin, int $seuilMax) {
        $this->id = $id;
        $this->nom = $nom;
        $this->seuilMin = $seuilMin;
        $this->seuilMax = $seuilMax;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getNom(): string {
        return $this->nom;
    }

    public function getSeuilMin(): int {
        return $this->seuilMin;
    }

    public function getSeuilMax(): int {
        return $this->seuilMax;
    }
}
