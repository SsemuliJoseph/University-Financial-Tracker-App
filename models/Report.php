<?php
// models/Report.php
// Handles data aggregation for charts and analytics

class Report
{
    private $db;

    public function __construct($dbConnection)
    {
        $this->db = $dbConnection;
    }

    /**
     * Gets total expenses grouped by category for a specific month/year
     * Used for the Pie Chart
     */
    public function getSpendingByCategory($user_id, $month, $year)
    {
        $stmt = $this->db->prepare("
            SELECT c.name as category_name, SUM(t.amount) as total
            FROM transactions t
            JOIN categories c ON t.category_id = c.category_id
            WHERE t.user_id = ? 
              AND t.type = 'expense' 
              AND MONTH(t.transaction_date) = ? 
              AND YEAR(t.transaction_date) = ?
            GROUP BY c.category_id
            ORDER BY total DESC
        ");
        $stmt->bind_param("iii", $user_id, $month, $year);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Gets total expenses grouped by day for a specific month/year
     * Used for the Line Graph
     */
    public function getDailySpending($user_id, $month, $year)
    {
        $stmt = $this->db->prepare("
            SELECT DATE(t.transaction_date) as date, SUM(t.amount) as total
            FROM transactions t
            WHERE t.user_id = ? 
              AND t.type = 'expense' 
              AND MONTH(t.transaction_date) = ? 
              AND YEAR(t.transaction_date) = ?
            GROUP BY DATE(t.transaction_date)
            ORDER BY DATE(t.transaction_date) ASC
        ");
        $stmt->bind_param("iii", $user_id, $month, $year);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Gets all transactions across the entire system.
     * Used by Finance Officers for auditing and exporting.
     */
    public function getAllSystemTransactions()
    {
        // We JOIN users so we know who made the transaction
        $result = $this->db->query("
            SELECT t.transaction_id, t.transaction_date, t.description, t.type, t.amount,
                   c.name as category_name, u.name as user_name, u.email as user_email
            FROM transactions t
            LEFT JOIN categories c ON t.category_id = c.category_id
            JOIN users u ON t.user_id = u.user_id
            ORDER BY t.transaction_date DESC, t.transaction_id DESC
        ");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
