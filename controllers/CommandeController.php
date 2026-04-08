<?php
/**
 * Contrôleur Commande — Option A
 * Version 2.0 - etiquettes JSON multi-blocs
 */
class CommandeController {
    private $db;
    private $commande;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->commande = new Commande($this->db);
    }

    // ── Liste ────────────────────────────────────────────────────────────────
    public function liste() {
        $perPage        = 50;
        $page           = isset($_GET['p']) ? max(1, intval($_GET['p'])) : 1;
        $stmt           = $this->commande->readAll($page, $perPage);
        $totalCommandes = $this->commande->count();
        $totalPages     = ceil($totalCommandes / $perPage);
        require_once 'views/sartorius/liste_commande_sartorius.php';
    }

    // ── Nouvelle ─────────────────────────────────────────────────────────────
    public function nouvelle() {
        $referenceController = new ReferenceController();
        $references = $referenceController->getAll();
        require_once 'views/sartorius/nouvelle_commande_sartorius.php';
    }

    // ── Créer ────────────────────────────────────────────────────────────────
    public function creer() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $token = $_POST['csrf_token'] ?? null;
        if (!CsrfToken::validate($token)) {
            header("Location: " . BASE_URL . "/sartorius?error=csrf_invalid"); exit;
        }

        $_SESSION['form_data'] = $_POST;

        $blocs = $_POST['commandes'] ?? [];
        if (empty($blocs) || !is_array($blocs)) {
            header("Location: " . BASE_URL . "/sartorius/nouvelle?error=no_data"); exit;
        }

        $validatedBlocs = $this->validerBlocs($blocs, BASE_URL . "/sartorius/nouvelle");
        $etiquettes     = $this->resoudreEtiquettes($validatedBlocs);

        try {
            $first = $etiquettes[0];
            $this->commande->reference_id    = $first['reference_id'];
            $this->commande->numero_commande = $first['numero_commande'];
            $this->commande->date_production = $first['date_production'];
            $this->commande->numero_lot      = $first['numero_lot'];
            $this->commande->quantites       = json_encode($first['quantites']);
            $this->commande->etiquettes      = json_encode($etiquettes);

            if ($this->commande->create()) {
                unset($_SESSION['form_data']);
                $this->genererPDF($this->commande->id);
                header("Location: " . BASE_URL . "/sartorius?success=commande_created"); exit();
            } else {
                header("Location: " . BASE_URL . "/sartorius/nouvelle?error=create_failed"); exit();
            }
        } catch (PDOException $e) {
            error_log("Erreur création commande: " . $e->getMessage());
            header("Location: " . BASE_URL . "/sartorius/nouvelle?error=create_failed"); exit();
        } catch (Exception $e) {
            error_log("Erreur inattendue: " . $e->getMessage());
            header("Location: " . BASE_URL . "/sartorius/nouvelle?error=create_failed"); exit();
        }
    }

    // ── Édition ──────────────────────────────────────────────────────────────
    public function edition() {
        $id = Validator::id($_GET['id'] ?? 0);
        if ($id === false) {
            header("Location: " . BASE_URL . "/sartorius?error=invalid_id"); exit();
        }

        $this->commande->id = $id;
        $commandeData = $this->commande->readOne();

        if ($commandeData) {
            // Décoder les étiquettes (nouveau format) ou construire depuis l'ancien format
            $etiquettesBatch = json_decode($commandeData['etiquettes'] ?? 'null', true);
            if (!$etiquettesBatch || !is_array($etiquettesBatch)) {
                // Ancien format : un seul bloc avec quantites JSON
                $quantitesArray = json_decode($commandeData['quantites'] ?? '[]', true);
                if (!$quantitesArray || empty($quantitesArray)) {
                    $quantitesArray = [['quantite_par_carton' => 0, 'quantite_etiquettes' => 0]];
                }
                $etiquettesBatch = [[
                    'reference_id'    => $commandeData['reference_id'],
                    'reference'       => $commandeData['reference'] ?? '',
                    'designation'     => $commandeData['designation'] ?? '',
                    'date_production' => $commandeData['date_production'],
                    'numero_commande' => $commandeData['numero_commande'],
                    'numero_lot'      => $commandeData['numero_lot'],
                    'quantites'       => $quantitesArray,
                ]];
            }

            $referenceController = new ReferenceController();
            $references = $referenceController->getAll();
            require_once 'views/sartorius/edition_commande_sartorius.php';
        } else {
            header("Location: " . BASE_URL . "/sartorius?error=not_found"); exit();
        }
    }

    // ── Modifier ─────────────────────────────────────────────────────────────
    public function modifier() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $token = $_POST['csrf_token'] ?? null;
        if (!CsrfToken::validate($token)) {
            header("Location: " . BASE_URL . "/sartorius?error=csrf_invalid"); exit;
        }

        $id = Validator::id($_POST['id'] ?? 0);
        if ($id === false) {
            header("Location: " . BASE_URL . "/sartorius?error=invalid_id"); exit;
        }

        $blocs = $_POST['commandes'] ?? [];
        if (empty($blocs) || !is_array($blocs)) {
            header("Location: " . BASE_URL . "/sartorius/commande/$id/editer?error=no_data"); exit;
        }

        $validatedBlocs = $this->validerBlocs($blocs, BASE_URL . "/sartorius/commande/$id/editer");
        $etiquettes     = $this->resoudreEtiquettes($validatedBlocs);

        try {
            $first = $etiquettes[0];
            $this->commande->id              = $id;
            $this->commande->reference_id    = $first['reference_id'];
            $this->commande->numero_commande = $first['numero_commande'];
            $this->commande->date_production = $first['date_production'];
            $this->commande->numero_lot      = $first['numero_lot'];
            $this->commande->quantites       = json_encode($first['quantites']);
            $this->commande->etiquettes      = json_encode($etiquettes);

            if ($this->commande->update()) {
                $this->genererPDF($id);
                header("Location: " . BASE_URL . "/sartorius?success=commande_updated"); exit();
            } else {
                header("Location: " . BASE_URL . "/sartorius/commande/$id/editer?error=update_failed"); exit();
            }
        } catch (PDOException $e) {
            error_log("Erreur modification commande: " . $e->getMessage());
            header("Location: " . BASE_URL . "/sartorius/commande/$id/editer?error=update_failed"); exit();
        }
    }

    // ── Supprimer ────────────────────────────────────────────────────────────
    public function supprimer() {
        $token = $_POST['csrf_token'] ?? null;
        if (!CsrfToken::validate($token)) {
            header("Location: " . BASE_URL . "/sartorius?error=csrf_invalid"); exit;
        }

        $id = Validator::id($_POST['id'] ?? 0);
        if ($id === false) {
            header("Location: " . BASE_URL . "/sartorius?error=invalid_id"); exit;
        }

        $this->commande->id = $id;
        try {
            $commandeData = $this->commande->readOne();
            if ($commandeData) {
                $this->supprimerPDF($commandeData);
            }
            if ($this->commande->delete()) {
                header("Location: " . BASE_URL . "/sartorius?success=commande_deleted"); exit();
            } else {
                header("Location: " . BASE_URL . "/sartorius?error=delete_failed"); exit();
            }
        } catch (PDOException $e) {
            header("Location: " . BASE_URL . "/sartorius?error=delete_failed"); exit();
        }
    }

    // ── Télécharger ──────────────────────────────────────────────────────────
    public function telecharger() {
        $id = Validator::id($_GET['id'] ?? 0);
        if ($id === false) {
            header("Location: " . BASE_URL . "/sartorius?error=invalid_id"); exit();
        }
        $this->commande->id = $id;
        $commandeData = $this->commande->readOne();
        if ($commandeData) {
            $this->genererPDF($id, true);
        } else {
            header("Location: " . BASE_URL . "/sartorius?error=not_found"); exit();
        }
    }

    // ── Supprimer sélection ──────────────────────────────────────────────────
    public function supprimerSelection() {
        $token = $_POST['csrf_token'] ?? null;
        if (!CsrfToken::validate($token)) {
            header("Location: " . BASE_URL . "/sartorius?error=csrf_invalid"); exit;
        }

        $ids = Validator::arrayOfIds($_POST['ids'] ?? []);
        if ($ids === false) {
            header("Location: " . BASE_URL . "/sartorius?error=no_selection"); exit;
        }

        try {
            foreach ($ids as $id) {
                $this->commande->id = $id;
                $commandeData = $this->commande->readOne();
                if ($commandeData) {
                    $this->supprimerPDF($commandeData);
                    $this->commande->delete();
                }
            }
            header("Location: " . BASE_URL . "/sartorius?success=selection_deleted"); exit();
        } catch (Exception $e) {
            header("Location: " . BASE_URL . "/sartorius?error=delete_failed"); exit();
        }
    }

    // ── Combiner sélection → PDF unique ─────────────────────────────────────
    public function combiner() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . BASE_URL . "/sartorius"); exit;
        }

        $token = $_POST['csrf_token'] ?? null;
        if (!CsrfToken::validate($token)) {
            header("Location: " . BASE_URL . "/sartorius?error=csrf_invalid"); exit;
        }

        $ids = Validator::arrayOfIds($_POST['ids'] ?? []);
        if ($ids === false || count($ids) < 1) {
            header("Location: " . BASE_URL . "/sartorius?error=no_selection"); exit;
        }

        require_once 'lib/SartoriusPdfGenerator.php';

        try {
            // Charger toutes les commandes sélectionnées (dans l'ordre des IDs soumis)
            $commandesDatas = [];
            foreach ($ids as $id) {
                $query = "SELECT c.*, r.reference, r.designation
                          FROM commandes c
                          LEFT JOIN `references` r ON c.reference_id = r.id
                          WHERE c.id = ?";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$id]);
                $data = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($data) {
                    $commandesDatas[] = $data;
                }
            }

            if (empty($commandesDatas)) {
                header("Location: " . BASE_URL . "/sartorius?error=not_found"); exit;
            }

            $pdfGen   = new SartoriusPdfGenerator();
            $filePath = $pdfGen->genererCombine($commandesDatas);

            // Nom du fichier = numéros de commandes séparés par _
            $numeros      = array_map(fn($d) => preg_replace('/[^a-zA-Z0-9_-]/', '_', $d['numero_commande']), $commandesDatas);
            $downloadName = implode('_', $numeros) . '.pdf';
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $downloadName . '"');
            header('Content-Length: ' . filesize($filePath));
            readfile($filePath);
            @unlink($filePath);
            exit;

        } catch (Exception $e) {
            error_log("Erreur combinaison PDF: " . $e->getMessage());
            header("Location: " . BASE_URL . "/sartorius?error=pdf_generation_failed"); exit;
        }
    }

    // ── Helpers privés ───────────────────────────────────────────────────────

    /**
     * Valider tous les blocs POST, redirige en cas d'erreur
     */
    private function validerBlocs(array $blocs, string $errorBase): array {
        $validated = [];
        foreach ($blocs as $bloc) {
            $referenceId = Validator::id($bloc['reference_id'] ?? 0);
            if ($referenceId === false) {
                header("Location: $errorBase?error=invalid_reference"); exit;
            }
            $numeroCommande = Validator::numeroCommande($bloc['numero_commande'] ?? '');
            if ($numeroCommande === false) {
                header("Location: $errorBase?error=invalid_numero"); exit;
            }
            $numeroLot = Validator::numeroLot($bloc['numero_lot'] ?? '');
            if ($numeroLot === false) {
                header("Location: $errorBase?error=invalid_lot"); exit;
            }
            $dateProduction = Validator::dateMoisAnnee($bloc['date_production'] ?? '');
            if ($dateProduction === false) {
                header("Location: $errorBase?error=invalid_date"); exit;
            }
            $quantites = Validator::quantitesSartorius($bloc['quantites'] ?? []);
            if ($quantites === false) {
                header("Location: $errorBase?error=invalid_quantities"); exit;
            }
            $validated[] = compact('referenceId', 'numeroCommande', 'numeroLot', 'dateProduction', 'quantites');
        }
        return $validated;
    }

    /**
     * Résoudre les références et construire le tableau etiquettes
     */
    private function resoudreEtiquettes(array $validatedBlocs): array {
        $etiquettes = [];
        foreach ($validatedBlocs as $data) {
            $stmt = $this->db->prepare("SELECT reference, designation FROM `references` WHERE id = ?");
            $stmt->execute([$data['referenceId']]);
            $ref = $stmt->fetch(PDO::FETCH_ASSOC);

            $etiquettes[] = [
                'reference_id'    => $data['referenceId'],
                'reference'       => $ref['reference']   ?? '',
                'designation'     => $ref['designation'] ?? '',
                'date_production' => $data['dateProduction'],
                'numero_commande' => $data['numeroCommande'],
                'numero_lot'      => $data['numeroLot'],
                'quantites'       => $data['quantites'],
            ];
        }
        return $etiquettes;
    }

    /**
     * Générer le PDF pour une commande
     */
    private function genererPDF($commandeId, $download = false) {
        require_once 'lib/SartoriusPdfGenerator.php';

        $query = "SELECT c.*, r.reference, r.designation
                  FROM commandes c
                  LEFT JOIN `references` r ON c.reference_id = r.id
                  WHERE c.id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $commandeId);
        $stmt->execute();
        $commandeData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$commandeData) return false;

        $pdfGen = new SartoriusPdfGenerator();

        try {
            $filename = $pdfGen->genererEtiquettes($commandeData);

            if ($download) {
                $refClean = preg_replace('/[^a-zA-Z0-9_-]/', '_', $commandeData['reference'] ?? 'commande');
                $cmdClean = preg_replace('/[^a-zA-Z0-9_-]/', '_', $commandeData['numero_commande']);
                $lotClean = preg_replace('/[^a-zA-Z0-9_-]/', '_', $commandeData['numero_lot']);
                $downloadName = $refClean . '_' . $cmdClean . '_' . $lotClean . '.pdf';

                if (!file_exists($filename)) {
                    header("Location: " . BASE_URL . "/sartorius?error=pdf_not_found"); exit();
                }
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; filename="' . $downloadName . '"');
                header('Content-Length: ' . filesize($filename));
                readfile($filename);
                exit();
            }
            return $filename;
        } catch (Exception $e) {
            error_log("Erreur génération PDF Sartorius: " . $e->getMessage());
            if ($download) {
                header("Location: " . BASE_URL . "/sartorius?error=pdf_generation_failed"); exit();
            }
            return false;
        }
    }

    /**
     * Supprimer le fichier PDF d'une commande
     */
    private function supprimerPDF(array $commandeData) {
        $refClean = preg_replace('/[^a-zA-Z0-9_-]/', '_', $commandeData['reference'] ?? '');
        $cmdClean = preg_replace('/[^a-zA-Z0-9_-]/', '_', $commandeData['numero_commande']);
        $lotClean = preg_replace('/[^a-zA-Z0-9_-]/', '_', $commandeData['numero_lot']);
        $pdfFile  = 'pdfs_sartorius/' . $refClean . '_' . $cmdClean . '_' . $lotClean . '.pdf';
        if (file_exists($pdfFile)) unlink($pdfFile);
    }
}
