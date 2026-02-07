<?php
/**
 * Modèle Commande
 */
class Commande {
    private $conn;
    private $table_name = "commandes";

    public $id;
    public $numero_commande;
    public $reference_id;
    public $quantite_par_carton;
    public $date_production;
    public $numero_lot;
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
                      quantite_par_carton=:quantite_par_carton,
                      date_production=:date_production,
                      numero_lot=:numero_lot,
                      quantite_etiquettes=:quantite_etiquettes";

        $stmt = $this->conn->prepare($query);

        $this->numero_commande = htmlspecialchars(strip_tags($this->numero_commande));
        $this->reference_id = htmlspecialchars(strip_tags($this->reference_id));
        $this->quantite_par_carton = htmlspecialchars(strip_tags($this->quantite_par_carton));
        $this->date_production = htmlspecialchars(strip_tags($this->date_production));
        $this->numero_lot = htmlspecialchars(strip_tags($this->numero_lot));
        $this->quantite_etiquettes = htmlspecialchars(strip_tags($this->quantite_etiquettes));

        $stmt->bindParam(":numero_commande", $this->numero_commande);
        $stmt->bindParam(":reference_id", $this->reference_id);
        $stmt->bindParam(":quantite_par_carton", $this->quantite_par_carton);
        $stmt->bindParam(":date_production", $this->date_production);
        $stmt->bindParam(":numero_lot", $this->numero_lot);
        $stmt->bindParam(":quantite_etiquettes", $this->quantite_etiquettes);

        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    /**
     * Lire toutes les commandes avec leurs références
     */
    public function readAll() {
        $query = "SELECT c.*, r.reference, r.designation 
                  FROM " . $this->table_name . " c
                  LEFT JOIN `references` r ON c.reference_id = r.id
                  ORDER BY c.id DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    /**
     * Lire une commande par ID
     */
    public function readOne() {
        $query = "SELECT c.*, r.reference, r.designation 
                  FROM " . $this->table_name . " c
                  LEFT JOIN `references` r ON c.reference_id = r.id
                  WHERE c.id = ? LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $this->numero_commande = $row['numero_commande'];
            $this->reference_id = $row['reference_id'];
            $this->quantite_par_carton = $row['quantite_par_carton'];
            $this->date_production = $row['date_production'];
            $this->numero_lot = $row['numero_lot'];
            $this->quantite_etiquettes = $row['quantite_etiquettes'];
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
                  SET numero_commande=:numero_commande,
                      reference_id=:reference_id,
                      quantite_par_carton=:quantite_par_carton,
                      date_production=:date_production,
                      numero_lot=:numero_lot,
                      quantite_etiquettes=:quantite_etiquettes
                  WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $this->numero_commande = htmlspecialchars(strip_tags($this->numero_commande));
        $this->reference_id = htmlspecialchars(strip_tags($this->reference_id));
        $this->quantite_par_carton = htmlspecialchars(strip_tags($this->quantite_par_carton));
        $this->date_production = htmlspecialchars(strip_tags($this->date_production));
        $this->numero_lot = htmlspecialchars(strip_tags($this->numero_lot));
        $this->quantite_etiquettes = htmlspecialchars(strip_tags($this->quantite_etiquettes));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":numero_commande", $this->numero_commande);
        $stmt->bindParam(":reference_id", $this->reference_id);
        $stmt->bindParam(":quantite_par_carton", $this->quantite_par_carton);
        $stmt->bindParam(":date_production", $this->date_production);
        $stmt->bindParam(":numero_lot", $this->numero_lot);
        $stmt->bindParam(":quantite_etiquettes", $this->quantite_etiquettes);
        $stmt->bindParam(":id", $this->id);

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
        $stmt->bindParam(1, $this->id);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }
}
