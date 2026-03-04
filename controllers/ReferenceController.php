<?php
/**
 * Contrôleur Reference
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

            // Validation CSRF
            $token = $_POST['csrf_token'] ?? null;
            if (!CsrfToken::validate($token)) {
                header("Location: index.php?error=csrf_invalid");
                exit;
            }

            // Démarrer la session si pas déjà fait
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            $this->reference->reference = $_POST['reference'] ?? '';
            $this->reference->designation = $_POST['designation'] ?? '';

            try {
                // Vérifier si la référence existe déjà
                $refExists = $this->reference->referenceExists();
                
                // Vérifier si la désignation existe déjà
                $desExists = $this->reference->designationExists();
                
                // Cas 1 : Les DEUX existent déjà
                if($refExists && $desExists) {
                    $_SESSION['form_data'] = $_POST;
                    header("Location: index.php?page=ajout-reference&error=duplicate_both");
                    exit();
                }
                
                // Cas 2 : Seulement la référence existe
                if($refExists) {
                    $_SESSION['form_data'] = $_POST;
                    header("Location: index.php?page=ajout-reference&error=duplicate_reference");
                    exit();
                }
                
                // Cas 3 : Seulement la désignation existe
                if($desExists) {
                    $_SESSION['form_data'] = $_POST;
                    header("Location: index.php?page=ajout-reference&error=duplicate_designation");
                    exit();
                }
                
                // Cas 4 : Tout est OK, créer la référence
                if($this->reference->create()) {
                    // Ne PAS stocker en session pour vider les champs
                    unset($_SESSION['form_data']);
                    header("Location: index.php?page=ajout-reference&success=reference_created");
                    exit();
                } else {
                    $_SESSION['form_data'] = $_POST;
                    header("Location: index.php?page=ajout-reference&error=create_failed");
                    exit();
                }
            } catch(PDOException $e) {
                error_log("Erreur création référence: " . $e->getMessage());
                $_SESSION['form_data'] = $_POST;
                header("Location: index.php?page=ajout-reference&error=create_failed");
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
        $id = $_GET['id'] ?? 0;
        $this->reference->id = $id;
        $referenceData = $this->reference->readOne();
        
        if($referenceData) {
            require_once 'views/sartorius/articles_sartorius/edition_article_sartorius.php';
        } else {
            header("Location: index.php?page=ajout-reference&error=not_found");
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
                header("Location: index.php?error=csrf_invalid");
                exit;
            }
            $this->reference->id = $_POST['id'] ?? '';
            $this->reference->reference = $_POST['reference'] ?? '';
            $this->reference->designation = $_POST['designation'] ?? '';

            try {
                // Vérifier si la référence existe déjà (en excluant l'ID actuel)
                if($this->reference->referenceExists($this->reference->id)) {
                    header("Location: index.php?page=editer-reference&id=" . $this->reference->id . "&error=duplicate_reference");
                    exit();
                }
                
                // Vérifier si la désignation existe déjà (en excluant l'ID actuel)
                if($this->reference->designationExists($this->reference->id)) {
                    header("Location: index.php?page=editer-reference&id=" . $this->reference->id . "&error=duplicate_designation");
                    exit();
                }
                
                if($this->reference->update()) {
                    header("Location: index.php?page=ajout-reference&success=reference_updated");
                    exit();
                } else {
                    header("Location: index.php?page=editer-reference&id=" . $this->reference->id . "&error=update_failed");
                    exit();
                }
            } catch(PDOException $e) {
                error_log("Erreur modification référence: " . $e->getMessage());
                header("Location: index.php?page=editer-reference&id=" . $this->reference->id . "&error=update_failed");
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
                header("Location: index.php?error=csrf_invalid");
                exit;
            }

            $id = $_POST['id'] ?? 0;
            $this->reference->id = $id;

            try {
                if($this->reference->delete()) {
                    header("Location: index.php?page=ajout-reference&success=reference_deleted");
                    exit();
                } else {
                    header("Location: index.php?page=ajout-reference&error=delete_failed");
                    exit();
                }
            } catch(PDOException $e) {
                header("Location: index.php?page=ajout-reference&error=delete_failed");
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
            header("Location: index.php?error=csrf_invalid");
            exit;
        }
        
        $ids = $_POST['ids'] ?? [];
        if(empty($ids)) {
            header("Location: index.php?page=ajout-reference&error=no_selection");
            exit();
        }

        try {
            foreach($ids as $id) {
                $this->reference->id = intval($id);
                $this->reference->delete();
            }
            header("Location: index.php?page=ajout-reference&success=selection_deleted");
            exit();
        } catch(Exception $e) {
            header("Location: index.php?page=ajout-reference&error=delete_failed");
            exit();
        }
    }
}
