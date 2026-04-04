<?php
// controllers/BudgetController.php

require_once 'models/Budget.php';

class BudgetController
{
    public function settings()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?page=login");
            exit;
        }

        $db = getConnection();
        $budgetModel = new Budget($db);
        $user_id = $_SESSION['user_id'];

        $month = (int) date('n');
        $year = (int) date('Y');

        // Handle AJAX POST request to update a single category budget
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if ($input && isset($input['category_id']) && isset($input['amount'])) {
                $category_id = (int)$input['category_id'];
                $amount = (int)$input['amount'];
                
                $budgetModel->setBudget($user_id, $category_id, $amount, $month, $year);
                
                // Return fresh data for this category to update the frontend
                $budgets = $budgetModel->getBudgetProgress($user_id, $month, $year);
                $updatedCategory = null;
                foreach ($budgets as $b) {
                    if ($b['category_id'] == $category_id) {
                        $updatedCategory = $b;
                        break;
                    }
                }
                
                // Calculate overall stats for the top summary
                $totalBudget = array_sum(array_column($budgets, 'budget_amount'));
                $totalSpent = array_sum(array_column($budgets, 'spent_amount'));
                
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'category' => $updatedCategory,
                    'totalBudget' => $totalBudget,
                    'totalSpent' => $totalSpent
                ]);
                exit;
            }
        }

        // Fetch the fresh budget progress for the normal page load
        $budgets = $budgetModel->getBudgetProgress($user_id, $month, $year);

        // Calculate overall totals
        $overallTotalBudget = array_sum(array_column($budgets, 'budget_amount'));
        $overallTotalSpent = array_sum(array_column($budgets, 'spent_amount'));
        
        $overallPercent = $overallTotalBudget > 0 ? round(($overallTotalSpent / $overallTotalBudget) * 100) : 0;
        
        require 'views/budgets/settings.php';
    }
}
