<?php

namespace FootX;

class Rang {
    public const RANKBRONZE = 'Bronze';
    public const RANKSILVER = 'Argent';
    public const RANKGOLD = 'Or';
    public const RANKMASTER = 'Master';
    public const RANCHALLENGER = 'Challenger';

    public const IMAGE_RANKBRONZE = '/images/RankBronze.webp';
    public const IMAGE_RANKSILVER = '/images/RankSilver.webp';
    public const IMAGE_RANKGOLD = '/images/RankGold.webp';
    public const IMAGE_RANKMASTER = '/images/RankMaster.webp';
    public const IMAGE_RANKCHALLENGER = '/images/RankChallenger.webp';

    public const SEUIL_MIN_BRONZE     = 0;
    public const SEUIL_MAX_BRONZE     = 99;
    public const SEUIL_MIN_ARGENT     = 100;
    public const SEUIL_MAX_ARGENT     = 299;
    public const SEUIL_MIN_OR         = 300;
    public const SEUIL_MAX_OR         = 599;
    public const SEUIL_MIN_MASTER     = 600;
    public const SEUIL_MAX_MASTER     = 999;
    public const SEUIL_MIN_CHALLENGER = 1000;
    public const SEUIL_MAX_CHALLENGER = 999999;

    private int $id;
    private string $nom;
    private string $images;
    private int $seuilMin;
    private int $seuilMax;

    public function __construct(int $id, string $nom, string $images, int $seuilMin, int $seuilMax) {
        $this->id = $id;
        $this->nom = $nom;
        $this->images = $images;
        $this->seuilMin = $seuilMin;
        $this->seuilMax = $seuilMax;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getNom(): string {
        return $this->nom;
    }

    public function getImages(): string {
        return $this->images;
    }

    public function getSeuilMin(): int {
        return $this->seuilMin;
    }

    public function getSeuilMax(): int {
        return $this->seuilMax;
    }

    // Un total négatif compte comme 0
    public function contientPoints(int $points): bool {
        return max(0, $points) >= $this->seuilMin;
    }
}
