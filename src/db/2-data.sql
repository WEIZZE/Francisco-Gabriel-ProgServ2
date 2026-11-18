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
 
INSERT INTO rang (nom, images, seuil_min, seuil_max) VALUES
    ('Bronze', '/images/RankBronze.webp',     0,      99),
    ('Argent', '/images/RankSilver.webp',      100,    299),
    ('Or', '/images/RankGold.webp',          300,    599),
    ('Master', '/images/RankMaster.webp',     600,    999),
    ('Challenger', '/images/RankChallenger.webp',     1000,   999999);
