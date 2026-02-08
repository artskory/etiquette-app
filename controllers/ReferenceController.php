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
                if($this->reference->create()) {
                    header("Location: index.php?page=sartorius&success=reference_created");
                    exit();
                } else {
                    header("Location: index.php?page=ajout-reference&error=create_failed");
                    exit();
                }
            } catch(PDOException $e) {
                // Vérifier si c'est une erreur de doublon
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
}
