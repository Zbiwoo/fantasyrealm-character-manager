# FantasyRealm Character Manager

## Présentation du projet

FantasyRealm Character Manager est une application web développée dans le cadre de mon ECF pour PixelVerse Studios.

L'application permet de gérer les personnages du MMORPG fictif **FantasyRealm Online**.

Les utilisateurs peuvent créer leurs propres personnages, personnaliser leur apparence, leur attribuer des équipements et des pouvoirs, puis choisir de les partager avec la communauté.

Les employés disposent également d'un espace permettant de modérer les personnages et les commentaires publiés par les utilisateurs.

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
- Refus d'un personnage
- Consultation des commentaires en attente
- Validation des commentaires
- Refus des commentaires

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

- MySQL
- MongoDB pour la journalisation des actions

### Outils

- Visual Studio Code
- XAMPP
- phpMyAdmin
- Git
- GitHub
- Figma

---

## Installation en local

### 1. Prérequis

Pour lancer le projet en local, il faut installer :

- XAMPP
- Un navigateur web
- Git, si le projet est récupéré depuis GitHub

---

### 2. Installation du projet

Placer le dossier du projet dans le dossier `htdocs` de XAMPP.

Exemple sous Windows :

```text
C:\xampp\htdocs\fantasyrealm-character-manager
```

---

### 3. Base de données

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

### 4. Configuration de la base de données

La connexion à MySQL est configurée dans le fichier :

```text
config.php
```

Configuration utilisée pour l'environnement local :

- Hôte : localhost
- Base de données : fantasyrealm
- Encodage : utf8mb4

Les identifiants devront être adaptés en fonction de l'environnement utilisé.

---

## Configuration des emails

L'application utilise PHPMailer afin d'envoyer certains emails, notamment pour :

- le formulaire de contact ;
- la réinitialisation du mot de passe.

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
│   │   └── CHARTE-GRAPHIQUE-FANTASYREALM.pdf
│   ├── documentation-technique/
│   ├── gestion-projet/
│   └── manuel-utilisateur/
│
├── PHPMailer/
│
├── config.php
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
└── README.md
```

---

## Documentation

La documentation du projet est regroupée dans le dossier `docs`.

Elle contient ou contiendra :

- La charte graphique
- La documentation technique
- Les documents de gestion de projet
- Le manuel utilisateur

La charte graphique contient notamment la palette de couleurs, les typographies, les composants graphiques, les wireframes desktop et mobile ainsi que les maquettes desktop et mobile.

---

## Compte de démonstration

Un compte administrateur est pré-configuré afin de permettre de tester les fonctionnalités réservées à l'administration.

- **Email :** admin@fantasyrealm.fr
- **Mot de passe :** Admin123!
- **Rôle :** Administrateur

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
- Protection des identifiants SMTP avec `.gitignore`

---

## Interface graphique

L'identité visuelle de FantasyRealm Online repose sur un univers fantasy sombre avec des couleurs prune, violet et or.

Les maquettes desktop et mobile ainsi que la charte graphique ont été réalisées avec Figma.

---

## Auteur

Projet réalisé dans le cadre de mon ECF **Graduate Développeur Web Gaming**.