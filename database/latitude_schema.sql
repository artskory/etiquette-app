-- Table pour les commandes Latitude
CREATE TABLE IF NOT EXISTS `commandes_latitude` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `numero_commande` VARCHAR(100) NOT NULL,
  `articles` TEXT NOT NULL COMMENT 'JSON array des articles',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Structure JSON pour articles :
-- [
--   {
--     "type": "Carte postale",
--     "quantite": 900,
--     "nombre_cartons": 25
--   },
--   {
--     "type": "Set de table",
--     "quantite": 500,
--     "nombre_cartons": 14
--   }
-- ]
