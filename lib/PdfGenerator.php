<?php
/**
 * Générateur de PDF pour les étiquettes
 * Utilise FPDF pour créer des PDFs d'étiquettes
 */

require_once 'lib/fpdf/fpdf.php';

class PdfGenerator extends FPDF {
    
    /**
     * Chemin vers l'icône d'usine
     */
    private $factoryIconPath = 'assets/factory-icon.png';
    
    /**
     * Générer un PDF d'étiquettes
     * 
     * @param array $data Données de la commande
     * @param int $quantite Nombre d'étiquettes à générer
     * @return string Chemin du fichier PDF généré
     */
    public function genererEtiquettes($data, $quantite) {
        // Format A4 paysage (297 x 210 mm)
        $this->AddPage('L', 'A4');
        $this->SetAutoPageBreak(false);
        
        // Dimensions d'une étiquette (environ 148,5mm x 105mm pour 4 par page)
        $etiquetteWidth = 148.5;
        $etiquetteHeight = 105;
        $marginLeft = 8.5;
        $marginTop = 10;
        $espaceEntreColonnes = 0;
        $espaceEntreLignes = 0;
        
        // Positions de départ
        $posX = $marginLeft;
        $posY = $marginTop;
        $col = 0;
        $row = 0;
        
        // Générer le nombre d'étiquettes demandé
        for($i = 0; $i < $quantite; $i++) {
            // Calculer la position
            $posX = $marginLeft + ($col * ($etiquetteWidth + $espaceEntreColonnes));
            $posY = $marginTop + ($row * ($etiquetteHeight + $espaceEntreLignes));
            
            // Dessiner une étiquette (sans bordure)
            $this->dessinerEtiquette($posX, $posY, $etiquetteWidth, $etiquetteHeight, $data);
            
            // Passer à la colonne suivante
            $col++;
            
            // Si on a atteint 2 colonnes, passer à la ligne suivante
            if($col >= 2) {
                $col = 0;
                $row++;
                
                // Si on a rempli la page (4 étiquettes), nouvelle page
                if($row >= 2) {
                    $this->AddPage('L', 'A4');
                    $row = 0;
                }
            }
        }
        
        // Créer le dossier pdfs s'il n'existe pas
        if(!is_dir('pdfs')) {
            mkdir('pdfs', 0755, true);
        }
        
        // Formater la date pour le nom du fichier (MM_AAAA)
        $dateParts = explode('/', $data['date_production']);
        $dateFormatted = $dateParts[0] . '_' . $dateParts[1];
        
        // Nom du fichier : REF-MM_AAAA.pdf
        $filename = 'pdfs/' . $data['reference'] . '-' . $dateFormatted . '.pdf';
        
        // Sauvegarder le PDF
        $this->Output('F', $filename);
        
        return $filename;
    }
    
    /**
     * Dessiner une seule étiquette
     */
    private function dessinerEtiquette($x, $y, $width, $height, $data) {
        // PAS de bordure autour de l'étiquette
        
        // Position Y courante pour le texte
        $currentY = $y + 3;
        
        // REF - Roboto Bold (mappé sur Helvetica-Bold)
        $this->SetFont('Helvetica', 'B', 18);
        $this->SetXY($x + 2, $currentY);
        $this->Cell(0, 5, 'REF : ' . utf8_decode($data['reference']), 0, 1);
        $currentY += 15;
        
        // QUANTITE - Roboto Regular (mappé sur Helvetica)
        $this->SetFont('Helvetica', '', 18);
        $this->SetXY($x + 2, $currentY);
        $this->Cell(0, 5, 'QUANTITE : 1 CARTON DE ' . $data['quantite_par_carton'] . ' ex', 0, 1);
        $currentY += 15;
        
        // Désignation - Roboto Bold (mappé sur Helvetica-Bold)
        $this->SetFont('Helvetica', 'B', 18);
        $this->SetXY($x + 2, $currentY);
        $this->MultiCell($width - 4, 5, utf8_decode($data['designation']), 0, 'L');
        $currentY = $this->GetY() + 2;
        
        // Icône usine (PNG) + Date de production (en bleu) - Roboto Bold
        $this->SetTextColor(41, 128, 185); // Bleu
        
        // Insérer l'icône d'usine (PNG) - Taille doublée : 8mm x 8mm
        if(file_exists($this->factoryIconPath)) {
            $this->Image($this->factoryIconPath, $x + 2, $currentY - 0.5, 8, 8);
        }
        
        // Date de production à côté de l'icône
        $this->SetFont('Helvetica', 'B', 18);
        $this->SetXY($x + 11, $currentY);
        $this->Cell(0, 6, utf8_decode($data['date_production']), 0, 1);
        $this->SetTextColor(0, 0, 0); // Retour au noir
        $currentY += 8;
        
        // CDE - Roboto Regular
        $this->SetFont('Helvetica', '', 18);
        $this->SetXY($x + 2, $currentY);
        $this->Cell(0, 4, 'CDE : ' . utf8_decode($data['numero_commande']), 0, 1);
        $currentY += 8;
        
        // LOT - Roboto Regular
        $this->SetXY($x + 2, $currentY);
        $this->Cell(0, 4, 'LOT : ' . utf8_decode($data['numero_lot']), 0, 1);
        $currentY += 8;
        
        // GUIFLEX - Roboto Regular
        $this->SetFont('Helvetica', '', 18);
        $this->SetXY($x + 2, $currentY);
        $this->Cell(0, 4, 'GUIFLEX', 0, 1);
        $currentY += 8;
        
        // MADE IN FRANCE - Roboto Regular
        $this->SetXY($x + 2, $currentY);
        $this->Cell(0, 4, 'MADE IN FRANCE', 0, 1);
    }
}
