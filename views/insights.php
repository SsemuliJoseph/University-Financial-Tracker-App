<?php require 'views/layout/header.php'; ?>

<!-- UPGRADE 8: Smart Insights page -->

<div class="d-flex justify-content-between align-items-center mb-4 slide-in-top">
    <div>
        <h2 class="mb-0">Financial Insights</h2>
        <p class="text-muted small mb-0 mt-1">Smart analysis based on your recording habits.</p>
    </div>
</div>

<div class="row g-4 mb-5">

    <!-- 1. STREAK TRACKER -->
    <div class="col-md-6 col-lg-4 slide-in-top" style="animation-delay: 0.1s;">
        <div class="card h-100 border-0 shadow-sm rounded-4 hover-lift">
            <div class="card-body p-4 text-center d-flex flex-column justify-content-center">
                <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 64px; height: 64px;">
                    <i class="bi bi-fire fs-2"></i>
                </div>
                <h6 class="text-uppercase fw-bold text-muted tracking-wider mb-2">Record Streak</h6>
                <?php if ($streak > 0): ?>
                    <h3 class="fw-bold mb-2"><?= $streak ?> Days</h3>
                    <p class="small text-muted mb-0">You have recorded transactions for <?= $streak ?> days in a row. Keep it up!</p>
                <?php else: ?>
                    <h3 class="fw-bold mb-2">0 Days</h3>
                    <p class="small text-muted mb-0">Record a transaction today to start a new streak!</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- 2. BIGGEST EXPENSE CATEGORY -->
    <div class="col-md-6 col-lg-4 slide-in-top" style="animation-delay: 0.2s;">
        <div class="card h-100 border-0 shadow-sm rounded-4 hover-lift">
            <div class="card-body p-4 text-center d-flex flex-column justify-content-center">
                <div class="rounded-circle bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 64px; height: 64px;">
                    <i class="bi bi-pie-chart fs-2"></i>
                </div>
                <h6 class="text-uppercase fw-bold text-muted tracking-wider mb-2">Top Expense</h6>
                <?php if ($biggestExpense): ?>
                    <h3 class="fw-bold mb-2 text-danger"><?= htmlspecialchars($biggestExpense['name']) ?></h3>
                    <p class="small text-muted mb-0">
                        <?= htmlspecialchars($biggestExpense['name']) ?> is your biggest expense — it takes <strong><?= $biggestExpensePercent ?>%</strong> of your monthly spending.
                    </p>
                <?php else: ?>
                    <h3 class="fw-bold mb-2 text-muted">None</h3>
                    <p class="small text-muted mb-0">No expenses recorded this month yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- 3. SAVINGS RATE -->
    <div class="col-md-6 col-lg-4 slide-in-top" style="animation-delay: 0.3s;">
        <div class="card h-100 border-0 shadow-sm rounded-4 hover-lift">
            <div class="card-body p-4 text-center d-flex flex-column justify-content-center">
                <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 64px; height: 64px;">
                    <i class="bi bi-piggy-bank fs-2"></i>
                </div>
                <h6 class="text-uppercase fw-bold text-muted tracking-wider mb-2">Savings Rate</h6>
                <?php if ($totalIncome > 0): ?>
                    <h3 class="fw-bold mb-2 <?= $savingsRate >= 20 ? 'text-success' : ($savingsRate > 0 ? 'text-warning' : 'text-danger') ?>">
                        <?= $savingsRate ?>%
                    </h3>
                    <p class="small text-muted mb-0">
                        <?php if ($savingsRate >= 20): ?>
                            Good savings rate! You are managing your money well this month.
                        <?php elseif ($savingsRate > 0): ?>
                            You're saving a little, but try to aim for 20% or more.
                        <?php else: ?>
                            You spent more than you earned this month. Time to review your budget!
                        <?php endif; ?>
                    </p>
                <?php else: ?>
                    <h3 class="fw-bold mb-2 text-muted">N/A</h3>
                    <p class="small text-muted mb-0">Record some income to see your savings rate.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- 4. PATTERN DETECTION -->
    <div class="col-md-6 slide-in-top" style="animation-delay: 0.4s;">
        <div class="card h-100 border-0 shadow-sm rounded-4 hover-lift">
            <div class="card-body p-4 d-flex align-items-center">
                <div class="rounded-circle bg-info bg-opacity-10 text-info d-flex align-items-center justify-content-center me-4" style="width: 64px; height: 64px; flex-shrink:0;">
                    <i class="bi bi-graph-up-arrow fs-2"></i>
                </div>
                <div>
                    <h6 class="text-uppercase fw-bold text-muted tracking-wider mb-1">Spending Pattern Alert</h6>
                    <?php if ($patternInsight): ?>
                        <?php if (isset($patternInsight['is_new'])): ?>
                            <p class="mb-0 fw-medium">You started spending on <strong><?= htmlspecialchars($patternInsight['name']) ?></strong> this month, which you didn't do last month.</p>
                        <?php else: ?>
                            <p class="mb-0 fw-medium">Your <strong><?= htmlspecialchars($patternInsight['name']) ?></strong> spending increased by <span class="text-danger fw-bold"><?= $patternInsight['percent'] ?>%</span> compared to last month.</p>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="mb-0 text-muted">No significant increases in category spending detected this month.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- 5. BEST SAVING MONTH -->
    <div class="col-md-6 slide-in-top" style="animation-delay: 0.5s;">
        <div class="card h-100 border-0 shadow-sm rounded-4 hover-lift">
            <div class="card-body p-4 d-flex align-items-center">
                <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center me-4" style="width: 64px; height: 64px; flex-shrink:0;">
                    <i class="bi bi-trophy fs-2"></i>
                </div>
                <div>
                    <h6 class="text-uppercase fw-bold text-muted tracking-wider mb-1">Best Month All-Time</h6>
                    <?php if ($bestMonthObj && $bestMonthObj['savings'] > 0): ?>
                        <?php
                        $bestDate = new DateTime($bestMonthObj['month_str'] . '-01');
                        ?>
                        <h4 class="mb-1 text-primary fw-bold"><?= $bestDate->format('F Y') ?></h4>
                        <p class="mb-0 small text-muted">You saved <strong>UGX <?= number_format($bestMonthObj['savings']) ?></strong>! That's your highest record.</p>
                    <?php else: ?>
                        <p class="mb-0 text-muted">No positive savings months recorded yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- EMAIL PREVIEW SECTION -->
<div class="card border-0 shadow-sm rounded-4 slide-in-top hover-lift mb-5" style="animation-delay: 0.6s;">
    <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0"><i class="bi bi-envelope-paper me-2 text-primary"></i> Monthly Financial Summary (Email Template)</h6>
        <button class="btn btn-sm btn-outline-primary shadow-sm" onclick="copyEmailTemplate()">
            <i class="bi bi-copy"></i> Copy Content
        </button>
    </div>
    <div class="card-body p-4 bg-light rounded-bottom-4">

        <div id="emailTemplate" class="p-4 bg-white border rounded shadow-sm" style="max-width: 600px; margin: 0 auto; font-family: sans-serif; color: #333;">
            <div style="text-align: center; margin-bottom: 30px;">
                <h2 style="color: #0f172a; margin-bottom: 5px;">UFTS Monthly Review</h2>
                <p style="color: #64748b; font-size: 14px; margin-top: 0;"><?= date('F Y') ?> Summary for <?= htmlspecialchars($_SESSION['name']) ?></p>
            </div>

            <div style="background-color: #f8fafc; padding: 20px; border-radius: 8px; margin-bottom: 25px;">
                <!-- Health Score & Budget Progress -->
                <div style="text-align: center; margin-bottom: 20px; padding: 15px; background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    <h3 style="margin: 0 0 10px 0; color: #475569; font-size: 14px; text-transform: uppercase;">Financial Health Score</h3>
                    <div style="font-size: 32px; font-weight: bold; color: <?= ($healthScore >= 50) ? '#22c55e' : (($healthScore > 0) ? '#eab308' : '#ef4444') ?>;">
                        <?= $healthScore ?> / 100
                    </div>
                </div>

                <div style="margin-bottom: 25px;">
                    <div style="display: flex; justify-content: space-between; font-size: 12px; font-weight: bold; color: #64748b; margin-bottom: 5px;">
                        <span>Spending vs Monthly Category Budgets</span>
                        <span><?= $budgetUsedPercent ?>% Used</span>
                    </div>
                    <div style="height: 10px; background-color: #e2e8f0; border-radius: 5px; overflow: hidden;">
                        <div style="height: 100%; width: <?= $budgetUsedPercent ?>%; background-color: <?= ($budgetUsedPercent > 90) ? '#ef4444' : (($budgetUsedPercent > 75) ? '#eab308' : '#22c55e') ?>;"></div>
                    </div>
                </div>

                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 10px 0; border-bottom: 1px solid #e2e8f0; color: #64748b;">Total Income:</td>
                        <td style="padding: 10px 0; border-bottom: 1px solid #e2e8f0; text-align: right; color: #0f172a; font-weight: bold;">UGX <?= number_format($totalIncome) ?></td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 0; border-bottom: 1px solid #e2e8f0; color: #64748b;">Total Spent:</td>
                        <td style="padding: 10px 0; border-bottom: 1px solid #e2e8f0; text-align: right; color: #0f172a; font-weight: bold;">UGX <?= number_format($totalExpense) ?></td>
                    </tr>
                    <tr>
                        <td style="padding: 15px 0 5px 0; color: #0f172a; font-weight: bold;">Net Savings:</td>
                        <td style="padding: 15px 0 5px 0; text-align: right; font-weight: 900; font-size: 20px; color: <?= ($totalIncome - $totalExpense >= 0) ? '#22c55e' : '#ef4444' ?>;">
                            UGX <?= number_format($totalIncome - $totalExpense) ?>
                        </td>
                    </tr>
                </table>
            </div>

            <h3 style="color: #0f172a; font-size: 16px; border-bottom: 2px solid #e2e8f0; padding-bottom: 8px; margin-bottom: 15px;">Comparative Insights</h3>
            <div style="display: flex; gap: 15px; margin-bottom: 25px;">
                <div style="flex: 1; background: #fff; border: 1px solid #e2e8f0; padding: 15px; border-radius: 8px; text-align: center;">
                    <div style="font-size: 12px; color: #64748b; margin-bottom: 5px;">Total Spent vs Prev Month</div>
                    <div style="font-size: 18px; font-weight: bold; color: <?= ($expenseDiffPercent > 0) ? '#ef4444' : '#22c55e' ?>;">
                        <?= ($expenseDiffPercent > 0) ? '▲ +' : '▼ ' ?><?= $expenseDiffPercent ?>%
                    </div>
                </div>
                <div style="flex: 1; background: #fff; border: 1px solid #e2e8f0; padding: 15px; border-radius: 8px; text-align: center;">
                    <div style="font-size: 12px; color: #64748b; margin-bottom: 5px;">Savings Rate vs Prev Month</div>
                    <div style="font-size: 18px; font-weight: bold; color: <?= ($savingsRateDiff > 0) ? '#22c55e' : '#ef4444' ?>;">
                        <?= ($savingsRateDiff > 0) ? '▲ +' : '▼ ' ?><?= $savingsRateDiff ?>%
                    </div>
                </div>
            </div>

            <h3 style="color: #0f172a; font-size: 16px; border-bottom: 2px solid #e2e8f0; padding-bottom: 8px; margin-bottom: 15px;">Top 3 Expense Categories</h3>
            <div style="margin-bottom: 25px;">
                <?php if (!empty($topExpenses)): ?>
                    <?php
                    $icons = ['💰', '🛒', '🍔', '🚗', '📚'];
                    $index = 0;
                    foreach ($topExpenses as $expense):
                        // Assign fallback icon
                        $icon = $icons[$index % count($icons)];
                        $index++;
                    ?>
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 10px; background: #f8fafc; border-radius: 6px; margin-bottom: 8px;">
                            <span style="font-weight: bold; color: #334155;"><?= $icon ?> <?= htmlspecialchars($expense['name']) ?></span>
                            <span style="color: #ef4444; font-weight: bold;">UGX <?= number_format($expense['total']) ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: #64748b; font-size: 14px;">No expenses recorded this month.</p>
                <?php endif; ?>
            </div>

            <h3 style="color: #0f172a; font-size: 16px; border-bottom: 2px solid #e2e8f0; padding-bottom: 8px; margin-bottom: 15px;">Actionable Advice</h3>
            <div style="padding: 15px; background: #eff6ff; border-left: 4px solid #3b82f6; border-radius: 4px; margin-bottom: 20px;">
                <p style="margin: 0; line-height: 1.5; font-size: 14px; color: #1e293b;">
                    <?= preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', htmlspecialchars($smartTip)) ?>
                </p>
            </div>

            <?php if ($patternInsight && !isset($patternInsight['is_new'])): ?>
                <div style="padding: 15px; background: #fff1f2; border-left: 4px solid #f43f5e; border-radius: 4px;">
                    <p style="margin: 0; line-height: 1.5; font-size: 14px; color: #881337;">
                        <strong>Pattern Alert:</strong> Spending on <?= htmlspecialchars($patternInsight['name']) ?> increased by <?= $patternInsight['percent'] ?>% compared to last month.
                    </p>
                </div>
            <?php endif; ?>

            <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e2e8f0; color: #94a3b8; font-size: 12px;">
                Generated automatically by University Finance Tracker System
            </div>
        </div>

    </div>
</div>

<style>
    .slide-in-top {
        animation: slideInTop 0.5s ease forwards;
        opacity: 0;
    }

    @keyframes slideInTop {
        from {
            transform: translateY(20px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
</style>

<script>
    function showCustomToast(title, message, type) {
        let toastContainer = document.getElementById('toastContainer');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toastContainer';
            toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            toastContainer.style.zIndex = '1055';
            document.body.appendChild(toastContainer);
        }

        const toastEl = document.createElement('div');
        toastEl.className = `toast align-items-center text-bg-${type} border-0 shadow`;
        toastEl.setAttribute('role', 'alert');
        toastEl.setAttribute('aria-live', 'assertive');
        toastEl.setAttribute('aria-atomic', 'true');

        toastEl.innerHTML = `
      <div class="d-flex">
        <div class="toast-body fw-medium">
          <i class="bi ${type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill'} me-2"></i> ${message}
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    `;

        toastContainer.appendChild(toastEl);
        const bsToast = new bootstrap.Toast(toastEl, {
            delay: 3000
        });
        bsToast.show();

        toastEl.addEventListener('hidden.bs.toast', () => {
            toastEl.remove();
        });
    }

    function copyEmailTemplate() {
        const emailHtml = document.getElementById('emailTemplate').innerHTML;

        const blob = new Blob([emailHtml], {
            type: 'text/html'
        });
        const clipboardItem = new ClipboardItem({
            'text/html': blob
        });

        navigator.clipboard.write([clipboardItem]).then(function() {
            showCustomToast('Copied', 'Email template copied to clipboard!', 'success');
        }).catch(function(err) {
            console.error('Failed to copy: ', err);
            showCustomToast('Error', 'Could not copy to clipboard.', 'danger');
        });
    }
</script>

<?php require 'views/layout/footer.php'; ?>