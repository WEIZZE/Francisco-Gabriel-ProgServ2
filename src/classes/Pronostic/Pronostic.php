<?php

namespace FootX;

class Pronostic {
    private int $id;
    private ?string $prediction;
    private ?int $scorePredictionDomicile;
    private ?int $scorePredictionExterieur;
    private int $pointsAGagner;
    private int $idStatutPronostic;
    private int $idUtilisateur;
    private int $idMatch;

    public function __construct(int $id, ?string $prediction, ?int $scorePredictionDomicile, ?int $scorePredictionExterieur, int $pointsAGagner, int $idStatutPronostic, int $idUtilisateur, int $idMatch) {
        $this->id = $id;
        $this->prediction = $prediction;
        $this->scorePredictionDomicile = $scorePredictionDomicile;
        $this->scorePredictionExterieur = $scorePredictionExterieur;
        $this->pointsAGagner = $pointsAGagner;
        $this->idStatutPronostic = $idStatutPronostic;
        $this->idUtilisateur = $idUtilisateur;
        $this->idMatch = $idMatch;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getPrediction(): ?string {
        return $this->prediction;
    }

    public function getScorePredictionDomicile(): ?int {
        return $this->scorePredictionDomicile;
    }

    public function getScorePredictionExterieur(): ?int {
        return $this->scorePredictionExterieur;
    }

    public function getPointsAGagner(): int {
        return $this->pointsAGagner;
    }

    public function getIdStatutPronostic(): int {
        return $this->idStatutPronostic;
    }

    public function getIdUtilisateur(): int {
        return $this->idUtilisateur;
    }

    public function getIdMatch(): int {
        return $this->idMatch;
    }
}
