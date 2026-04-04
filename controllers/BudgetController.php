<?php
// controllers/BudgetController.php
// Controller specifically for managing the user's budgets

require_once 'models/Budget.php';

class BudgetController
{

    public function settings()
    {
        // Enforce session security
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?page=login");
            exit;
        }

        $db = getConnection();
        $budgetModel = new Budget($db);
        $user_id = $_SESSION['user_id'];

        // PHP's date() function gets the current month (1-12) and year (e.g., 2026)
        $month = (int) date('n');
        $year = (int) date('Y');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Check if the budgets array was submitted
            if (isset($_POST['budgets']) && is_array($_POST['budgets'])) {

                // Loop through each input field. $category_id is the key, $amount is the value
                foreach ($_POST['budgets'] as $category_id => $amount) {
                    $category_id = (int) $category_id;
                    $amount = (int) $amount; // If left empty, becomes 0

                    // Tell the model to save or update it
                    $budgetModel->setBudget($user_id, $category_id, $amount, $month, $year);
                }

                $success = "Budgets updated successfully for this month!";
            }
        }

        // Fetch the fresh budget progress *after* saving updates so the view has the correct numbers
        $budgets = $budgetModel->getBudgetProgress($user_id, $month, $year);

        // Load the view
        require 'views/budgets/settings.php';
    }
}
