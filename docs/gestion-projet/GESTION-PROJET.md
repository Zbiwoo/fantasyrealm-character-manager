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
- validation et refus des personnages ;
- modération des commentaires ;
- gestion des utilisateurs ;
- gestion des éléments de personnalisation ;
- administration des employés ;
- journalisation des actions.

---

## Organisation du projet

Le développement a été séparé en plusieurs grandes étapes :

1. Analyse du cahier des charges.
2. Création des wireframes.
3. Création des maquettes desktop et mobile.
4. Création de la charte graphique.
5. Préparation de l'environnement de développement.
6. Création de la base de données MySQL.
7. Mise en place de MongoDB pour la journalisation.
8. Développement de l'authentification.
9. Développement de la gestion des personnages.
10. Développement de la personnalisation.
11. Mise en place du partage communautaire.
12. Ajout des commentaires et des notes.
13. Développement de l'espace employé.
14. Développement de l'administration.
15. Mise en place des emails avec PHPMailer.
16. Mise en place de la journalisation MongoDB.
17. Réalisation de la documentation.
18. Tests et corrections.
19. Préparation du déploiement.

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

Les fonctionnalités administratives, l'envoi des emails et la journalisation des actions ont ensuite été intégrés afin de compléter l'application.

---

## Méthode de travail

Le projet a été développé progressivement, fonctionnalité par fonctionnalité.

Pour chaque fonctionnalité, j'ai suivi autant que possible les étapes suivantes :

1. comprendre le besoin ;
2. préparer la structure nécessaire dans la base de données ;
3. développer la fonctionnalité ;
4. tester son fonctionnement ;
5. corriger les erreurs rencontrées ;
6. valider la fonctionnalité avant de passer à la suivante.

Cette méthode m'a permis de limiter les modifications simultanées de plusieurs parties importantes de l'application et de détecter plus facilement les erreurs.

Des tests ont également été réalisés après les modifications importantes afin de vérifier que les fonctionnalités existantes continuaient de fonctionner.

---

## Difficultés rencontrées

Plusieurs difficultés ont été rencontrées pendant le développement.

La personnalisation visuelle des personnages a été l'une des parties les plus compliquées. L'objectif est de permettre à l'utilisateur de modifier plusieurs caractéristiques de son personnage, notamment le modèle de base, le visage, la coiffure, la couleur des cheveux ainsi que la forme et la couleur des yeux.

La structure permettant d'enregistrer ces choix dans la base de données a été mise en place. La représentation visuelle complète de ces modifications reste cependant une partie à finaliser avant la livraison.

J'ai également rencontré des difficultés avec l'organisation du CSS. Plusieurs pages utilisent la même feuille de style et certaines classes pouvaient entrer en conflit. J'ai donc utilisé des sélecteurs plus précis afin de limiter les modifications aux pages concernées.

La mise en place des emails a également demandé plusieurs tests, notamment pour la configuration SMTP, le formulaire de contact et la réinitialisation du mot de passe.

La mise en place de MongoDB a nécessité une configuration supplémentaire par rapport à la base MySQL. MongoDB est maintenant utilisé pour enregistrer les actions importantes réalisées dans les espaces employé et administrateur.

Ces problèmes ont été corrigés progressivement en testant les fonctionnalités après chaque modification importante.

---

## Outils utilisés pour le suivi

Le développement du projet utilise notamment :

- Git pour le suivi des modifications du code ;
- GitHub pour l'hébergement du dépôt ;
- Figma pour les wireframes, les maquettes et la charte graphique ;
- Visual Studio Code pour le développement ;
- XAMPP pour l'environnement local ;
- phpMyAdmin pour la gestion de MySQL ;
- MongoDB Compass et MongoDB Shell pour la gestion et la vérification de MongoDB.

Le dépôt GitHub utilise plusieurs branches afin de séparer le développement et les fonctionnalités :

```text
main
develop
feature/finalisation-documentation
```

---

## Gestion des versions

Git a été utilisé pendant le développement afin de conserver un historique des modifications.

Le dépôt a été initialisé puis relié au dépôt GitHub du projet.

La branche `main` contient la version principale du projet.

La branche `develop` est utilisée pour le développement.

Une branche dédiée à la finalisation de la documentation a également été créée :

```text
feature/finalisation-documentation
```

Les fichiers contenant des informations confidentielles, notamment la configuration SMTP réelle, sont exclus du dépôt grâce au fichier `.gitignore`.

---

## État des fonctionnalités

### Fonctionnalités utilisateur

Les fonctionnalités principales destinées aux utilisateurs sont réalisées :

- inscription ;
- connexion ;
- déconnexion ;
- récupération du mot de passe ;
- création de personnages ;
- gestion des personnages ;
- personnalisation ;
- partage des personnages ;
- consultation des personnages partagés ;
- commentaires ;
- notation ;
- formulaire de contact.

### Fonctionnalités employé

L'espace employé permet notamment :

- la validation des personnages ;
- le refus des personnages avec un motif ;
- la validation des commentaires ;
- le refus des commentaires ;
- la gestion des utilisateurs ;
- la gestion des éléments de personnalisation.

### Fonctionnalités administrateur

L'espace administrateur permet notamment :

- la gestion des employés ;
- la création d'employés ;
- la modification de leur mot de passe ;
- la suspension d'un employé ;
- la réactivation d'un employé ;
- la suppression d'un employé ;
- la consultation du journal des actions.

### Journalisation

La journalisation MongoDB est mise en place.

Les actions importantes effectuées dans les espaces employé et administrateur peuvent être enregistrées dans la base :

```text
fantasyrealm_logs
```

et dans la collection :

```text
logs
```

---

## Planning et suivi du projet

Le projet a été organisé en plusieurs phases afin d'avancer progressivement et de pouvoir tester les fonctionnalités au fur et à mesure.

| Phase | Travail réalisé | État |
|---|---|---|
| Analyse | Lecture du cahier des charges et identification des besoins | Terminé |
| Conception | Wireframes desktop et mobile | Terminé |
| Design | Maquettes et charte graphique | Terminé |
| Environnement | Installation et configuration de XAMPP et du projet | Terminé |
| Base de données | Création et évolution de la base MySQL | Terminé |
| MongoDB | Mise en place de la base et des journaux d'activité | Terminé |
| Authentification | Inscription, connexion et récupération du mot de passe | Terminé |
| Personnages | Création, consultation et gestion des personnages | Terminé |
| Personnalisation | Choix d'apparence, équipements et pouvoirs | En cours de finalisation |
| Communauté | Partage, commentaires et notation | Terminé |
| Modération | Validation et refus des personnages et commentaires | Terminé |
| Utilisateurs | Gestion des utilisateurs par les employés | Terminé |
| Administration | Gestion des employés | Terminé |
| Emails | Contact, récupération du mot de passe et notifications | Terminé |
| Journalisation | Enregistrement des actions dans MongoDB | Terminé |
| Documentation | Documentation technique, gestion de projet et manuel utilisateur | En cours de finalisation |
| Tests | Vérification générale de l'application | En cours |
| Git / GitHub | Versionnement et dépôt distant | Terminé |
| Déploiement | Mise en ligne de la version finale | À réaliser après les tests |

Ce planning a évolué pendant le développement selon les difficultés rencontrées et le temps nécessaire pour certaines fonctionnalités.

Les fonctionnalités principales destinées aux utilisateurs ont été réalisées en priorité.

Les fonctionnalités de modération, d'administration, d'envoi des emails et de journalisation ont ensuite été intégrées.

Les dernières étapes du projet concernent principalement la finalisation de la personnalisation visuelle, les tests finaux, les vérifications de sécurité et la préparation du déploiement.

---

## État actuel du projet

Le projet dispose actuellement des principaux éléments nécessaires à son fonctionnement :

- application PHP fonctionnelle ;
- base MySQL ;
- journalisation MongoDB ;
- authentification et gestion des sessions ;
- gestion des rôles ;
- création et gestion des personnages ;
- système communautaire ;
- modération ;
- administration ;
- envoi d'emails avec PHPMailer ;
- protection des informations sensibles ;
- documentation ;
- dépôt GitHub.

La personnalisation visuelle des personnages constitue encore un point de finalisation. Les données de personnalisation sont enregistrées en base, mais l'affichage graphique de certaines modifications doit encore être amélioré.

Les tests finaux seront réalisés après cette finalisation afin de vérifier l'ensemble des fonctionnalités avant le déploiement.

---

## Prochaines étapes

Les dernières étapes prévues sont :

1. Finaliser la personnalisation visuelle des personnages.
2. Vérifier l'ensemble des fonctionnalités utilisateur.
3. Vérifier l'ensemble des fonctionnalités employé.
4. Vérifier l'ensemble des fonctionnalités administrateur.
5. Tester les emails.
6. Vérifier les journaux MongoDB.
7. Vérifier les protections de sécurité.
8. Effectuer les tests responsive desktop et mobile.
9. Vérifier les documents de livraison.
10. Effectuer le dernier export de la base MySQL.
11. Effectuer le commit final et pousser la version définitive sur GitHub.
12. Déployer la version finale de l'application.

Le déploiement sera réalisé uniquement après la validation de la version finale afin d'éviter de mettre en ligne une version encore en cours de modification.

## Dépôt GitHub

https://github.com/Zbiwoo/fantasyrealm-character-manager
## Gestion de projet

Le suivi du projet FantasyRealm Online est disponible sur GitHub Projects :

https://github.com/users/Zbiwoo/projects/2