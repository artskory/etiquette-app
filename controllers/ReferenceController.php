<?php
/**
 * Contrôleur Reference
 * Version 1.0.11 - Protection CSRF + Validation entrées
 */
class ReferenceController {
    private $db;
    private $reference;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->reference = new Reference($this->db);
    }

    /**
     * Afficher la page d'ajout de référence
     */
    public function ajout() {
        require_once 'views/sartorius/articles_sartorius/nouveau_article_sartorius.php';
    }

    /**
     * Créer une nouvelle référence
     */
    public function creer() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Démarrer la session si pas déjà fait
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // Récupérer la provenance pour redirection
            $from   = $_POST['from'] ?? '';
            $suffix = (!empty($from)) ? '?from=' . urlencode($from) : '';
            $ajoutUrl = BASE_URL . '/sartorius/reference/ajout' . $suffix;
            
            // Validation CSRF
            $token = $_POST['csrf_token'] ?? null;
            if (!CsrfToken::validate($token)) {
                header("Location: " . BASE_URL . "/sartorius?error=csrf_invalid");
                exit;
            }
            
            // Validation des données
            $reference = Validator::reference($_POST['reference'] ?? '');
            $designation = Validator::text($_POST['designation'] ?? '');
            
            // Vérifier que les données sont valides
            if ($reference === false || Validator::isEmpty($_POST['reference'] ?? '')) {
                $_SESSION['form_data'] = $_POST;
                header("Location: " . $ajoutUrl . "&error=invalid_reference");
                exit;
            }
            
            if ($designation === false || Validator::isEmpty($_POST['designation'] ?? '')) {
                $_SESSION['form_data'] = $_POST;
                header("Location: " . $ajoutUrl . "&error=invalid_designation");
                exit;
            }
            
            // Assigner les données validées
            $this->reference->reference = $reference;
            $this->reference->designation = $designation;

            try {
                // Vérifier si la référence existe déjà
                $refExists = $this->reference->referenceExists();
                
                // Vérifier si la désignation existe déjà
                $desExists = $this->reference->designationExists();
                
                // Cas 1 : Les DEUX existent déjà
                if($refExists && $desExists) {
                    $_SESSION['form_data'] = $_POST;
                    header("Location: " . $ajoutUrl . "&error=duplicate_both");
                    exit();
                }
                
                // Cas 2 : Seulement la référence existe
                if($refExists) {
                    $_SESSION['form_data'] = $_POST;
                    header("Location: " . $ajoutUrl . "&error=duplicate_reference");
                    exit();
                }
                
                // Cas 3 : Seulement la désignation existe
                if($desExists) {
                    $_SESSION['form_data'] = $_POST;
                    header("Location: " . $ajoutUrl . "&error=duplicate_designation");
                    exit();
                }
                
                // Cas 4 : Tout est OK, créer la référence
                if($this->reference->create()) {
                    // Ne PAS stocker en session pour vider les champs
                    unset($_SESSION['form_data']);
                    unset($_SESSION['ref_from']);
                    header("Location: " . $ajoutUrl . "&success=reference_created");
                    exit();
                } else {
                    $_SESSION['form_data'] = $_POST;
                    header("Location: " . $ajoutUrl . "&error=create_failed");
                    exit();
                }
            } catch(PDOException $e) {
                error_log("Erreur création référence: " . $e->getMessage());
                $_SESSION['form_data'] = $_POST;
                header("Location: " . $ajoutUrl . "&error=create_failed");
                exit();
            }
        }
    }

    /**
     * Obtenir toutes les références
     */
    public function getAll() {
        return $this->reference->readAll();
    }
    
    /**
     * Afficher la page d'édition
     */
    public function edition() {
        $id = Validator::id($_GET['id'] ?? 0);
        if ($id === false) {
            header("Location: " . BASE_URL . "/sartorius/reference/ajout?error=invalid_id");
            exit();
        }
        
        $this->reference->id = $id;
        $referenceData = $this->reference->readOne();
        
        if($referenceData) {
            require_once 'views/sartorius/articles_sartorius/edition_article_sartorius.php';
        } else {
            header("Location: " . BASE_URL . "/sartorius/reference/ajout?error=not_found");
            exit();
        }
    }
    
    /**
     * Modifier une référence
     */
    public function modifier() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // Validation CSRF
            $token = $_POST['csrf_token'] ?? null;
            if (!CsrfToken::validate($token)) {
                header("Location: " . BASE_URL . "/sartorius?error=csrf_invalid");
                exit;
            }
            
            // Valider l'ID
            $id = Validator::id($_POST['id'] ?? 0);
            if ($id === false) {
                header("Location: " . BASE_URL . "/sartorius/reference/ajout?error=invalid_id");
                exit;
            }
            
            // Valider les données
            $reference = Validator::reference($_POST['reference'] ?? '');
            $designation = Validator::text($_POST['designation'] ?? '');
            
            if ($reference === false || $designation === false) {
                header("Location: " . BASE_URL . "/sartorius/reference/$id/editer?error=invalid_data");
                exit;
            }
            
            $this->reference->id = $id;
            $this->reference->reference = $reference;
            $this->reference->designation = $designation;

            try {
                // Vérifier si la référence existe déjà (en excluant l'ID actuel)
                if($this->reference->referenceExists($this->reference->id)) {
                    header("Location: " . BASE_URL . "/sartorius/reference/" . $this->reference->id . "/editer?error=duplicate_reference");
                    exit();
                }
                
                // Vérifier si la désignation existe déjà (en excluant l'ID actuel)
                if($this->reference->designationExists($this->reference->id)) {
                    header("Location: " . BASE_URL . "/sartorius/reference/" . $this->reference->id . "/editer?error=duplicate_designation");
                    exit();
                }
                
                if($this->reference->update()) {
                    header("Location: " . BASE_URL . "/sartorius/reference/ajout?success=reference_updated");
                    exit();
                } else {
                    header("Location: " . BASE_URL . "/sartorius/reference/" . $this->reference->id . "/editer?error=update_failed");
                    exit();
                }
            } catch(PDOException $e) {
                error_log("Erreur modification référence: " . $e->getMessage());
                header("Location: " . BASE_URL . "/sartorius/reference/" . $this->reference->id . "/editer?error=update_failed");
                exit();
            }
        }
    }
    
    /**
     * Supprimer une référence
     */
    public function supprimer() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // Validation CSRF
            $token = $_POST['csrf_token'] ?? null;
            if (!CsrfToken::validate($token)) {
                header("Location: " . BASE_URL . "/sartorius?error=csrf_invalid");
                exit;
            }
            
            // Valider l'ID
            $id = Validator::id($_POST['id'] ?? 0);
            if ($id === false) {
                header("Location: " . BASE_URL . "/sartorius/reference/ajout?error=invalid_id");
                exit;
            }
            
            $this->reference->id = $id;

            try {
                if($this->reference->delete()) {
                    header("Location: " . BASE_URL . "/sartorius/reference/ajout?success=reference_deleted");
                    exit();
                } else {
                    header("Location: " . BASE_URL . "/sartorius/reference/ajout?error=delete_failed");
                    exit();
                }
            } catch(PDOException $e) {
                header("Location: " . BASE_URL . "/sartorius/reference/ajout?error=delete_failed");
                exit();
            }
        }
    }

    /**
     * Supprimer une sélection de références
     */
    public function supprimerSelection() {
        
        // Validation CSRF
        $token = $_POST['csrf_token'] ?? null;
        if (!CsrfToken::validate($token)) {
            header("Location: " . BASE_URL . "/sartorius?error=csrf_invalid");
            exit;
        }
        
        // Valider le tableau d'IDs
        $ids = Validator::arrayOfIds($_POST['ids'] ?? []);
        
        if ($ids === false) {
            header("Location: " . BASE_URL . "/sartorius/reference/ajout?error=no_selection");
            exit;
        }

        try {
            foreach($ids as $id) {
                $this->reference->id = $id;
                $this->reference->delete();
            }
            header("Location: " . BASE_URL . "/sartorius/reference/ajout?success=selection_deleted");
            exit();
        } catch(Exception $e) {
            header("Location: " . BASE_URL . "/sartorius/reference/ajout?error=delete_failed");
            exit();
        }
    }
}
