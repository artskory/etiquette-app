-- ============================================================================
-- BASE DE DONNÉES UNIFIÉE - APPLICATION ÉTIQUETTES
-- Version 1.0.0
-- ============================================================================
-- 
-- Cette base de données contient toutes les tables nécessaires pour :
-- - Gestion des références Sartorius
-- - Gestion des commandes Sartorius avec quantités JSON
-- - Gestion des articles Latitude
-- - Gestion des commandes Latitude avec articles JSON
--
-- ============================================================================

-- Créer la base de données
CREATE DATABASE IF NOT EXISTS etiquette_db 
    DEFAULT CHARACTER SET utf8mb4 
    COLLATE utf8mb4_unicode_ci;

USE etiquette_db;

-- ============================================================================
-- TABLES SARTORIUS
-- ============================================================================

-- Table des références Sartorius
CREATE TABLE IF NOT EXISTS `references` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reference VARCHAR(100) NOT NULL,
    designation VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_reference_designation (reference, designation)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des commandes Sartorius
CREATE TABLE IF NOT EXISTS commandes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_commande VARCHAR(100) NOT NULL,
    reference_id INT NOT NULL,
    quantites TEXT NOT NULL COMMENT 'JSON: [{quantite_par_carton, quantite_etiquettes}]',
    date_production VARCHAR(20) NOT NULL,
    numero_lot VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Anciennes colonnes (conservées pour compatibilité, peuvent être supprimées)
    quantite_par_carton INT DEFAULT NULL,
    quantite_etiquettes INT DEFAULT NULL,
    
    FOREIGN KEY (reference_id) REFERENCES `references`(id) ON DELETE CASCADE,
    INDEX idx_reference_id (reference_id),
    INDEX idx_numero_commande (numero_commande),
    INDEX idx_date_production (date_production)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLES LATITUDE
-- ============================================================================

-- Table des articles Latitude
CREATE TABLE IF NOT EXISTS articles_latitude (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(200) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_nom (nom)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des commandes Latitude
CREATE TABLE IF NOT EXISTS commandes_latitude (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_commande VARCHAR(100) NOT NULL,
    articles TEXT NOT NULL COMMENT 'JSON: [{type, quantite, nombre_cartons}]',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_numero_commande (numero_commande)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- DONNÉES D'EXEMPLE (OPTIONNEL)
-- ============================================================================

-- Références Sartorius exemple
INSERT IGNORE INTO `references` (reference, designation) VALUES
('REF-001', 'Étiquette standard A4'),
('REF-002', 'Étiquette premium A5'),
('REF-003', 'Étiquette économique A6');

-- Articles Latitude exemple
INSERT IGNORE INTO articles_latitude (nom) VALUES
('Carte postale'),
('Carte stickers'),
('Set de table'),
('Livre'),
('Flyer A5'),
('Brochure A4');

-- ============================================================================
-- INFORMATIONS
-- ============================================================================

-- Afficher les tables créées
SHOW TABLES;

-- Afficher les statistiques
SELECT 
    'references' as table_name, 
    COUNT(*) as row_count 
FROM `references`
UNION ALL
SELECT 
    'commandes', 
    COUNT(*) 
FROM commandes
UNION ALL
SELECT 
    'articles_latitude', 
    COUNT(*) 
FROM articles_latitude
UNION ALL
SELECT 
    'commandes_latitude', 
    COUNT(*) 
FROM commandes_latitude;
