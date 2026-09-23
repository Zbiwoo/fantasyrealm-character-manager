# FantasyRealm Character Manager

## Présentation du projet

FantasyRealm Character Manager est une application web développée dans le cadre de mon ECF Graduate Développeur Web Gaming pour PixelVerse Studios.

L'application permet de gérer les personnages du MMORPG fictif **FantasyRealm Online**.

Les utilisateurs peuvent créer leurs propres personnages, personnaliser leur apparence, leur attribuer des équipements et des pouvoirs, puis choisir de les partager avec la communauté.

Les employés disposent d'un espace permettant de modérer les personnages et les commentaires publiés par les utilisateurs.

Les administrateurs disposent également d'un espace dédié à la gestion des employés et à la consultation des journaux d'activité.

---

## Fonctionnalités

### Utilisateur

- Création d'un compte
- Connexion et déconnexion
- Réinitialisation du mot de passe par email
- Consultation des personnages partagés
- Filtrage des personnages
- Consultation du profil des créateurs
- Création d'un personnage
- Gestion de ses propres personnages
- Personnalisation d'un personnage validé
- Choix d'équipements et de pouvoirs
- Partage et retrait du partage d'un personnage
- Suppression d'un personnage
- Ajout d'une note et d'un commentaire
- Formulaire de contact

### Employé

- Accès à un espace réservé
- Consultation des personnages en attente
- Validation des noms de personnages
- Refus d'un personnage avec un motif
- Notification du propriétaire après validation ou refus
- Consultation des commentaires en attente
- Validation des commentaires
- Refus des commentaires
- Gestion des équipements et pouvoirs
- Gestion des utilisateurs
- Journalisation des actions

### Administrateur

- Accès à l'espace d'administration
- Création d'un compte employé
- Modification du mot de passe d'un employé
- Suspension d'un employé
- Réactivation d'un employé
- Suppression d'un employé
- Consultation des journaux d'activité

---

## Technologies utilisées

### Front-end

- HTML5
- CSS3
- JavaScript

### Back-end

- PHP
- PDO

### Bases de données

- MySQL / MariaDB
- MongoDB pour la journalisation des actions

### Emails

- PHPMailer
- SMTP

### Outils

- Visual Studio Code
- XAMPP
- Apache
- phpMyAdmin
- MongoDB Compass
- MongoDB Shell
- Git
- GitHub
- Figma

---

## Installation en local

### 1. Prérequis

Pour lancer le projet en local, il faut installer :

- XAMPP
- MongoDB Community Server
- MongoDB Compass ou MongoDB Shell
- Un navigateur web
- Git si le projet est récupéré depuis GitHub

---

### 2. Installation du projet

Placer le dossier du projet dans le dossier `htdocs` de XAMPP.

Exemple sous Windows :

```text
C:\xampp\htdocs\fantasyrealm-character-manager
```

---

### 3. Base de données MySQL

Démarrer **Apache** et **MySQL** depuis XAMPP.

Ouvrir ensuite phpMyAdmin :

```text
http://localhost/phpmyadmin/
```

Créer une base de données nommée :

```text
fantasyrealm
```

Importer ensuite le fichier :

```text
database/fantasyrealm.sql
```

---

### 4. Configuration de MySQL

La connexion à MySQL est configurée dans :

```text
config.php
```

Configuration utilisée pour l'environnement local :

- Hôte : `localhost`
- Base de données : `fantasyrealm`
- Encodage : `utf8mb4`

Les identifiants doivent être adaptés selon l'environnement utilisé.

---

## Configuration de MongoDB

MongoDB est utilisé pour la journalisation des actions.

La base utilisée est :

```text
fantasyrealm_logs
```

La collection utilisée est :

```text
logs
```

La connexion à MongoDB est configurée dans :

```text
mongodb.php
```

MongoDB doit être démarré avant d'utiliser les fonctionnalités nécessitant l'enregistrement des journaux.

---

## Configuration des emails

L'application utilise PHPMailer afin d'envoyer certains emails, notamment pour :

- le formulaire de contact ;
- la réinitialisation du mot de passe ;
- la validation d'un personnage ;
- le refus d'un personnage ;
- certaines notifications de modération.

Pour des raisons de sécurité, le fichier contenant les véritables identifiants SMTP n'est pas présent dans le dépôt GitHub.

Créer un fichier :

```text
mail-config.php
```

à partir du fichier :

```text
mail-config.example.php
```

Puis renseigner les informations SMTP nécessaires.

**Ne jamais publier les identifiants ou mots de passe SMTP sur GitHub.**

---

## Lancement de l'application

Démarrer Apache et MySQL avec XAMPP.

Démarrer également MongoDB si les fonctionnalités de journalisation doivent être utilisées.

Puis ouvrir dans le navigateur :

```text
http://localhost/fantasyrealm-character-manager/
```

---

## Organisation du projet

```text
fantasyrealm-character-manager/

│
├── assets/
│   ├── CSS/
│   ├── JS/
│   └── images/
│
├── database/
│   └── fantasyrealm.sql
│
├── docs/
│   ├── charte-graphique/
│   ├── documentation-technique/
│   ├── gestion-projet/
│   └── manuel-utilisateur/
│
├── PHPMailer/
│
├── config.php
├── mongodb.php
├── index.php
├── connexion.php
├── inscription.php
├── deconnexion.php
├── contact.php
├── personnages.php
├── personnage.php
├── creer-personnage.php
├── mes-personnages.php
├── modifier-personnage.php
├── profil.php
├── validation-personnages.php
├── mot-de-passe-oublie.php
├── reinitialiser-mot-de-passe.php
├── mentions-legales.php
├── cgv.php
├── navbar.php
├── mail-config.example.php
├── .gitignore
└── README.md
```

---

## Documentation

La documentation du projet est regroupée dans le dossier `docs`.

Elle contient notamment :

- la charte graphique ;
- les wireframes desktop et mobile ;
- les maquettes desktop et mobile ;
- la documentation technique ;
- les diagrammes techniques ;
- les documents de gestion de projet ;
- le manuel utilisateur.

---

## Compte de démonstration

Un compte administrateur de démonstration peut être utilisé pour tester les fonctionnalités d'administration.

Les identifiants ne sont volontairement pas publiés dans ce README afin d'éviter de diffuser des informations d'authentification dans le dépôt public.

Les identifiants de démonstration peuvent être communiqués séparément lors de la présentation du projet.

---

## Sécurité

Plusieurs mesures sont utilisées dans l'application :

- Hachage des mots de passe avec `password_hash()`
- Vérification des mots de passe avec `password_verify()`
- Requêtes préparées avec PDO
- Contrôle des sessions utilisateur
- Contrôle des rôles utilisateur
- Vérification de la propriété des personnages avant modification
- Validation des données reçues depuis les formulaires
- Protection contre l'affichage de données HTML non échappées avec `htmlspecialchars()`
- Protection des identifiants SMTP avec `.gitignore`
- Séparation des données relationnelles et des journaux d'activité

---

## Git et GitHub

Le projet utilise Git pour le versionnement du code.

Les principales branches utilisées sont :

```text
main
develop
feature/finalisation-documentation
```

Le fichier `.gitignore` permet notamment d'exclure les informations sensibles de la configuration SMTP.

Le dépôt GitHub contient le code source et les documents nécessaires à la présentation du projet.

---

## Interface graphique

L'identité visuelle de FantasyRealm Online repose sur un univers fantasy sombre avec des couleurs prune, violet et or.

Les maquettes desktop et mobile ainsi que la charte graphique ont été réalisées avec Figma.

L'interface utilise une feuille de style CSS personnalisée sans framework graphique externe.

---

## État du projet

Le projet comprend actuellement :

- l'application utilisateur ;
- l'espace employé ;
- l'espace administrateur ;
- la base MySQL ;
- la journalisation MongoDB ;
- l'envoi d'emails avec PHPMailer ;
- les documents techniques ;
- les documents de gestion de projet ;
- le dépôt GitHub.

La personnalisation visuelle des traits du personnage fait encore l'objet d'une finalisation avant les tests définitifs et le déploiement.

---

## Auteur

Projet réalisé dans le cadre de mon ECF **Graduate Développeur Web Gaming**.