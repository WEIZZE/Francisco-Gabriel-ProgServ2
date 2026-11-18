<?php

namespace FootX;

class RangManager {

    //Retourne le nom du rang selon le nombre de points.

    public function getNomRang(int $points): string {
        if ($points >= Rang::SEUIL_MIN_CHALLENGER) {
            return Rang::RANKCHALLENGER;
        }
        if ($points >= Rang::SEUIL_MIN_MASTER) {
            return Rang::RANKMASTER;
        }
        if ($points >= Rang::SEUIL_MIN_OR) {
            return Rang::RANKGOLD;
        }
        if ($points >= Rang::SEUIL_MIN_ARGENT) {
            return Rang::RANKSILVER;
        }
        return Rang::RANKBRONZE;
    }

    //Retourne l'image du rang.

    public function getImageRang(int $points): string {
        if ($points >= Rang::SEUIL_MIN_CHALLENGER) {
            return Rang::IMAGE_RANKCHALLENGER;
        }
        if ($points >= Rang::SEUIL_MIN_MASTER) {
            return Rang::IMAGE_RANKMASTER;
        }
        if ($points >= Rang::SEUIL_MIN_OR) {
            return Rang::IMAGE_RANKGOLD;
        }
        if ($points >= Rang::SEUIL_MIN_ARGENT) {
            return Rang::IMAGE_RANKSILVER;
        }
        return Rang::IMAGE_RANKBRONZE;
    }

    public function getRangParPoints(int $points): Rang {
        if ($points >= Rang::SEUIL_MIN_CHALLENGER) {
            return new Rang(
                5,
                Rang::RANKCHALLENGER,
                Rang::IMAGE_RANKCHALLENGER,
                Rang::SEUIL_MIN_CHALLENGER,
                Rang::SEUIL_MAX_CHALLENGER
            );
        }

        if ($points >= Rang::SEUIL_MIN_MASTER) {
            return new Rang(
                4,
                Rang::RANKMASTER,
                Rang::IMAGE_RANKMASTER,
                Rang::SEUIL_MIN_MASTER,
                Rang::SEUIL_MAX_MASTER
            );
        }

        if ($points >= Rang::SEUIL_MIN_OR) {
            return new Rang(
                3,
                Rang::RANKGOLD,
                Rang::IMAGE_RANKGOLD,
                Rang::SEUIL_MIN_OR,
                Rang::SEUIL_MAX_OR
            );
        }

        if ($points >= Rang::SEUIL_MIN_ARGENT) {
            return new Rang(
                2,
                Rang::RANKSILVER,
                Rang::IMAGE_RANKSILVER,
                Rang::SEUIL_MIN_ARGENT,
                Rang::SEUIL_MAX_ARGENT
            );
        }

        return new Rang(
            1,
            Rang::RANKBRONZE,
            Rang::IMAGE_RANKBRONZE,
            Rang::SEUIL_MIN_BRONZE,
            Rang::SEUIL_MAX_BRONZE
        );
    }
}
