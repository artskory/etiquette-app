<?php
/**
 * SCRIPT DE TEST - Suppression commandes fictives
 * À UTILISER UNIQUEMENT EN DÉVELOPPEMENT
 * 
 * Ce script supprime toutes les commandes commençant par "TEST-"
 * 
 * Usage : http://localhost/colisage-app/supprimer_commandes_test.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/database.php';

$database = new Database();
$conn = $database->getConnection();

echo "<h1>Suppression des commandes de test</h1>";

try {
    // Compter les commandes de test
    $count = $conn->query("SELECT COUNT(*) FROM commandes WHERE numero_commande LIKE 'TEST-%'")->fetchColumn();
    
    if ($count == 0) {
        echo "<div style='background:#cfe2ff; padding:20px; border-radius:10px;'>";
        echo "<p>ℹ️ Aucune commande de test à supprimer.</p>";
        echo "</div>";
    } else {
        // Supprimer les commandes de test
        $stmt = $conn->prepare("DELETE FROM commandes WHERE numero_commande LIKE 'TEST-%'");
        $stmt->execute();
        
        echo "<div style='background:#d1e7dd; padding:20px; border-radius:10px;'>";
        echo "<h2 style='color:#0f5132;'>✅ Suppression réussie</h2>";
        echo "<p><strong>$count commande(s)</strong> de test supprimée(s).</p>";
        echo "</div>";
    }
    
    echo "<hr>";
    echo "<h3>Statistiques :</h3>";
    $total = $conn->query("SELECT COUNT(*) FROM commandes")->fetchColumn();
    echo "<p>Nombre total de commandes restantes : <strong>$total</strong></p>";
    
    echo "<hr>";
    echo "<p><a href='index.php?page=sartorius'>← Retour à la liste des commandes</a></p>";
    
} catch(Exception $e) {
    echo "<div style='background:#f8d7da; padding:20px; border-radius:10px;'>";
    echo "<h2 style='color:#842029;'>❌ Erreur</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}
?>
