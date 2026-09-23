# Documentation technique - FantasyRealm Character Manager

## Présentation

Cette documentation technique présente la conception et le développement de l'application FantasyRealm Character Manager réalisée pour PixelVerse Studios dans le cadre de mon ECF.

Elle regroupe les choix techniques, l'environnement de développement, la conception des bases de données, les différents diagrammes ainsi que les informations nécessaires au fonctionnement et au déploiement du projet.

---

## Environnement de développement

Le projet a été développé en local avec les outils suivants :

- Visual Studio Code
- XAMPP
- Apache
- PHP
- MySQL / MariaDB
- phpMyAdmin
- MongoDB Community Server
- MongoDB Compass
- MongoDB Shell (mongosh)
- HTML5
- CSS3
- JavaScript
- PDO
- PHPMailer
- Git et GitHub
- Figma

Le projet est placé dans le dossier `htdocs` de XAMPP afin de pouvoir être exécuté avec le serveur Apache local.

```text
C:\xampp\htdocs\fantasyrealm-character-manager
```

La base de données relationnelle utilisée par l'application est nommée :

```text
fantasyrealm
```

La base de données non relationnelle utilisée pour la journalisation est nommée :

```text
fantasyrealm_logs
```

Elle contient une collection :

```text
logs
```

---

## Architecture générale

L'application utilise PHP pour la partie serveur et MySQL pour le stockage des données principales.

PDO est utilisé pour communiquer avec la base de données relationnelle à l'aide de requêtes préparées.

Le front-end est réalisé en HTML5, CSS3 et JavaScript sans utilisation de Bootstrap.

PHPMailer est utilisé pour l'envoi des emails de l'application via SMTP.

MongoDB est utilisé comme base de données non relationnelle pour la journalisation des actions importantes réalisées par les employés et les administrateurs.

L'application utilise donc deux systèmes de stockage :

- MySQL pour les données principales de l'application ;
- MongoDB pour les journaux d'activité.

---

## Réflexion initiale et choix techniques

Avant de commencer le développement, j'ai d'abord analysé les besoins de PixelVerse Studios afin d'identifier les différentes fonctionnalités de l'application et les rôles des utilisateurs.

L'application possède trois types de comptes :

- Utilisateur
- Employé
- Administrateur

Chaque rôle possède des droits différents dans l'application.

J'ai choisi PHP pour réaliser la partie serveur car il permet de gérer les sessions, les formulaires, les droits des utilisateurs et les interactions avec les bases de données.

MySQL est utilisé comme base de données relationnelle pour stocker les utilisateurs, les personnages, les commentaires ainsi que les éléments de personnalisation.

PDO permet de communiquer avec MySQL et d'utiliser des requêtes préparées afin de sécuriser les échanges avec la base de données.

JavaScript est utilisé pour certaines interactions dynamiques de l'interface, notamment les onglets, les aperçus et certaines fonctionnalités de personnalisation des personnages.

Pour l'envoi des emails, j'ai choisi PHPMailer avec un serveur SMTP. Il est notamment utilisé pour :

- le formulaire de contact ;
- la réinitialisation du mot de passe ;
- les notifications de validation d'un personnage ;
- les notifications de refus d'un personnage ;
- les notifications liées à la validation des commentaires.

Les identifiants SMTP sont stockés dans un fichier séparé `mail-config.php`.

Ce fichier est exclu du dépôt Git grâce au fichier `.gitignore`.

Un fichier `mail-config.example.php` permet de présenter la configuration nécessaire sans publier les véritables identifiants.

MongoDB est utilisé pour enregistrer un journal des actions importantes effectuées dans les espaces employé et administrateur.

L'interface a d'abord été pensée avec des wireframes puis avec des maquettes desktop et mobile réalisées sur Figma. Le développement CSS reprend ensuite cette identité graphique.

Le projet utilise une architecture PHP simple avec des fichiers séparés pour les différentes pages et fonctionnalités afin de faciliter l'organisation et la compréhension du code.

---

## Base de données relationnelle MySQL

La base de données MySQL contient les données principales nécessaires au fonctionnement de l'application.

Elle contient notamment les informations concernant :

- les utilisateurs ;
- les personnages ;
- les commentaires ;
- les éléments de personnalisation ;
- les équipements et pouvoirs associés aux personnages.

Les relations entre les différentes tables permettent par exemple :

- d'associer un personnage à son propriétaire ;
- d'associer un commentaire à son auteur et à un personnage ;
- d'associer plusieurs éléments de personnalisation à un personnage.

Un export SQL de la base de données est disponible dans :

```text
database/fantasyrealm.sql
```

Cet export permet de recréer la structure et les données nécessaires au fonctionnement du projet.

---

## Base de données non relationnelle MongoDB

MongoDB est utilisé pour la journalisation des actions importantes de l'application.

La base utilisée est :

```text
fantasyrealm_logs
```

La collection utilisée est :

```text
logs
```

Chaque journal peut notamment contenir :

- un identifiant MongoDB ;
- le type d'action réalisée ;
- une description de l'action ;
- l'identifiant de l'utilisateur ayant effectué l'action ;
- la date de l'action.

Exemple de structure d'un journal :

```text
{
    _id: ObjectId(...),
    action: "...",
    details: "...",
    utilisateur_id: 5,
    date: ISODate(...)
}
```

La connexion à MongoDB est centralisée dans le fichier :

```text
mongodb.php
```

Une fonction permet ensuite aux différentes parties de l'application d'enregistrer les actions dans MongoDB.

Parmi les actions pouvant être journalisées :

- validation d'un personnage ;
- refus d'un personnage ;
- validation d'un commentaire ;
- refus d'un commentaire ;
- suspension d'un utilisateur ;
- réactivation d'un utilisateur ;
- suppression d'un personnage ;
- ajout d'un élément de personnalisation ;
- désactivation d'un élément de personnalisation ;
- réactivation d'un élément de personnalisation ;
- suppression d'un élément de personnalisation ;
- création d'un employé ;
- modification du mot de passe d'un employé ;
- suspension d'un employé ;
- réactivation d'un employé ;
- suppression d'un employé.

L'administrateur dispose également d'un journal permettant de consulter les actions enregistrées.

---

## Modélisation et diagrammes

Plusieurs diagrammes ont été réalisés afin de présenter la conception et le fonctionnement de l'application.

### MCD

Le modèle conceptuel de données présente les principales entités de la base de données relationnelle ainsi que leurs relations.

Fichier :

```text
docs/documentation-technique/MCD-FANTASYREALM.pdf
```

### Diagramme de classes

Le diagramme de classes représente les principales entités métier de l'application ainsi que leurs attributs, leurs fonctionnalités et leurs relations.

Fichier :

```text
docs/documentation-technique/DIAGRAMME-CLASSES-FANTASYREALM.pdf
```

### Diagramme de cas d'utilisation

Le diagramme de cas d'utilisation présente les principales fonctionnalités accessibles selon les différents rôles de l'application : utilisateur, employé et administrateur.

Fichier :

```text
docs/documentation-technique/DIAGRAMME-CAS-UTILISATION-FANTASYREALM.pdf
```

### Diagramme de séquence

Le diagramme de séquence présente le scénario de création puis de validation d'un personnage.

Il montre les échanges entre l'utilisateur, l'application PHP, la base de données MySQL, l'employé et le système d'envoi d'emails.

Lorsqu'un personnage est validé ou refusé par un employé, l'application peut envoyer une notification par email à son propriétaire.

Lors d'un refus, le motif peut être communiqué à l'utilisateur.

Fichier :

```text
docs/documentation-technique/DIAGRAMME-SEQUENCE-FANTASYREALM.pdf
```

### Diagramme de déploiement

Le diagramme de déploiement représente l'architecture technique de l'application.

En environnement local, l'application utilise XAMPP avec Apache, PHP et MySQL.

MongoDB fonctionne localement comme base de données non relationnelle destinée aux journaux d'activité.

PHPMailer communique avec un serveur SMTP pour l'envoi des emails.

Le déploiement public constitue la dernière étape de mise en ligne du projet.

Fichier :

```text
docs/documentation-technique/DIAGRAMME-DEPLOIEMENT-FANTASYREALM.pdf
```

---

## Gestion des rôles

### Utilisateur

Un utilisateur peut notamment :

- créer un compte ;
- se connecter ;
- demander la réinitialisation de son mot de passe ;
- créer un personnage ;
- gérer ses personnages ;
- personnaliser un personnage validé ;
- partager ou retirer du partage un personnage ;
- consulter les personnages publics ;
- consulter le détail d'un personnage ;
- attribuer une note ;
- proposer un commentaire ;
- utiliser le formulaire de contact.

### Employé

Un employé possède des fonctionnalités de modération.

Il peut notamment :

- valider un nom de personnage ;
- refuser un personnage avec un motif ;
- valider un commentaire ;
- refuser un commentaire ;
- gérer les éléments de personnalisation ;
- suspendre ou réactiver un utilisateur ;
- supprimer certains contenus.

Les actions importantes réalisées depuis cet espace sont journalisées dans MongoDB.

### Administrateur

L'administrateur possède les droits nécessaires à la gestion des employés.

Il peut notamment :

- créer un compte employé ;
- modifier le mot de passe d'un employé ;
- suspendre un employé ;
- réactiver un employé ;
- supprimer un employé ;
- consulter le journal des actions.

Les actions administratives importantes sont également enregistrées dans MongoDB.

---

## Envoi des emails

PHPMailer est utilisé afin d'envoyer de véritables emails depuis l'application.

La configuration SMTP est stockée séparément dans :

```text
mail-config.php
```

Ce fichier contient les informations privées nécessaires à la connexion au serveur SMTP et n'est pas envoyé sur le dépôt Git public.

L'application utilise les emails notamment pour :

- le formulaire de contact ;
- la récupération du mot de passe ;
- la validation d'un personnage ;
- le refus d'un personnage ;
- certaines notifications liées à la modération.

---

## Filtrage des contenus interdits

Un système de filtrage automatique est utilisé pour limiter l'enregistrement de certains contenus interdits.

La logique commune est centralisée dans :

```text
mots-interdits.php
```

Ce contrôle est notamment utilisé lors de la création d'un personnage et lors de l'envoi d'un commentaire.

Ce filtrage automatique complète la modération humaine réalisée par les employés.

---

## Sécurité et protection des données

La sécurité a été prise en compte pendant le développement de l'application afin de protéger les comptes utilisateurs et les données enregistrées.

### Sécurité des mots de passe

Les mots de passe ne sont pas enregistrés directement dans la base de données.

PHP utilise `password_hash()` pour créer un hash du mot de passe.

Lors de la connexion, `password_verify()` permet de comparer le mot de passe renseigné avec le hash enregistré dans la base de données.

La création d'un compte impose également plusieurs règles pour le mot de passe :

- au moins une lettre majuscule ;
- au moins une lettre minuscule ;
- au moins un chiffre ;
- au moins un caractère spécial.

### Sécurité de la base de données

Les échanges avec MySQL sont réalisés avec PDO.

Des requêtes préparées sont utilisées afin de limiter les risques d'injection SQL.

Les données reçues depuis les formulaires sont vérifiées avant leur utilisation.

### Protection contre les attaques XSS

Lors de l'affichage de données pouvant provenir d'un utilisateur, l'application utilise notamment `htmlspecialchars()` afin d'éviter que du code HTML ou JavaScript soit directement interprété par le navigateur.

### Gestion des sessions et des droits

Les sessions PHP permettent d'identifier l'utilisateur connecté.

Certaines pages sont accessibles uniquement lorsque l'utilisateur est connecté.

Les rôles permettent de limiter l'accès aux fonctionnalités réservées aux employés et aux administrateurs.

Avant la modification ou la suppression d'un personnage, l'application vérifie que celui-ci appartient bien à l'utilisateur connecté.

Les espaces de modération vérifient également le rôle de l'utilisateur avant d'autoriser l'accès aux fonctionnalités sensibles.

### Protection des informations SMTP

Les identifiants permettant l'envoi des emails sont enregistrés dans un fichier `mail-config.php`.

Ce fichier est exclu du dépôt Git grâce au fichier `.gitignore`.

Le fichier :

```text
mail-config.example.php
```

permet d'expliquer la configuration nécessaire sans publier les véritables identifiants SMTP.

---

## Git et GitHub

Git est utilisé pour le versionnement du projet.

Le projet utilise notamment les branches :

```text
main
develop
feature/finalisation-documentation
```

Le dépôt GitHub permet de sauvegarder le code source et de présenter le projet.

Les fichiers contenant des informations confidentielles, notamment `mail-config.php`, sont exclus du dépôt grâce au fichier `.gitignore`.

---

## RGPD

L'application utilise certaines données personnelles nécessaires au fonctionnement des comptes utilisateurs.

Les principales données enregistrées sont :

- le pseudo ;
- l'adresse email ;
- le mot de passe sous forme de hash ;
- les personnages créés ;
- les commentaires et les notes publiés.

Les données demandées sont limitées aux informations nécessaires au fonctionnement de l'application.

Les mots de passe ne sont jamais enregistrés en clair dans la base de données.

L'adresse email est notamment utilisée pour l'authentification, la récupération du mot de passe et certaines notifications liées à l'application.

Les données ne doivent pas être communiquées à des personnes non autorisées.

Les fonctionnalités de gestion des comptes participent à la gestion des données enregistrées dans l'application.

Les mentions légales et les informations concernant le traitement des données sont accessibles depuis l'application.

---

## Accessibilité et RGAA

L'accessibilité de l'interface a été prise en compte pendant la conception et le développement de l'application.

Les principaux points pris en compte ou vérifiés sont :

- présence de textes alternatifs pour les images importantes ;
- utilisation de labels compréhensibles pour les champs de formulaire ;
- lisibilité des textes ;
- contraste entre le texte et l'arrière-plan ;
- structure logique des titres ;
- boutons et liens compréhensibles ;
- messages d'erreur compréhensibles dans les formulaires ;
- adaptation de l'affichage aux écrans desktop et mobile.

Les couleurs principales de l'application sont le prune, le violet, l'or et le blanc cassé.

Une attention particulière est portée au contraste entre ces couleurs afin de conserver une bonne lisibilité.

Une vérification finale de l'interface sera effectuée avant la livraison.

---

## État actuel du projet

Les principaux éléments techniques sont maintenant intégrés :

- base de données relationnelle MySQL ;
- base de données non relationnelle MongoDB ;
- authentification ;
- gestion des rôles ;
- création et gestion des personnages ;
- système de commentaires et de notation ;
- modération des personnages ;
- modération des commentaires ;
- gestion des utilisateurs ;
- administration des employés ;
- journalisation MongoDB ;
- envoi d'emails avec PHPMailer ;
- récupération du mot de passe ;
- filtrage automatique de certains contenus ;
- versionnement Git et dépôt GitHub ;
- documentation de conception.

La personnalisation des personnages et les dernières vérifications de l'application sont finalisées avant la livraison.

Le déploiement public est réalisé après les tests finaux et la préparation de la version définitive du projet.


---

## Dépôt GitHub

Le code source du projet est disponible sur GitHub :

https://github.com/Zbiwoo/fantasyrealm-character-manager