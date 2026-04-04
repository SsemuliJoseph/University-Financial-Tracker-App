<?php
// controllers/DashboardController.php
// Prepares data for the dashboard view

require_once 'models/Transaction.php';

class DashboardController {
    
    public function index() {
        // Ensure the user is actually logged in. If not, kick them to the login page immediately.
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?page=login");
            exit;
        }

        $db = getConnection();
        $transactionModel = new Transaction($db);
        
        $user_id = $_SESSION['user_id'];
        
        // 1. Get the user's total balance
        $balance = $transactionModel->getBalance($user_id);
        
        // 2. Get the 5 most recent transactions
        $recent_transactions = $transactionModel->getRecent($user_id, 5);

        // 3. (We will add budget usage summary here when we build the Budget feature!)
        
        // Require the view and pass our specific data
        require 'views/dashboard.php';
    }
}
?>