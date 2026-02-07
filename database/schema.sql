-- Base de données pour l'application Étiquettes
-- Version 0.0.1

CREATE DATABASE IF NOT EXISTS etiquette_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE etiquette_db;

-- Table des références
CREATE TABLE IF NOT EXISTS references (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reference VARCHAR(100) NOT NULL UNIQUE,
    designation TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des commandes/étiquettes
CREATE TABLE IF NOT EXISTS commandes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_commande VARCHAR(100) NOT NULL,
    reference_id INT NOT NULL,
    quantite_par_carton INT NOT NULL,
    date_production VARCHAR(7) NOT NULL COMMENT 'Format: MM/YYYY',
    numero_lot VARCHAR(100) NOT NULL,
    quantite_etiquettes INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (reference_id) REFERENCES `references`(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Index pour améliorer les performances
CREATE INDEX idx_numero_commande ON commandes(numero_commande);
CREATE INDEX idx_reference_id ON commandes(reference_id);
