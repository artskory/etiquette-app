<?php
/**
 * Script pour télécharger les polices Roboto depuis Google Fonts
 * et les préparer pour FPDF
 */

// URLs des polices Roboto depuis Google Fonts
$fonts = [
    'Roboto-Regular' => 'https://github.com/google/roboto/raw/main/src/hinted/Roboto-Regular.ttf',
    'Roboto-Bold' => 'https://github.com/google/roboto/raw/main/src/hinted/Roboto-Bold.ttf'
];

$fontsDir = __DIR__ . '/fonts/';

if (!is_dir($fontsDir)) {
    mkdir($fontsDir, 0755, true);
}

echo "Téléchargement des polices Roboto...\n";

foreach ($fonts as $name => $url) {
    $ttfFile = $fontsDir . $name . '.ttf';
    
    echo "Téléchargement de $name...\n";
    
    // Télécharger le fichier TTF
    $content = @file_get_contents($url);
    
    if ($content === false) {
        echo "Erreur: Impossible de télécharger $name depuis $url\n";
        continue;
    }
    
    file_put_contents($ttfFile, $content);
    echo "✓ $name téléchargé avec succès\n";
}

echo "\nPolices téléchargées dans : $fontsDir\n";
echo "Les polices sont prêtes à être utilisées avec FPDF.\n";
