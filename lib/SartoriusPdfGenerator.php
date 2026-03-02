<?php
/**
 * Générateur de PDF pour les étiquettes Sartorius
 * Utilise FPDF pour créer des PDFs d'étiquettes
 */

require_once 'lib/fpdf/fpdf.php';

class SartoriusPdfGenerator extends FPDF {
    
    /**
     * Chemin vers l'icône d'usine
     */
    private $factoryIconPath = 'assets/factory-icon.png';
    
    /**
     * Générer un PDF d'étiquettes avec support JSON
     * 
     * @param array $data Données de la commande (avec quantites JSON)
     * @return string Chemin du fichier PDF généré
     */
    public function genererEtiquettes($data) {
        try {
            // Debug: Log des données reçues
            error_log("PdfGenerator: Données reçues - " . print_r(array_keys($data), true));
            error_log("PdfGenerator: quantites field = " . ($data['quantites'] ?? 'ABSENT'));
            
            // Format A4 portrait (210 x 297 mm)
            $this->AddPage('P', 'A4');
            $this->SetAutoPageBreak(false);
            
            // Dimensions étiquettes : 105 x 74 mm (8 par page en 2x4)
            $etiquetteWidth = 105;
            $etiquetteHeight = 74;
            $marginLeft = 2.5;   // Centrage horizontal: (210 - 2*105) / 2 = 0, ajusté à 2.5
            $marginTop = 0.5;    // Petite marge en haut: (297 - 4*74) / 2 = 0.5
            $espaceEntreColonnes = 0;
            $espaceEntreLignes = 0.25;  // Petit espace entre les lignes
            
            // Positions de départ
            $posX = $marginLeft;
            $posY = $marginTop;
            $col = 0;
            $row = 0;
            
            // Décoder les quantités JSON
            $quantites = json_decode($data['quantites'] ?? '[]', true);
            
            // Fallback pour les anciennes colonnes (avant migration)
            if(!$quantites || !is_array($quantites) || empty($quantites)) {
                if(isset($data['quantite_par_carton']) && isset($data['quantite_etiquettes'])) {
                    // Utiliser les anciennes colonnes
                    $quantites = [[
                        'quantite_par_carton' => (int)$data['quantite_par_carton'],
                        'quantite_etiquettes' => (int)$data['quantite_etiquettes']
                    ]];
                } else {
                    throw new Exception("Aucune donnée de quantités trouvée (ni JSON ni anciennes colonnes)");
                }
            }
            
            // Calculer le nombre total d'étiquettes à générer
            $totalEtiquettes = 0;
            foreach($quantites as $qty) {
                $totalEtiquettes += (int)$qty['quantite_etiquettes'];
            }
            
            $etiquettesGenerees = 0;
            
            // Générer les étiquettes pour chaque ligne de quantités
            foreach($quantites as $qty) {
                $quantiteParCarton = $qty['quantite_par_carton'];
                $nombreEtiquettes = $qty['quantite_etiquettes'];
                
                // Créer les données pour cette variation
                $dataVariation = $data;
                $dataVariation['quantite_par_carton'] = $quantiteParCarton;
                
                // Générer le nombre d'étiquettes demandé
                for($i = 0; $i < $nombreEtiquettes; $i++) {
                    $etiquettesGenerees++;
                    
                    // Calculer la position
                    $posX = $marginLeft + ($col * ($etiquetteWidth + $espaceEntreColonnes));
                    $posY = $marginTop + ($row * ($etiquetteHeight + $espaceEntreLignes));
                    
                    // Dessiner une étiquette (sans bordure)
                    $this->dessinerEtiquette($posX, $posY, $etiquetteWidth, $etiquetteHeight, $dataVariation);
                    
                    // Passer à la colonne suivante
                    $col++;
                    
                    // Si on a atteint 2 colonnes, passer à la ligne suivante
                    if($col >= 2) {
                        $col = 0;
                        $row++;
                        
                        // Si on a rempli la page (8 étiquettes = 4 lignes), nouvelle page
                        // MAIS SEULEMENT s'il reste des étiquettes à générer
                        if($row >= 4 && $etiquettesGenerees < $totalEtiquettes) {
                            $this->AddPage('P', 'A4');
                            $row = 0;
                        }
                    }
                }
            }
            
            // Créer le dossier pdfs s'il n'existe pas
            $pdfDir = dirname(__FILE__) . '/../pdfs_sartorius/';
            if(!is_dir($pdfDir)) {
                if(!mkdir($pdfDir, 0777, true)) {
                    throw new Exception("Impossible de créer le dossier pdfs. Vérifiez les permissions.");
                }
                chmod($pdfDir, 0777); // Permissions maximales pour Mac
            }
            
            // Formater la date pour le nom du fichier (MM_AAAA)
            $dateParts = explode('/', $data['date_production']);
            $dateFormatted = $dateParts[0] . '_' . $dateParts[1];
            
            // Nettoyer le nom de référence pour éviter les caractères spéciaux
            $refClean = preg_replace('/[^a-zA-Z0-9_-]/', '_', $data['reference']);
            
            // Nom du fichier avec chemin absolu
            $filename = $pdfDir . '/' . $refClean . '-' . $dateFormatted . '.pdf';
            
            // Sauvegarder le PDF
            $this->Output('F', $filename);
            
            // Vérifier que le fichier a bien été créé
            if(!file_exists($filename)) {
                throw new Exception("Le fichier PDF n'a pas été créé. Chemin: " . $filename);
            }
            
            // Changer les permissions du fichier pour qu'il soit accessible
            chmod($filename, 0666);
            
            // Retourner le chemin relatif pour l'application
            return 'pdfs_sartorius/' . $refClean . '-' . $dateFormatted . '.pdf';
            
        } catch(Exception $e) {
            error_log("Erreur PdfGenerator: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Dessiner une seule étiquette
     */
    private function dessinerEtiquette($x, $y, $width, $height, $data) {
        // PAS de bordure autour de l'étiquette
        //$this->Rect($x, $y, $width, $height);
       
        // Position Y courante pour le texte
        $currentY = $y + 8;
        
        // REF - Roboto Bold (mappé sur Helvetica-Bold)
        $this->SetFont('Helvetica', 'B', 12);
        $this->SetXY($x + 3, $currentY);
        $this->Cell(0, 5, 'REF : ' . mb_convert_encoding($data['reference'], 'ISO-8859-1', 'UTF-8'), 0, 1);
        $currentY += 8;
        
        // QUANTITE - Roboto Regular (mappé sur Helvetica)
        $this->SetFont('Helvetica', '', 12);
        $this->SetXY($x + 3, $currentY);
        $this->Cell(0, 5, 'QUANTITE : 1 CARTON DE ' . $data['quantite_par_carton'] . ' ex', 0, 1);
        $currentY += 8;
        
        // Désignation - Roboto Bold (mappé sur Helvetica-Bold)
        $this->SetFont('Helvetica', 'B', 12);
        $this->SetXY($x + 3, $currentY);
        $this->MultiCell($width - 4, 5, mb_convert_encoding($data['designation'], 'ISO-8859-1', 'UTF-8'), 0, 'L');
        $currentY = $this->GetY() + 2;
        
        // Icône usine (PNG) + Date de production (en bleu) - Roboto Bold
        $this->SetTextColor(41, 128, 185); // Bleu
        
        // Insérer l'icône d'usine (PNG) - Taille doublée : 8mm x 8mm
        if(file_exists($this->factoryIconPath)) {
            $this->Image($this->factoryIconPath, $x + 3, $currentY - 3, 8, 8);
        }
        
        
        // Date de production à côté de l'icône
        $this->SetFont('Helvetica', 'B', 12);
        $this->SetXY($x + 11, $currentY);
        $this->Cell(0, 6, mb_convert_encoding($data['date_production'], 'ISO-8859-1', 'UTF-8'), 0, 1);
        $this->SetTextColor(0, 0, 0); // Retour au noir
        $currentY += 8;
        
        // CDE - Roboto Regular
        $this->SetFont('Helvetica', '', 12);
        $this->SetXY($x + 3, $currentY);
        $this->Cell(0, 4, 'CDE : ' . mb_convert_encoding($data['numero_commande'], 'ISO-8859-1', 'UTF-8'), 0, 1);
        $currentY += 8;
        
        // LOT - Roboto Regular
        $this->SetXY($x + 3, $currentY);
        $this->Cell(0, 4, 'LOT : ' . mb_convert_encoding($data['numero_lot'], 'ISO-8859-1', 'UTF-8'), 0, 1);
        $currentY += 8;
        
        // GUIFLEX - Roboto Regular
        $this->SetFont('Helvetica', '', 12);
        $this->SetXY($x + 3, $currentY);
        $this->Cell(0, 4, 'GUIFLEX', 0, 1);
        $currentY += 6;
        
        // MADE IN FRANCE - Roboto Regular
        $this->SetXY($x + 3, $currentY);
        $this->Cell(0, 4, 'MADE IN FRANCE', 0, 1);
    }
}
