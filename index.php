<?php
// index.php

// error_reporting(E_ALL) and ini_set show us all PHP errors. 
// Very helpful during development! (Remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// session_start() begins a new session or resumes an existing one. 
// A session is how we remember that a user is logged in as they navigate between pages.
session_start();

// We include the database file so we can test the connection below.
// require_once ensures the file is included only once to prevent 'function already declared' errors.
require_once 'config/database.php';

// A simple router mechanism to figure out what page the user wants.
// $_GET is a superglobal array that collects data sent in the URL (e.g., ?page=login)
// Here, if 'page' is set in the URL, we use it. Otherwise, we default to 'home'.
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// For now, let's just make sure the basics work and prove routing is functional.
switch ($page) {
    case 'home':
        echo "<h1>Welcome to UFTS!</h1>";
        echo "<p>Your entry point is working successfully.</p>";
        echo "<a href='index.php?page=test_db'>Test Database Connection</a>";
        break;

    case 'test_db':
        // Let's test if our connection works!
        $db = getConnection();
        echo "<h1>Database Connection Successful!</h1>";
        echo "<p>Connected to MariaDB via PHP. Ready to build the database schema.</p>";
        echo "<a href='index.php'>Go Back Home</a>";
        break;

    // ----- ADD THE REGISTER ROUTE -----
    case 'register':
        require_once 'controllers/AuthController.php';
        $authController = new AuthController();
        $authController->register(); // Call the logic we wrote above
        break;

    // ----- ADD THE LOGIN ROUTE -----
    case 'login':
        require_once 'controllers/AuthController.php';
        $authController = new AuthController();
        $authController->login(); // Handles authentication logic
        break;

    // ----- ADD A PLACEHOLDER FOR DASHBOARD -----
    case 'dashboard':
        require_once 'controllers/DashboardController.php';
        $dashboardController = new DashboardController();
        $dashboardController->index();
        break;

    // ----- ADD TRANSACTION ROUTE -----
    case 'transaction_add':
        require_once 'controllers/TransactionController.php';
        $transactionController = new TransactionController();
        $transactionController->add();
        break;

    // ----- VIEW ALL TRANSACTIONS ROUTE -----
    case 'transactions':
        require_once 'controllers/TransactionController.php';
        $transactionController = new TransactionController();
        $transactionController->list();
        break;

    // ----- DELETE TRANSACTION ROUTE -----
    // ----- BULK DELETE ROUTE -----
    case 'transactions_bulk_delete':
        require_once 'controllers/TransactionController.php';
        $transactionController = new TransactionController();
        $transactionController->bulkDelete();
        break;

    // ----- CSV EXPORT ROUTE -----
    case 'transactions_export':
        require_once 'controllers/TransactionController.php';
        $transactionController = new TransactionController();
        $transactionController->exportCsv();
        break;

    case 'transaction_delete':
        require_once 'controllers/TransactionController.php';
        $transactionController = new TransactionController();
        $transactionController->delete();
        break;

    // ----- EDIT TRANSACTION ROUTE -----
    case 'transaction_edit':
        require_once 'controllers/TransactionController.php';
        $transactionController = new TransactionController();
        $transactionController->edit();
        break;

    // ----- BUDGET SETTINGS ROUTE -----
    case 'budget_settings':
        require_once 'controllers/BudgetController.php';
        $budgetController = new BudgetController();
        $budgetController->settings();
        break;

    // ----- REPORTS ROUTE -----
    // ----- REPORT EXPORT ROUTE -----
    case 'reports_export':
        require_once 'controllers/ReportController.php';
        $reportController = new ReportController();
        $reportController->export();
        break;

    case 'reports':
        require_once 'controllers/ReportController.php';
        $reportController = new ReportController();
        $reportController->index();
        break;

    // ----- ADMIN PANEL ROUTE -----
    case 'admin_panel':
        require_once 'controllers/AdminController.php';
        $adminController = new AdminController();
        $adminController->panel();
        break;

    // ----- ADMIN DELETE USER ROUTE -----
    case 'admin_delete_user':
        require_once 'controllers/AdminController.php';
        $adminController = new AdminController();
        $adminController->deleteUser();
        break;

    // ----- NOTIFICATIONS ROUTE -----
    case 'notifications':
        require_once 'controllers/NotificationController.php';
        $notificationController = new NotificationController();
        $notificationController->handleRequest();
        break;

    // ----- FINANCE OFFICER REPORTS ROUTE -----
    case 'finance_reports':
        require_once 'controllers/FinanceController.php';
        $financeController = new FinanceController();
        $financeController->reports();
        break;

    // ----- FINANCE OFFICER CSV EXPORT ROUTE -----
    case 'finance_export':
        require_once 'controllers/FinanceController.php';
        $financeController = new FinanceController();
        $financeController->exportCsv();
        break;

    // ----- LOGOUT & FULL SESSION DESTRUCTION ROUTE -----
    case 'logout':
        // Destroy the session variables
        $_SESSION = array();

        // Destroy the session cookie too
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        // Destroy the session on server side
        session_destroy();

        // Redirect clearly logged out
        header("Location: index.php?page=login&msg=logged_out");
        exit;
        break;

    default:
        // http_response_code(404) tells the browser the page was not found.
        http_response_code(404);
        echo "<h1>404 - Page Not Found</h1>";
        break;
}
