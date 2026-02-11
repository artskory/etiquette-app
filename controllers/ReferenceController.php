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
        require_once 'views/references/ajout.php';
    }

    /**
     * Créer une nouvelle référence
     */
    public function creer() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->reference->reference = $_POST['reference'] ?? '';
            $this->reference->designation = $_POST['designation'] ?? '';

            try {
                // Vérifier si la combinaison référence + désignation existe déjà
                if($this->reference->exists()) {
                    header("Location: index.php?page=ajout-reference&error=duplicate_combination");
                    exit();
                }
                
                if($this->reference->create()) {
                    header("Location: index.php?page=sartorius&success=reference_created");
                    exit();
                } else {
                    header("Location: index.php?page=ajout-reference&error=create_failed");
                    exit();
                }
            } catch(PDOException $e) {
                // Vérifier si c'est une erreur de doublon de référence uniquement
                if($e->getCode() == 23000) {
                    header("Location: index.php?page=ajout-reference&error=duplicate_reference");
                    exit();
                } else {
                    header("Location: index.php?page=ajout-reference&error=create_failed");
                    exit();
                }
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
            require_once 'views/references/edition.php';
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
            $this->reference->id = $_POST['id'] ?? '';
            $this->reference->reference = $_POST['reference'] ?? '';
            $this->reference->designation = $_POST['designation'] ?? '';

            try {
                if($this->reference->update()) {
                    header("Location: index.php?page=ajout-reference&success=reference_updated");
                    exit();
                } else {
                    header("Location: index.php?page=editer-reference&id=" . $this->reference->id . "&error=update_failed");
                    exit();
                }
            } catch(PDOException $e) {
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
}
