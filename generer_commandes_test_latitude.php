<?php
/**
 * SCRIPT DE TEST - Génération commandes Latitude fictives
 * À UTILISER UNIQUEMENT EN DÉVELOPPEMENT
 * 
 * Ce script crée 100 commandes Latitude fictives pour tester la pagination
 * 
 * Usage : http://localhost/colisage-app/generer_commandes_test_latitude.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/database.php';
require_once 'models/ArticleLatitude.php';

$database = new Database();
$conn = $database->getConnection();

echo "<h1>Génération de commandes Latitude de test</h1>";
echo "<p>Création de 100 commandes fictives...</p>";

try {
    // Récupérer tous les articles Latitude disponibles
    $articleModel = new ArticleLatitude($conn);
    $articlesStmt = $articleModel->readAll();
    $articlesDisponibles = [];
    
    while ($art = $articlesStmt->fetch(PDO::FETCH_ASSOC)) {
        $articlesDisponibles[] = $art['nom'];
    }
    
    if (empty($articlesDisponibles)) {
        die("❌ ERREUR : Aucun article Latitude trouvé. Créez au moins un article d'abord !<br><br>
             <a href='index.php?page=nouveau-article-latitude'>Créer un article</a>");
    }
    
    echo "<p>✅ " . count($articlesDisponibles) . " article(s) disponible(s) : " . implode(', ', $articlesDisponibles) . "</p>";
    
    // Générer 100 commandes
    $sql = "INSERT INTO commandes_latitude (numero_commande, articles, created_at) 
            VALUES (:numero_commande, :articles, NOW())";
    
    $stmt = $conn->prepare($sql);
    
    $created = 0;
    
    for ($i = 1; $i <= 100; $i++) {
        $numeroCommande = "TEST-LAT-" . str_pad($i, 4, '0', STR_PAD_LEFT);
        
        // Générer 1 à 4 articles aléatoires
        $nbArticles = rand(1, 4);
        $articles = [];
        
        for ($j = 0; $j < $nbArticles; $j++) {
            // Choisir un article aléatoire
            $typeArticle = $articlesDisponibles[array_rand($articlesDisponibles)];
            
            $articles[] = [
                'type' => $typeArticle,
                'quantite' => rand(50, 1000),
                'nombre_cartons' => rand(5, 50)
            ];
        }
        
        $articlesJson = json_encode($articles);
        
        $stmt->bindParam(':numero_commande', $numeroCommande);
        $stmt->bindParam(':articles', $articlesJson);
        
        if ($stmt->execute()) {
            $created++;
        }
    }
    
    echo "<div style='background:#d1e7dd; padding:20px; border-radius:10px; margin:20px 0;'>";
    echo "<h2 style='color:#0f5132;'>✅ Succès !</h2>";
    echo "<p><strong>$created commandes Latitude</strong> créées avec succès.</p>";
    echo "</div>";
    
    echo "<h3>Statistiques :</h3>";
    $total = $conn->query("SELECT COUNT(*) FROM commandes_latitude")->fetchColumn();
    echo "<p>Nombre total de commandes Latitude : <strong>$total</strong></p>";
    
    echo "<hr>";
    echo "<h3>Actions :</h3>";
    echo "<ul>";
    echo "<li><a href='index.php?page=latitude'>Voir la liste des commandes Latitude</a> (pagination visible si >50)</li>";
    echo "<li><a href='supprimer_commandes_test_latitude.php'>Supprimer les commandes de test Latitude</a></li>";
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

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 50px auto;
    padding: 20px;
    background: #f8f9fa;
}
h1 {
    color: #0d6efd;
    border-bottom: 3px solid #0d6efd;
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
