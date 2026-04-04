<?php
// controllers/TransactionController.php
// The controller governing all Transaction actions (Add, View, Edit, Delete, Bulk Delete, Export)

require_once 'models/Transaction.php';
require_once 'models/Category.php';

class TransactionController
{
    public function add()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?page=login");
            exit;
        }

        $db = getConnection();
        $categoryModel = new Category($db);
        $transactionModel = new Transaction($db);
        $categories = $categoryModel->getAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_SESSION['user_id'];
            $amount = (int) $_POST['amount'];
            $transaction_date = $_POST['transaction_date'];
            $description = trim($_POST['description']);

            $cat_data = explode('-', $_POST['category_data']);
            $category_id = (int) $cat_data[0];
            $type = $cat_data[1];

            $success = $transactionModel->add($user_id, $category_id, $amount, $type, $description, $transaction_date);

            // UPGRADE 4: AJAX Response for Toast Notification
            if (isset($_POST['ajax']) && $_POST['ajax'] === '1') {
                header('Content-Type: application/json');
                if ($success) {
                    echo json_encode(['success' => true, 'message' => "Transaction added! UGX " . number_format($amount) . " $type recorded", 'type' => $type]);
                } else {
                    echo json_encode(['success' => false, 'error' => "Failed to add transaction."]);
                }
                exit;
            }

            if ($success) {
                header("Location: index.php?page=dashboard");
                exit;
            } else {
                $error = "Failed to add transaction.";
            }
        }
        require 'views/transactions/add.php';
    }

    // UPGRADE 3: Advanced List with filtering, sorting and pagination
    public function list()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?page=login");
            exit;
        }

        $db = getConnection();
        $transactionModel = new Transaction($db);
        $categoryModel = new Category($db);
        $user_id = $_SESSION['user_id'];

        // Get filters from URL
        $filters = [
            'category_id' => isset($_GET['category']) ? (int) $_GET['category'] : '',
            'type'        => isset($_GET['type']) ? $_GET['type'] : '',
            'date_from'   => isset($_GET['date_from']) ? $_GET['date_from'] : '',
            'date_to'     => isset($_GET['date_to']) ? $_GET['date_to'] : '',
        ];

        // Get sort params
        $sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'transaction_date';
        $sortDir = isset($_GET['dir']) && strtolower($_GET['dir']) == 'asc' ? 'asc' : 'desc';

        // Pagination calculations
        $page = isset($_GET['p']) ? (int) $_GET['p'] : 1;
        if ($page < 1) $page = 1;
        $limit = 15; // 15 transactions per page as per prompt
        $offset = ($page - 1) * $limit;

        $totalRecords = $transactionModel->getCountFiltered($user_id, $filters);
        $totalPages = ceil($totalRecords / $limit);

        // Fetch paginated and filtered transactions
        $transactions = $transactionModel->getFilteredByUser($user_id, $filters, $sortBy, $sortDir, $limit, $offset);
        $categories = $categoryModel->getAll(); // For filter dropdown

        require 'views/transactions/list.php';
    }

    public function delete()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?page=login");
            exit;
        }

        if (isset($_GET['id'])) {
            $transaction_id = (int) $_GET['id'];
            $user_id = $_SESSION['user_id'];
            $db = getConnection();
            $transactionModel = new Transaction($db);
            $transactionModel->delete($transaction_id, $user_id);
        }

        header("Location: index.php?page=transactions");
        exit;
    }

    // UPGRADE 3: Bulk Delete implementation
    public function bulkDelete()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?page=login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['selected_ids'])) {
            $ids = array_map('intval', $_POST['selected_ids']);
            $user_id = $_SESSION['user_id'];

            $db = getConnection();
            $transactionModel = new Transaction($db);
            $transactionModel->bulkDelete($ids, $user_id);
        }

        // redirect back with existing query params if possible, or just base list
        header("Location: " . (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php?page=transactions'));
        exit;
    }

    // UPGRADE 3: Export filtered data to CSV
    public function exportCsv()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?page=login");
            exit;
        }

        $db = getConnection();
        $transactionModel = new Transaction($db);
        $user_id = $_SESSION['user_id'];

        $filters = [
            'category_id' => isset($_GET['category']) ? (int) $_GET['category'] : '',
            'type'        => isset($_GET['type']) ? $_GET['type'] : '',
            'date_from'   => isset($_GET['date_from']) ? $_GET['date_from'] : '',
            'date_to'     => isset($_GET['date_to']) ? $_GET['date_to'] : '',
        ];

        $sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'transaction_date';
        $sortDir = isset($_GET['dir']) && strtolower($_GET['dir']) == 'asc' ? 'asc' : 'desc';

        // Pass 0 to limit to fetch ALL filtered rows
        $transactions = $transactionModel->getFilteredByUser($user_id, $filters, $sortBy, $sortDir, 0, 0);

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="finance_export_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Date', 'Category', 'Description', 'Type', 'Amount (UGX)']);

        foreach ($transactions as $t) {
            fputcsv($output, [
                $t['transaction_date'],
                $t['category_name'],
                $t['description'],
                ucfirst($t['type']),
                $t['amount']
            ]);
        }
        fclose($output);
        exit;
    }

    public function edit()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?page=login");
            exit;
        }

        if (!isset($_GET['id'])) {
            header("Location: index.php?page=transactions");
            exit;
        }

        $transaction_id = (int) $_GET['id'];
        $user_id = $_SESSION['user_id'];

        $db = getConnection();
        $categoryModel = new Category($db);
        $transactionModel = new Transaction($db);

        $transaction = $transactionModel->getById($transaction_id, $user_id);
        if (!$transaction) {
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

            if ($transactionModel->update($transaction_id, $user_id, $category_id, $amount, $type, $description, $transaction_date)) {
                header("Location: index.php?page=transactions");
                exit;
            } else {
                $error = "Failed to update transaction.";
            }

            $transaction = $transactionModel->getById($transaction_id, $user_id);
        }

        require 'views/transactions/edit.php';
    }
}
