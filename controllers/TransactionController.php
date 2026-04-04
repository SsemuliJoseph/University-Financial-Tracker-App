<?php
// controllers/TransactionController.php
// The controller governing all Transaction actions (Add, View, Edit, Delete)

require_once 'models/Transaction.php';
require_once 'models/Category.php';

class TransactionController
{

    // Shows the form and saves a new transaction
    public function add()
    {
        // Enforce session security block - kicking guests away
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?page=login");
            exit;
        }

        $db = getConnection();

        // We initialize both models so we get categories and can save transactions
        $categoryModel = new Category($db);
        $transactionModel = new Transaction($db);

        // Fetch categories to populate our HTML <select> dropdown
        $categories = $categoryModel->getAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_SESSION['user_id'];

            // Extract the POST variables submitted by the user
            $amount = (int) $_POST['amount'];
            $transaction_date = $_POST['transaction_date'];
            $description = trim($_POST['description']);

            // The value of our select box actually sends BOTH the category_id AND the type 
            // joined by a hyphen (e.g. "5-expense"). We use explode() to separate them!
            $cat_data = explode('-', $_POST['category_data']);
            $category_id = (int) $cat_data[0];
            $type = $cat_data[1]; // 'income' or 'expense'

            // Model executes the addition
            if ($transactionModel->add($user_id, $category_id, $amount, $type, $description, $transaction_date)) {
                // If it worked, instantly redirect to dashboard
                header("Location: index.php?page=dashboard");
                exit;
            } else {
                $error = "Failed to add transaction.";
            }
        }

        // Require the new HTML form view
        require 'views/transactions/add.php';
    }

    // Fetches all transactions for the user and displays them in a table
    public function list()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?page=login");
            exit;
        }

        $db = getConnection();
        $transactionModel = new Transaction($db);

        // Fetch all of the user's transactions
        $transactions = $transactionModel->getAllByUser($_SESSION['user_id']);

        require 'views/transactions/list.php';
    }

    // Handles deleting a transaction safely
    public function delete()
    {
        // Enforce session security
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?page=login");
            exit;
        }

        // We use GET specifically for links (e.g. index.php?page=transaction_delete&id=5)
        if (isset($_GET['id'])) {
            $transaction_id = (int) $_GET['id'];
            $user_id = $_SESSION['user_id'];

            $db = getConnection();
            $transactionModel = new Transaction($db);

            // Delete it (the Model verifies the user is the owner)
            $transactionModel->delete($transaction_id, $user_id);
        }

        // Redirect back to the list
        header("Location: index.php?page=transactions");
        exit;
    }

    // Handles fetching a transaction and saving the edits
    public function edit()
    {
        // Auth check
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?page=login");
            exit;
        }

        // Get ID from URL
        if (!isset($_GET['id'])) {
            header("Location: index.php?page=transactions");
            exit;
        }

        $transaction_id = (int) $_GET['id'];
        $user_id = $_SESSION['user_id'];

        $db = getConnection();
        $categoryModel = new Category($db);
        $transactionModel = new Transaction($db);

        // Fetch transaction to pre-fill the form and ensure they own it
        $transaction = $transactionModel->getById($transaction_id, $user_id);
        if (!$transaction) {
            // Unauth/Invalid ID fallback
            header("Location: index.php?page=transactions");
            exit;
        }

        $categories = $categoryModel->getAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $amount = (int) $_POST['amount'];
            $transaction_date = $_POST['transaction_date'];
            $description = trim($_POST['description']);

            $cat_data = explode('-', $_POST['category_data']);
            $category_id = (int) $cat_data[0];
            $type = $cat_data[1];

            // Execute the update
            if ($transactionModel->update($transaction_id, $user_id, $category_id, $amount, $type, $description, $transaction_date)) {
                header("Location: index.php?page=transactions");
                exit;
            } else {
                $error = "Failed to update transaction.";
            }

            // Reload transaction so form reflects new data immediately on failure
            $transaction = $transactionModel->getById($transaction_id, $user_id);
        }

        // Require the new HTML edit view
        require 'views/transactions/edit.php';
    }
}
