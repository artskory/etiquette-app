<?php
/**
 * Contrôleur Commande
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
        require_once 'views/commandes/liste.php';
    }

    /**
     * Afficher la page de création
     */
    public function nouvelle() {
        $referenceController = new ReferenceController();
        $references = $referenceController->getAll();
        require_once 'views/commandes/nouvelle.php';
    }

    /**
     * Créer une nouvelle commande
     */
    public function creer() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->commande->numero_commande = $_POST['numero_commande'] ?? '';
            $this->commande->reference_id = $_POST['reference_id'] ?? '';
            $this->commande->quantite_par_carton = $_POST['quantite_par_carton'] ?? '';
            $this->commande->date_production = $_POST['date_production'] ?? '';
            $this->commande->numero_lot = $_POST['numero_lot'] ?? '';
            $this->commande->quantite_etiquettes = $_POST['quantite_etiquettes'] ?? '';

            try {
                if($this->commande->create()) {
                    // Générer le PDF
                    $this->genererPDF($this->commande->id);
                    
                    header("Location: index.php?page=sartorius&success=commande_created");
                    exit();
                } else {
                    header("Location: index.php?page=nouvelle-commande&error=create_failed");
                    exit();
                }
            } catch(PDOException $e) {
                header("Location: index.php?page=nouvelle-commande&error=create_failed");
                exit();
            }
        }
    }

    /**
     * Afficher la page d'édition
     */
    public function edition() {
        $id = $_GET['id'] ?? 0;
        $this->commande->id = $id;
        $commandeData = $this->commande->readOne();
        
        if($commandeData) {
            $referenceController = new ReferenceController();
            $references = $referenceController->getAll();
            require_once 'views/commandes/edition.php';
        } else {
            header("Location: index.php?page=sartorius");
            exit();
        }
    }

    /**
     * Mettre à jour une commande
     */
    public function modifier() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->commande->id = $_POST['id'] ?? '';
            $this->commande->numero_commande = $_POST['numero_commande'] ?? '';
            $this->commande->reference_id = $_POST['reference_id'] ?? '';
            $this->commande->quantite_par_carton = $_POST['quantite_par_carton'] ?? '';
            $this->commande->date_production = $_POST['date_production'] ?? '';
            $this->commande->numero_lot = $_POST['numero_lot'] ?? '';
            $this->commande->quantite_etiquettes = $_POST['quantite_etiquettes'] ?? '';

            try {
                if($this->commande->update()) {
                    header("Location: index.php?page=sartorius&success=commande_updated");
                    exit();
                } else {
                    header("Location: index.php?page=edition-commande&id=" . $this->commande->id . "&error=update_failed");
                    exit();
                }
            } catch(PDOException $e) {
                header("Location: index.php?page=edition-commande&id=" . $this->commande->id . "&error=update_failed");
                exit();
            }
        }
    }

    /**
     * Supprimer une commande
     */
    public function supprimer() {
        $id = $_POST['id'] ?? 0;
        $this->commande->id = $id;

        try {
            if($this->commande->delete()) {
                header("Location: index.php?page=sartorius&success=commande_deleted");
                exit();
            } else {
                header("Location: index.php?page=sartorius&error=delete");
                exit();
            }
        } catch(PDOException $e) {
            header("Location: index.php?page=sartorius&error=delete");
            exit();
        }
    }

    /**
     * Télécharger le PDF
     */
    public function telecharger() {
        $id = $_GET['id'] ?? 0;
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
        require_once 'lib/PdfGenerator.php';
        
        // Récupérer les données de la commande
        $this->commande->id = $commandeId;
        $commandeData = $this->commande->readOne();
        
        if(!$commandeData) {
            return false;
        }
        
        // Créer le générateur PDF
        $pdfGen = new PdfGenerator();
        
        // Générer le PDF
        try {
            $filename = $pdfGen->genererEtiquettes($commandeData, $commandeData['quantite_etiquettes']);
            
            if($download) {
                // Formater la date pour le nom de téléchargement
                $dateParts = explode('/', $commandeData['date_production']);
                $dateFormatted = $dateParts[0] . '_' . $dateParts[1];
                $downloadName = $commandeData['reference'] . '-' . $dateFormatted . '.pdf';
                
                // Forcer le téléchargement
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; filename="' . $downloadName . '"');
                header('Content-Length: ' . filesize($filename));
                readfile($filename);
                exit();
            }
            
            return $filename;
        } catch(Exception $e) {
            error_log("Erreur génération PDF: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Vider tous les fichiers PDF
     */
    public function viderPdf() {
        try {
            $pdfDir = 'pdfs/';
            $count = 0;
            
            // Vérifier si le dossier existe
            if(is_dir($pdfDir)) {
                // Parcourir tous les fichiers du dossier
                $files = glob($pdfDir . '*.pdf');
                
                foreach($files as $file) {
                    if(is_file($file)) {
                        unlink($file);
                        $count++;
                    }
                }
            }
            
            header("Location: index.php?page=sartorius&success=pdf_cleared&count=" . $count);
            exit();
        } catch(Exception $e) {
            header("Location: index.php?page=sartorius&error=pdf_clear_failed");
            exit();
        }
    }
    
    /**
     * Supprimer toutes les commandes
     */
    public function supprimerTout() {
        try {
            // Supprimer toutes les commandes de la base de données
            $query = "DELETE FROM commandes";
            $stmt = $this->db->prepare($query);
            
            if($stmt->execute()) {
                // Optionnel : aussi supprimer les PDF
                $pdfDir = 'pdfs/';
                if(is_dir($pdfDir)) {
                    $files = glob($pdfDir . '*.pdf');
                    foreach($files as $file) {
                        if(is_file($file)) {
                            unlink($file);
                        }
                    }
                }
                
                header("Location: index.php?page=sartorius&success=all_deleted");
                exit();
            } else {
                header("Location: index.php?page=sartorius&error=delete_all_failed");
                exit();
            }
        } catch(Exception $e) {
            header("Location: index.php?page=sartorius&error=delete_all_failed");
            exit();
        }
    }
}
