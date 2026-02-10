<?php
/**
 * Modèle CommandeLatitude
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

        $this->numero_commande = htmlspecialchars(strip_tags($this->numero_commande));
        $this->articles = htmlspecialchars(strip_tags($this->articles));

        $stmt->bindParam(":numero_commande", $this->numero_commande);
        $stmt->bindParam(":articles", $this->articles);

        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    /**
     * Lire toutes les commandes avec détails
     */
    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " 
                  ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
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
                  SET numero_commande=:numero_commande,
                      articles=:articles
                  WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $this->numero_commande = htmlspecialchars(strip_tags($this->numero_commande));
        $this->articles = htmlspecialchars(strip_tags($this->articles));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":numero_commande", $this->numero_commande);
        $stmt->bindParam(":articles", $this->articles);
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
    
    /**
     * Calculer le nombre total d'étiquettes
     */
    public function getTotalEtiquettes() {
        $articlesArray = json_decode($this->articles, true);
        $total = 0;
        
        if($articlesArray && is_array($articlesArray)) {
            foreach($articlesArray as $article) {
                $total += intval($article['nombre_cartons']);
            }
        }
        
        return $total;
    }
}
