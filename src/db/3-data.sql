-------------- DONNEES DE REFERENCE --------------

INSERT INTO statut_matchs (libelle) VALUES
    ('a venir'),
    ('en cours'),
    ('termine'),
    ('reporte'),
    ('annule');

INSERT INTO statut_battle (libelle) VALUES
    ('ouverte'),
    ('en cours'),
    ('terminee'),
    ('annulee');

INSERT INTO statut_pronostic (libelle) VALUES
    ('en attente'),
    ('gagne'),
    ('perdu'),
    ('annule');

-- Le rang "Bronze" doit avoir l'id 1 (valeur par défaut de utilisateur.id_rang)
INSERT INTO rang (nom, seuil_min, seuil_max) VALUES
    ('Bronze',      0,      99),
    ('Argent',      100,    299),
    ('Or',          300,    599),
    ('Platine',     600,    999),
    ('Diamant',     1000,   999999);
