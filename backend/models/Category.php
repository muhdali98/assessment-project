<?php
class Category {
    private $conn;
    private $table = 'categories';
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function index() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    
    public function create($data) {
        $query = "INSERT INTO " . $this->table . " (name, color) VALUES (:name, :color)";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':color', $data['color']);
        
        return $stmt->execute();
    }
}