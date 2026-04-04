<?php
// models/Transaction.php
// Handles all database queries related to financial transactions

class Transaction {
    private $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    /**
     * Calculates the total balance for a user.
     * Incomes add to the balance, expenses subtract from it.
     */
    public function getBalance($user_id) {
        // We use the SQL SUM() function and a CASE statement to mathematically calculate the balance in MariaDB directly
        $stmt = $this->db->prepare("
            SELECT SUM(CASE WHEN type = 'income' THEN amount ELSE -amount END) as balance 
            FROM transactions 
            WHERE user_id = ?
        ");
        $stmt->bind_param("i", $user_id); // 'i' means integer
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        // If the balance is null (no transactions yet), return 0
        return $result['balance'] ?? 0;
    }

    /**
     * Fetches the most recent transactions for the dashboard
     */
    public function getRecent($user_id, $limit = 5) {
        // We JOIN with the categories table so we can show the category name instead of just the ID
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
        
        // fetch_all(MYSQLI_ASSOC) returns an array of associative arrays (a list of all our rows)
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Adds a new transaction to the database
     */
    public function add($user_id, $category_id, $amount, $type, $description, $transaction_date) {
        // The ? placeholders protect us from SQL injection. 
        $stmt = $this->db->prepare("
            INSERT INTO transactions (user_id, category_id, amount, type, description, transaction_date) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        // 'iiisss' means Integer, Integer, Integer, String, String, String
        $stmt->bind_param("iiisss", $user_id, $category_id, $amount, $type, $description, $transaction_date);
        
        return $stmt->execute();
    }

    /**
     * Gets all transactions for a specific user to display in the list view
     */
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

    /**
     * Deletes a transaction, ensuring it belongs to the logged-in user
     */
    public function delete($transaction_id, $user_id) {
        // We check BOTH transaction_id and user_id to prevent users from deleting someone else's data!
        $stmt = $this->db->prepare("DELETE FROM transactions WHERE transaction_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $transaction_id, $user_id);
        return $stmt->execute();
    }

    /**
     * Fetches a single transaction by ID for the edit form
     */
    public function getById($transaction_id, $user_id) {
        $stmt = $this->db->prepare("SELECT * FROM transactions WHERE transaction_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $transaction_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }

    /**
     * Updates an existing transaction
     */
    public function update($transaction_id, $user_id, $category_id, $amount, $type, $description, $transaction_date) {
        $stmt = $this->db->prepare("
            UPDATE transactions 
            SET category_id = ?, amount = ?, type = ?, description = ?, transaction_date = ? 
            WHERE transaction_id = ? AND user_id = ?
        ");
        
        // 'iisssii' means Integer, Integer, String, String, String, Integer, Integer
        $stmt->bind_param("iisssii", $category_id, $amount, $type, $description, $transaction_date, $transaction_id, $user_id);
        
        return $stmt->execute();
    }
}
?>