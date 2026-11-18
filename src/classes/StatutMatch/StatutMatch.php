<?php

namespace FootX;

class StatutMatch {
    public const A_VENIR  = 'a venir';
    public const EN_COURS = 'en cours';
    public const TERMINE  = 'termine';
    public const REPORTE  = 'reporte';
    public const ANNULE   = 'annule';

    private int $id;
    private string $libelle;

    public function __construct(int $id, string $libelle) {
        $this->id = $id;
        $this->libelle = $libelle;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getLibelle(): string {
        return $this->libelle;
    }

    public function estAVenir(): bool {
        return $this->libelle === self::A_VENIR;
    }

    public function estEnCours(): bool {
        return $this->libelle === self::EN_COURS;
    }

    public function estTermine(): bool {
        return $this->libelle === self::TERMINE;
    }

    public function estReporte(): bool {
        return $this->libelle === self::REPORTE;
    }

    public function estAnnule(): bool {
        return $this->libelle === self::ANNULE;
    }
}
