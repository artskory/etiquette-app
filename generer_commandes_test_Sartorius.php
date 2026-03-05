<?php
/**
 * SCRIPT DE TEST - Génération commandes fictives
 * À UTILISER UNIQUEMENT EN DÉVELOPPEMENT
 * 
 * Ce script crée 100 commandes Sartorius fictives pour tester la pagination
 * 
 * Usage : http://localhost/colisage-app/generer_commandes_test.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/database.php';

$database = new Database();
$conn = $database->getConnection();

echo "<h1>Génération de commandes de test</h1>";
echo "<p>Création de 100 commandes fictives...</p>";

try {
    // Vérifier qu'il y a au moins une référence
    $refStmt = $conn->query("SELECT id FROM `references` LIMIT 1");
    $ref = $refStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$ref) {
        die("❌ ERREUR : Aucune référence trouvée. Créez au moins une référence d'abord !");
    }
    
    $referenceId = $ref['id'];
    
    // Générer 100 commandes
    $sql = "INSERT INTO commandes (numero_commande, reference_id, date_production, numero_lot, quantites, created_at) 
            VALUES (:numero_commande, :reference_id, :date_production, :numero_lot, :quantites, NOW())";
    
    $stmt = $conn->prepare($sql);
    
    $created = 0;
    
    for ($i = 1; $i <= 100; $i++) {
        $numeroCommande = "TEST-" . str_pad($i, 4, '0', STR_PAD_LEFT);
        $mois = str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT);
        $annee = rand(2024, 2026);
        $dateProduction = $mois . '/' . $annee;
        $numeroLot = "LOT-" . rand(1000, 9999);
        
        // Générer 1 à 3 lignes de quantités
        $nbLignes = rand(1, 3);
        $quantites = [];
        for ($j = 0; $j < $nbLignes; $j++) {
            $quantites[] = [
                'quantite_par_carton' => rand(10, 100),
                'quantite_etiquettes' => rand(100, 1000)
            ];
        }
        
        $stmt->bindParam(':numero_commande', $numeroCommande);
        $stmt->bindParam(':reference_id', $referenceId);
        $stmt->bindParam(':date_production', $dateProduction);
        $stmt->bindParam(':numero_lot', $numeroLot);
        $quantitesJson = json_encode($quantites);
        $stmt->bindParam(':quantites', $quantitesJson);
        
        if ($stmt->execute()) {
            $created++;
        }
    }
    
    echo "<div style='background:#d1e7dd; padding:20px; border-radius:10px; margin:20px 0;'>";
    echo "<h2 style='color:#0f5132;'>✅ Succès !</h2>";
    echo "<p><strong>$created commandes</strong> créées avec succès.</p>";
    echo "</div>";
    
    echo "<h3>Statistiques :</h3>";
    $total = $conn->query("SELECT COUNT(*) FROM commandes")->fetchColumn();
    echo "<p>Nombre total de commandes : <strong>$total</strong></p>";
    
    echo "<hr>";
    echo "<h3>Actions :</h3>";
    echo "<ul>";
    echo "<li><a href='index.php?page=sartorius'>Voir la liste des commandes</a> (pagination visible si >50)</li>";
    echo "<li><a href='supprimer_commandes_test.php'>Supprimer les commandes de test</a></li>";
    echo "</ul>";
    
    echo "<hr>";
    echo "<div style='background:#fff3cd; padding:15px; border-radius:5px;'>";
    echo "<strong>⚠️ NOTE :</strong> Ces commandes sont fictives et n'ont PAS de PDF générés.";
    echo "</div>";
    
} catch(Exception $e) {
    echo "<div style='background:#f8d7da; padding:20px; border-radius:10px;'>";
    echo "<h2 style='color:#842029;'>❌ Erreur</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}
?>
