<?php
// controllers/ReportController.php
// Prepares data for the reports view and passes it to javascript for Chart.js

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

        $month = (int) date('n');
        $year = (int) date('Y');

        // Fetch the aggregated data
        $categoryData = $reportModel->getSpendingByCategory($user_id, $month, $year);
        $dailyData = $reportModel->getDailySpending($user_id, $month, $year);

        // We need to convert PHP arrays into JSON strings so that JavaScript (Chart.js) can read them!
        // json_encode() magically takes a PHP array and formats it as a JavaScript array/object.
        $jsonCategoryData = json_encode($categoryData);
        $jsonDailyData = json_encode($dailyData);

        require 'views/reports/index.php';
    }
}
