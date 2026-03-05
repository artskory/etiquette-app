<?php
/**
 * Contrôleur Latitude
 * Version 1.0.11 - Protection CSRF + Validation entrées
 */
class LatitudeController {
    private $db;
    private $commande;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->commande = new CommandeLatitude($this->db);
    }

    /**
     * Afficher la liste des commandes
     */
    public function liste() {
        $stmt = $this->commande->readAll();
        require_once 'views/latitude/liste_commande_latitude.php';
    }

    /**
     * Afficher le formulaire de nouvelle commande
     */
    public function nouvelle() {
        // Charger les articles
        require_once 'models/ArticleLatitude.php';
        $articleModel = new ArticleLatitude($this->db);
        $articles = $articleModel->readAll();
        
        require_once 'views/latitude/nouvelle_commande_latitude.php';
    }
    
    /**
     * Afficher le formulaire d'édition
     */
    public function edition() {
        $id = Validator::id($_GET['id'] ?? 0);
        if ($id === false) {
            header("Location: index.php?page=latitude&error=invalid_id");
            exit();
        }
        
        $this->commande->id = $id;
        $commandeData = $this->commande->readOne();
        
        if($commandeData) {
            // Charger les articles disponibles pour le dropdown
            require_once 'models/ArticleLatitude.php';
            $articleModel = new ArticleLatitude($this->db);
            $articles = $articleModel->readAll();
            
            require_once 'views/latitude/edition_commande_latitude.php';
        } else {
            header("Location: index.php?page=latitude&error=not_found");
            exit();
        }
    }

    /**
     * Créer une nouvelle commande
     */
    public function creer() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Validation CSRF
            $token = $_POST['csrf_token'] ?? null;
            if (!CsrfToken::validate($token)) {
                header("Location: index.php?error=csrf_invalid");
                exit;
            }

            // Valider le numéro de commande
            $numeroCommande = Validator::numeroCommande($_POST['numero_commande'] ?? '');
            if ($numeroCommande === false) {
                header("Location: index.php?page=nouvelle-commande-latitude&error=invalid_numero");
                exit;
            }
            
            // Valider les articles
            $articles = Validator::articlesLatitude($_POST['articles'] ?? []);
            if ($articles === false) {
                header("Location: index.php?page=nouvelle-commande-latitude&error=invalid_articles");
                exit;
            }
            
            $this->commande->numero_commande = $numeroCommande;
            $this->commande->articles = json_encode($articles);

            try {
                if($this->commande->create()) {
                    // Générer le PDF
                    $this->genererPDF($this->commande->id);
                    
                    header("Location: index.php?page=latitude&success=commande_created");
                    exit();
                } else {
                    header("Location: index.php?page=nouvelle-commande-latitude&error=create_failed");
                    exit();
                }
            } catch(PDOException $e) {
                error_log("Erreur création commande Latitude: " . $e->getMessage());
                header("Location: index.php?page=nouvelle-commande-latitude&error=create_failed");
                exit();
            }
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
                header("Location: index.php?page=latitude&error=invalid_id");
                exit;
            }
            
            // Valider le numéro de commande
            $numeroCommande = Validator::numeroCommande($_POST['numero_commande'] ?? '');
            if ($numeroCommande === false) {
                header("Location: index.php?page=editer-commande-latitude&id=$id&error=invalid_numero");
                exit;
            }
            
            // Valider les articles
            $articles = Validator::articlesLatitude($_POST['articles'] ?? []);
            if ($articles === false) {
                header("Location: index.php?page=editer-commande-latitude&id=$id&error=invalid_articles");
                exit;
            }
            
            $this->commande->id = $id;
            $this->commande->numero_commande = $numeroCommande;
            $this->commande->articles = json_encode($articles);

            try {
                if($this->commande->update()) {
                    // Régénérer le PDF
                    $this->genererPDF($this->commande->id);
                    
                    header("Location: index.php?page=latitude&success=commande_updated");
                    exit();
                } else {
                    header("Location: index.php?page=editer-commande-latitude&id=" . $this->commande->id . "&error=update_failed");
                    exit();
                }
            } catch(PDOException $e) {
                error_log("Erreur modification commande Latitude: " . $e->getMessage());
                header("Location: index.php?page=editer-commande-latitude&id=" . $this->commande->id . "&error=update_failed");
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
            header("Location: index.php?page=latitude&error=invalid_id");
            exit;
        }
        
        $this->commande->id = $id;

        try {
            // Récupérer les données avant suppression
            $commandeData = $this->commande->readOne();
            
            if($commandeData) {
                // Construire le nom du fichier PDF
                $cmdClean = preg_replace('/[^a-zA-Z0-9_-]/', '_', $commandeData['numero_commande']);
                $pdfFilename = 'pdfs_latitude/' . $cmdClean . '.pdf';
                
                // Supprimer le PDF s'il existe
                if(file_exists($pdfFilename)) {
                    unlink($pdfFilename);
                }
            }
            
            // Supprimer la commande
            if($this->commande->delete()) {
                header("Location: index.php?page=latitude&success=commande_deleted");
                exit();
            } else {
                header("Location: index.php?page=latitude&error=delete_failed");
                exit();
            }
        } catch(PDOException $e) {
            header("Location: index.php?page=latitude&error=delete_failed");
            exit();
        }
    }

    /**
     * Supprimer une sélection de commandes Latitude
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
            header("Location: index.php?page=latitude&error=no_selection");
            exit;
        }

        try {
            foreach($ids as $id) {
                $this->commande->id = $id;
                $commandeData = $this->commande->readOne();
                if($commandeData) {
                    $cmdClean = preg_replace('/[^a-zA-Z0-9_-]/', '_', $commandeData['numero_commande']);
                    $pdfFilename = 'pdfs_latitude/' . $cmdClean . '.pdf';
                    if(file_exists($pdfFilename)) unlink($pdfFilename);
                    $this->commande->delete();
                }
            }
            header("Location: index.php?page=latitude&success=selection_deleted");
            exit();
        } catch(Exception $e) {
            header("Location: index.php?page=latitude&error=delete_failed");
            exit();
        }
    }

    /**
     * Télécharger le PDF
     */
    public function telecharger() {
    // Valider l'ID depuis GET
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if ($id <= 0) {
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
     * Générer ou télécharger le PDF
     */
    private function genererPDF($id, $download = false) {
        $this->commande->id = $id;
        $commandeData = $this->commande->readOne();
        
        if(!$commandeData) {
            if($download) {
                header("Location: index.php?page=latitude&error=not_found");
                exit();
            }
            return false;
        }
        
        // Créer le générateur PDF
        require_once 'lib/LatitudePdfGenerator.php';
        $pdfGen = new LatitudePdfGenerator();
        
        // Générer le PDF
        try {
            $filename = $pdfGen->genererEtiquettes($commandeData);
            
            if($download) {
                $cmdClean = preg_replace('/[^a-zA-Z0-9_-]/', '_', $commandeData['numero_commande']);
                $downloadName = $cmdClean . '.pdf';
                
                // Vérifier que le fichier existe
                if(!file_exists($filename)) {
                    error_log("Fichier PDF Latitude introuvable: " . $filename);
                    header("Location: index.php?page=latitude&error=pdf_not_found");
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
            error_log("Erreur génération PDF Latitude: " . $e->getMessage());
            if($download) {
                header("Location: index.php?page=latitude&error=pdf_generation_failed");
                exit();
            }
            return false;
        }
    }
}
