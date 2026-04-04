<?php
// models/Budget.php
// Handles database operations for User Budgets

class Budget
{
    private $db;

    public function __construct($dbConnection)
    {
        $this->db = $dbConnection;
    }

    /**
     * Gets all expense categories, their set budget limits, and how much has been spent so far this month
     */
    public function getBudgetProgress($user_id, $month, $year)
    {
        // This is a complex query! 
        // 1. It lists all 'expense' categories
        // 2. It LEFT JOINs the budgets table to find the limit for the current month
        // 3. It runs a subquery to elegantly SUM the transactions for that category matching the month/year
        $stmt = $this->db->prepare("
            SELECT 
                c.category_id, 
                c.name as category_name, 
                COALESCE(b.amount, 0) as budget_amount,
                COALESCE((
                    SELECT SUM(amount) 
                    FROM transactions t 
                    WHERE t.category_id = c.category_id 
                      AND t.user_id = ? 
                      AND MONTH(t.transaction_date) = ? 
                      AND YEAR(t.transaction_date) = ?
                ), 0) as spent_amount
            FROM categories c
            LEFT JOIN budgets b ON c.category_id = b.category_id 
                AND b.user_id = ? 
                AND b.month = ? 
                AND b.year = ?
            WHERE c.type = 'expense'
            ORDER BY c.name ASC
        ");

        // We bind 6 parameters in total ('iiiiii' = 6 integers).
        $stmt->bind_param("iiiiii", $user_id, $month, $year, $user_id, $month, $year);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Sets or updates a budget for a category
     */
    public function setBudget($user_id, $category_id, $amount, $month, $year)
    {
        // First check if a budget for this category and month already exists
        $stmt = $this->db->prepare("SELECT budget_id FROM budgets WHERE user_id = ? AND category_id = ? AND month = ? AND year = ?");
        $stmt->bind_param("iiii", $user_id, $category_id, $month, $year);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // It exists! Run an UPDATE query
            $row = $result->fetch_assoc();
            $budget_id = $row['budget_id'];

            $stmtUpdate = $this->db->prepare("UPDATE budgets SET amount = ? WHERE budget_id = ?");
            $stmtUpdate->bind_param("ii", $amount, $budget_id);
            return $stmtUpdate->execute();
        } else {
            // Doesn't exist yet! Run an INSERT query
            $stmtInsert = $this->db->prepare("INSERT INTO budgets (user_id, category_id, amount, month, year) VALUES (?, ?, ?, ?, ?)");
            $stmtInsert->bind_param("iiiii", $user_id, $category_id, $amount, $month, $year);
            return $stmtInsert->execute();
        }
    }
}
