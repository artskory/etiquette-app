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
    public $quantite_par_carton;
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
                  SET reference=:reference, designation=:designation, quantite_par_carton=:quantite_par_carton";

        $stmt = $this->conn->prepare($query);

        $this->reference = strip_tags($this->reference);
        $this->designation = strip_tags($this->designation);
        $qpc = ($this->quantite_par_carton !== null && $this->quantite_par_carton !== '') ? (int)$this->quantite_par_carton : null;

        $stmt->bindParam(":reference", $this->reference);
        $stmt->bindParam(":designation", $this->designation);
        $stmt->bindParam(":quantite_par_carton", $qpc, PDO::PARAM_INT);

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
            $this->quantite_par_carton = $row['quantite_par_carton'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            return $row;
        }

        return false;
    }

    /**
     * Mettre à jour une référence
     */
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET reference=:reference, designation=:designation, quantite_par_carton=:quantite_par_carton
                  WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $this->reference = strip_tags($this->reference);
        $this->designation = strip_tags($this->designation);
        $this->id = strip_tags($this->id);
        $qpc = ($this->quantite_par_carton !== null && $this->quantite_par_carton !== '') ? (int)$this->quantite_par_carton : null;

        $stmt->bindParam(":reference", $this->reference);
        $stmt->bindParam(":designation", $this->designation);
        $stmt->bindParam(":quantite_par_carton", $qpc, PDO::PARAM_INT);
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
    
    /**
     * Vérifier si une référence avec la même désignation existe déjà
     */
    public function exists() {
        $query = "SELECT id FROM " . $this->table_name . " 
                  WHERE reference = :reference AND designation = :designation 
                  LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        
        $this->reference = strip_tags($this->reference);
        $this->designation = strip_tags($this->designation);
        
        $stmt->bindParam(":reference", $this->reference);
        $stmt->bindParam(":designation", $this->designation);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
    }

    /**
     * Vérifier si une référence existe déjà (indépendamment de la désignation)
     */
    public function referenceExists($excludeId = null) {
        $query = "SELECT id FROM " . $this->table_name . " 
                  WHERE reference = :reference";
        
        if ($excludeId !== null) {
            $query .= " AND id != :excludeId";
        }
        
        $query .= " LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        
        $cleanRef = strip_tags($this->reference);
        $stmt->bindParam(":reference", $cleanRef);
        
        if ($excludeId !== null) {
            $stmt->bindParam(":excludeId", $excludeId);
        }
        
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
    }

    /**
     * Vérifier si une désignation existe déjà (indépendamment de la référence)
     */
    public function designationExists($excludeId = null) {
        $query = "SELECT id FROM " . $this->table_name . " 
                  WHERE designation = :designation";
        
        if ($excludeId !== null) {
            $query .= " AND id != :excludeId";
        }
        
        $query .= " LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        
        $cleanDes = strip_tags($this->designation);
        $stmt->bindParam(":designation", $cleanDes);
        
        if ($excludeId !== null) {
            $stmt->bindParam(":excludeId", $excludeId);
        }
        
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
    }
}
