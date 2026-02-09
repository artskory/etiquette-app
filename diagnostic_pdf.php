<?php
/**
 * Script de diagnostic pour la génération de PDF
 * À placer à la racine de l'application et à exécuter via le navigateur
 */

echo "<h1>Diagnostic de génération PDF</h1>";
echo "<hr>";

// 1. Vérifier la version de PHP
echo "<h2>1. Version PHP</h2>";
echo "Version PHP: " . phpversion() . "<br>";
echo "PHP doit être >= 7.4<br>";
echo "<hr>";

// 2. Vérifier les permissions du dossier pdfs
echo "<h2>2. Dossier pdfs</h2>";
$pdfDir = __DIR__ . '/pdfs';
echo "Chemin: " . $pdfDir . "<br>";

if(is_dir($pdfDir)) {
    echo "✓ Le dossier existe<br>";
    echo "Permissions: " . substr(sprintf('%o', fileperms($pdfDir)), -4) . "<br>";
    
    if(is_writable($pdfDir)) {
        echo "✓ Le dossier est accessible en écriture<br>";
    } else {
        echo "✗ <strong style='color:red'>Le dossier n'est PAS accessible en écriture</strong><br>";
        echo "Exécutez: chmod 777 " . $pdfDir . "<br>";
    }
} else {
    echo "✗ Le dossier n'existe pas<br>";
    echo "Tentative de création...<br>";
    if(mkdir($pdfDir, 0777, true)) {
        echo "✓ Dossier créé avec succès<br>";
        chmod($pdfDir, 0777);
    } else {
        echo "✗ <strong style='color:red'>Impossible de créer le dossier</strong><br>";
    }
}
echo "<hr>";

// 3. Vérifier FPDF
echo "<h2>3. Bibliothèque FPDF</h2>";
$fpdfPath = __DIR__ . '/lib/fpdf/fpdf.php';
echo "Chemin: " . $fpdfPath . "<br>";

if(file_exists($fpdfPath)) {
    echo "✓ Le fichier FPDF existe<br>";
    require_once $fpdfPath;
    if(class_exists('FPDF')) {
        echo "✓ La classe FPDF est chargée<br>";
    } else {
        echo "✗ <strong style='color:red'>La classe FPDF n'est pas disponible</strong><br>";
    }
} else {
    echo "✗ <strong style='color:red'>Le fichier FPDF n'existe pas</strong><br>";
}
echo "<hr>";

// 4. Vérifier l'icône factory
echo "<h2>4. Icône factory</h2>";
$iconPath = __DIR__ . '/assets/factory-icon.png';
echo "Chemin: " . $iconPath . "<br>";

if(file_exists($iconPath)) {
    echo "✓ L'icône existe<br>";
    $imageInfo = getimagesize($iconPath);
    if($imageInfo) {
        echo "Dimensions: " . $imageInfo[0] . "x" . $imageInfo[1] . "px<br>";
        echo "Type: " . $imageInfo['mime'] . "<br>";
    }
} else {
    echo "✗ <strong style='color:red'>L'icône n'existe pas</strong><br>";
}
echo "<hr>";

// 5. Test de génération PDF simple
echo "<h2>5. Test de génération PDF</h2>";

try {
    require_once $fpdfPath;
    
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(40, 10, 'Test PDF');
    
    $testFile = $pdfDir . '/test.pdf';
    $pdf->Output('F', $testFile);
    
    if(file_exists($testFile)) {
        echo "✓ <strong style='color:green'>Test PDF créé avec succès !</strong><br>";
        echo "Fichier: " . $testFile . "<br>";
        echo "Taille: " . filesize($testFile) . " octets<br>";
        echo "<a href='pdfs/test.pdf' target='_blank'>Ouvrir le PDF de test</a><br>";
    } else {
        echo "✗ <strong style='color:red'>Le fichier PDF n'a pas été créé</strong><br>";
    }
    
} catch(Exception $e) {
    echo "✗ <strong style='color:red'>Erreur: " . $e->getMessage() . "</strong><br>";
}
echo "<hr>";

// 6. Vérifier les logs d'erreur PHP
echo "<h2>6. Configuration PHP</h2>";
echo "Display Errors: " . ini_get('display_errors') . "<br>";
echo "Error Reporting: " . error_reporting() . "<br>";
echo "Log Errors: " . ini_get('log_errors') . "<br>";
echo "Error Log: " . ini_get('error_log') . "<br>";
echo "<hr>";

// 7. Permissions système
echo "<h2>7. Informations système</h2>";
echo "Système d'exploitation: " . PHP_OS . "<br>";
echo "Utilisateur PHP: " . get_current_user() . "<br>";
if(function_exists('posix_getpwuid') && function_exists('posix_geteuid')) {
    $processUser = posix_getpwuid(posix_geteuid());
    echo "Utilisateur du processus: " . $processUser['name'] . "<br>";
}
echo "<hr>";

echo "<h2>Résumé</h2>";
echo "<p>Si tous les tests sont verts (✓), la génération de PDF devrait fonctionner.</p>";
echo "<p>Si vous voyez des croix rouges (✗), corrigez les problèmes indiqués.</p>";
echo "<p><strong>Pour Mac avec XAMPP:</strong></p>";
echo "<ul>";
echo "<li>Vérifiez que le dossier pdfs a les permissions 777</li>";
echo "<li>Utilisez Terminal: <code>chmod -R 777 " . $pdfDir . "</code></li>";
echo "<li>Si le problème persiste, vérifiez les logs: /Applications/XAMPP/xamppfiles/logs/php_error_log</li>";
echo "</ul>";
?>
