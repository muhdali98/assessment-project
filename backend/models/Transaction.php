<?php
class Transaction {
    private $conn;
    private $table = 'transactions';
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (title, amount, currency, category_id, description, transaction_date) 
                  VALUES (:title, :amount, :currency, :category_id, :description, :transaction_date)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':amount', $data['amount']);
        $stmt->bindParam(':currency', $data['currency']);
        $stmt->bindParam(':category_id', $data['category_id']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':transaction_date', $data['transaction_date']);
        
        return $stmt->execute();
    }
    
    public function index($filters = []) {
        $query = "SELECT t.*, c.name as category_name, c.color as category_color 
                  FROM " . $this->table . " t 
                  LEFT JOIN categories c ON t.category_id = c.id 
                  WHERE 1=1";
        
        if (!empty($filters['search'])) {
            $query .= " AND (t.title LIKE :search OR t.description LIKE :search)";
        }
        
        if (!empty($filters['currency'])) {
            $query .= " AND t.currency = :currency";
        }
        
        if (!empty($filters['category_id'])) {
            $query .= " AND t.category_id = :category_id";
        }
        
        if (!empty($filters['date_from'])) {
            $query .= " AND t.transaction_date >= :date_from";
        }
        
        if (!empty($filters['date_to'])) {
            $query .= " AND t.transaction_date <= :date_to";
        }
        
        $query .= " ORDER BY t.transaction_date DESC, t.created_at DESC";
        
        if (!empty($filters['limit'])) {
            $offset = !empty($filters['offset']) ? $filters['offset'] : 0;
            $query .= " LIMIT :limit OFFSET :offset";
        }
        
        $stmt = $this->conn->prepare($query);
        
        if (!empty($filters['search'])) {
            $search = '%' . $filters['search'] . '%';
            $stmt->bindParam(':search', $search);
        }
        
        if (!empty($filters['currency'])) {
            $stmt->bindParam(':currency', $filters['currency']);
        }
        
        if (!empty($filters['category_id'])) {
            $stmt->bindParam(':category_id', $filters['category_id']);
        }
        
        if (!empty($filters['date_from'])) {
            $stmt->bindParam(':date_from', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $stmt->bindParam(':date_to', $filters['date_to']);
        }
        
        if (!empty($filters['limit'])) {
            $stmt->bindValue(':limit', (int)$filters['limit'], PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$filters['offset'], PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt;
    }
    
    public function view($id) {
        $query = "SELECT t.*, c.name as category_name, c.color as category_color 
                  FROM " . $this->table . " t 
                  LEFT JOIN categories c ON t.category_id = c.id 
                  WHERE t.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    public function update($id, $data) {
        $query = "UPDATE " . $this->table . " 
                  SET title = :title, 
                      amount = :amount, 
                      currency = :currency, 
                      category_id = :category_id, 
                      description = :description, 
                      transaction_date = :transaction_date 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':amount', $data['amount']);
        $stmt->bindParam(':currency', $data['currency']);
        $stmt->bindParam(':category_id', $data['category_id']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':transaction_date', $data['transaction_date']);
        
        return $stmt->execute();
    }
    
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
    
    public function count($filters = []) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE 1=1";
        
        if (!empty($filters['search'])) {
            $query .= " AND (title LIKE :search OR description LIKE :search)";
        }
        
        if (!empty($filters['currency'])) {
            $query .= " AND currency = :currency";
        }
        
        if (!empty($filters['category_id'])) {
            $query .= " AND category_id = :category_id";
        }
        
        if (!empty($filters['date_from'])) {
            $query .= " AND transaction_date >= :date_from";
        }
        
        if (!empty($filters['date_to'])) {
            $query .= " AND transaction_date <= :date_to";
        }
        
        $stmt = $this->conn->prepare($query);
        
        if (!empty($filters['search'])) {
            $search = '%' . $filters['search'] . '%';
            $stmt->bindParam(':search', $search);
        }
        
        if (!empty($filters['currency'])) {
            $stmt->bindParam(':currency', $filters['currency']);
        }
        
        if (!empty($filters['category_id'])) {
            $stmt->bindParam(':category_id', $filters['category_id']);
        }
        
        if (!empty($filters['date_from'])) {
            $stmt->bindParam(':date_from', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $stmt->bindParam(':date_to', $filters['date_to']);
        }
        
        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'];
    }
}