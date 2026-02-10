<?php
/**
 * Script de diagnostic pour la génération PDF
 * Accéder via : http://localhost/etiquette-app/test_pdf.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/database.php';
require_once 'models/Commande.php';
require_once 'lib/PdfGenerator.php';

echo "<h1>Test de génération PDF</h1>";
echo "<hr>";

try {
    // Connexion base de données
    $database = new Database();
    $db = $database->getConnection();
    echo "✓ Connexion base de données OK<br><br>";
    
    // Récupérer une commande de test
    $query = "SELECT c.*, r.reference, r.designation 
              FROM commandes c 
              LEFT JOIN `references` r ON c.reference_id = r.id 
              ORDER BY c.id DESC LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $commande = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if(!$commande) {
        echo "✗ Aucune commande trouvée dans la base de données<br>";
        echo "Créez une commande via l'application d'abord.";
        exit;
    }
    
    echo "✓ Commande trouvée (ID: {$commande['id']})<br>";
    echo "N° Commande: {$commande['numero_commande']}<br>";
    echo "Référence: {$commande['reference']}<br>";
    echo "Désignation: {$commande['designation']}<br>";
    echo "Quantité: {$commande['quantite_etiquettes']}<br><br>";
    
    // Vérifier les dossiers
    echo "<strong>Vérification des dossiers:</strong><br>";
    $pdfDir = __DIR__ . '/pdfs';
    echo "Dossier pdfs: $pdfDir<br>";
    
    if(!is_dir($pdfDir)) {
        echo "✗ Le dossier pdfs n'existe pas<br>";
        if(mkdir($pdfDir, 0777, true)) {
            echo "✓ Dossier créé<br>";
        }
    } else {
        echo "✓ Le dossier pdfs existe<br>";
    }
    
    if(is_writable($pdfDir)) {
        echo "✓ Le dossier est accessible en écriture<br><br>";
    } else {
        echo "✗ <strong style='color:red'>Le dossier n'est PAS accessible en écriture</strong><br>";
        echo "Exécutez: chmod 777 pdfs<br><br>";
    }
    
    // Vérifier l'icône factory
    $iconPath = __DIR__ . '/assets/factory-icon.png';
    if(file_exists($iconPath)) {
        echo "✓ Icône factory existe<br><br>";
    } else {
        echo "✗ Icône factory manquante: $iconPath<br><br>";
    }
    
    // Test de génération
    echo "<strong>Test de génération PDF:</strong><br>";
    
    $pdfGen = new PdfGenerator();
    $filename = $pdfGen->genererEtiquettes($commande, $commande['quantite_etiquettes']);
    
    if($filename && file_exists($filename)) {
        echo "✓ <strong style='color:green'>PDF généré avec succès !</strong><br>";
        echo "Fichier: $filename<br>";
        echo "Taille: " . filesize($filename) . " octets<br>";
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
