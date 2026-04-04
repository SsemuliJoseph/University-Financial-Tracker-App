<?php
// controllers/ReportController.php
// Prepares data for the reports view and handles AJAX JSON responses

require_once 'models/Report.php';

class ReportController
{
    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?page=login");
            exit;
        }

        $db = getConnection();
        $reportModel = new Report($db);
        $user_id = $_SESSION['user_id'];

        // Get requested month/year or default to current
        $requestedMonth = isset($_GET['month']) ? $_GET['month'] : date('Y-m');

        // Parse "YYYY-MM" string
        $parts = explode('-', $requestedMonth);
        $year = (int)$parts[0];
        $month = (int)$parts[1];

        // Ensure valid parsing
        if (!$year || !$month) {
            $year = (int) date('Y');
            $month = (int) date('n');
        }

        // Calculate previous month securely
        $prevDate = date('Y-m', strtotime($year . '-' . sprintf('%02d', $month) . '-01 -1 month'));
        $prevParts = explode('-', $prevDate);
        $prevYear = (int)$prevParts[0];
        $prevMonth = (int)$prevParts[1];

        // 1. Fetch current month category spending
        $categoryData = $reportModel->getSpendingByCategory($user_id, $month, $year);

        // 2. Fetch previous month category spending for comparisons
        $prevCategoryData = $reportModel->getSpendingByCategory($user_id, $prevMonth, $prevYear);

        // Convert previous data to simple associative array: ['Food' => 5000]
        $prevCatMap = [];
        foreach ($prevCategoryData as $pc) {
            $prevCatMap[$pc['category_name']] = (float) $pc['total'];
        }

        // 3. Fetch 6 month trend
        $sixMonthTrend = $reportModel->getSixMonthTrend($user_id, $month, $year);

        // 4. Fetch the Totals for Health Score
        $totals = $reportModel->getMonthlyTotals($user_id, $month, $year);

        // Build health score = ((income - expenses) / income) * 100
        $healthScore = 0;
        $totalExpense = $totals['expense'];
        $totalIncome = $totals['income'];
        if ($totalIncome > 0) {
            $score = (($totalIncome - $totalExpense) / $totalIncome) * 100;
            $healthScore = max(0, min(100, (int)$score)); // Clamp between 0 and 100
        }

        // Map Category data to include % distributions and previous month diff
        $richCategoryData = [];
        foreach ($categoryData as $cat) {
            $amount = (float) $cat['total'];
            $pct = $totalExpense > 0 ? ($amount / $totalExpense) * 100 : 0;

            // Check previous month
            $prevAmount = isset($prevCatMap[$cat['category_name']]) ? $prevCatMap[$cat['category_name']] : 0;

            // Difference > 0 means we spent more this month (Bad)
            $diff = $amount - $prevAmount;

            $richCategoryData[] = [
                'name' => escapeshellcmd($cat['category_name']), // Escaping for safety
                'total' => $amount,
                'pct' => round($pct, 1),
                'prevAmount' => $prevAmount,
                'diff' => $diff
            ];
        }

        // If AJAX request is detected, output JSON directly
        if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
            header('Content-Type: application/json');
            echo json_encode([
                'categories' => $richCategoryData,
                'trend' => $sixMonthTrend,
                'totals' => $totals,
                'healthScore' => $healthScore
            ]);
            exit;
        }

        // Standard load
        $jsonCategoryData = json_encode($richCategoryData);
        $jsonTrendData = json_encode($sixMonthTrend);

        require 'views/reports/index.php';
    }

    // UPGRADE 5: Export Summary Table to CSV
    public function export()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?page=login");
            exit;
        }

        $db = getConnection();
        $reportModel = new Report($db);
        $user_id = $_SESSION['user_id'];

        $requestedMonth = isset($_GET['month']) ? $_GET['month'] : date('Y-m');
        $parts = explode('-', $requestedMonth);
        $year = (int)$parts[0];
        $month = (int)$parts[1];
        if (!$year || !$month) {
            $year = (int) date('Y');
            $month = (int) date('n');
        }

        $prevDate = date('Y-m', strtotime($year . '-' . sprintf('%02d', $month) . '-01 -1 month'));
        $prevParts = explode('-', $prevDate);
        $prevYear = (int)$prevParts[0];
        $prevMonth = (int)$prevParts[1];

        $categoryData = $reportModel->getSpendingByCategory($user_id, $month, $year);
        $prevCategoryData = $reportModel->getSpendingByCategory($user_id, $prevMonth, $prevYear);

        $prevCatMap = [];
        foreach ($prevCategoryData as $pc) {
            $prevCatMap[$pc['category_name']] = (float) $pc['total'];
        }

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="report_summary_' . $requestedMonth . '.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Category', 'Amount (UGX)', 'Previous Month (UGX)', 'Difference (UGX)']);

        $totalSpent = 0;
        foreach ($categoryData as $cat) {
            $amount = (float) $cat['total'];
            $totalSpent += $amount;
            $prevAmount = isset($prevCatMap[$cat['category_name']]) ? $prevCatMap[$cat['category_name']] : 0;
            $diff = $amount - $prevAmount;

            fputcsv($output, [
                $cat['category_name'],
                $amount,
                $prevAmount,
                $diff
            ]);
        }

        fputcsv($output, []); // Empty line
        fputcsv($output, ['TOTAL SPENT', $totalSpent]);
        fclose($output);
        exit;
    }
}
