<?php
// Créer une petite icône d'usine en PNG (16x16)
$img = imagecreate(16, 16);

// Couleurs
$transparent = imagecolorallocate($img, 255, 255, 255);
$blue = imagecolorallocate($img, 41, 128, 185);

imagecolortransparent($img, $transparent);

// Dessiner une usine stylisée
// Base
imagefilledrectangle($img, 1, 13, 14, 15, $blue);

// Cheminées
imagefilledrectangle($img, 3, 8, 4, 12, $blue);
imagefilledrectangle($img, 7, 5, 8, 12, $blue);
imagefilledrectangle($img, 11, 8, 12, 12, $blue);

// Fenêtres
imagesetpixel($img, 5, 14, $transparent);
imagesetpixel($img, 9, 14, $transparent);

// Sauvegarder
imagepng($img, __DIR__ . '/images/factory.png');
imagedestroy($img);

echo "Icône d'usine créée : " . __DIR__ . '/images/factory.png' . "\n";
