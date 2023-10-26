-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 26 oct. 2023 à 11:52
-- Version du serveur : 10.4.28-MariaDB
-- Version de PHP : 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `snowtricks`
--

-- --------------------------------------------------------

--
-- Structure de la table `comment`
--

CREATE TABLE `comment` (
  `id` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `user_profile_image` varchar(255) NOT NULL,
  `created_at` date NOT NULL,
  `content` varchar(255) NOT NULL,
  `id_trick` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `comment`
--

INSERT INTO `comment` (`id`, `id_user`, `user_profile_image`, `created_at`, `content`, `id_trick`) VALUES
(1, 2, '/assets/img/user-profile/9wlgcd6OmiUp.jpg\r\n', '2023-10-23', 'Commentaire 1', 299),
(2, 2, '/assets/img/user-profile/9wlgcd6OmiUp.jpg\r\n', '2023-10-23', 'Commentaire 2', 299),
(3, 2, '/assets/img/user-profile/9wlgcd6OmiUp.jpg\r\n', '2023-10-23', 'Commentaire 3', 299),
(4, 2, '/assets/img/user-profile/9wlgcd6OmiUp.jpg\r\n', '2023-10-23', 'Commentaire 4', 299),
(5, 2, '/assets/img/user-profile/9wlgcd6OmiUp.jpg\r\n', '2023-10-23', 'Commentaire 5', 299),
(6, 2, '/assets/img/user-profile/9wlgcd6OmiUp.jpg\r\n', '2023-10-23', 'Commentaire 6', 299),
(7, 2, '/assets/img/user-profile/9wlgcd6OmiUp.jpg\r\n', '2023-10-23', 'Commentaire 7', 299),
(8, 2, '/assets/img/user-profile/9wlgcd6OmiUp.jpg\r\n', '2023-10-23', 'Commentaire 8', 299),
(9, 2, '/assets/img/user-profile/9wlgcd6OmiUp.jpg\r\n', '2023-10-23', 'Commentaire 9', 299),
(10, 2, '/assets/img/user-profile/9wlgcd6OmiUp.jpg\r\n', '2023-10-23', 'Commentaire 10', 299);

-- --------------------------------------------------------

--
-- Structure de la table `media`
--

CREATE TABLE `media` (
  `id` int(11) NOT NULL,
  `media_path` varchar(255) NOT NULL,
  `media_type` varchar(255) NOT NULL,
  `is_banner` tinyint(1) DEFAULT NULL,
  `id_trick` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `media`
--

INSERT INTO `media` (`id`, `media_path`, `media_type`, `is_banner`, `id_trick`) VALUES
(454, '/assets/img/banner/4lWfF3bm9fHP.jpg', 'jpg', 1, 298),
(455, '/assets/img/banner/hCdk9Cv1ptOx.jpg', 'jpg', 1, 299),
(456, '/assets/img/banner/2XgzzQzGOBf5.jpg', 'jpg', 1, 300),
(457, '/assets/img/banner/PaQpmGrtOcAT.jpg', 'jpg', 1, 301),
(458, '/assets/img/banner/tKSD45W1m57q.jpg', 'jpg', 1, 302),
(459, '/assets/img/banner/WAvyALxRUrgx.jpg', 'jpg', 1, 303),
(460, '/assets/img/banner/KMzl558qGcJ5.jpg', 'jpg', 1, 304),
(461, '/assets/img/banner/4DEETkMZ5Y2M.jpg', 'jpg', 1, 305),
(462, '/assets/img/banner/w5N7sl0etfBp.jpg', 'jpg', 1, 306),
(463, '/assets/img/banner/oMRZH3aLnEF.jpg', 'jpg', 1, 307);

-- --------------------------------------------------------

--
-- Structure de la table `trick`
--

CREATE TABLE `trick` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `trick_group` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `slug` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `trick`
--

INSERT INTO `trick` (`id`, `name`, `description`, `trick_group`, `created_at`, `updated_at`, `slug`) VALUES
(298, 'Indy', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed nibh enim, vehicula eu tortor nec, luctus varius justo. Ut bibendum fermentum neque ut rhoncus. Sed finibus non turpis ac porttitor. Proin quis consequat eros. Nulla ipsum purus, mollis eu nisl', 'Slides', '2023-10-07 16:42:02', NULL, 'indy'),
(299, 'Big foot', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed nibh enim, vehicula eu tortor nec, luctus varius justo. Ut bibendum fermentum neque ut rhoncus. Sed finibus non turpis ac porttitor. Proin quis consequat eros. Nulla ipsum purus, mollis eu nisl ', 'Rotations', '2023-10-07 16:45:07', NULL, 'big-foot'),
(300, 'Trois six', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed nibh enim, vehicula eu tortor nec, luctus varius justo. Ut bibendum fermentum neque ut rhoncus. Sed finibus non turpis ac porttitor. Proin quis consequat eros. Nulla ipsum purus, mollis eu nisl ', 'Rotations', '2023-10-07 16:54:00', NULL, 'trois-six'),
(301, 'Cinq quatre', 'Maecenas sed libero dapibus, volutpat magna vel, porttitor velit. Praesent consectetur efficitur magna, sed auctor est porttitor a. Maecenas aliquet mi lectus, ac suscipit diam eleifend vel. Vestibulum iaculis nulla eget sollicitudin efficitur. Duis vitae', 'Rotations', '2023-10-07 16:55:14', NULL, 'cinq-quatre'),
(302, 'Truck driver', 'Maecenas sed libero dapibus, volutpat magna vel, porttitor velit. Praesent consectetur efficitur magna, sed auctor est porttitor a. Maecenas aliquet mi lectus, ac suscipit diam eleifend vel. Vestibulum iaculis nulla eget sollicitudin efficitur. Duis vitae', 'Grabs', '2023-10-07 16:56:28', NULL, 'truck-driver'),
(303, 'Seat belt', 'Maecenas sed libero dapibus, volutpat magna vel, porttitor velit. Praesent consectetur efficitur magna, sed auctor est porttitor a. Maecenas aliquet mi lectus, ac suscipit diam eleifend vel. Vestibulum iaculis nulla eget sollicitudin efficitur. Duis vitae', 'Grabs', '2023-10-07 16:57:09', NULL, 'seat-belt'),
(304, 'Stalefish', 'Maecenas sed libero dapibus, volutpat magna vel, porttitor velit. Praesent consectetur efficitur magna, sed auctor est porttitor a. Maecenas aliquet mi lectus, ac suscipit diam eleifend vel. Vestibulum iaculis nulla eget sollicitudin efficitur. Duis vitae', 'Grabs', '2023-10-07 16:57:50', NULL, 'stalefish'),
(305, 'Japan air', 'Maecenas sed libero dapibus, volutpat magna vel, porttitor velit. Praesent consectetur efficitur magna, sed auctor est porttitor a. Maecenas aliquet mi lectus, ac suscipit diam eleifend vel. Vestibulum iaculis nulla eget sollicitudin efficitur. Duis vitae', 'Grabs', '2023-10-07 16:58:41', NULL, 'japan-air'),
(306, 'Backside air', 'Maecenas sed libero dapibus, volutpat magna vel, porttitor velit. Praesent consectetur efficitur magna, sed auctor est porttitor a. Maecenas aliquet mi lectus, ac suscipit diam eleifend vel. Vestibulum iaculis nulla eget sollicitudin efficitur. Duis vitae', 'Old School', '2023-10-07 16:59:31', NULL, 'backside-air'),
(307, 'Method air', 'Maecenas sed libero dapibus, volutpat magna vel, porttitor velit. Praesent consectetur efficitur magna, sed auctor est porttitor a. Maecenas aliquet mi lectus, ac suscipit diam eleifend vel. Vestibulum iaculis nulla eget sollicitudin efficitur. Duis vitae', 'Old School', '2023-10-07 17:00:54', NULL, 'method-air');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `profile_image` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `email` varchar(255) NOT NULL,
  `roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '(DC2Type:json)' CHECK (json_valid(`roles`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `username`, `profile_image`, `password`, `status`, `email`, `roles`) VALUES
(2, 'Test', '/assets/img/user-profile/IDsASalJat1W.jpg', '$2y$10$AQLk4BEagJ5ZFbjmqbeYxuws4vsRSZrpv44LHJvC45gEg62CFYPUO', 1, 'toto@live.fr', '[\"ROLE_USER\"]');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_9474526C3675E82E` (`id_trick`),
  ADD KEY `IDX_9474526C6B3CA4B` (`id_user`);

--
-- Index pour la table `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_6A2CA10C3675E82E` (`id_trick`);

--
-- Index pour la table `trick`
--
ALTER TABLE `trick`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_8D93D649F85E0677` (`username`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `comment`
--
ALTER TABLE `comment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT pour la table `media`
--
ALTER TABLE `media`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=635;

--
-- AUTO_INCREMENT pour la table `trick`
--
ALTER TABLE `trick`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=337;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `FK_9474526C3675E82E` FOREIGN KEY (`id_trick`) REFERENCES `trick` (`id`),
  ADD CONSTRAINT `FK_9474526C6B3CA4B` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `media`
--
ALTER TABLE `media`
  ADD CONSTRAINT `FK_6A2CA10C3675E82E` FOREIGN KEY (`id_trick`) REFERENCES `trick` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
