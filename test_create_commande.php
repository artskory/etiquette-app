<?php
/**
 * Script de test pour la création de commandes multiples
 * Accéder via : http://localhost/etiquette-app/test_create_commande.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/database.php';
require_once 'models/Commande.php';

echo "<h1>Test de création de commandes multiples</h1>";
echo "<hr>";

// Simuler les données POST
$_POST = [
    'reference_id' => 1,
    'date_production' => '01/2026',
    'numero_commande' => 'TEST-001',
    'numero_lot' => 'LOT-001',
    'quantites' => [
        0 => [
            'quantite_par_carton' => 50,
            'quantite_etiquettes' => 200
        ],
        1 => [
            'quantite_par_carton' => 100,
            'quantite_etiquettes' => 500
        ],
        2 => [
            'quantite_par_carton' => 25,
            'quantite_etiquettes' => 100
        ]
    ]
];

echo "<h2>Données à créer :</h2>";
echo "<pre>" . print_r($_POST, true) . "</pre>";

try {
    $database = new Database();
    $db = $database->getConnection();
    
    $reference_id = $_POST['reference_id'];
    $date_production = $_POST['date_production'];
    $numero_commande = $_POST['numero_commande'];
    $numero_lot = $_POST['numero_lot'];
    $quantites = $_POST['quantites'];
    
    echo "<h2>Création des commandes :</h2>";
    
    $createdCount = 0;
    $createdIds = [];
    
    foreach($quantites as $index => $qty) {
        echo "<strong>Ligne " . ($index + 1) . ":</strong><br>";
        echo "- Quantité par carton: {$qty['quantite_par_carton']}<br>";
        echo "- Quantité étiquettes: {$qty['quantite_etiquettes']}<br>";
        
        // Créer une nouvelle instance pour chaque ligne
        $commande = new Commande($db);
        
        $commande->reference_id = $reference_id;
        $commande->date_production = $date_production;
        $commande->numero_commande = $numero_commande;
        $commande->numero_lot = $numero_lot;
        $commande->quantite_par_carton = $qty['quantite_par_carton'];
        $commande->quantite_etiquettes = $qty['quantite_etiquettes'];
        
        if($commande->create()) {
            echo "✓ <span style='color:green'>Créée avec succès - ID: {$commande->id}</span><br>";
            $createdCount++;
            $createdIds[] = $commande->id;
        } else {
            echo "✗ <span style='color:red'>Échec de création</span><br>";
        }
        echo "<br>";
    }
    
    echo "<hr>";
    echo "<h2>Résumé :</h2>";
    echo "Commandes créées : <strong>$createdCount / " . count($quantites) . "</strong><br>";
    echo "IDs créés : " . implode(', ', $createdIds) . "<br><br>";
    
    // Vérifier dans la base
    echo "<h2>Vérification en base de données :</h2>";
    $query = "SELECT * FROM commandes WHERE numero_commande = 'TEST-001' ORDER BY id DESC";
    $stmt = $db->query($query);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Nombre de lignes trouvées : <strong>" . count($results) . "</strong><br><br>";
    
    if(count($results) > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Qty/carton</th><th>Qty étiquettes</th><th>Date création</th></tr>";
        foreach($results as $row) {
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>{$row['quantite_par_carton']}</td>";
            echo "<td>{$row['quantite_etiquettes']}</td>";
            echo "<td>{$row['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table><br>";
    }
    
    echo "<hr>";
    echo "<h2>Conclusion :</h2>";
    if($createdCount == count($quantites) && count($results) == count($quantites)) {
        echo "<p style='color:green; font-weight:bold;'>✓ SUCCÈS : Toutes les lignes ont été créées correctement !</p>";
    } else {
        echo "<p style='color:red; font-weight:bold;'>✗ PROBLÈME : Certaines lignes n'ont pas été créées</p>";
        echo "<p>Créées en mémoire : $createdCount</p>";
        echo "<p>Trouvées en base : " . count($results) . "</p>";
        echo "<p>Attendues : " . count($quantites) . "</p>";
    }
    
    // Nettoyer les données de test
    echo "<br><hr>";
    echo "<h2>Nettoyage :</h2>";
    $deleteQuery = "DELETE FROM commandes WHERE numero_commande = 'TEST-001'";
    $db->exec($deleteQuery);
    echo "✓ Commandes de test supprimées<br>";
    
} catch(Exception $e) {
    echo "<br><strong style='color:red'>Erreur:</strong><br>";
    echo $e->getMessage() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<br><p style='color: orange;'><strong>Note :</strong> Vous pouvez supprimer ce fichier : test_create_commande.php</p>";
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
    pre {
        background: #f0f0f0;
        padding: 10px;
        border-radius: 5px;
        overflow-x: auto;
    }
</style>
