<?php
// models/Transaction.php
// Handles all database queries related to financial transactions

class Transaction {
    private $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    public function getBalance($user_id) {
        $stmt = $this->db->prepare("
            SELECT SUM(CASE WHEN type = 'income' THEN amount ELSE -amount END) as balance 
            FROM transactions 
            WHERE user_id = ?
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['balance'] ?? 0;
    }

    public function getRecent($user_id, $limit = 5) {
        $stmt = $this->db->prepare("
            SELECT t.*, c.name as category_name 
            FROM transactions t 
            LEFT JOIN categories c ON t.category_id = c.category_id 
            WHERE t.user_id = ? 
            ORDER BY t.transaction_date DESC, t.created_at DESC 
            LIMIT ?
        ");
        $stmt->bind_param("ii", $user_id, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function add($user_id, $category_id, $amount, $type, $description, $transaction_date) {
        $stmt = $this->db->prepare("
            INSERT INTO transactions (user_id, category_id, amount, type, description, transaction_date) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("iiisss", $user_id, $category_id, $amount, $type, $description, $transaction_date);
        return $stmt->execute();
    }

    public function getAllByUser($user_id) {
        $stmt = $this->db->prepare("
            SELECT t.*, c.name as category_name 
            FROM transactions t 
            LEFT JOIN categories c ON t.category_id = c.category_id 
            WHERE t.user_id = ? 
            ORDER BY t.transaction_date DESC, t.created_at DESC
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // UPGRADE 3: Advanced Filtering, Sorting, and Pagination
    public function getFilteredByUser($user_id, $filters = [], $sortBy = 'transaction_date', $sortDir = 'DESC', $limit = 15, $offset = 0) {
        $query = "SELECT t.*, c.name as category_name 
                  FROM transactions t 
                  LEFT JOIN categories c ON t.category_id = c.category_id 
                  WHERE t.user_id = ?";
        $types = "i";
        $params = [$user_id];

        if (!empty($filters['category_id'])) {
            $query .= " AND t.category_id = ?";
            $types .= "i";
            $params[] = $filters['category_id'];
        }
        if (!empty($filters['type'])) {
            $query .= " AND t.type = ?";
            $types .= "s";
            $params[] = $filters['type'];
        }
        if (!empty($filters['date_from'])) {
            $query .= " AND t.transaction_date >= ?";
            $types .= "s";
            $params[] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $query .= " AND t.transaction_date <= ?";
            $types .= "s";
            $params[] = $filters['date_to'];
        }

        // Whitelist sort columns to prevent SQL injection
        $allowedSorts = ['transaction_date', 'amount', 'type', 'category_name', 'description'];
        $sortBy = in_array($sortBy, $allowedSorts) ? $sortBy : 'transaction_date';
        $sortDir = strtoupper($sortDir) === 'ASC' ? 'ASC' : 'DESC';

        // Add sorting, limit and offset
        // If sorting by category_name, we use the alias c.name directly or category_name depending on ORDER BY support
        // But t.transaction_date is prefixed to avoid ambiguity
        $sortPrefix = $sortBy == 'category_name' ? '' : 't.';
        
        $query .= " ORDER BY {$sortPrefix}{$sortBy} {$sortDir}, t.created_at DESC ";
        
        // Exclude pagination if limit is 0 (Used for CSV export)
        if ($limit > 0) {
            $query .= " LIMIT ? OFFSET ?";
            $types .= "ii";
            $params[] = $limit;
            $params[] = $offset;
        }

        $stmt = $this->db->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // UPGRADE 3: Get total count for pagination
    public function getCountFiltered($user_id, $filters = []) {
        $query = "SELECT COUNT(*) as total FROM transactions t WHERE t.user_id = ?";
        $types = "i";
        $params = [$user_id];

        if (!empty($filters['category_id'])) {
            $query .= " AND t.category_id = ?";
            $types .= "i";
            $params[] = $filters['category_id'];
        }
        if (!empty($filters['type'])) {
            $query .= " AND t.type = ?";
            $types .= "s";
            $params[] = $filters['type'];
        }
        if (!empty($filters['date_from'])) {
            $query .= " AND t.transaction_date >= ?";
            $types .= "s";
            $params[] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $query .= " AND t.transaction_date <= ?";
            $types .= "s";
            $params[] = $filters['date_to'];
        }

        $stmt = $this->db->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        return $res['total'];
    }

    public function delete($transaction_id, $user_id) {
        $stmt = $this->db->prepare("DELETE FROM transactions WHERE transaction_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $transaction_id, $user_id);
        return $stmt->execute();
    }

    // UPGRADE 3: Bulk delete transactions securely
    public function bulkDelete($ids, $user_id) {
        if (empty($ids)) return false;
        
        // Generate ? placeholders equal to number of IDs
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        
        $query = "DELETE FROM transactions WHERE user_id = ? AND transaction_id IN ($placeholders)";
        $stmt = $this->db->prepare($query);
        
        // First param is user_id, followed by all transaction ids
        $types = "i" . str_repeat('i', count($ids));
        $params = array_merge([$user_id], $ids);
        
        $stmt->bind_param($types, ...$params);
        return $stmt->execute();
    }

    public function getById($transaction_id, $user_id) {
        $stmt = $this->db->prepare("SELECT * FROM transactions WHERE transaction_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $transaction_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function update($transaction_id, $user_id, $category_id, $amount, $type, $description, $transaction_date) {
        $stmt = $this->db->prepare("
            UPDATE transactions 
            SET category_id = ?, amount = ?, type = ?, description = ?, transaction_date = ? 
            WHERE transaction_id = ? AND user_id = ?
        ");
        $stmt->bind_param("iisssii", $category_id, $amount, $type, $description, $transaction_date, $transaction_id, $user_id);
        return $stmt->execute();
    }
}
