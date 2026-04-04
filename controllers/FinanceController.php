<?php
// controllers/FinanceController.php

require_once 'models/Report.php';

class FinanceController
{
    private $reportModel;

    public function __construct()
    {
        global $db;
        $db = getConnection();
        $this->reportModel = new Report($db);
    }

    private function checkAccess()
    {
        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'finance_officer' && $_SESSION['role'] !== 'admin')) {
            header("Location: index.php?page=dashboard");
            exit();
        }
    }

    public function reports()
    {
        $this->checkAccess();

        $transactions = $this->reportModel->getAllSystemTransactions();

        require_once 'views/finance/reports.php';
    }

    public function exportCsv()
    {
        $this->checkAccess();

        $transactions = $this->reportModel->getAllSystemTransactions();

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="system_financial_report_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');

        // Write header row
        fputcsv($output, ['Transaction ID', 'User Name', 'User Email', 'Date', 'Type', 'Category', 'Description', 'Amount']);

        // Write data rows
        foreach ($transactions as $t) {
            fputcsv($output, [
                $t['transaction_id'],
                $t['user_name'],
                $t['user_email'],
                $t['transaction_date'],
                ucfirst($t['type']),
                $t['category_name'] ?? 'N/A',
                $t['description'],
                number_format($t['amount'], 2)
            ]);
        }

        fclose($output);
        exit();
    }
}
