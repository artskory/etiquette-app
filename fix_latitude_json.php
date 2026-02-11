<?php
/**
 * Script de correction des JSON corrompus par htmlspecialchars
 * Accéder via : http://localhost/etiquette-app/fix_latitude_json.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/database.php';

echo "<h1>Correction des JSON Latitude</h1>";
echo "<hr>";

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Récupérer toutes les commandes
    $query = "SELECT id, numero_commande, articles FROM commandes_latitude";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Commandes trouvées : " . count($commandes) . "<br><br>";
    
    $corrected = 0;
    $alreadyOk = 0;
    
    foreach($commandes as $commande) {
        $id = $commande['id'];
        $articles = $commande['articles'];
        
        echo "<strong>Commande #{$id} ({$commande['numero_commande']})</strong><br>";
        echo "JSON actuel : " . htmlspecialchars($articles) . "<br>";
        
        // Vérifier si le JSON est valide
        $decoded = json_decode($articles, true);
        
        if($decoded && is_array($decoded) && count($decoded) > 0) {
            echo "<span style='color:green'>✓ JSON valide, rien à faire</span><br><br>";
            $alreadyOk++;
        } else {
            // Décoder les entités HTML
            $fixed = html_entity_decode($articles, ENT_QUOTES, 'UTF-8');
            
            echo "JSON corrigé : " . htmlspecialchars($fixed) . "<br>";
            
            // Vérifier que la correction fonctionne
            $decodedFixed = json_decode($fixed, true);
            
            if($decodedFixed && is_array($decodedFixed) && count($decodedFixed) > 0) {
                // Mettre à jour en base
                $updateQuery = "UPDATE commandes_latitude SET articles = :articles WHERE id = :id";
                $updateStmt = $db->prepare($updateQuery);
                $updateStmt->bindParam(':articles', $fixed);
                $updateStmt->bindParam(':id', $id);
                
                if($updateStmt->execute()) {
                    echo "<span style='color:green'>✓ Corrigé avec succès !</span><br><br>";
                    $corrected++;
                } else {
                    echo "<span style='color:red'>✗ Erreur lors de la mise à jour</span><br><br>";
                }
            } else {
                echo "<span style='color:red'>✗ Impossible de corriger ce JSON</span><br><br>";
            }
        }
    }
    
    echo "<hr>";
    echo "<h2>Résumé</h2>";
    echo "Commandes déjà correctes : $alreadyOk<br>";
    echo "Commandes corrigées : $corrected<br>";
    echo "<br>";
    echo "<a href='index.php?page=latitude' style='padding: 10px 20px; background: #0061f2; color: white; text-decoration: none; border-radius: 5px;'>Retour à Latitude</a>";
    echo "<br><br>";
    echo "<p style='color: orange;'><strong>Note :</strong> Vous pouvez maintenant supprimer ce fichier : fix_latitude_json.php</p>";
    
} catch(Exception $e) {
    echo "<strong style='color:red'>Erreur :</strong><br>";
    echo $e->getMessage();
}
?>
