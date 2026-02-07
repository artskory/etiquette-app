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

            if($this->reference->create()) {
                header("Location: index.php?page=sartorius");
                exit();
            } else {
                $error = "Erreur lors de la création de la référence.";
                require_once 'views/references/ajout.php';
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
