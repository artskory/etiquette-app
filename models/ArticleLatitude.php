<?php
/**
 * Modèle ArticleLatitude
 */
class ArticleLatitude {
    private $conn;
    private $table_name = "articles_latitude";

    public $id;
    public $nom;
    public $created_at;
    public $updated_at;

    /**
     * Constructeur
     */
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Créer un nouvel article
     */
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET nom=:nom";

        $stmt = $this->conn->prepare($query);

        $this->nom = htmlspecialchars(strip_tags($this->nom));

        $stmt->bindParam(":nom", $this->nom);

        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    /**
     * Lire tous les articles
     */
    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY nom ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    /**
     * Lire un article par ID
     */
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $this->nom = $row['nom'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            return $row;
        }

        return false;
    }

    /**
     * Mettre à jour un article
     */
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET nom=:nom
                  WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $this->nom = htmlspecialchars(strip_tags($this->nom));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":nom", $this->nom);
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    /**
     * Supprimer un article
     */
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(1, $this->id);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    /**
     * Vérifier si un article existe déjà
     */
    public function exists() {
        $query = "SELECT id FROM " . $this->table_name . " 
                  WHERE nom = :nom
                  LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $this->nom = htmlspecialchars(strip_tags($this->nom));
        $stmt->bindParam(":nom", $this->nom);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            return true;
        }

        return false;
    }
}
