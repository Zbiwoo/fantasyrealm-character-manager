# Documentation technique - FantasyRealm Character Manager

## Présentation

Cette documentation technique présente la conception et le développement de l'application FantasyRealm Character Manager réalisée pour PixelVerse Studios dans le cadre de mon ECF.

Elle regroupe les choix techniques, l'environnement de développement, la conception des bases de données, les différents diagrammes ainsi que les informations nécessaires au déploiement du projet.

---

## Environnement de développement

Le projet a été développé en local avec les outils suivants :

- Visual Studio Code
- XAMPP
- Apache
- PHP
- MySQL / MariaDB
- phpMyAdmin
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

---

## Architecture générale

L'application utilise PHP pour la partie serveur et MySQL pour le stockage des données principales.

PDO est utilisé pour communiquer avec la base de données à l'aide de requêtes préparées.

Le front-end est réalisé en HTML5, CSS3 et JavaScript sans utilisation de Bootstrap.

PHPMailer est utilisé pour l'envoi des emails de l'application.

Une base de données non relationnelle MongoDB est prévue pour la journalisation des actions de l'application.

---

## Réflexion initiale et choix techniques

Avant de commencer le développement, j'ai d'abord analysé les besoins de PixelVerse Studios afin d'identifier les différentes fonctionnalités de l'application et les rôles des utilisateurs.

L'application possède trois types de comptes :

- Utilisateur
- Employé
- Administrateur

Chaque rôle possède des droits différents dans l'application.

J'ai choisi PHP pour réaliser la partie serveur car il permet de gérer les sessions, les formulaires et les interactions avec la base de données.

MySQL est utilisé comme base de données relationnelle pour stocker les utilisateurs, les personnages, les commentaires ainsi que les éléments de personnalisation.

PDO permet de communiquer avec MySQL et d'utiliser des requêtes préparées afin de sécuriser les échanges avec la base de données.

JavaScript est utilisé pour certaines interactions dynamiques de l'interface, notamment dans la personnalisation des personnages.

Pour l'envoi des emails, j'ai choisi PHPMailer avec un serveur SMTP. Il est utilisé pour le formulaire de contact et la réinitialisation du mot de passe.

Les identifiants SMTP sont stockés dans un fichier séparé `mail-config.php`. Ce fichier est exclu du dépôt Git grâce au fichier `.gitignore`.

L'interface a d'abord été pensée avec des wireframes puis avec des maquettes desktop et mobile réalisées sur Figma. Le développement CSS reprend ensuite cette identité graphique.

Le projet utilise une architecture simple en PHP avec des fichiers séparés pour les différentes pages et fonctionnalités afin de faciliter l'organisation et la compréhension du code.

---

## Organisation des données

La base de données relationnelle contient notamment les informations concernant :

- les utilisateurs ;
- les personnages ;
- les commentaires ;
- les éléments de personnalisation ;
- les équipements et pouvoirs associés aux personnages.

Les relations entre les différentes tables permettent par exemple d'associer un personnage à son propriétaire, un commentaire à son auteur et à un personnage, ou encore plusieurs éléments de personnalisation à un personnage.

Un export de la base de données est disponible dans :

```text
database/fantasyrealm.sql
```

---

## Modélisation et diagrammes

Plusieurs diagrammes ont été réalisés afin de présenter la conception et le fonctionnement de l'application.

### MCD

Le modèle conceptuel de données présente les principales entités de la base de données ainsi que leurs relations.

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

La notification par email après validation ou refus d'un personnage reste à finaliser dans l'application.

Fichier :

```text
docs/documentation-technique/DIAGRAMME-SEQUENCE-FANTASYREALM.pdf
```

### Diagramme de déploiement

Le diagramme de déploiement représente l'architecture technique de l'application.

En environnement local, l'application utilise XAMPP avec Apache, PHP et MySQL.

PHPMailer communique avec un serveur SMTP pour l'envoi des emails.

MongoDB est prévu pour la journalisation des actions mais son intégration reste à finaliser.

Le déploiement public de l'application reste également à réaliser.

Fichier :

```text
docs/documentation-technique/DIAGRAMME-DEPLOIEMENT-FANTASYREALM.pdf
```

---

## État actuel de la documentation technique

Les documents suivants sont disponibles :

- MCD ;
- diagramme de classes ;
- diagramme de cas d'utilisation ;
- diagramme de séquence ;
- diagramme de déploiement.

Les éléments liés au déploiement final et à MongoDB seront complétés lorsque ces fonctionnalités seront intégrées au projet.

---

## Sécurité et protection des données

La sécurité a été prise en compte pendant le développement de l'application afin de protéger les comptes utilisateurs et les données enregistrées.

### Sécurité des mots de passe

Les mots de passe ne sont pas enregistrés directement dans la base de données.

PHP utilise `password_hash()` pour créer un hash du mot de passe lors de l'inscription.

Lors de la connexion, `password_verify()` permet de comparer le mot de passe renseigné avec le hash enregistré dans la base de données.

La création d'un compte impose également plusieurs règles pour le mot de passe :

- au moins une lettre majuscule ;
- au moins une lettre minuscule ;
- au moins un chiffre ;
- au moins un caractère spécial.

### Sécurité de la base de données

Les échanges avec MySQL sont réalisés avec PDO.

Des requêtes préparées sont utilisées afin de limiter les risques d'injection SQL.

Les données reçues depuis les formulaires sont également vérifiées avant leur utilisation.

### Gestion des sessions et des droits

Les sessions PHP permettent d'identifier l'utilisateur connecté.

Certaines pages sont accessibles uniquement lorsque l'utilisateur est connecté.

Les rôles permettent également de limiter l'accès aux fonctionnalités réservées aux employés et aux administrateurs.

Avant la modification ou la suppression d'un personnage, l'application vérifie que celui-ci appartient bien à l'utilisateur connecté.

### Protection des informations SMTP

Les identifiants permettant l'envoi des emails sont enregistrés dans un fichier `mail-config.php`.

Ce fichier est exclu du dépôt Git grâce au fichier `.gitignore`.

Un fichier `mail-config.example.php` peut être présent dans le dépôt afin d'expliquer la configuration nécessaire sans publier les véritables identifiants.

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

Les fonctionnalités permettant la gestion et la suppression des comptes doivent également permettre de respecter les droits des utilisateurs concernant leurs données.

Les mentions légales et les informations concernant le traitement des données doivent être accessibles depuis l'application.

---

## Accessibilité et RGAA

L'accessibilité de l'interface est prise en compte pendant la conception et le développement de l'application.

Plusieurs points doivent être vérifiés avant la livraison :

- présence de textes alternatifs pour les images importantes ;
- utilisation de labels compréhensibles pour les champs de formulaire ;
- lisibilité des textes ;
- contraste suffisant entre le texte et l'arrière-plan ;
- navigation utilisable au clavier ;
- structure logique des titres ;
- boutons et liens suffisamment compréhensibles ;
- indication compréhensible des erreurs dans les formulaires ;
- affichage adapté aux écrans desktop et mobile.

Les couleurs principales de l'application sont le prune, le violet, l'or et le blanc cassé. Une attention particulière doit être portée au contraste entre ces couleurs afin de conserver une bonne lisibilité.

Une vérification finale de l'accessibilité sera réalisée avant la livraison afin de corriger les problèmes identifiés.