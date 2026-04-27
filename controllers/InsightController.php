<?php
// controllers/InsightController.php

require_once 'models/Insight.php';
require_once 'models/Budget.php';

class InsightController
{
    public function index()
    {
        // Enforce session security
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?page=login");
            exit;
        }

        $db = getConnection();
        $insightModel = new Insight($db);
        $budgetModel = new Budget($db);
        $user_id = $_SESSION['user_id'];

        $currentMonth = (int)date('n');
        $currentYear = (int)date('Y');

        $prevMonthDate = new DateTime('first day of last month');
        $prevMonth = (int)$prevMonthDate->format('n');
        $prevYear = (int)$prevMonthDate->format('Y');

        // 1. STREAK TRACKER
        $streak = $insightModel->getCurrentStreak($user_id);

        // 2. TOP EXPENSE CATEGORIES
        $topExpenses = $insightModel->getTopExpenseCategories($user_id, $currentMonth, $currentYear, 3);
        $biggestExpense = !empty($topExpenses) ? $topExpenses[0] : null;

        $monthlyTotals = $insightModel->getMonthlyTotals($user_id, $currentMonth, $currentYear);
        $totalIncome = $monthlyTotals['total_income'] ?? 0;
        $totalExpense = $monthlyTotals['total_expense'] ?? 0;

        // Convert biggest expense sum to a percentage
        $biggestExpensePercent = 0;
        if ($biggestExpense && $totalExpense > 0) {
            $biggestExpensePercent = round(($biggestExpense['total'] / $totalExpense) * 100);
        }
        $prevMonthlyTotals = $insightModel->getMonthlyTotals($user_id, $prevMonth, $prevYear);
        $prevTotalIncome = $prevMonthlyTotals['total_income'] ?? 0;
        $prevTotalExpense = $prevMonthlyTotals['total_expense'] ?? 0;

        // Calculate % increase/decrease of spent
        $expenseDiffPercent = 0;
        if ($prevTotalExpense > 0) {
            $expenseDiffPercent = round((($totalExpense - $prevTotalExpense) / $prevTotalExpense) * 100);
        }

        // 3. BEST SAVING MONTH
        $bestMonthObj = $insightModel->getBestSavingMonth($user_id);

        // 4. SAVINGS RATE (This Month vs Prev Month)
        $savingsRate = 0;
        if ($totalIncome > 0) {
            $savingsRate = round((($totalIncome - $totalExpense) / $totalIncome) * 100);
        }

        $prevSavingsRate = 0;
        if ($prevTotalIncome > 0) {
            $prevSavingsRate = round((($prevTotalIncome - $prevTotalExpense) / $prevTotalIncome) * 100);
        }
        $savingsRateDiff = $savingsRate - $prevSavingsRate;

        // BUDGET vs SPENDING (for Health score and progress bar)
        $budgetProgress = $budgetModel->getBudgetProgress($user_id, $currentMonth, $currentYear);
        $totalBudgetLimit = 0;
        foreach ($budgetProgress as $b) {
            $totalBudgetLimit += $b['budget_amount'];
        }
        $budgetUsedPercent = 0;
        if ($totalBudgetLimit > 0) {
            $budgetUsedPercent = round(min(100, ($totalExpense / $totalBudgetLimit) * 100));
        }

        // Health Score (0-100) based on savings rate
        $healthScore = 0;
        if ($totalIncome > 0) {
            $healthScore = max(0, min(100, (int)$savingsRate));
        }

        // Actionable Advice Logic
        $smartTip = "";
        $smartTipColor = "text-primary";
        if ($savingsRate < 0) {
            $smartTip = "🚨 **Critical Alert:** Your savings are at $savingsRate%. Let's get back on track! Consider applying the '50/30/20' rule customized for students. Are there high hidden tuition costs or subscriptions dragging you down? Cut non-essentials temporarily.";
            $smartTipColor = "text-danger";
        } elseif ($savingsRate < 10) {
            $smartTip = "⏳ **Yellow Zone:** You are barely saving (" . $savingsRate . "%). Try packing lunch more often or finding free campus events to improve your margin.";
            $smartTipColor = "text-warning";
        } elseif ($savingsRate >= 20) {
            $smartTip = "🏆 **Rockstar Status!** A $savingsRate% savings rate is incredible. Keep prioritizing your long-term goals and maybe look into a student high-yield savings account.";
            $smartTipColor = "text-success";
        } else {
            $smartTip = "👍 **Steady Pace:** You're saving moderately. Review the Top Categories list below to see if there's a small area you can optimize further.";
            $smartTipColor = "text-primary";
        }

        // 5. SPENDING PATTERN DETECTION
        $categoryComparisons = $insightModel->getCategoryComparison($user_id, $currentMonth, $currentYear, $prevMonth, $prevYear);

        // Find the category with biggest percentage increase
        $patternInsight = null;
        $highestIncreasePercent = 0;

        foreach ($categoryComparisons as $cat) {
            $curr = (int)$cat['current_total'];
            $prev = (int)$cat['prev_total'];

            if ($prev > 0 && $curr > $prev) {
                $increasePercent = round((($curr - $prev) / $prev) * 100);
                if ($increasePercent > $highestIncreasePercent) {
                    $highestIncreasePercent = $increasePercent;
                    $patternInsight = [
                        'name' => $cat['name'],
                        'percent' => $increasePercent
                    ];
                }
            } elseif ($prev == 0 && $curr > 0) {
                // New spending completely
                if ($highestIncreasePercent == 0) {
                    $patternInsight = [
                        'name' => $cat['name'],
                        'is_new' => true
                    ];
                }
            }
        }

        require 'views/insights.php';
    }
}
