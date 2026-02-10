<?php
/**
 * Script d'installation de la table commandes_latitude
 * À exécuter une seule fois via : http://localhost/etiquette-app/install_latitude.php
 */

require_once 'config/database.php';

echo "<h1>Installation de la table commandes_latitude</h1>";
echo "<hr>";

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Script SQL pour créer la table
    $sql = "
    CREATE TABLE IF NOT EXISTS `commandes_latitude` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `numero_commande` VARCHAR(100) NOT NULL,
      `articles` TEXT NOT NULL COMMENT 'JSON array des articles',
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
    
    // Exécuter le script
    $db->exec($sql);
    
    echo "<p style='color: green; font-weight: bold;'>✓ Table commandes_latitude créée avec succès !</p>";
    echo "<p>Vous pouvez maintenant utiliser le module Latitude.</p>";
    echo "<p><a href='index.php?page=latitude' style='padding: 10px 20px; background: #0061f2; color: white; text-decoration: none; border-radius: 5px;'>Aller à Latitude</a></p>";
    echo "<hr>";
    echo "<p style='color: orange;'><strong>Important :</strong> Pour des raisons de sécurité, supprimez ce fichier après l'installation :</p>";
    echo "<p><code>C:\\xampp\\htdocs\\etiquette-app\\install_latitude.php</code></p>";
    
} catch(PDOException $e) {
    echo "<p style='color: red; font-weight: bold;'>✗ Erreur lors de la création de la table :</p>";
    echo "<p style='color: red;'>" . $e->getMessage() . "</p>";
    echo "<hr>";
    echo "<p><strong>Solution :</strong> Vérifiez que :</p>";
    echo "<ul>";
    echo "<li>MySQL est démarré dans XAMPP</li>";
    echo "<li>La base de données 'etiquette_db' existe</li>";
    echo "<li>Les paramètres de connexion dans config/database.php sont corrects</li>";
    echo "</ul>";
}
?>
