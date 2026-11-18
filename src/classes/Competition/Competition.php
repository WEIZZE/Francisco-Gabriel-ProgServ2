<?php

namespace FootX;

class Competition {
    private int $id;
    private string $nom;
    private string $logo;
    private int $annee;
    private \DateTime $dateDeDebut;
    private \DateTime $dateDeFin;

    public function __construct(int $id, string $nom, string $logo, int $annee, \DateTime $dateDeDebut, \DateTime $dateDeFin) {
        $this->id = $id;
        $this->nom = $nom;
        $this->logo = $logo;
        $this->annee = $annee;
        $this->dateDeDebut = $dateDeDebut;
        $this->dateDeFin = $dateDeFin;
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

    public function getAnnee(): int {
        return $this->annee;
    }

    public function getDateDeDebut(): \DateTime {
        return $this->dateDeDebut;
    }

    public function getDateDeFin(): \DateTime {
        return $this->dateDeFin;
    }
}
