-- Active: 1791960355698@@127.0.0.1@3306@my_database
-------------- TABLES DE REFERENCE --------------
CREATE TABLE statut_match (
    id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(30) NOT NULL UNIQUE
);
 
CREATE TABLE statut_battle (
    id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(30) NOT NULL UNIQUE
);
 
CREATE TABLE statut_pronostic (
    id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(30) NOT NULL UNIQUE
);
 
CREATE TABLE rang (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL UNIQUE,
    seuil_min INT NOT NULL UNIQUE,
    seuil_max INT NOT NULL UNIQUE,
    CONSTRAINT ck_rang_seuils CHECK (seuil_min >= 0 AND seuil_max > seuil_min)
);
 
-------------- TABLES FOOTBALL --------------
 
CREATE TABLE equipes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL UNIQUE,
    logo VARCHAR(255) NOT NULL UNIQUE
);
 
CREATE TABLE competition (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    logo VARCHAR(255) NOT NULL UNIQUE,
    annee INT NOT NULL,
    date_de_debut DATE NOT NULL,
    date_de_fin DATE NOT NULL,
    CONSTRAINT uq_competition_nom_annee UNIQUE (nom, annee),
    CONSTRAINT ck_competition_dates CHECK (date_de_fin >= date_de_debut)
);

CREATE TABLE `match` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL,
    heure_debut TIME NOT NULL,
    heure_fin TIME NOT NULL,
    score_domicile INT,
    score_exterieur INT,
    id_statut_match INT NOT NULL,
    id_competition INT NOT NULL,
    id_equipe_d INT NOT NULL,
    id_equipe_e INT NOT NULL,
    CONSTRAINT fk_match_statut FOREIGN KEY (id_statut_match) REFERENCES statut_match(id),
    CONSTRAINT fk_match_competition FOREIGN KEY (id_competition) REFERENCES competition(id),
    CONSTRAINT fk_match_equipe_d FOREIGN KEY (id_equipe_d) REFERENCES equipes(id),
    CONSTRAINT fk_match_equipe_e FOREIGN KEY (id_equipe_e) REFERENCES equipes(id),
    CONSTRAINT ck_match_equipes CHECK (id_equipe_d <> id_equipe_e),
    CONSTRAINT ck_match_scores CHECK (
        (score_domicile IS NULL OR score_domicile >= 0)
        AND (score_exterieur IS NULL OR score_exterieur >= 0)
    )
);
 
-------------- TABLES UTILISATEUR --------------
 
CREATE TABLE utilisateur (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pseudo VARCHAR(50) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    email VARCHAR(80) NOT NULL UNIQUE,
    photo_de_profil VARCHAR(255),
    points INT NOT NULL DEFAULT 0,
    id_rang INT NOT NULL DEFAULT 1,
    id_equipe INT,
    CONSTRAINT fk_utilisateur_rang FOREIGN KEY (id_rang) REFERENCES rang(id),
    CONSTRAINT fk_utilisateur_equipe FOREIGN KEY (id_equipe) REFERENCES equipes(id)
);
 
CREATE TABLE pronostic (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prediction VARCHAR(1) NOT NULL,
    score_prediction_domicile INT,
    score_prediction_exterieur INT,
    points_a_gagner INT NOT NULL DEFAULT 0,
    id_statut_pronostic INT NOT NULL,
    id_utilisateur INT NOT NULL,
    id_match INT NOT NULL,
    CONSTRAINT fk_pronostic_statut FOREIGN KEY (id_statut_pronostic) REFERENCES statut_pronostic(id),
    CONSTRAINT fk_pronostic_utilisateur FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id),
    CONSTRAINT fk_pronostic_match FOREIGN KEY (id_match) REFERENCES `match`(id),
    CONSTRAINT ck_pronostic_prediction CHECK (prediction IN ('1', 'N', '2')),
    CONSTRAINT ck_pronostic_scores CHECK (
        (score_prediction_domicile IS NULL OR score_prediction_domicile >= 0)
        AND (score_prediction_exterieur IS NULL OR score_prediction_exterieur >= 0)
    ),
    CONSTRAINT uq_pronostic_utilisateur_match UNIQUE (id_utilisateur, id_match)
);


