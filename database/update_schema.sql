-- Script de mise à jour de la base de données
-- Version 0.3.2
-- Exécute uniquement les modifications nécessaires

USE etiquette_db;

-- S'assurer que la table references a la bonne structure
ALTER TABLE `references` 
    MODIFY COLUMN reference VARCHAR(100) NOT NULL,
    MODIFY COLUMN designation TEXT NOT NULL;

-- S'assurer que la contrainte UNIQUE existe sur reference
-- (Peut générer une erreur si elle existe déjà, c'est normal)
-- ALTER TABLE `references` ADD UNIQUE KEY unique_reference (reference);

-- S'assurer que la table commandes a tous les champs nécessaires
ALTER TABLE commandes 
    MODIFY COLUMN numero_commande VARCHAR(100) NOT NULL,
    MODIFY COLUMN quantite_par_carton INT NOT NULL,
    MODIFY COLUMN date_production VARCHAR(7) NOT NULL COMMENT 'Format: MM/YYYY',
    MODIFY COLUMN numero_lot VARCHAR(100) NOT NULL,
    MODIFY COLUMN quantite_etiquettes INT NOT NULL;

-- Vérifier les index
-- CREATE INDEX idx_numero_commande ON commandes(numero_commande);
-- CREATE INDEX idx_reference_id ON commandes(reference_id);

-- Note: Les lignes commentées peuvent générer des erreurs si elles existent déjà
-- Décommentez-les uniquement si nécessaire
