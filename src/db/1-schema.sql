-------------- TABLES DE REFERENCE --------------
CREATE TABLE statut_match (
    id SERIAL PRIMARY KEY,
    libelle VARCHAR(30) NOT NULL UNIQUE
);

CREATE TABLE statut_battle (
    id SERIAL PRIMARY KEY,
    libelle VARCHAR(30) NOT NULL UNIQUE
);

CREATE TABLE statut_pronostic (
    id SERIAL PRIMARY KEY,
    libelle VARCHAR(30) NOT NULL UNIQUE
);

CREATE TABLE rang (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(50) NOT NULL UNIQUE,
    seuil_min INTEGER NOT NULL UNIQUE,
    seuil_max INTEGER NOT NULL UNIQUE,
    CONSTRAINT ck_rang_seuils CHECK (seuil_min >= 0 AND seuil_max > seuil_min)
);

-------------- TABLES FOOTBALL --------------

CREATE TABLE equipes (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL UNIQUE,
    logo VARCHAR(255) NOT NULL UNIQUE
);

CREATE TABLE competition (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    logo VARCHAR(255) NOT NULL UNIQUE,
    annee INTEGER NOT NULL,
    date_de_debut DATE NOT NULL,
    date_de_fin DATE NOT NULL,
    CONSTRAINT uq_competition_nom_annee UNIQUE (nom, annee),
    CONSTRAINT ck_competition_dates CHECK (date_de_fin >= date_de_debut)
);

CREATE TABLE match (
    id SERIAL PRIMARY KEY,
    date DATE NOT NULL,
    heure_debut TIME NOT NULL,
    heure_fin TIME NOT NULL,
    score_domicile INTEGER,
    score_exterieur INTEGER,
    id_statut_match INTEGER NOT NULL REFERENCES statut_match(id),
    id_competition INTEGER NOT NULL REFERENCES competition(id),
    id_equipe_d INTEGER NOT NULL REFERENCES equipes(id),
    id_equipe_e INTEGER NOT NULL REFERENCES equipes(id),
    CONSTRAINT ck_match_equipes CHECK (id_equipe_d <> id_equipe_e),
    CONSTRAINT ck_match_scores CHECK (
        (score_domicile IS NULL OR score_domicile >= 0)
        AND (score_exterieur IS NULL OR score_exterieur >= 0)
    )
);

-------------- TABLES UTILISATEUR --------------

CREATE TABLE utilisateur (
    id SERIAL PRIMARY KEY,
    pseudo VARCHAR(50) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    email VARCHAR(80) NOT NULL UNIQUE,
    photo_de_profil VARCHAR(255),
    points INTEGER NOT NULL DEFAULT 0,
    id_rang INTEGER NOT NULL DEFAULT 1 REFERENCES rang(id),
    id_equipe INTEGER REFERENCES equipes(id)
);

CREATE TABLE pronostic (
    id SERIAL PRIMARY KEY,
    prediction VARCHAR(1) NOT NULL,
    score_prediction_domicile INTEGER,
    score_prediction_exterieur INTEGER,
    points_a_gagner INTEGER NOT NULL DEFAULT 0,
    id_statut_pronostic INTEGER NOT NULL REFERENCES statut_pronostic(id),
    id_utilisateur INTEGER NOT NULL REFERENCES utilisateur(id),
    id_match INTEGER NOT NULL REFERENCES match(id),
    CONSTRAINT ck_pronostic_prediction CHECK (prediction IN ('1', 'N', '2')),
    CONSTRAINT ck_pronostic_scores CHECK (
        (score_prediction_domicile IS NULL OR score_prediction_domicile >= 0)
        AND (score_prediction_exterieur IS NULL OR score_prediction_exterieur >= 0)
    ),
    CONSTRAINT uq_pronostic_utilisateur_match UNIQUE (id_utilisateur, id_match)
);

-------------- TABLES BATTLE --------------

CREATE TABLE battle (
    id SERIAL PRIMARY KEY,
    date DATE NOT NULL,
    heure_debut TIME NOT NULL,
    heure_fin TIME NOT NULL,
    point_engages INTEGER NOT NULL CHECK (point_engages > 0),
    id_statut_battle INTEGER NOT NULL REFERENCES statut_battle(id),
    id_match INTEGER NOT NULL REFERENCES match(id)
);

-------------- TABLES DE LIAISON --------------

CREATE TABLE battle_utilisateur (
    id SERIAL PRIMARY KEY,
    id_utilisateur INTEGER NOT NULL REFERENCES utilisateur(id),
    id_battle INTEGER NOT NULL REFERENCES battle(id),
    CONSTRAINT uq_battle_utilisateur UNIQUE (id_battle, id_utilisateur)
);
