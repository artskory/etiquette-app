<?php
/**
 * Script de test pour PDF Latitude
 * Accéder via : http://localhost/etiquette-app/test_pdf_latitude.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/database.php';
require_once 'models/CommandeLatitude.php';
require_once 'lib/LatitudePdfGenerator.php';

echo "<h1>Test de génération PDF Latitude</h1>";
echo "<hr>";

try {
    // Connexion base de données
    $database = new Database();
    $db = $database->getConnection();
    echo "✓ Connexion base de données OK<br><br>";
    
    // Récupérer une commande de test
    $query = "SELECT * FROM commandes_latitude ORDER BY id DESC LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $commande = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if(!$commande) {
        echo "✗ Aucune commande Latitude trouvée dans la base de données<br>";
        echo "Créez une commande Latitude via l'application d'abord.";
        exit;
    }
    
    echo "✓ Commande trouvée (ID: {$commande['id']})<br>";
    echo "N° Commande: {$commande['numero_commande']}<br>";
    echo "Articles (JSON): {$commande['articles']}<br><br>";
    
    // Décoder le JSON pour vérifier
    $articles = json_decode($commande['articles'], true);
    
    if(!$articles || !is_array($articles)) {
        echo "✗ <strong style='color:red'>Erreur : articles JSON invalide ou vide</strong><br>";
        echo "JSON reçu: {$commande['articles']}<br>";
        exit;
    }
    
    echo "✓ Articles décodés : " . count($articles) . " article(s)<br>";
    foreach($articles as $index => $article) {
        echo "  - Article " . ($index + 1) . ": {$article['type']} - {$article['quantite']} ex - {$article['nombre_cartons']} cartons<br>";
    }
    echo "<br>";
    
    // Calculer le total d'étiquettes
    $totalEtiquettes = 0;
    foreach($articles as $article) {
        $totalEtiquettes += $article['nombre_cartons'];
    }
    echo "Total étiquettes à générer: $totalEtiquettes<br><br>";
    
    // Vérifier les dossiers
    echo "<strong>Vérification des dossiers:</strong><br>";
    $pdfDir = __DIR__ . '/pdfs_latitude';
    echo "Dossier pdfs_latitude: $pdfDir<br>";
    
    if(!is_dir($pdfDir)) {
        echo "✗ Le dossier pdfs_latitude n'existe pas<br>";
        if(mkdir($pdfDir, 0777, true)) {
            echo "✓ Dossier créé<br>";
        }
    } else {
        echo "✓ Le dossier pdfs_latitude existe<br>";
    }
    
    if(is_writable($pdfDir)) {
        echo "✓ Le dossier est accessible en écriture<br><br>";
    } else {
        echo "✗ <strong style='color:red'>Le dossier n'est PAS accessible en écriture</strong><br>";
        echo "Exécutez: chmod 777 pdfs_latitude<br><br>";
    }
    
    // Test de génération
    echo "<strong>Test de génération PDF:</strong><br>";
    
    $pdfGen = new LatitudePdfGenerator();
    $filename = $pdfGen->genererEtiquettes($commande);
    
    if($filename && file_exists($filename)) {
        echo "✓ <strong style='color:green'>PDF généré avec succès !</strong><br>";
        echo "Fichier: $filename<br>";
        echo "Taille: " . filesize($filename) . " octets<br>";
        
        if(filesize($filename) < 1000) {
            echo "<strong style='color:orange'>⚠ Attention: Le fichier PDF est très petit (" . filesize($filename) . " octets)</strong><br>";
            echo "Cela peut indiquer un PDF vide ou presque vide.<br>";
        }
        
        echo "<a href='$filename' target='_blank' style='padding: 10px 20px; background: #0061f2; color: white; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 10px;'>Ouvrir le PDF</a><br>";
    } else {
        echo "✗ <strong style='color:red'>Échec de génération du PDF</strong><br>";
    }
    
} catch(Exception $e) {
    echo "<br><strong style='color:red'>Erreur:</strong><br>";
    echo $e->getMessage() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
