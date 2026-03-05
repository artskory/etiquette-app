<?php
/**
 * SCRIPT DE TEST - Suppression commandes Latitude fictives
 * À UTILISER UNIQUEMENT EN DÉVELOPPEMENT
 * 
 * Ce script supprime toutes les commandes Latitude commençant par "TEST-LAT-"
 * 
 * Usage : http://localhost/colisage-app/supprimer_commandes_test_latitude.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/database.php';

$database = new Database();
$conn = $database->getConnection();

echo "<h1>Suppression des commandes Latitude de test</h1>";

try {
    // Compter les commandes de test
    $count = $conn->query("SELECT COUNT(*) FROM commandes_latitude WHERE numero_commande LIKE 'TEST-LAT-%'")->fetchColumn();
    
    if ($count == 0) {
        echo "<div style='background:#cfe2ff; padding:20px; border-radius:10px;'>";
        echo "<p>ℹ️ Aucune commande Latitude de test à supprimer.</p>";
        echo "</div>";
    } else {
        // Supprimer les commandes de test
        $stmt = $conn->prepare("DELETE FROM commandes_latitude WHERE numero_commande LIKE 'TEST-LAT-%'");
        $stmt->execute();
        
        echo "<div style='background:#d1e7dd; padding:20px; border-radius:10px;'>";
        echo "<h2 style='color:#0f5132;'>✅ Suppression réussie</h2>";
        echo "<p><strong>$count commande(s) Latitude</strong> de test supprimée(s).</p>";
        echo "</div>";
    }
    
    echo "<hr>";
    echo "<h3>Statistiques :</h3>";
    $total = $conn->query("SELECT COUNT(*) FROM commandes_latitude")->fetchColumn();
    echo "<p>Nombre total de commandes Latitude restantes : <strong>$total</strong></p>";
    
    echo "<hr>";
    echo "<p><a href='index.php?page=latitude'>← Retour à la liste des commandes Latitude</a></p>";
    
} catch(Exception $e) {
    echo "<div style='background:#f8d7da; padding:20px; border-radius:10px;'>";
    echo "<h2 style='color:#842029;'>❌ Erreur</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 50px auto;
    padding: 20px;
    background: #f8f9fa;
}
h1 {
    color: #dc3545;
    border-bottom: 3px solid #dc3545;
    padding-bottom: 10px;
}
a {
    color: #0d6efd;
    text-decoration: none;
}
a:hover {
    text-decoration: underline;
}
</style>
