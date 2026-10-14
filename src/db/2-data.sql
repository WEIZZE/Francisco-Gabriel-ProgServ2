-------------- DONNEES DE REFERENCE --------------
INSERT INTO statut_match (libelle) VALUES
    ('a venir'),
    ('en cours'),
    ('termine'),
    ('reporte'),
    ('annule');
 
INSERT INTO statut_pronostic (libelle) VALUES
    ('en attente'),
    ('gagne'),
    ('perdu'),
    ('annule');
 
INSERT INTO rang (nom, seuil_min, seuil_max) VALUES
    ('Bronze',      0,      99),
    ('Argent',      100,    299),
    ('Or',          300,    599),
    ('Master',     600,    999),
    ('Challenger',     1000,   999999);
