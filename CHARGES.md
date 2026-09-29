Cahier des charges – Application de pronostics footballistiques

1. Contexte et objectifs 1.1 Contexte Ce projet s’inscrit dans le cadre de
   l’unité d’enseignement Programmation serveur 2 (ProgServ2) à la HEIG-VD. Il
   s’agit de concevoir et développer une application web de pronostics
   footballistiques permettant aux utilisateurs de prédire les résultats de
   matchs de grandes compétitions européennes. L’application doit être
   développée en respectant les bonnes pratiques de la programmation serveur :
   architecture propre, sécurité, gestion des sessions, base de données
   relationnelle, etc.

1.2 Objectif principal Permettre aux utilisateurs de : Prédire les résultats de
matchs (Champions League, Premier League, La Liga). Gagner des points en
fonction de la justesse de leurs pronostics (équipe gagnante et/ou score exact).
Accéder à un classement global et à un classement mensuel. Débloquer des titres
en fonction du nombre de points accumulés. Viser à avoir le plus de points à la
fin de la saison.

2. Périmètre fonctionnel

2.1 Fonctionnalités principales (Priorité 1)

2.1.1 Gestion des utilisateurs et authentification Inscription : Formulaire
d’inscription avec : pseudo, email, mot de passe. Validation des données (format
email, longueur et complexité du mot de passe). Vérification de l’unicité du
pseudo et de l’email. Connexion : Formulaire de connexion (email ou pseudo + mot
de passe). Gestion des sessions utilisateur. Redirection vers la page d’accueil
après connexion. Déconnexion : Bouton de déconnexion dans l’interface.
Invalidation de la session. Profil utilisateur : Affichage du pseudo, email,
nombre de points, titre actuel. Possibilité de modifier certaines informations
(ex : pseudo, mot de passe).

2.1.2 Système de pronostics Liste des matchs à venir : Affichage des matchs des
compétitions suivantes : Champions League Premier League La Liga Pour chaque
match : équipes, compétition, date et heure. Saisie des pronostics : Pour chaque
match, l’utilisateur peut : Choisir l’équipe gagnante (ou match nul). Optionnel
: saisir un score exact (ex : 2–1). Validation avant enregistrement (pas de
pronostic après le début du match). Historique des pronostics : Chaque
utilisateur peut consulter ses pronostics passés, avec : Résultat réel du match.
Points obtenus pour chaque pronostic.

2.1.3 Calcul des points Règles de points (exemple à confirmer/adapter) :
Résultat correct (équipe gagnante ou nul) : +X points. Score exact correct : +Y
points supplémentaires. Pronostic incorrect : 0 point. Les règles doivent être :
Configurables (via fichier de config ou table en base). Affichées clairement
dans l’interface (section “Règles du jeu”).

2.1.4 Classements Classement global (saison) : Liste des utilisateurs triée par
nombre de points décroissant. Affichage : rang, pseudo, points, titre.
Classement mensuel : Même principe, mais limité aux points gagnés sur le mois en
cours. Possibilité de naviguer entre les mois (mois précédent / mois suivant).
Détail par utilisateur : Page de profil publique montrant : Points totaux.
Titre. Historique des pronostics et points associés.

2.1.5 Système de titres Attribution de titres en fonction du nombre de points
cumulés. Exemple (à ajuster) : 0–999 points : Débutant 1 000–1 999 points : FAN
2 000–4 999 points : Socio 5 000+ points : ULTRA Le titre doit : Se mettre à
jour automatiquement quand le seuil est franchi. Être visible sur le profil et
dans les classements.

2.2 Fonctionnalités secondaires (Priorité 2)

2.2.1 Améliorations de l’expérience utilisateur Tableau de bord personnel :
Récapitulatif : points totaux, titre, position au classement global et mensuel.
Prochains matchs sur lesquels il reste à faire des pronostics. Notifications /
rappels : Indication visuelle des matchs pour lesquels l’utilisateur n’a pas
encore fait de pronostic. Optionnel : email de rappel avant la clôture des
pronostics d’une journée. Statistiques personnelles : Taux de réussite global.
Taux de réussite par compétition. Nombre de scores exacts trouvés.

2.2.2 Fonctionnalités sociales Profil public : Accessible sans être connecté (ou
avec restrictions). Affichage des points, titre, et historique anonymisé ou
partiel. Partage : Possibilité de partager son classement ou un pronostic sur
les réseaux sociaux (lien vers le profil).

2.2.3 Administration Interface admin (réservée à un ou plusieurs
administrateurs) : Gestion des compétitions, équipes et matchs (CRUD). Saisie
des résultats réels des matchs. Lancement du recalcul des points et des
classements. Gestion des utilisateurs (bannissement, réinitialisation de mot de
passe, etc.).

3. Ergonomie et interface utilisateur

3.1 Pages principales Page d’accueil : Présentation de l’application. Accès aux
classements (global et mensuel). Boutons de connexion / inscription.

Page de connexion / inscription : Formulaires simples et clairs. Messages
d’erreur explicites.

Page des pronostics : Liste des matchs par compétition et par date. Formulaire
de pronostic par match. Indication claire de la date limite de pronostic.

Classements : Tableau lisible, trié par points. Pagination si nécessaire.

Profil utilisateur : Informations personnelles. Points, titre. Historique des
pronostics.

Interface admin (si implémentée) : Menus pour gérer matchs, résultats,
utilisateurs.

3.2 Charte graphique Design sobre et lisible. Couleurs en lien avec le football
(ex. vert terrain, blanc, noir). Utilisation d’icônes pour les compétitions
(optionnel). Interface responsive (mobile, tablette, desktop)
