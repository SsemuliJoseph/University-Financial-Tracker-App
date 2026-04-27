<?php
// models/Insight.php
// Handles analytical queries to generate smart financial insights

class Insight
{
    private $db;

    public function __construct($dbConnection)
    {
        $this->db = $dbConnection;
    }

    /**
     * Gets the user's current transaction streak (consecutive days of recording)
     */
    public function getCurrentStreak($user_id)
    {
        $stmt = $this->db->prepare("
            SELECT DISTINCT DATE(transaction_date) as t_date 
            FROM transactions 
            WHERE user_id = ? 
            ORDER BY t_date DESC
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        if (empty($result)) return 0;

        $streak = 0;
        $currentDate = new DateTime();
        $currentDate->setTime(0, 0, 0);

        // Check if the latest transaction is today or yesterday
        $latestDate = new DateTime($result[0]['t_date']);
        $diff = $currentDate->diff($latestDate)->days;

        // If latest is older than yesterday, streak is broken 
        if ($diff > 1) {
            return 0;
        }

        $expectedDate = clone $latestDate;

        foreach ($result as $row) {
            $rowDate = new DateTime($row['t_date']);
            if ($rowDate->format('Y-m-d') === $expectedDate->format('Y-m-d')) {
                $streak++;
                $expectedDate->modify('-1 day');
            } else {
                break;
            }
        }

        return $streak;
    }

    /**
     * Finds the category with the highest spending this month
     */
    public function getBiggestExpenseCategory($user_id, $month, $year)
    {
        $stmt = $this->db->prepare("
            SELECT c.name, SUM(t.amount) as total 
            FROM transactions t
            JOIN categories c ON t.category_id = c.category_id
            WHERE t.user_id = ? AND c.type = 'expense'
              AND MONTH(t.transaction_date) = ? AND YEAR(t.transaction_date) = ?
            GROUP BY c.category_id
            ORDER BY total DESC
            LIMIT 1
        ");
        $stmt->bind_param("iii", $user_id, $month, $year);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getTopExpenseCategories($user_id, $month, $year, $limit = 3)
    {
        $stmt = $this->db->prepare("
            SELECT c.name, SUM(t.amount) as total 
            FROM transactions t
            JOIN categories c ON t.category_id = c.category_id
            WHERE t.user_id = ? AND c.type = 'expense'
              AND MONTH(t.transaction_date) = ? AND YEAR(t.transaction_date) = ?
            GROUP BY c.category_id
            ORDER BY total DESC
            LIMIT ?
        ");
        $stmt->bind_param("iiii", $user_id, $month, $year, $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Gets total income and expenses for a specific month
     */
    public function getMonthlyTotals($user_id, $month, $year)
    {
        $stmt = $this->db->prepare("
            SELECT 
                SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as total_income,
                SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as total_expense
            FROM transactions
            WHERE user_id = ? AND MONTH(transaction_date) = ? AND YEAR(transaction_date) = ?
        ");
        $stmt->bind_param("iii", $user_id, $month, $year);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Finds the month with the highest savings (Income - Expense)
     */
    public function getBestSavingMonth($user_id)
    {
        $stmt = $this->db->prepare("
            SELECT 
                DATE_FORMAT(transaction_date, '%Y-%m') as month_str,
                SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) - 
                SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as savings
            FROM transactions
            WHERE user_id = ?
            GROUP BY month_str
            ORDER BY savings DESC
            LIMIT 1
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Compare category spending between two months for pattern detection
     */
    public function getCategoryComparison($user_id, $currentMonth, $currentYear, $prevMonth, $prevYear)
    {
        // Query to get category totals for current and previous month
        $stmt = $this->db->prepare("
            SELECT c.name,
                SUM(CASE WHEN MONTH(t.transaction_date) = ? AND YEAR(t.transaction_date) = ? THEN t.amount ELSE 0 END) as current_total,
                SUM(CASE WHEN MONTH(t.transaction_date) = ? AND YEAR(t.transaction_date) = ? THEN t.amount ELSE 0 END) as prev_total
            FROM transactions t
            JOIN categories c ON t.category_id = c.category_id
            WHERE t.user_id = ? AND c.type = 'expense'
            GROUP BY c.category_id
            HAVING current_total > 0 OR prev_total > 0
        ");
        $stmt->bind_param("iiiii", $currentMonth, $currentYear, $prevMonth, $prevYear, $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
