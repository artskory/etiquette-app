<?php
/**
 * Contrôleur Latitude
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
        require_once 'views/latitude/liste.php';
    }

    /**
     * Afficher le formulaire de nouvelle commande
     */
    public function nouvelle() {
        require_once 'views/latitude/nouvelle.php';
    }
    
    /**
     * Afficher le formulaire d'édition
     */
    public function edition() {
        $id = $_GET['id'] ?? 0;
        $this->commande->id = $id;
        $commandeData = $this->commande->readOne();
        
        if($commandeData) {
            require_once 'views/latitude/edition.php';
        } else {
            header("Location: index.php?page=latitude");
            exit();
        }
    }

    /**
     * Créer une nouvelle commande
     */
    public function creer() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->commande->numero_commande = $_POST['numero_commande'] ?? '';
            
            // Debug: Afficher les données POST reçues
            error_log("LatitudeController::creer - POST data: " . print_r($_POST, true));
            
            // Récupérer et encoder les articles en JSON
            $articles = [];
            if(isset($_POST['articles']) && is_array($_POST['articles'])) {
                foreach($_POST['articles'] as $article) {
                    if(!empty($article['type']) && !empty($article['quantite']) && !empty($article['nombre_cartons'])) {
                        $articles[] = [
                            'type' => $article['type'],
                            'quantite' => intval($article['quantite']),
                            'nombre_cartons' => intval($article['nombre_cartons'])
                        ];
                    }
                }
            }
            
            $this->commande->articles = json_encode($articles);
            
            // Debug: Afficher le JSON généré
            error_log("LatitudeController::creer - Articles JSON: " . $this->commande->articles);
            error_log("LatitudeController::creer - Nombre d'articles: " . count($articles));

            try {
                if($this->commande->create()) {
                    // Générer le PDF
                    $this->genererPDF($this->commande->id);
                    
                    header("Location: index.php?page=latitude&success=commande_created");
                    exit();
                } else {
                    header("Location: index.php?page=latitude-nouvelle&error=create_failed");
                    exit();
                }
            } catch(PDOException $e) {
                error_log("Erreur création commande Latitude: " . $e->getMessage());
                header("Location: index.php?page=latitude-nouvelle&error=create_failed");
                exit();
            }
        }
    }
    
    /**
     * Mettre à jour une commande
     */
    public function modifier() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->commande->id = $_POST['id'] ?? '';
            $this->commande->numero_commande = $_POST['numero_commande'] ?? '';
            
            // Récupérer et encoder les articles en JSON
            $articles = [];
            if(isset($_POST['articles']) && is_array($_POST['articles'])) {
                foreach($_POST['articles'] as $article) {
                    if(!empty($article['type']) && !empty($article['quantite']) && !empty($article['nombre_cartons'])) {
                        $articles[] = [
                            'type' => $article['type'],
                            'quantite' => intval($article['quantite']),
                            'nombre_cartons' => intval($article['nombre_cartons'])
                        ];
                    }
                }
            }
            
            $this->commande->articles = json_encode($articles);

            try {
                if($this->commande->update()) {
                    // Régénérer le PDF
                    $this->genererPDF($this->commande->id);
                    
                    header("Location: index.php?page=latitude&success=commande_updated");
                    exit();
                } else {
                    header("Location: index.php?page=latitude-edition&id=" . $this->commande->id . "&error=update_failed");
                    exit();
                }
            } catch(PDOException $e) {
                error_log("Erreur modification commande Latitude: " . $e->getMessage());
                header("Location: index.php?page=latitude-edition&id=" . $this->commande->id . "&error=update_failed");
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
                header("Location: index.php?page=latitude&error=delete");
                exit();
            }
        } catch(PDOException $e) {
            header("Location: index.php?page=latitude&error=delete");
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
            header("Location: index.php?page=latitude&error=not_found");
            exit();
        }
    }

    /**
     * Supprimer toutes les commandes
     */
    public function supprimerTout() {
        try {
            // Supprimer toutes les commandes
            $query = "DELETE FROM commandes_latitude";
            $stmt = $this->db->prepare($query);
            
            if($stmt->execute()) {
                // Supprimer tous les PDF
                $pdfDir = 'pdfs_latitude/';
                if(is_dir($pdfDir)) {
                    $files = glob($pdfDir . '*.pdf');
                    foreach($files as $file) {
                        if(is_file($file)) {
                            unlink($file);
                        }
                    }
                }
                
                header("Location: index.php?page=latitude&success=all_deleted");
                exit();
            } else {
                header("Location: index.php?page=latitude&error=delete_all_failed");
                exit();
            }
        } catch(Exception $e) {
            header("Location: index.php?page=latitude&error=delete_all_failed");
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
