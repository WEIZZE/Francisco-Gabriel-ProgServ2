<?php

namespace FootX;

class StatutPronostic {
    public const EN_ATTENTE = 'en attente';
    public const GAGNE      = 'gagne';
    public const PERDU      = 'perdu';
    public const ANNULE     = 'annule';

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

    public function estEnAttente(): bool {
        return $this->libelle === self::EN_ATTENTE;
    }

    public function estGagne(): bool {
        return $this->libelle === self::GAGNE;
    }

    public function estPerdu(): bool {
        return $this->libelle === self::PERDU;
    }

    public function estAnnule(): bool {
        return $this->libelle === self::ANNULE;
    }

    public function estCorrige(): bool {
        return !$this->estEnAttente();
    }
}
