-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 28 nov. 2025 à 15:47
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
-- Base de données : `bd-unify`
--

-- --------------------------------------------------------

--
-- Structure de la table `priority`
--

CREATE TABLE `priority` (
  `priority_id` int(11) NOT NULL,
  `priority_name` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `priority`
--

INSERT INTO `priority` (`priority_id`, `priority_name`) VALUES
(1, 'minimum'),
(2, 'medium'),
(3, 'important'),
(4, 'urgent');

-- --------------------------------------------------------

--
-- Structure de la table `project`
--

CREATE TABLE `project` (
  `project_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `project`
--

INSERT INTO `project` (`project_id`, `user_id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 1, 'test', 'sali', '2025-11-24 09:27:10', '2025-11-24 09:27:34'),
(2, 2, 'test', 'testfdsfs', '2025-11-25 08:53:32', '2025-11-25 08:53:44'),
(3, 3, 'jb test', 'jb qui code pas', '2025-11-25 09:44:18', '2025-11-25 09:44:30'),
(5, 5, 'test', 'test\"', '2025-11-28 14:29:25', '2025-11-28 14:29:25'),
(6, 5, 'sdq', 'dsqdq', '2025-11-28 14:34:10', '2025-11-28 14:34:10');

-- --------------------------------------------------------

--
-- Structure de la table `status`
--

CREATE TABLE `status` (
  `status_id` int(11) NOT NULL,
  `status_name` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `status`
--

INSERT INTO `status` (`status_id`, `status_name`) VALUES
(1, 'to do'),
(2, 'in progress'),
(3, 'completed'),
(4, 'testing'),
(5, 'blocked');

-- --------------------------------------------------------

--
-- Structure de la table `task`
--

CREATE TABLE `task` (
  `task_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `status_id` int(11) NOT NULL,
  `priority_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `task`
--

INSERT INTO `task` (`task_id`, `project_id`, `user_id`, `name`, `description`, `status_id`, `priority_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'zob', 'aze', 1, 2, '2025-11-24 09:27:55', '2025-11-24 09:28:05'),
(2, 1, 1, 'dsfa', 'zfaze', 5, 3, '2025-11-24 09:28:52', '2025-11-24 09:29:06'),
(3, 3, 3, 'jb fait le code nul', 'vite', 2, 4, '2025-11-25 09:44:50', '2025-11-28 11:45:03'),
(4, 3, 3, 'jb fasse le css', 'zaeazdsadsqd', 5, 1, '2025-11-25 09:45:48', '2025-11-28 11:29:20'),
(6, 3, 3, 'zob', 'esfd', 1, 1, '2025-11-28 11:39:50', '2025-11-28 11:39:50'),
(7, 5, 5, 'dsads', 'dsad', 2, 2, '2025-11-28 14:29:35', '2025-11-28 14:29:35'),
(8, 5, 5, 'dsa', 'dsa', 1, 1, '2025-11-28 14:29:39', '2025-11-28 14:29:39'),
(9, 5, 5, 'ads', 'dsa', 1, 1, '2025-11-28 14:29:43', '2025-11-28 14:29:43'),
(10, 5, 5, 'dsa', 'dsad', 1, 1, '2025-11-28 14:29:46', '2025-11-28 14:29:46');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`user_id`, `name`, `email`, `password_hash`, `created_at`) VALUES
(1, 'andy', 'andy@email.com', '$2y$10$qnrnvHrT407KDSR.1az21.QubPhFeK0.1LD0nSp/Y88pewO/2qCN2', '2025-11-24 09:27:01'),
(2, 'clement', 'clement@mail.com', '$2y$10$gllXvqzsa/PC7B/uwILedeu3iXl7T/4WNK88oMb3tP3ubf.xKX5NG', '2025-11-25 08:52:57'),
(3, 'jb', 'jb@mail.com', '$2y$10$.n574wz1Hvx3z5AFoLztXe2hYuPUuEVBpkqQgeGBO9mXtIVkI2KEa', '2025-11-25 09:43:44'),
(5, 'test', 'tests@gmail.com', '$2y$10$A2HkvKipc5rmA/ud9gJDnOWmzA5w3Mv/GXTtXRX1VFDsZqRTxPVUK', '2025-11-28 14:28:43');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `priority`
--
ALTER TABLE `priority`
  ADD PRIMARY KEY (`priority_id`);

--
-- Index pour la table `project`
--
ALTER TABLE `project`
  ADD PRIMARY KEY (`project_id`),
  ADD KEY `fk_project_user` (`user_id`);

--
-- Index pour la table `status`
--
ALTER TABLE `status`
  ADD PRIMARY KEY (`status_id`);

--
-- Index pour la table `task`
--
ALTER TABLE `task`
  ADD PRIMARY KEY (`task_id`),
  ADD KEY `fk_task_project` (`project_id`),
  ADD KEY `fk_task_user` (`user_id`),
  ADD KEY `fk_task_status` (`status_id`),
  ADD KEY `fk_task_priority` (`priority_id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `priority`
--
ALTER TABLE `priority`
  MODIFY `priority_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `project`
--
ALTER TABLE `project`
  MODIFY `project_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `status`
--
ALTER TABLE `status`
  MODIFY `status_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `task`
--
ALTER TABLE `task`
  MODIFY `task_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `project`
--
ALTER TABLE `project`
  ADD CONSTRAINT `fk_project_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `task`
--
ALTER TABLE `task`
  ADD CONSTRAINT `fk_task_priority` FOREIGN KEY (`priority_id`) REFERENCES `priority` (`priority_id`),
  ADD CONSTRAINT `fk_task_project` FOREIGN KEY (`project_id`) REFERENCES `project` (`project_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_task_status` FOREIGN KEY (`status_id`) REFERENCES `status` (`status_id`),
  ADD CONSTRAINT `fk_task_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
