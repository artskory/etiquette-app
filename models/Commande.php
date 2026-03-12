<?php
/**
 * Modèle Commande
 * Version 1.0.11 - Avec pagination
 */
class Commande {
    private $conn;
    private $table_name = "commandes";

    public $id;
    public $numero_commande;
    public $reference_id;
    public $quantites; // JSON: [{quantite_par_carton, quantite_etiquettes}]
    public $date_production;
    public $numero_lot;
    
    // Propriétés obsolètes (conservées pour compatibilité)
    public $quantite_par_carton;
    public $quantite_etiquettes;
    
    public $created_at;
    public $updated_at;

    /**
     * Constructeur
     */
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Créer une nouvelle commande
     */
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET numero_commande=:numero_commande, 
                      reference_id=:reference_id,
                      quantites=:quantites,
                      date_production=:date_production,
                      numero_lot=:numero_lot";

        $stmt = $this->conn->prepare($query);

        $this->numero_commande = strip_tags($this->numero_commande);
        $this->reference_id = strip_tags($this->reference_id);
        // Ne pas échapper le JSON
        $this->date_production = strip_tags($this->date_production);
        $this->numero_lot = strip_tags($this->numero_lot);

        $stmt->bindParam(":numero_commande", $this->numero_commande);
        $stmt->bindParam(":reference_id", $this->reference_id);
        $stmt->bindParam(":quantites", $this->quantites);
        $stmt->bindParam(":date_production", $this->date_production);
        $stmt->bindParam(":numero_lot", $this->numero_lot);

        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    /**
     * Lire toutes les commandes AVEC PAGINATION
     * 
     * @param int $page Numéro de page (commence à 1)
     * @param int $perPage Nombre d'éléments par page
     * @return PDOStatement
     */
    public function readAll($page = 1, $perPage = 50) {
        // Calculer l'offset
        $offset = ($page - 1) * $perPage;
        
        $query = "SELECT c.*, r.reference, r.designation 
                  FROM " . $this->table_name . " c
                  LEFT JOIN `references` r ON c.reference_id = r.id
                  ORDER BY c.id DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        
        // Bind avec PDO::PARAM_INT pour LIMIT/OFFSET
        $stmt->bindParam(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();

        return $stmt;
    }
    
    /**
     * Compter le nombre total de commandes
     * 
     * @return int Nombre total de commandes
     */
    public function count() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$row['total'];
    }

    /**
     * Lire une commande spécifique
     */
    public function readOne() {
        $query = "SELECT c.*, r.reference, r.designation 
                  FROM " . $this->table_name . " c
                  LEFT JOIN `references` r ON c.reference_id = r.id
                  WHERE c.id = ?
                  LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $this->numero_commande = $row['numero_commande'];
            $this->reference_id = $row['reference_id'];
            $this->quantites = $row['quantites'];
            $this->date_production = $row['date_production'];
            $this->numero_lot = $row['numero_lot'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            
            return $row;
        }

        return false;
    }

    /**
     * Mettre à jour une commande
     */
    public function update() {
        $query = "UPDATE " . $this->table_name . "
                  SET numero_commande = :numero_commande,
                      reference_id = :reference_id,
                      quantites = :quantites,
                      date_production = :date_production,
                      numero_lot = :numero_lot
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $this->numero_commande = strip_tags($this->numero_commande);
        $this->reference_id = strip_tags($this->reference_id);
        $this->date_production = strip_tags($this->date_production);
        $this->numero_lot = strip_tags($this->numero_lot);
        $this->id = strip_tags($this->id);

        $stmt->bindParam(':numero_commande', $this->numero_commande);
        $stmt->bindParam(':reference_id', $this->reference_id);
        $stmt->bindParam(':quantites', $this->quantites);
        $stmt->bindParam(':date_production', $this->date_production);
        $stmt->bindParam(':numero_lot', $this->numero_lot);
        $stmt->bindParam(':id', $this->id);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    /**
     * Supprimer une commande
     */
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";

        $stmt = $this->conn->prepare($query);
        $this->id = strip_tags($this->id);
        $stmt->bindParam(1, $this->id);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }
}
