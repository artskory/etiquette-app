<?php
/**
 * Modèle Reference
 */
class Reference {
    private $conn;
    private $table_name = "`references`";

    public $id;
    public $reference;
    public $designation;
    public $created_at;
    public $updated_at;

    /**
     * Constructeur
     */
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Créer une nouvelle référence
     */
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET reference=:reference, designation=:designation";

        $stmt = $this->conn->prepare($query);

        $this->reference = htmlspecialchars(strip_tags($this->reference));
        $this->designation = htmlspecialchars(strip_tags($this->designation));

        $stmt->bindParam(":reference", $this->reference);
        $stmt->bindParam(":designation", $this->designation);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    /**
     * Lire toutes les références
     */
    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY reference ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    /**
     * Lire une référence par ID
     */
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $this->reference = $row['reference'];
            $this->designation = $row['designation'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            return true;
        }

        return false;
    }

    /**
     * Mettre à jour une référence
     */
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET reference=:reference, designation=:designation 
                  WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $this->reference = htmlspecialchars(strip_tags($this->reference));
        $this->designation = htmlspecialchars(strip_tags($this->designation));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":reference", $this->reference);
        $stmt->bindParam(":designation", $this->designation);
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    /**
     * Supprimer une référence
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
