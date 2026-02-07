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
                    // TODO: Générer le PDF ici plus tard
                    header("Location: index.php?page=sartorius&success=commande_created");
                    exit();
                } else {
                    $error = "Erreur lors de la création de la commande.";
                    $referenceController = new ReferenceController();
                    $references = $referenceController->getAll();
                    require_once 'views/commandes/nouvelle.php';
                }
            } catch(PDOException $e) {
                $error = "Erreur lors de la création de la commande : " . $e->getMessage();
                $referenceController = new ReferenceController();
                $references = $referenceController->getAll();
                require_once 'views/commandes/nouvelle.php';
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
                    $error = "Erreur lors de la modification de la commande.";
                    $commandeData = $this->commande->readOne();
                    $referenceController = new ReferenceController();
                    $references = $referenceController->getAll();
                    require_once 'views/commandes/edition.php';
                }
            } catch(PDOException $e) {
                $error = "Erreur lors de la modification de la commande : " . $e->getMessage();
                $commandeData = $this->commande->readOne();
                $referenceController = new ReferenceController();
                $references = $referenceController->getAll();
                require_once 'views/commandes/edition.php';
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
        // TODO: Implémenter la génération et le téléchargement du PDF
        echo "Téléchargement du PDF pour la commande " . $id;
    }
}
