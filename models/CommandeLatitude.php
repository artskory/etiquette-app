<?php
/**
 * Modèle CommandeLatitude
 * Version 1.0.11 - Avec pagination
 */
class CommandeLatitude {
    private $conn;
    private $table_name = "commandes_latitude";

    public $id;
    public $numero_commande;
    public $articles; // JSON string
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
                      articles=:articles";

        $stmt = $this->conn->prepare($query);

        $this->numero_commande = strip_tags($this->numero_commande);
        // Ne pas échapper le JSON - il est déjà valide
        // $this->articles est déjà encodé en JSON par le contrôleur

        $stmt->bindParam(":numero_commande", $this->numero_commande);
        $stmt->bindParam(":articles", $this->articles);

        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    /**
     * Lire toutes les commandes avec détails AVEC PAGINATION
     * 
     * @param int $page Numéro de page (commence à 1)
     * @param int $perPage Nombre d'éléments par page
     * @return PDOStatement
     */
    public function readAll($page = 1, $perPage = 50) {
        // Calculer l'offset
        $offset = ($page - 1) * $perPage;
        
        $query = "SELECT * FROM " . $this->table_name . " 
                  ORDER BY created_at DESC
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
     * Lire une commande par ID
     */
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE id = ? LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $this->numero_commande = $row['numero_commande'];
            $this->articles = $row['articles'];
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
                      articles = :articles
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $this->numero_commande = strip_tags($this->numero_commande);
        $this->id = strip_tags($this->id);

        $stmt->bindParam(':numero_commande', $this->numero_commande);
        $stmt->bindParam(':articles', $this->articles);
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
