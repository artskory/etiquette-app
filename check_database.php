<?php
/**
 * Script de vérification et mise à jour de la base de données
 * Accéder via : http://localhost/etiquette-app/check_database.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/database.php';

echo "<h1>Vérification de la base de données</h1>";
echo "<hr>";

try {
    $database = new Database();
    $db = $database->getConnection();
    echo "✓ Connexion base de données OK<br><br>";
    
    // Vérifier la structure de la table references
    echo "<h2>Table 'references'</h2>";
    $query = "DESCRIBE `references`";
    $stmt = $db->query($query);
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Champ</th><th>Type</th><th>Null</th><th>Clé</th><th>Défaut</th><th>Extra</th></tr>";
    foreach($columns as $col) {
        echo "<tr>";
        echo "<td>{$col['Field']}</td>";
        echo "<td>{$col['Type']}</td>";
        echo "<td>{$col['Null']}</td>";
        echo "<td>{$col['Key']}</td>";
        echo "<td>{$col['Default']}</td>";
        echo "<td>{$col['Extra']}</td>";
        echo "</tr>";
    }
    echo "</table><br>";
    
    $expectedRefColumns = ['id', 'reference', 'designation', 'created_at', 'updated_at'];
    $actualRefColumns = array_column($columns, 'Field');
    $missingRefColumns = array_diff($expectedRefColumns, $actualRefColumns);
    
    if(empty($missingRefColumns)) {
        echo "✓ Structure de 'references' correcte<br><br>";
    } else {
        echo "✗ <strong style='color:red'>Colonnes manquantes dans 'references': " . implode(', ', $missingRefColumns) . "</strong><br><br>";
    }
    
    // Vérifier la structure de la table commandes
    echo "<h2>Table 'commandes'</h2>";
    $query = "DESCRIBE commandes";
    $stmt = $db->query($query);
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Champ</th><th>Type</th><th>Null</th><th>Clé</th><th>Défaut</th><th>Extra</th></tr>";
    foreach($columns as $col) {
        echo "<tr>";
        echo "<td>{$col['Field']}</td>";
        echo "<td>{$col['Type']}</td>";
        echo "<td>{$col['Null']}</td>";
        echo "<td>{$col['Key']}</td>";
        echo "<td>{$col['Default']}</td>";
        echo "<td>{$col['Extra']}</td>";
        echo "</tr>";
    }
    echo "</table><br>";
    
    $expectedCmdColumns = ['id', 'numero_commande', 'reference_id', 'quantite_par_carton', 'date_production', 'numero_lot', 'quantite_etiquettes', 'created_at', 'updated_at'];
    $actualCmdColumns = array_column($columns, 'Field');
    $missingCmdColumns = array_diff($expectedCmdColumns, $actualCmdColumns);
    
    if(empty($missingCmdColumns)) {
        echo "✓ Structure de 'commandes' correcte<br><br>";
    } else {
        echo "✗ <strong style='color:red'>Colonnes manquantes dans 'commandes': " . implode(', ', $missingCmdColumns) . "</strong><br><br>";
    }
    
    // Vérifier la table commandes_latitude
    echo "<h2>Table 'commandes_latitude'</h2>";
    try {
        $query = "DESCRIBE commandes_latitude";
        $stmt = $db->query($query);
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Champ</th><th>Type</th><th>Null</th><th>Clé</th><th>Défaut</th><th>Extra</th></tr>";
        foreach($columns as $col) {
            echo "<tr>";
            echo "<td>{$col['Field']}</td>";
            echo "<td>{$col['Type']}</td>";
            echo "<td>{$col['Null']}</td>";
            echo "<td>{$col['Key']}</td>";
            echo "<td>{$col['Default']}</td>";
            echo "<td>{$col['Extra']}</td>";
            echo "</tr>";
        }
        echo "</table><br>";
        echo "✓ Table 'commandes_latitude' existe<br><br>";
    } catch(PDOException $e) {
        echo "✗ <strong style='color:orange'>Table 'commandes_latitude' n'existe pas</strong><br>";
        echo "Exécutez le script d'installation : <a href='install_latitude.php'>install_latitude.php</a><br><br>";
    }
    
    // Compter les enregistrements
    echo "<h2>Statistiques</h2>";
    
    $query = "SELECT COUNT(*) as count FROM `references`";
    $stmt = $db->query($query);
    $refCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "Références: <strong>$refCount</strong><br>";
    
    $query = "SELECT COUNT(*) as count FROM commandes";
    $stmt = $db->query($query);
    $cmdCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "Commandes Sartorius: <strong>$cmdCount</strong><br>";
    
    try {
        $query = "SELECT COUNT(*) as count FROM commandes_latitude";
        $stmt = $db->query($query);
        $latCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "Commandes Latitude: <strong>$latCount</strong><br><br>";
    } catch(PDOException $e) {
        echo "Commandes Latitude: <strong style='color:orange'>Table non créée</strong><br><br>";
    }
    
    // Vérifier les contraintes de clé étrangère
    echo "<h2>Contraintes de clé étrangère</h2>";
    $query = "SELECT 
                CONSTRAINT_NAME,
                TABLE_NAME,
                COLUMN_NAME,
                REFERENCED_TABLE_NAME,
                REFERENCED_COLUMN_NAME
              FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
              WHERE TABLE_SCHEMA = 'etiquette_db'
              AND REFERENCED_TABLE_NAME IS NOT NULL";
    $stmt = $db->query($query);
    $constraints = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if(!empty($constraints)) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Contrainte</th><th>Table</th><th>Colonne</th><th>Table Référencée</th><th>Colonne Référencée</th></tr>";
        foreach($constraints as $constraint) {
            echo "<tr>";
            echo "<td>{$constraint['CONSTRAINT_NAME']}</td>";
            echo "<td>{$constraint['TABLE_NAME']}</td>";
            echo "<td>{$constraint['COLUMN_NAME']}</td>";
            echo "<td>{$constraint['REFERENCED_TABLE_NAME']}</td>";
            echo "<td>{$constraint['REFERENCED_COLUMN_NAME']}</td>";
            echo "</tr>";
        }
        echo "</table><br>";
        echo "✓ Contraintes de clé étrangère présentes<br><br>";
    } else {
        echo "✗ <strong style='color:orange'>Aucune contrainte de clé étrangère trouvée</strong><br><br>";
    }
    
    echo "<hr>";
    echo "<h2>Résumé</h2>";
    echo "<p>✓ Base de données opérationnelle</p>";
    echo "<p><a href='index.php' style='padding: 10px 20px; background: #0061f2; color: white; text-decoration: none; border-radius: 5px;'>Retour à l'application</a></p>";
    echo "<br>";
    echo "<p style='color: orange;'><strong>Note :</strong> Vous pouvez supprimer ce fichier après vérification : check_database.php</p>";
    
} catch(Exception $e) {
    echo "<br><strong style='color:red'>Erreur:</strong><br>";
    echo $e->getMessage() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>

<style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
        background: #f5f5f5;
    }
    table {
        background: white;
        border-collapse: collapse;
        margin: 10px 0;
    }
    th {
        background: #0061f2;
        color: white;
        padding: 8px;
    }
    td {
        padding: 5px 10px;
    }
    h1 {
        color: #0061f2;
    }
    h2 {
        color: #333;
        margin-top: 20px;
    }
</style>
