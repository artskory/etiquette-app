<?php
/**
 * Contrôleur Commande
 * Version 1.0.11 - Protection CSRF + Validation entrées
 */
class CommandeController {
    private $db;
    private $commande;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->commande = new Commande($this->db);
    }

    /**
     * Afficher la liste des commandes
     */
    public function liste() {
        $stmt = $this->commande->readAll();
        require_once 'views/sartorius/liste_commande_sartorius.php';
    }

    /**
     * Afficher la page de création
     */
    public function nouvelle() {
        $referenceController = new ReferenceController();
        $references = $referenceController->getAll();
        require_once 'views/sartorius/nouvelle_commande_sartorius.php';
    }

    /**
     * Créer une nouvelle commande avec plusieurs quantités
     */
    public function creer() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // Validation CSRF
            $token = $_POST['csrf_token'] ?? null;
            if (!CsrfToken::validate($token)) {
                header("Location: index.php?error=csrf_invalid");
                exit;
            }
            
            // Valider l'ID de référence
            $referenceId = Validator::id($_POST['reference_id'] ?? 0);
            if ($referenceId === false) {
                header("Location: index.php?page=nouvelle-commande&error=invalid_reference");
                exit;
            }
            
            // Valider le numéro de commande
            $numeroCommande = Validator::numeroCommande($_POST['numero_commande'] ?? '');
            if ($numeroCommande === false) {
                header("Location: index.php?page=nouvelle-commande&error=invalid_numero");
                exit;
            }
            
            // Valider le numéro de lot
            $numeroLot = Validator::numeroLot($_POST['numero_lot'] ?? '');
            if ($numeroLot === false) {
                header("Location: index.php?page=nouvelle-commande&error=invalid_lot");
                exit;
            }
            
            // Valider la date
            $dateProduction = Validator::dateMoisAnnee($_POST['date_production'] ?? '');
            if ($dateProduction === false) {
                header("Location: index.php?page=nouvelle-commande&error=invalid_date");
                exit;
            }
            
            // Valider les quantités
            $quantites = Validator::quantitesSartorius($_POST['quantites'] ?? []);
            if ($quantites === false) {
                header("Location: index.php?page=nouvelle-commande&error=invalid_quantities");
                exit;
            }
            
            try {
                // Créer UNE SEULE commande avec toutes les quantités
                $this->commande->reference_id = $referenceId;
                $this->commande->date_production = $dateProduction;
                $this->commande->numero_commande = $numeroCommande;
                $this->commande->numero_lot = $numeroLot;
                $this->commande->quantites = json_encode($quantites);

                if($this->commande->create()) {
                    // Générer le PDF pour cette commande
                    $this->genererPDF($this->commande->id);
                    
                    header("Location: index.php?page=sartorius&success=commande_created");
                    exit();
                } else {
                    header("Location: index.php?page=nouvelle-commande&error=create_failed");
                    exit();
                }
            } catch(PDOException $e) {
                error_log("Erreur création commande: " . $e->getMessage());
                
                // Vérifier si l'erreur est due à la colonne manquante
                if(strpos($e->getMessage(), 'quantites') !== false || strpos($e->getMessage(), 'Unknown column') !== false) {
                    header("Location: index.php?page=nouvelle-commande&error=migration_required");
                } else {
                    header("Location: index.php?page=nouvelle-commande&error=create_failed");
                }
                exit();
            } catch(Exception $e) {
                error_log("Erreur inattendue: " . $e->getMessage());
                header("Location: index.php?page=nouvelle-commande&error=create_failed");
                exit();
            }
        }
    }

    /**
     * Afficher la page d'édition
     */
    public function edition() {
        $id = Validator::id($_GET['id'] ?? 0);
        if ($id === false) {
            header("Location: index.php?page=sartorius&error=invalid_id");
            exit();
        }
        
        $this->commande->id = $id;
        $commandeData = $this->commande->readOne();
        
        if($commandeData) {
            $referenceController = new ReferenceController();
            $references = $referenceController->getAll();
            require_once 'views/sartorius/edition_commande_sartorius.php';
        } else {
            header("Location: index.php?page=sartorius&error=not_found");
            exit();
        }
    }

    /**
     * Mettre à jour une commande
     */
    public function modifier() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // Validation CSRF
            $token = $_POST['csrf_token'] ?? null;
            if (!CsrfToken::validate($token)) {
                header("Location: index.php?error=csrf_invalid");
                exit;
            }
            
            // Valider l'ID
            $id = Validator::id($_POST['id'] ?? 0);
            if ($id === false) {
                header("Location: index.php?page=sartorius&error=invalid_id");
                exit;
            }
            
            // Valider l'ID de référence
            $referenceId = Validator::id($_POST['reference_id'] ?? 0);
            if ($referenceId === false) {
                header("Location: index.php?page=editer-commande&id=$id&error=invalid_reference");
                exit;
            }
            
            // Valider le numéro de commande
            $numeroCommande = Validator::numeroCommande($_POST['numero_commande'] ?? '');
            if ($numeroCommande === false) {
                header("Location: index.php?page=editer-commande&id=$id&error=invalid_numero");
                exit;
            }
            
            // Valider le numéro de lot
            $numeroLot = Validator::numeroLot($_POST['numero_lot'] ?? '');
            if ($numeroLot === false) {
                header("Location: index.php?page=editer-commande&id=$id&error=invalid_lot");
                exit;
            }
            
            // Valider la date
            $dateProduction = Validator::date($_POST['date_production'] ?? '');
            if ($dateProduction === false) {
                header("Location: index.php?page=editer-commande&id=$id&error=invalid_date");
                exit;
            }
            
            // Valider les quantités
            $quantites = Validator::quantitesSartorius($_POST['quantites'] ?? []);
            if ($quantites === false) {
                header("Location: index.php?page=editer-commande&id=$id&error=invalid_quantities");
                exit;
            }
            
            $this->commande->id = $id;
            $this->commande->numero_commande = $numeroCommande;
            $this->commande->reference_id = $referenceId;
            $this->commande->date_production = $dateProduction;
            $this->commande->numero_lot = $numeroLot;
            $this->commande->quantites = json_encode($quantites);

            try {
                if($this->commande->update()) {
                    // Régénérer le PDF
                    $this->genererPDF($this->commande->id);
                    
                    header("Location: index.php?page=sartorius&success=commande_updated");
                    exit();
                } else {
                    header("Location: index.php?page=editer-commande&id=" . $this->commande->id . "&error=update_failed");
                    exit();
                }
            } catch(PDOException $e) {
                error_log("Erreur modification commande: " . $e->getMessage());
                header("Location: index.php?page=editer-commande&id=" . $this->commande->id . "&error=update_failed");
                exit();
            }
        }
    }

    /**
     * Supprimer une commande
     */
    public function supprimer() {
        
        // Validation CSRF
        $token = $_POST['csrf_token'] ?? null;
        if (!CsrfToken::validate($token)) {
            header("Location: index.php?error=csrf_invalid");
            exit;
        }
        
        // Valider l'ID
        $id = Validator::id($_POST['id'] ?? 0);
        if ($id === false) {
            header("Location: index.php?page=sartorius&error=invalid_id");
            exit;
        }
        
        $this->commande->id = $id;

        try {
            // Récupérer les données de la commande avant suppression pour obtenir le nom du fichier
            $commandeData = $this->commande->readOne();
            
            if($commandeData) {
                // Construire le nom du fichier PDF
                $dateParts = explode('/', $commandeData['date_production']);
                $dateFormatted = $dateParts[0] . '_' . $dateParts[1];
                $refClean = preg_replace('/[^a-zA-Z0-9_-]/', '_', $commandeData['reference']);
                $pdfFilename = 'pdfs_sartorius/' . $refClean . '-' . $dateFormatted . '.pdf';
                
                // Supprimer le PDF s'il existe
                if(file_exists($pdfFilename)) {
                    unlink($pdfFilename);
                }
            }
            
            // Supprimer la commande de la base de données
            if($this->commande->delete()) {
                header("Location: index.php?page=sartorius&success=commande_deleted");
                exit();
            } else {
                header("Location: index.php?page=sartorius&error=delete_failed");
                exit();
            }
        } catch(PDOException $e) {
            header("Location: index.php?page=sartorius&error=delete_failed");
            exit();
        }
    }

    /**
     * Télécharger le PDF
     */
    public function telecharger() {
        $id = Validator::id($_GET['id'] ?? 0);
        if ($id === false) {
            header("Location: index.php?page=sartorius&error=invalid_id");
            exit();
        }
        
        $this->commande->id = $id;
        $commandeData = $this->commande->readOne();
        
        if($commandeData) {
            $this->genererPDF($id, true);
        } else {
            header("Location: index.php?page=sartorius&error=not_found");
            exit();
        }
    }
    
    /**
     * Générer le PDF pour une commande
     */
    private function genererPDF($commandeId, $download = false) {
        require_once 'lib/SartoriusPdfGenerator.php';
        
        // Récupérer les données complètes de la commande avec la référence
        $query = "SELECT c.*, r.reference, r.designation 
                  FROM commandes c 
                  LEFT JOIN `references` r ON c.reference_id = r.id 
                  WHERE c.id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $commandeId);
        $stmt->execute();
        $commandeData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if(!$commandeData) {
            return false;
        }
        
        // Créer le générateur PDF
        $pdfGen = new SartoriusPdfGenerator();
        
        // Générer le PDF
        try {
            $filename = $pdfGen->genererEtiquettes($commandeData);
            
            if($download) {
                // Formater la date pour le nom de téléchargement
                $dateParts = explode('/', $commandeData['date_production']);
                $dateFormatted = $dateParts[0] . '_' . $dateParts[1];
                $refClean = preg_replace('/[^a-zA-Z0-9_-]/', '_', $commandeData['reference']);
                $downloadName = $refClean . '-' . $dateFormatted . '.pdf';
                
                // Vérifier que le fichier existe
                if(!file_exists($filename)) {
                    error_log("Fichier PDF introuvable: " . $filename);
                    header("Location: index.php?page=sartorius&error=pdf_not_found");
                    exit();
                }
                
                // Forcer le téléchargement
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; filename="' . $downloadName . '"');
                header('Content-Length: ' . filesize($filename));
                readfile($filename);
                exit();
            }
            
            return $filename;
        } catch(Exception $e) {
            error_log("Erreur génération PDF Sartorius: " . $e->getMessage());
            if($download) {
                header("Location: index.php?page=sartorius&error=pdf_generation_failed");
                exit();
            }
            return false;
        }
    }

    /**
     * Supprimer une sélection de commandes
     */
    public function supprimerSelection() {
        
        // Validation CSRF
        $token = $_POST['csrf_token'] ?? null;
        if (!CsrfToken::validate($token)) {
            header("Location: index.php?error=csrf_invalid");
            exit;
        }
        
        // Valider le tableau d'IDs
        $ids = Validator::arrayOfIds($_POST['ids'] ?? []);
        
        if ($ids === false) {
            header("Location: index.php?page=sartorius&error=no_selection");
            exit;
        }

        try {
            foreach($ids as $id) {
                $this->commande->id = $id;
                $commandeData = $this->commande->readOne();
                if($commandeData) {
                    $dateParts = explode('/', $commandeData['date_production']);
                    $dateFormatted = $dateParts[0] . '_' . $dateParts[1];
                    $refClean = preg_replace('/[^a-zA-Z0-9_-]/', '_', $commandeData['reference']);
                    $pdfFilename = 'pdfs_sartorius/' . $refClean . '-' . $dateFormatted . '.pdf';
                    if(file_exists($pdfFilename)) unlink($pdfFilename);
                    $this->commande->delete();
                }
            }
            header("Location: index.php?page=sartorius&success=selection_deleted");
            exit();
        } catch(Exception $e) {
            header("Location: index.php?page=sartorius&error=delete_failed");
            exit();
        }
    }
}
