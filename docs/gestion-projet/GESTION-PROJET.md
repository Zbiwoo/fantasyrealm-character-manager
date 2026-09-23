# Gestion de projet - FantasyRealm Character Manager

## Présentation

FantasyRealm Character Manager est une application web réalisée dans le cadre de mon ECF Graduate Développeur Web Gaming.

Le projet répond à une demande fictive de PixelVerse Studios pour son MMORPG FantasyRealm Online.

L'objectif principal est de permettre aux utilisateurs de créer, personnaliser et partager leurs personnages, tout en proposant des outils de modération pour les employés et les administrateurs.

---

## Analyse du besoin

Avant de commencer le développement, j'ai analysé le cahier des charges afin de déterminer les fonctionnalités principales à mettre en place.

Les besoins ont été séparés selon trois types d'utilisateurs :

- utilisateur ;
- employé ;
- administrateur.

Les fonctionnalités principales identifiées sont :

- inscription et connexion ;
- récupération du mot de passe ;
- création et personnalisation de personnages ;
- partage des personnages avec la communauté ;
- consultation des personnages partagés ;
- commentaires et notation ;
- formulaire de contact ;
- validation des personnages ;
- modération des commentaires ;
- gestion des utilisateurs ;
- gestion des éléments de personnalisation ;
- administration de l'application.

---

## Organisation du projet

Le développement a été séparé en plusieurs grandes étapes :

1. Analyse du cahier des charges.
2. Création des wireframes.
3. Création des maquettes desktop et mobile.
4. Création de la charte graphique.
5. Préparation de l'environnement de développement.
6. Création de la base de données.
7. Développement de l'authentification.
8. Développement de la gestion des personnages.
9. Développement de la personnalisation.
10. Mise en place du partage communautaire.
11. Ajout des commentaires et des notes.
12. Développement de l'espace employé.
13. Développement de l'administration.
14. Réalisation de la documentation.
15. Tests et corrections.
16. Déploiement de l'application.

---

## Priorisation des tâches

Les fonctionnalités indispensables au fonctionnement de l'application ont été développées en priorité.

La priorité a été donnée à :

- la gestion des utilisateurs ;
- l'authentification ;
- la création des personnages ;
- la validation des personnages ;
- la personnalisation ;
- la gestion des personnages de l'utilisateur ;
- le partage avec la communauté ;
- les commentaires et les notes ;
- la modération.

Les fonctionnalités administratives et la journalisation des actions sont réalisées dans une seconde partie du développement.

---

## Méthode de travail

Le projet a été développé progressivement, fonctionnalité par fonctionnalité.

Pour chaque fonctionnalité, j'ai essayé de suivre les étapes suivantes :

1. comprendre le besoin ;
2. préparer la structure nécessaire dans la base de données ;
3. développer la fonctionnalité ;
4. tester son fonctionnement ;
5. corriger les erreurs rencontrées ;
6. valider la fonctionnalité avant de passer à la suivante.

Cette méthode m'a permis d'éviter de modifier plusieurs parties importantes de l'application en même temps.

---

## Difficultés rencontrées

Plusieurs difficultés ont été rencontrées pendant le développement.

La personnalisation visuelle des personnages a été une des parties les plus compliquées. L'objectif initial était de permettre une modification visuelle très détaillée du personnage. Certaines personnalisations sont actuellement enregistrées dans la base de données mais ne sont pas toutes représentées directement sur l'illustration du personnage.

J'ai également rencontré des difficultés avec l'organisation du CSS. Plusieurs pages utilisent la même feuille de style et certaines classes pouvaient entrer en conflit. J'ai donc utilisé des sélecteurs plus précis afin de limiter les modifications aux pages concernées.

La mise en place des emails a également demandé plusieurs tests, notamment pour la configuration SMTP et la réinitialisation du mot de passe.

Ces problèmes ont été corrigés progressivement en testant les fonctionnalités après chaque modification importante.

---

## Outils utilisés pour le suivi

Le développement du projet utilise notamment :

- Git pour le suivi des modifications du code ;
- GitHub pour l'hébergement du dépôt ;
- Figma pour les wireframes, les maquettes et la charte graphique ;
- Visual Studio Code pour le développement ;
- XAMPP pour l'environnement local ;
- phpMyAdmin pour la gestion de la base de données.

---

## État du projet

Le projet est développé progressivement jusqu'à la date de livraison.

Les fonctionnalités terminées sont testées avant de passer aux suivantes.

Les dernières étapes concernent principalement la finalisation des fonctionnalités employé et administrateur, la journalisation des actions, le déploiement, les tests finaux et la préparation des documents de livraison.

---

## Planning et suivi du projet

Le projet a été organisé en plusieurs phases afin d'avancer progressivement et de pouvoir tester les fonctionnalités au fur et à mesure.

| Phase | Travail réalisé | État |
|---|---|---|
| Analyse | Lecture du cahier des charges et identification des besoins | Terminé |
| Conception | Wireframes desktop et mobile | Terminé |
| Design | Maquettes et charte graphique | Terminé |
| Environnement | Installation et configuration de XAMPP et du projet | Terminé |
| Base de données | Création de la base MySQL et des principales relations | Terminé |
| Authentification | Inscription, connexion et mot de passe oublié | Terminé |
| Personnages | Création, consultation et gestion des personnages | Terminé |
| Personnalisation | Apparence, équipements et pouvoirs | Terminé |
| Communauté | Partage, commentaires et notation | Terminé |
| Modération | Validation des personnages et commentaires | En cours |
| Administration | Gestion des utilisateurs et employés | En cours |
| Journalisation | Mise en place de MongoDB et des logs | À faire |
| Documentation | Documentation technique et utilisateur | En cours |
| Tests | Vérification générale de l'application | En cours |
| Déploiement | Mise en ligne de l'application | À faire |

Ce planning a évolué pendant le développement selon les difficultés rencontrées et le temps nécessaire pour certaines fonctionnalités.

Les fonctionnalités principales destinées aux utilisateurs ont été réalisées en priorité afin d'obtenir rapidement une version fonctionnelle de l'application.

Les fonctionnalités de modération, d'administration et la documentation sont ensuite finalisées avant la livraison.
