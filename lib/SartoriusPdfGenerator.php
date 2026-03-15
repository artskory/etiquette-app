<?php
/**
 * Générateur PDF Sartorius — Option A
 * Version 2.0 - supporte etiquettes JSON multi-blocs
 */
require_once 'lib/fpdf/fpdf.php';

class SartoriusPdfGenerator extends FPDF {

    private $factoryIconPath = 'assets/factory-icon.png';

    public function genererEtiquettes($data) {
        try {
            $this->AddPage('P', 'A4');
            $this->SetAutoPageBreak(false);

            $etiquetteWidth  = 105;
            $etiquetteHeight = 74;
            $marginLeft      = 2.5;
            $marginTop       = 0.5;
            $espaceEntreLignes = 0.25;

            $col = 0;
            $row = 0;

            // ── Résoudre les blocs d'étiquettes ──────────────────────────────
            $blocs = json_decode($data['etiquettes'] ?? 'null', true);

            if (!$blocs || !is_array($blocs)) {
                // Ancien format : un seul bloc avec quantites JSON
                $quantites = json_decode($data['quantites'] ?? '[]', true);
                if (!$quantites || empty($quantites)) {
                    if (isset($data['quantite_par_carton']) && isset($data['quantite_etiquettes'])) {
                        $quantites = [[
                            'quantite_par_carton' => (int)$data['quantite_par_carton'],
                            'quantite_etiquettes' => (int)$data['quantite_etiquettes'],
                        ]];
                    } else {
                        throw new Exception("Aucune donnée de quantités trouvée.");
                    }
                }
                $blocs = [[
                    'reference'       => $data['reference']       ?? '',
                    'designation'     => $data['designation']     ?? '',
                    'date_production' => $data['date_production'] ?? '',
                    'numero_commande' => $data['numero_commande'] ?? '',
                    'numero_lot'      => $data['numero_lot']      ?? '',
                    'quantites'       => $quantites,
                ]];
            }

            // ── Compter le total d'étiquettes ─────────────────────────────────
            $totalEtiquettes = 0;
            foreach ($blocs as $bloc) {
                foreach ($bloc['quantites'] as $qty) {
                    $totalEtiquettes += (int)($qty['quantite_etiquettes'] ?? 0);
                }
            }

            $etiquettesGenerees = 0;

            // ── Générer les étiquettes bloc par bloc ──────────────────────────
            foreach ($blocs as $bloc) {
                foreach ($bloc['quantites'] as $qty) {
                    $qpc       = (int)$qty['quantite_par_carton'];
                    $nbLabels  = (int)$qty['quantite_etiquettes'];

                    $dataLabel = [
                        'reference'          => $bloc['reference'],
                        'designation'        => $bloc['designation'],
                        'date_production'    => $bloc['date_production'],
                        'numero_commande'    => $bloc['numero_commande'],
                        'numero_lot'         => $bloc['numero_lot'],
                        'quantite_par_carton'=> $qpc,
                    ];

                    for ($i = 0; $i < $nbLabels; $i++) {
                        $etiquettesGenerees++;
                        $posX = $marginLeft + ($col * $etiquetteWidth);
                        $posY = $marginTop  + ($row * ($etiquetteHeight + $espaceEntreLignes));
                        $this->dessinerEtiquette($posX, $posY, $etiquetteWidth, $etiquetteHeight, $dataLabel);

                        $col++;
                        if ($col >= 2) {
                            $col = 0;
                            $row++;
                            if ($row >= 4 && $etiquettesGenerees < $totalEtiquettes) {
                                $this->AddPage('P', 'A4');
                                $row = 0;
                            }
                        }
                    }
                }
            }

            // ── Sauvegarder ───────────────────────────────────────────────────
            $pdfDir = dirname(__FILE__) . '/../pdfs_sartorius/';
            if (!is_dir($pdfDir)) {
                mkdir($pdfDir, 0777, true);
                chmod($pdfDir, 0777);
            }

            $refClean = preg_replace('/[^a-zA-Z0-9_-]/', '_', $data['reference'] ?? 'commande');
            $cmdClean = preg_replace('/[^a-zA-Z0-9_-]/', '_', $data['numero_commande']);
            $lotClean = preg_replace('/[^a-zA-Z0-9_-]/', '_', $data['numero_lot']);
            $pdfName  = $refClean . '_' . $cmdClean . '_' . $lotClean;
            $filename = $pdfDir . $pdfName . '.pdf';

            $this->Output('F', $filename);

            if (!file_exists($filename)) {
                throw new Exception("Le fichier PDF n'a pas été créé. Chemin: " . $filename);
            }
            chmod($filename, 0666);

            return 'pdfs_sartorius/' . $pdfName . '.pdf';

        } catch (Exception $e) {
            error_log("Erreur PdfGenerator: " . $e->getMessage());
            throw $e;
        }
    }

    private function dessinerEtiquette($x, $y, $width, $height, $data) {
        $currentY = $y + 8;

        $this->SetFont('Helvetica', 'B', 12);
        $this->SetXY($x + 3, $currentY);
        $this->Cell(0, 5, 'REF : ' . mb_convert_encoding($data['reference'], 'ISO-8859-1', 'UTF-8'), 0, 1);
        $currentY += 8;

        $this->SetFont('Helvetica', '', 12);
        $this->SetXY($x + 3, $currentY);
        $this->Cell(0, 5, 'QUANTITE : 1 CARTON DE ' . $data['quantite_par_carton'] . ' ex', 0, 1);
        $currentY += 8;

        $this->SetFont('Helvetica', 'B', 12);
        $this->SetXY($x + 3, $currentY);
        $this->MultiCell($width - 4, 5, mb_convert_encoding($data['designation'], 'ISO-8859-1', 'UTF-8'), 0, 'L');
        $currentY = $this->GetY() + 2;

        $this->SetTextColor(41, 128, 185);
        if (file_exists($this->factoryIconPath)) {
            $this->Image($this->factoryIconPath, $x + 3, $currentY - 3, 8, 8);
        }
        $this->SetFont('Helvetica', 'B', 12);
        $this->SetXY($x + 11, $currentY);
        $this->Cell(0, 6, mb_convert_encoding($data['date_production'], 'ISO-8859-1', 'UTF-8'), 0, 1);
        $this->SetTextColor(0, 0, 0);
        $currentY += 8;

        $this->SetFont('Helvetica', '', 12);
        $this->SetXY($x + 3, $currentY);
        $this->Cell(0, 4, 'CDE : ' . mb_convert_encoding($data['numero_commande'], 'ISO-8859-1', 'UTF-8'), 0, 1);
        $currentY += 8;

        $this->SetXY($x + 3, $currentY);
        $this->Cell(0, 4, 'LOT : ' . mb_convert_encoding($data['numero_lot'], 'ISO-8859-1', 'UTF-8'), 0, 1);
        $currentY += 8;

        $this->SetFont('Helvetica', '', 12);
        $this->SetXY($x + 3, $currentY);
        $this->Cell(0, 4, 'GUIFLEX', 0, 1);
        $currentY += 6;

        $this->SetXY($x + 3, $currentY);
        $this->Cell(0, 4, 'MADE IN FRANCE', 0, 1);
    }
}
