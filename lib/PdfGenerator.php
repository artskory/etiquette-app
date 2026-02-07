<?php
/**
 * Générateur de PDF pour les étiquettes
 * Utilise FPDF pour créer des PDFs d'étiquettes
 */

require_once 'lib/fpdf/fpdf.php';

class PdfGenerator extends FPDF {
    
    /**
     * Générer un PDF d'étiquettes
     * 
     * @param array $data Données de la commande
     * @param int $quantite Nombre d'étiquettes à générer
     * @return string Chemin du fichier PDF généré
     */
    public function genererEtiquettes($data, $quantite) {
        // Format A4 (210 x 297 mm)
        $this->AddPage();
        $this->SetAutoPageBreak(true, 10);
        
        // Dimensions d'une étiquette (environ 100mm x 70mm pour 4 par page)
        $etiquetteWidth = 95;
        $etiquetteHeight = 65;
        $marginLeft = 10;
        $marginTop = 15;
        $espaceEntreColonnes = 10;
        $espaceEntreLignes = 5;
        
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
            
            // Dessiner une étiquette
            $this->dessinerEtiquette($posX, $posY, $etiquetteWidth, $etiquetteHeight, $data);
            
            // Passer à la colonne suivante
            $col++;
            
            // Si on a atteint 2 colonnes, passer à la ligne suivante
            if($col >= 2) {
                $col = 0;
                $row++;
                
                // Si on a rempli la page (4 étiquettes), nouvelle page
                if($row >= 4) {
                    $this->AddPage();
                    $row = 0;
                }
            }
        }
        
        // Créer le dossier pdfs s'il n'existe pas
        if(!is_dir('pdfs')) {
            mkdir('pdfs', 0755, true);
        }
        
        // Nom du fichier
        $filename = 'pdfs/etiquette_' . $data['numero_commande'] . '_' . date('YmdHis') . '.pdf';
        
        // Sauvegarder le PDF
        $this->Output('F', $filename);
        
        return $filename;
    }
    
    /**
     * Dessiner une seule étiquette
     */
    private function dessinerEtiquette($x, $y, $width, $height, $data) {
        // Bordure de l'étiquette
        $this->Rect($x, $y, $width, $height);
        
        // Position Y courante pour le texte
        $currentY = $y + 3;
        
        // REF
        $this->SetFont('Arial', 'B', 10);
        $this->SetXY($x + 2, $currentY);
        $this->Cell(0, 5, 'REF : ' . utf8_decode($data['reference']), 0, 1);
        $currentY += 6;
        
        // QUANTITE
        $this->SetFont('Arial', '', 9);
        $this->SetXY($x + 2, $currentY);
        $this->Cell(0, 5, 'QUANTITE : 1 CARTON DE ' . $data['quantite_par_carton'] . ' e', 0, 1);
        $currentY += 7;
        
        // Désignation (en gras)
        $this->SetFont('Arial', 'B', 11);
        $this->SetXY($x + 2, $currentY);
        $this->MultiCell($width - 4, 5, utf8_decode($data['designation']), 0, 'L');
        $currentY = $this->GetY() + 2;
        
        // Logo calendrier + Date de production (en bleu)
        $this->SetFont('Arial', 'B', 10);
        $this->SetTextColor(41, 128, 185); // Bleu
        $this->SetXY($x + 2, $currentY);
        // Symbole calendrier Unicode
        $this->Cell(5, 5, chr(128), 0, 0); // Icône approximative
        $this->Cell(0, 5, utf8_decode($data['date_production']), 0, 1);
        $this->SetTextColor(0, 0, 0); // Retour au noir
        $currentY += 6;
        
        // CDE
        $this->SetFont('Arial', '', 9);
        $this->SetXY($x + 2, $currentY);
        $this->Cell(0, 4, 'CDE : ' . utf8_decode($data['numero_commande']), 0, 1);
        $currentY += 5;
        
        // LOT
        $this->SetXY($x + 2, $currentY);
        $this->Cell(0, 4, 'LOT : ' . utf8_decode($data['numero_lot']), 0, 1);
        $currentY += 5;
        
        // GUIFLEX
        $this->SetFont('Arial', '', 9);
        $this->SetXY($x + 2, $currentY);
        $this->Cell(0, 4, 'GUIFLEX', 0, 1);
        $currentY += 5;
        
        // MADE IN FRANCE
        $this->SetXY($x + 2, $currentY);
        $this->Cell(0, 4, 'MADE IN FRANCE', 0, 1);
    }
}
