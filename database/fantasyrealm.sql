-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 23 sep. 2026 à 16:55
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `fantasyrealm`
--

-- --------------------------------------------------------

--
-- Structure de la table `commentaires`
--

CREATE TABLE `commentaires` (
  `id` int(11) NOT NULL,
  `personnage_id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `commentaire` text NOT NULL,
  `note` int(11) NOT NULL,
  `statut` varchar(20) NOT NULL DEFAULT 'en_attente',
  `date_creation` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `commentaires`
--

INSERT INTO `commentaires` (`id`, `personnage_id`, `utilisateur_id`, `commentaire`, `note`, `statut`, `date_creation`) VALUES
(1, 4, 1, 'Trop belle', 5, 'valide', '2026-09-17 19:51:10'),
(2, 7, 3, 'magnifiqueee', 5, 'valide', '2026-09-22 22:12:30'),
(3, 7, 5, 'belle', 5, 'valide', '2026-09-23 12:00:06');

-- --------------------------------------------------------

--
-- Structure de la table `elements_personnalisation`
--

CREATE TABLE `elements_personnalisation` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `type` enum('equipement','pouvoir') NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  `date_creation` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `elements_personnalisation`
--

INSERT INTO `elements_personnalisation` (`id`, `nom`, `type`, `description`, `actif`, `date_creation`) VALUES
(1, 'Épée des Ombres', 'equipement', 'Une lame forgée dans les ténèbres.', 1, '2026-09-18 06:49:36'),
(2, 'Armure obscure', 'equipement', 'Une armure renforcée par la magie noire.', 1, '2026-09-18 06:49:36'),
(3, 'Arc elfique', 'equipement', 'Un arc léger conçu par les elfes.', 1, '2026-09-18 06:49:36'),
(4, 'Bâton mystique', 'equipement', 'Un bâton permettant de canaliser la magie.', 1, '2026-09-18 06:49:36'),
(5, 'Bouclier royal', 'equipement', 'Un bouclier robuste aux ornements royaux.', 1, '2026-09-18 06:49:36'),
(6, 'Boule de feu', 'pouvoir', 'Projette une puissante boule de feu.', 1, '2026-09-18 06:49:36'),
(7, 'Foudre', 'pouvoir', 'Invoque un éclair sur un adversaire.', 1, '2026-09-18 06:49:36'),
(8, 'Soin', 'pouvoir', 'Restaure une partie de la vitalité.', 1, '2026-09-18 06:49:36'),
(9, 'Invisibilité', 'pouvoir', 'Permet de disparaître temporairement.', 1, '2026-09-18 06:49:36'),
(10, 'Téléportation', 'pouvoir', 'Permet de se déplacer instantanément.', 1, '2026-09-18 06:49:36');

-- --------------------------------------------------------

--
-- Structure de la table `personnages`
--

CREATE TABLE `personnages` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `genre` varchar(20) NOT NULL,
  `modele_visuel` varchar(20) NOT NULL DEFAULT 'guerriere',
  `image` varchar(100) DEFAULT NULL,
  `visage` varchar(50) DEFAULT NULL,
  `couleur_cheveux` varchar(50) DEFAULT NULL,
  `couleur_yeux` varchar(50) DEFAULT NULL,
  `coiffure` varchar(50) DEFAULT NULL,
  `forme_yeux` varchar(50) DEFAULT NULL,
  `date_creation` datetime NOT NULL DEFAULT current_timestamp(),
  `statut_nom` varchar(20) NOT NULL DEFAULT 'en_attente',
  `partage` tinyint(1) NOT NULL DEFAULT 0,
  `motif_refus` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `personnages`
--

INSERT INTO `personnages` (`id`, `utilisateur_id`, `nom`, `genre`, `modele_visuel`, `image`, `visage`, `couleur_cheveux`, `couleur_yeux`, `coiffure`, `forme_yeux`, `date_creation`, `statut_nom`, `partage`, `motif_refus`) VALUES
(1, 1, 'Mage Noire', 'Femme', 'guerriere', NULL, 'Normal', '#2d0047', '#000000', 'Long', 'Etroits', '2026-09-17 14:14:13', 'valide', 1, NULL),
(4, 1, 'Guerrière', 'Femme', 'guerriere', NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-17 18:56:32', 'valide', 1, NULL),
(5, 1, 'Archère', 'Femme', 'guerriere', NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-17 18:56:32', 'valide', 1, NULL),
(6, 1, 'Sorcière', 'Femme', 'guerriere', NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-17 18:56:32', 'valide', 1, NULL),
(7, 3, 'Nyxaria', 'Femme', 'guerriere', 'tieffelin.png', 'Rond', '#09011e', '#000000', 'Court', 'Ronds', '2026-09-17 22:55:36', 'valide', 1, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `personnage_elements`
--

CREATE TABLE `personnage_elements` (
  `personnage_id` int(11) NOT NULL,
  `element_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `personnage_elements`
--

INSERT INTO `personnage_elements` (`personnage_id`, `element_id`) VALUES
(7, 2),
(7, 3),
(7, 9);

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL,
  `pseudo` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'utilisateur',
  `suspendu` tinyint(1) NOT NULL DEFAULT 0,
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_token_expiration` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `pseudo`, `email`, `mot_de_passe`, `role`, `suspendu`, `reset_token`, `reset_token_expiration`) VALUES
(1, 'Testeur1', 'testeur1@test.fr', '$2y$10$2KHahQDXi9pViZoRZPYZqebdWnQyCfHi1PopOOQxaMDq3ptNAS1Sm', 'utilisateur', 0, NULL, NULL),
(2, 'Employe1', 'employe1@test.fr', '$2y$10$uXSL6jm.XjAoTdrzf90WheLMMwjs3/63K5G45NSyT4WlsrG2sIxEi', 'employe', 0, NULL, NULL),
(3, 'fantasyo142004', 'fantasyo447@gmail.com', '$2y$10$OdiG6l264rYiy153xpU0s.oU3l7b3PtCVabwfaCMpz.QRGZAUKuay', 'utilisateur', 0, NULL, NULL),
(4, 'Testeur2', 'testeur2@test.fr', '$2y$10$N1NvEo45TCJ7TY8xTtXSgOLVcxAfPiyXQbUsjyXSTuy3XwpR2QM3a', 'utilisateur', 0, NULL, NULL),
(5, 'admin', 'admin@fantasyrealm.fr', '$2y$10$sAZQt8SNG4qy/e7sspzRFuFp99/23OHl5Wm9wOSZQAIUyuUZa7VHK', 'admin', 0, NULL, NULL),
(8, 'EmployeMongo', 'employemongo@test.fr', '$2y$10$wawsMtXYghdq8Ro.ETfokuHzAL.u1LBzB4GvtGxmB445kHoESwK3u', 'employe', 0, NULL, NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `commentaires`
--
ALTER TABLE `commentaires`
  ADD PRIMARY KEY (`id`),
  ADD KEY `personnage_id` (`personnage_id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`);

--
-- Index pour la table `elements_personnalisation`
--
ALTER TABLE `elements_personnalisation`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nom` (`nom`);

--
-- Index pour la table `personnages`
--
ALTER TABLE `personnages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`);

--
-- Index pour la table `personnage_elements`
--
ALTER TABLE `personnage_elements`
  ADD PRIMARY KEY (`personnage_id`,`element_id`),
  ADD KEY `element_id` (`element_id`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pseudo` (`pseudo`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `commentaires`
--
ALTER TABLE `commentaires`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `elements_personnalisation`
--
ALTER TABLE `elements_personnalisation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pour la table `personnages`
--
ALTER TABLE `personnages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `commentaires`
--
ALTER TABLE `commentaires`
  ADD CONSTRAINT `commentaires_ibfk_1` FOREIGN KEY (`personnage_id`) REFERENCES `personnages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `commentaires_ibfk_2` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `personnages`
--
ALTER TABLE `personnages`
  ADD CONSTRAINT `personnages_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`);

--
-- Contraintes pour la table `personnage_elements`
--
ALTER TABLE `personnage_elements`
  ADD CONSTRAINT `personnage_elements_ibfk_1` FOREIGN KEY (`personnage_id`) REFERENCES `personnages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `personnage_elements_ibfk_2` FOREIGN KEY (`element_id`) REFERENCES `elements_personnalisation` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
