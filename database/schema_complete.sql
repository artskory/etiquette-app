-- ============================================================================
-- BASE DE DONNÉES UNIFIÉE - APPLICATION ÉTIQUETTES
-- Version 2.1.0
-- ============================================================================
--
-- Historique des modifications :
-- v1.0.0 : Structure initiale
-- v2.0.0 : Ajout colonne etiquettes JSON (Option A multi-blocs),
--          reference_id nullable, suppression batch_id et colonnes obsolètes
-- v2.1.0 : Ajout colonne quantite_par_carton dans references
--
-- ============================================================================

-- ============================================================================
-- TABLES SARTORIUS
-- ============================================================================

-- Table des références Sartorius
CREATE TABLE IF NOT EXISTS `references` (
    id                   INT AUTO_INCREMENT PRIMARY KEY,
    reference            VARCHAR(100) NOT NULL,
    designation          VARCHAR(255) NOT NULL,
    quantite_par_carton  INT UNSIGNED DEFAULT NULL COMMENT 'Quantité par carton (prérempli dans Nouvelle étiquette)',
    created_at           TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at           TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_reference_designation (reference, designation),
    INDEX idx_reference   (reference),
    INDEX idx_designation (designation)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des commandes Sartorius
-- reference_id et les champs "premier bloc" sont conservés pour compatibilité
-- Le champ etiquettes contient l'intégralité des blocs en JSON
CREATE TABLE IF NOT EXISTS commandes (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    numero_commande  VARCHAR(100) NOT NULL  COMMENT 'N° commande du premier bloc (compatibilité)',
    reference_id     INT DEFAULT NULL       COMMENT 'Référence du premier bloc (compatibilité)',
    quantites        TEXT DEFAULT NULL      COMMENT 'JSON quantités du premier bloc (compatibilité)',
    etiquettes       TEXT DEFAULT NULL      COMMENT 'JSON: [{reference_id, reference, designation, date_production, numero_commande, numero_lot, quantites:[{quantite_par_carton, quantite_etiquettes}]}]',
    date_production  VARCHAR(20) NOT NULL   COMMENT 'Date du premier bloc (compatibilité)',
    numero_lot       VARCHAR(100) NOT NULL  COMMENT 'N° lot du premier bloc (compatibilité)',
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (reference_id) REFERENCES `references`(id) ON DELETE SET NULL,
    INDEX idx_reference_id    (reference_id),
    INDEX idx_numero_commande (numero_commande),
    INDEX idx_date_production (date_production),
    INDEX idx_numero_lot      (numero_lot)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLES LATITUDE
-- ============================================================================

-- Table des articles Latitude
CREATE TABLE IF NOT EXISTS articles_latitude (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    nom        VARCHAR(200) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_nom (nom)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des commandes Latitude
CREATE TABLE IF NOT EXISTS commandes_latitude (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    numero_commande VARCHAR(100) NOT NULL,
    articles        TEXT NOT NULL COMMENT 'JSON: [{type, quantite, nombre_cartons}]',
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_numero_commande (numero_commande)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- DONNÉES D'EXEMPLE (OPTIONNEL)
-- ============================================================================

INSERT IGNORE INTO `references` (reference, designation) VALUES
('1000076404', 'PH SENSOR CALIBRATION AND USE'),
('1000076405', 'USE AND CALIBRATION'),
('IU114789',     'IU  Flexsafe 3D bag for Palletank 1000 L');

INSERT IGNORE INTO articles_latitude (nom) VALUES
('Carte postale'),
('Carte stickers'),
('Set de table'),
('Livre');
