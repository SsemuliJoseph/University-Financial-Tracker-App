<?php
// controllers/DashboardController.php
// Prepares data for the dashboard view

require_once 'models/Transaction.php';
require_once 'models/Budget.php'; // Included for Upgrade 2 Budget Tracking

class DashboardController {
    
    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?page=login");
            exit;
        }

        $db = getConnection();
        $transactionModel = new Transaction($db);
        $budgetModel = new Budget($db);
        $user_id = $_SESSION['user_id'];
        
        $month = date('m');
        $year = date('Y');
        
        // 1. Get the user's total balance
        $balance = $transactionModel->getBalance($user_id);
        
        // 2. Get the 8 most recent transactions (Upgrade 2 requirement)
        $recent_transactions = $transactionModel->getRecent($user_id, 8);

        // 3. Calculate This Month's Income & Expenses
        $stmt = $db->prepare("SELECT type, SUM(amount) as total FROM transactions WHERE user_id = ? AND MONTH(transaction_date) = ? AND YEAR(transaction_date) = ? GROUP BY type");
        $stmt->bind_param("iii", $user_id, $month, $year);
        $stmt->execute();
        $res = $stmt->get_result();
        
        $monthlyIncome = 0;
        $monthlyExpense = 0;
        while ($row = $res->fetch_assoc()) {
            if ($row['type'] == 'income') $monthlyIncome = $row['total'];
            if ($row['type'] == 'expense') $monthlyExpense = $row['total'];
        }

        // 4. Budget Progress Calculation
        $budgets = $budgetModel->getBudgetProgress($user_id, $month, $year);
        $totalBudget = 0;
        $activeBudgets = []; // Only budgets that have transactions this month
        
        foreach ($budgets as $b) {
            $totalBudget += $b['budget_amount'];
            if ($b['spent_amount'] > 0) {
                // Calculate percentage for this specific category
                $pct = $b['budget_amount'] > 0 ? ($b['spent_amount'] / $b['budget_amount']) * 100 : 100;
                $b['percent'] = min(100, round($pct));
                $activeBudgets[] = $b;
            }
        }
        
        // Overall Budget Used %
        $budgetUsedPercent = $totalBudget > 0 ? min(100, round(($monthlyExpense / $totalBudget) * 100)) : 0;

        // 5. Six Months Spending Overview Chart Data (Bar Chart)
        $chartLabels = [];
        $chartIncome = [];
        $chartExpense = [];
        
        // We look back 5 months + current month = 6 months
        for ($i = 5; $i >= 0; $i--) {
            // Using first day to avoid edge case skips (e.g. Feb 30th)
            $targetDate = strtotime(date('Y-m-01') . " -$i months");
            $m = date('m', $targetDate);
            $y = date('Y', $targetDate);
            $chartLabels[] = date('M', $targetDate); // e.g. "Jan", "Feb"
            
            $stmt = $db->prepare("SELECT type, SUM(amount) as total FROM transactions WHERE user_id = ? AND MONTH(transaction_date) = ? AND YEAR(transaction_date) = ? GROUP BY type");
            $stmt->bind_param("iii", $user_id, $m, $y);
            $stmt->execute();
            $r = $stmt->get_result();
            $inc = 0; $exp = 0;
            while ($row = $r->fetch_assoc()) {
                if ($row['type'] == 'income') $inc = (float)$row['total'];
                if ($row['type'] == 'expense') $exp = (float)$row['total'];
            }
            $chartIncome[] = $inc;
            $chartExpense[] = $exp;
        }

        // Pass variables to the view explicitly (optional, but good practice to document)
        // require the view
        require 'views/dashboard.php';
    }
}
?>
