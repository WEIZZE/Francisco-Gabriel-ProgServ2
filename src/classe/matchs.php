<?php

namespace FootX;

class Matchs {
    private int $id;
    private \DateTime $date;
    private ?string $api;
    private \DateTime $heureDebut;
    private \DateTime $heureFin;
    private ?int $scoreDomicile;
    private ?int $scoreExterieur;
    private int $idStatutMatch;
    private int $idCompetition;
    private int $idEquipeD;
    private int $idEquipeE;

    public function __construct(int $id, \DateTime $date, ?string $api, \DateTime $heureDebut, \DateTime $heureFin, ?int $scoreDomicile, ?int $scoreExterieur, int $idStatutMatch, int $idCompetition, int $idEquipeD, int $idEquipeE) {
        $this->id = $id;
        $this->date = $date;
        $this->api = $api;
        $this->heureDebut = $heureDebut;
        $this->heureFin = $heureFin;
        $this->scoreDomicile = $scoreDomicile;
        $this->scoreExterieur = $scoreExterieur;
        $this->idStatutMatch = $idStatutMatch;
        $this->idCompetition = $idCompetition;
        $this->idEquipeD = $idEquipeD;
        $this->idEquipeE = $idEquipeE;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getDate(): \DateTime {
        return $this->date;
    }

    public function getApi(): ?string {
        return $this->api;
    }

    public function getHeureDebut(): \DateTime {
        return $this->heureDebut;
    }

    public function getHeureFin(): \DateTime {
        return $this->heureFin;
    }

    public function getScoreDomicile(): ?int {
        return $this->scoreDomicile;
    }

    public function getScoreExterieur(): ?int {
        return $this->scoreExterieur;
    }

    public function getIdStatutMatch(): int {
        return $this->idStatutMatch;
    }

    public function getIdCompetition(): int {
        return $this->idCompetition;
    }

    public function getIdEquipeD(): int {
        return $this->idEquipeD;
    }

    public function getIdEquipeE(): int {
        return $this->idEquipeE;
    }
}
