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

    // UPGRADE 5: Get totals (income & expense) for a specific month
    public function getMonthlyTotals($user_id, $month, $year)
    {
        $stmt = $this->db->prepare("
            SELECT type, SUM(amount) as total
            FROM transactions
            WHERE user_id = ? 
              AND MONTH(transaction_date) = ? 
              AND YEAR(transaction_date) = ?
            GROUP BY type
        ");
        $stmt->bind_param("iii", $user_id, $month, $year);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        $totals = ['income' => 0, 'expense' => 0];
        foreach ($result as $row) {
            $totals[$row['type']] = (float) $row['total'];
        }
        return $totals;
    }

    // UPGRADE 5: Category spending for a specific month (extended)
    public function getSpendingByCategory($user_id, $month, $year)
    {
        $stmt = $this->db->prepare("
            SELECT c.category_id, c.name as category_name, SUM(t.amount) as total
            FROM transactions t
            JOIN categories c ON t.category_id = c.category_id
            WHERE t.user_id = ? 
              AND t.type = 'expense' 
              AND MONTH(t.transaction_date) = ? 
              AND YEAR(t.transaction_date) = ?
            GROUP BY c.category_id, c.name
            ORDER BY total DESC
        ");
        $stmt->bind_param("iii", $user_id, $month, $year);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // UPGRADE 5: 6-month trend data
    public function getSixMonthTrend($user_id, $endMonth, $endYear)
    {
        // Calculate the start date (5 months prior + the end month = 6 months)
        $endDate = sprintf("%04d-%02d-01", $endYear, $endMonth);
        $startDate = date("Y-m-01", strtotime("-5 months", strtotime($endDate)));
        $realEndDate = date("Y-m-t", strtotime($endDate)); // Last day of the end month

        $stmt = $this->db->prepare("
            SELECT 
                DATE_FORMAT(transaction_date, '%Y-%m') as month_str,
                SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as total_income,
                SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as total_expense
            FROM transactions
            WHERE user_id = ? 
              AND transaction_date >= ? 
              AND transaction_date <= ?
            GROUP BY month_str
            ORDER BY month_str ASC
        ");
        
        $stmt->bind_param("iss", $user_id, $startDate, $realEndDate);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

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

    public function getAllSystemTransactions()
    {
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
