<?php
/**
 * Générateur de PDF pour les étiquettes Latitude
 */

require_once 'lib/fpdf/fpdf.php';

class LatitudePdfGenerator extends FPDF {
    
    /**
     * Générer un PDF d'étiquettes Latitude
     * 
     * @param array $data Données de la commande
     * @return string Chemin du fichier PDF généré
     */
    public function genererEtiquettes($data) {
        try {
            // Debug: Vérifier les données reçues
            error_log("LatitudePdfGenerator - Données reçues: " . print_r($data, true));
            
            // Format A4 portrait (210 x 297 mm)
            $this->AddPage('P', 'A4');
            $this->SetAutoPageBreak(false);
            
            // Dimensions étiquettes : 105 x 74 mm (8 par page en 2x4)
            $etiquetteWidth = 105;
            $etiquetteHeight = 74;
            $marginLeft = 2.5;   // Centrage horizontal
            $marginTop = 0.5;    // Petite marge en haut
            
            // Décoder les articles JSON
            $articles = json_decode($data['articles'], true);
            
            // Debug: Vérifier le décodage JSON
            error_log("LatitudePdfGenerator - Articles JSON: " . $data['articles']);
            error_log("LatitudePdfGenerator - Articles décodés: " . print_r($articles, true));
            error_log("LatitudePdfGenerator - Nombre articles: " . (is_array($articles) ? count($articles) : 0));
            
            if(!$articles || !is_array($articles) || count($articles) === 0) {
                throw new Exception("Aucun article trouvé ou JSON invalide");
            }
            
            // Compteur global pour les numéros de carton
            $cartonNumero = 1;
            
            // Position dans la grille
            $col = 0;
            $row = 0;
            
            // Calculer le nombre total d'étiquettes (cartons) à générer
            $totalCartons = 0;
            foreach($articles as $article) {
                $totalCartons += (int)$article['nombre_cartons'];
            }
            
            $cartonsGeneres = 0;
            
            // Parcourir chaque article
            foreach($articles as $article) {
                $type = $article['type'];
                $quantite = $article['quantite'];
                $nombreCartons = $article['nombre_cartons'];
                
                // Générer une étiquette pour chaque carton de cet article
                for($i = 0; $i < $nombreCartons; $i++) {
                    $cartonsGeneres++;
                    
                    // Calculer la position
                    $posX = $marginLeft + ($col * $etiquetteWidth);
                    $posY = $marginTop + ($row * $etiquetteHeight);
                    
                    // Dessiner l'étiquette
                    $this->dessinerEtiquette(
                        $posX, 
                        $posY, 
                        $etiquetteWidth, 
                        $etiquetteHeight, 
                        $data['numero_commande'],
                        $type,
                        $quantite,
                        $cartonNumero
                    );
                    
                    // Incrémenter le numéro de carton
                    $cartonNumero++;
                    
                    // Passer à la colonne suivante
                    $col++;
                    
                    // Si on a atteint 2 colonnes, passer à la ligne suivante
                    if($col >= 2) {
                        $col = 0;
                        $row++;
                        
                        // Si on a rempli la page (8 étiquettes = 4 lignes), nouvelle page
                        // MAIS SEULEMENT s'il reste des cartons à générer
                        if($row >= 4 && $cartonsGeneres < $totalCartons) {
                            $this->AddPage('P', 'A4');
                            $row = 0;
                        }
                    }
                }
            }
            
            // Créer le dossier pdfs_latitude s'il n'existe pas
            $pdfDir = dirname(__FILE__) . '/../pdfs_latitude';
            if(!is_dir($pdfDir)) {
                if(!mkdir($pdfDir, 0777, true)) {
                    throw new Exception("Impossible de créer le dossier pdfs_latitude");
                }
                chmod($pdfDir, 0777);
            }
            
            // Nettoyer le numéro de commande
            $cmdClean = preg_replace('/[^a-zA-Z0-9_-]/', '_', $data['numero_commande']);
            
            // Nom du fichier
            $pdfName = 'Etiquette_latitudes_commande_' . $cmdClean;
            $filename = $pdfDir . '/' . $pdfName . '.pdf';
            
            // Sauvegarder le PDF
            $this->Output('F', $filename);
            
            // Vérifier la création
            if(!file_exists($filename)) {
                throw new Exception("Le fichier PDF n'a pas été créé");
            }
            
            chmod($filename, 0666);
            
            // Retourner le chemin relatif
            return 'pdfs_latitude/' . $pdfName . '.pdf';
            
        } catch(Exception $e) {
            error_log("Erreur LatitudePdfGenerator: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Dessiner une seule étiquette
     */
    private function dessinerEtiquette($x, $y, $width, $height, $numeroCommande, $article, $quantite, $cartonNumero) {
        $currentY = $y + 5;
        
        // Carton n° - En gros et gras
        $this->SetFont('Helvetica', 'B', 21);
        $this->SetXY($x + 2, $currentY);
        $this->Cell(0, 8, 'Carton n' . chr(176) . ' ' . $cartonNumero, 0, 1);
        $currentY += 12;
        
        // Fournisseur
        $this->SetFont('Helvetica', '', 12);
        $this->SetXY($x + 2, $currentY);
        $this->Cell(0, 6, 'Fournisseur : MEXICHROME IMPRESSIONS', 0, 1);
        $currentY += 10;
        
        // Commande n°
        $this->SetFont('Helvetica', '', 12);
        $this->SetXY($x + 2, $currentY);
        $this->Cell(0, 6, 'Commande n' . chr(176) . ' ' . mb_convert_encoding($numeroCommande, 'ISO-8859-1', 'UTF-8'), 0, 1);
        $currentY += 10;
        
        // Article
        $this->SetFont('Helvetica', 'B', 14);
        $this->SetXY($x + 2, $currentY);
        $this->Cell(0, 7, 'Article : ' . mb_convert_encoding($article, 'ISO-8859-1', 'UTF-8'), 0, 1);
        $currentY += 12;
        
        // Quantité (Nombre d'articles)
        $this->SetFont('Helvetica', '', 12);
        $this->SetXY($x + 2, $currentY);
        $this->Cell(0, 6, 'Quantite : ' . $quantite . ' ex', 0, 1);
    }
}
