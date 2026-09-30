# Cahier des charges – Application de pronostics footballistiques

## 1. Contexte et objectifs

### 1.1 Contexte 

Ce projet s’inscrit dans le cadre de l’unité d’enseignement Programmation Serveur 2 (ProgServ2) à la HEIG-VD. Dans ce cadre nous allons concevoir et développer une application web de pronostics
footballistiques non payants permettant aux utilisateurs de prédire les résultats de matchs
de grandes compétitions européennes.

### 1.2 Objectif principal 

Permettre aux utilisateurs de : 
- Prédire les résultats de
matchs (Champions League, Premier League, La Liga). 
- Gagner des points en
fonction de la justesse de leurs pronostics (équipe gagnante et/ou score exact).
- Accéder à un classement global et à un classement mensuel. 
- Débloquer des titres
en fonction du nombre de points accumulés. Viser à avoir le plus de points à la
fin de la saison.

## 2. Périmètre fonctionnel

### 2.1 Fonctionnalités principales (Priorité 1)

#### 2.1.1 Gestion des utilisateurs et authentification 

- Formulaire d’inscription (pseudo, email, mot de passe).
- Validation des données (format
email, longueur et complexité du mot de passe). 
- **Vérification de l’unicité du
pseudo et de l’email. (À voir)**
- Connexion : Formulaire de connexion (email ou pseudo + mot
de passe). 
- Gestion des sessions utilisateur. 
- Redirection vers la page d’accueil après connexion. 
- Déconnexion : Bouton de déconnexion dans l’interface.
- Invalidation de la session. 
- Profil utilisateur : Affichage du pseudo, email, nombre de points, titre actuel. 
- Possibilité de modifier certaines informations
(ex : pseudo, mot de passe).

#### 2.1.2 Système de pronostics 
- Liste des matchs à venir
- Affichage des matchs des compétitions suivantes : Champions League, Premier League, La Liga, 
- Pour chaque match on voit : équipes, compétition, date et heure, saisie des pronostics
- Pour chaque match on peut : Choisir l’équipe gagnante (ou match nul). 
- Optionnel : saisir un score exact (ex : 2–1). 
- Validation avant enregistrement (pas de pronostic après le début du match). 
- Fermeture des pronostics 5 minutes avant le début du match
- Historique des pronostics : Chaque utilisateur peut consulter ses pronostics passés, avec le résultat réel du match, et les points obtenus pour chaque pronostic.

#### 2.1.3 Calcul des points 

- Règles de points (exemple à confirmer/adapter) :
- Résultat correct (équipe gagnante ou nul) : +X points. 
- Score exact correct : +Y points supplémentaires. 
- Pronostic incorrect : 0 point. 
- Les règles doivent être claires pour tout le monde et affichées quelque part de simple à trouver
dans l’interface (section “Règles du jeu”).

#### 2.1.4 Classements 
- Classement global (saison) : Liste des utilisateurs triée par nombre de points décroissant. 
- Affichage : rang, pseudo, points, titre.
- Classement mensuel : Même principe, mais limité aux points gagnés sur le mois en cours. 
- Possibilité de naviguer entre les mois (mois précédent / mois suivant).
- Page de profil publique montrant : points totaux, titre, historique des pronostics et points associés.

#### 2.1.5 Système de titres 
- Attribution de titres en fonction du nombre de points cumulés.
- Exemple (à ajuster)
   - 0–999 points : Débutant 
   - 1 000–1 999 points : FAN
   - 2 000–4 999 points : Socio 
   - 5 000+ points : ULTRA 
- Le titre doit : Se mettre à jour automatiquement quand le seuil est franchi et être visible sur le profil ainsi que
dans les classements.

### 2.2 Fonctionnalités secondaires (Priorité 2)

#### 2.2.1 Améliorations de l’expérience utilisateur 
- Tableau de bord personnel
   - Récapitulatif des points totaux, titre, position au classement global et mensuel.
- Notifications / rappels : Indication visuelle des matchs pour lesquels l’utilisateur n’a pas
encore fait de pronostic. 
- Optionnel : email de rappel avant la clôture des
pronostics d’une journée. 
- Statistiques personnelles : taux de réussite global, taux de réussite par compétition, nombre de scores exacts trouvés.

#### 2.2.2 Fonctionnalités sociales 
- Profil public : Accessible sans être connecté (ou avec restrictions) ainsi qu'état privé ou public. 
- Partage : Possibilité de partager son classement ou un pronostic sur les réseaux sociaux (lien vers le profil).

#### 2.2.3 Administration 
- Interface admin (réservée à un ou plusieurs
administrateurs)
- Gestion des compétitions, équipes et matchs (CRUD). 
- Saisie des résultats réels des matchs et lancement du recalcul des points et des classements. 
- Gestion des utilisateurs (bannissement, réinitialisation de mot de passe, etc.).

## 3. Ergonomie et interface utilisateur

### 3.1 Pages principales 
- Page d’accueil
   - Présentation de l’application. 
   - Accès aux classements (global et mensuel). 
   - Boutons de connexion / inscription.
- Page de connexion / inscription
   - Formulaires simples et clairs. 
   - Messages d’erreur explicites.
- Page des pronostics
   - Liste des matchs par compétition et par date. 
   - Formulaire de pronostic par match. 
   - Indication claire de la date limite de pronostic.
- Classements 
   - Tableau lisible, trié par points / compétitions.

### 3.2 Charte graphique 
- Design sobre et lisible. 
- Couleurs en lien avec le football (ex. vert terrain, blanc, noir). 
- Utilisation d’icônes pour les compétitions (optionnel)
- Interface responsive (mobile, tablette, desktop)
