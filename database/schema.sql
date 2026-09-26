-- ============================================================
-- Schéma de base de données — Immo Colucci (Agadir)
-- Généré depuis la structure réelle de la base.
-- Moteur : InnoDB / Charset : utf8mb4
-- ============================================================

-- ------------------------------------------------------------
-- Table : articles
-- ------------------------------------------------------------
CREATE TABLE `articles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(200) NOT NULL,
  `slug` varchar(220) NOT NULL,
  `extrait` varchar(500) DEFAULT NULL,
  `contenu` longtext DEFAULT NULL,
  `categorie_id` int(11) DEFAULT NULL,
  `statut` enum('brouillon','publie','archive') NOT NULL DEFAULT 'brouillon',
  `publie_le` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table : biens
-- ------------------------------------------------------------
CREATE TABLE `biens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(200) NOT NULL,
  `slug` varchar(220) NOT NULL,
  `description` text DEFAULT NULL,
  `type_bien` enum('villa','riad','appartement','terrain','local') NOT NULL,
  `transaction_type` enum('vente','location') NOT NULL,
  `statut` enum('disponible','reserve','vendu','loue') NOT NULL DEFAULT 'disponible',
  `prix` decimal(15,2) DEFAULT NULL,
  `surface_habitable` int(11) DEFAULT NULL,
  `chambres` int(11) DEFAULT NULL,
  `salles_bain` int(11) DEFAULT NULL,
  `quartier` varchar(100) DEFAULT NULL,
  `ville` varchar(100) NOT NULL DEFAULT 'Agadir',
  `equipements` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`equipements`)),
  `vedette` tinyint(1) NOT NULL DEFAULT 0,
  `publie` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table : biens_medias
-- ------------------------------------------------------------
CREATE TABLE `biens_medias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bien_id` int(11) NOT NULL,
  `media_id` int(11) NOT NULL,
  `ordre` int(11) NOT NULL DEFAULT 0,
  `est_principale` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table : categories
-- ------------------------------------------------------------
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` enum('bien','service','article') NOT NULL,
  `nom` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `description` text DEFAULT NULL,
  `ordre` int(11) NOT NULL DEFAULT 0,
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table : chatbot_connaissances
-- ------------------------------------------------------------
CREATE TABLE `chatbot_connaissances` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `categorie` varchar(100) NOT NULL DEFAULT 'general',
  `question` text NOT NULL,
  `reponse` text NOT NULL,
  `mots_cles` varchar(500) DEFAULT NULL,
  `priorite` int(11) NOT NULL DEFAULT 0,
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table : chatbot_conversations
-- ------------------------------------------------------------
CREATE TABLE `chatbot_conversations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` varchar(100) NOT NULL,
  `question` text NOT NULL,
  `reponse` text NOT NULL,
  `source` enum('regle','contenu','ia','fallback') NOT NULL DEFAULT 'regle',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table : medias
-- ------------------------------------------------------------
CREATE TABLE `medias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom_fichier` varchar(255) NOT NULL,
  `chemin` varchar(500) NOT NULL,
  `type_mime` varchar(100) DEFAULT NULL,
  `taille` int(11) DEFAULT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `dossier` varchar(100) NOT NULL DEFAULT 'general',
  `uploade_par` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table : messages
-- ------------------------------------------------------------
CREATE TABLE `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `telephone` varchar(30) DEFAULT NULL,
  `sujet` varchar(200) DEFAULT NULL,
  `message` text NOT NULL,
  `bien_id` int(11) DEFAULT NULL,
  `lu` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table : parametres
-- ------------------------------------------------------------
CREATE TABLE `parametres` (
  `cle` varchar(100) NOT NULL,
  `valeur` text DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`cle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table : services
-- ------------------------------------------------------------
CREATE TABLE `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(200) NOT NULL,
  `slug` varchar(220) NOT NULL,
  `description_courte` varchar(300) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `icone` varchar(100) DEFAULT NULL,
  `ordre` int(11) NOT NULL DEFAULT 0,
  `publie` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table : utilisateurs
-- ------------------------------------------------------------
CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `role` enum('admin','editeur') NOT NULL DEFAULT 'editeur',
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  `derniere_connexion` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
