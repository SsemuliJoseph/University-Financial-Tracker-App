<?php require_once 'views/layout/header.php'; ?>
<!-- Chart.js CDN for Overview Chart -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="row g-4 mb-4">
    <!-- 1. Total Balance Card -->
    <div class="col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm hover-lift h-100 position-relative overflow-hidden">
            <div class="card-body skeleton p-4 d-flex flex-column justify-content-center">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-body-secondary fw-semibold">Total Balance</span>
                    <div class="bg-primary bg-opacity-10 text-primary rounded px-2 py-1">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-0 text-primary"><?= htmlspecialchars($_SESSION['currency'] ?? 'UGX') ?> <span class="count-up" data-value="<?= (float)$balance ?>">0</span></h3>
            </div>
            <!-- Decorative accent line at bottom -->
            <div class="position-absolute bottom-0 start-0 w-100 bg-primary" style="height: 4px;"></div>
        </div>
    </div>

    <!-- 2. This Month's Income Card -->
    <div class="col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm hover-lift h-100 position-relative overflow-hidden">
            <div class="card-body skeleton p-4 d-flex flex-column justify-content-center">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-body-secondary fw-semibold">Monthly Income</span>
                    <div class="bg-success bg-opacity-10 text-success rounded p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="bi bi-arrow-down-left"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-0 text-success"><?= htmlspecialchars($_SESSION['currency'] ?? 'UGX') ?> <span class="count-up" data-value="<?= (float)$monthlyIncome ?>">0</span></h3>
            </div>
            <div class="position-absolute bottom-0 start-0 w-100 bg-success" style="height: 4px;"></div>
        </div>
    </div>

    <!-- 3. This Month's Expenses Card -->
    <div class="col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm hover-lift h-100 position-relative overflow-hidden">
            <div class="card-body skeleton p-4 d-flex flex-column justify-content-center">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-body-secondary fw-semibold">Monthly Expenses</span>
                    <div class="bg-danger bg-opacity-10 text-danger rounded p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="bi bi-arrow-up-right"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-0 text-danger"><?= htmlspecialchars($_SESSION['currency'] ?? 'UGX') ?> <span class="count-up" data-value="<?= (float)$monthlyExpense ?>">0</span></h3>
            </div>
            <div class="position-absolute bottom-0 start-0 w-100 bg-danger" style="height: 4px;"></div>
        </div>
    </div>

    <!-- 4. Budget Used % Card -->
    <div class="col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm hover-lift h-100 position-relative overflow-hidden">
            <div class="card-body skeleton p-4 d-flex flex-column justify-content-center">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-body-secondary fw-semibold">Budget Used</span>
                    <div class="bg-warning bg-opacity-10 text-warning rounded px-2 py-1">
                        <i class="bi bi-speedometer2"></i>
                    </div>
                </div>
                <div class="d-flex align-items-end gap-2 mb-2">
                    <h3 class="fw-bold mb-0 text-warning"><span class="count-up" data-value="<?= (float)$budgetUsedPercent ?>">0</span>%</h3>
                </div>
                <div class="progress" style="height: 6px;">
                    <!-- The width calculates dynamically -->
                    <div class="progress-bar bg-warning" role="progressbar" style="width: <?= htmlspecialchars($budgetUsedPercent) ?>%;" aria-valuenow="<?= htmlspecialchars($budgetUsedPercent) ?>" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
            <div class="position-absolute bottom-0 start-0 w-100 bg-warning" style="height: 4px;"></div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column (Span 8) -->
    <div class="col-lg-8">

        <!-- Spending Overview Bar Chart -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-0 pt-4 pb-0">
                <h5 class="fw-bold mb-0">6-Month Spending Overview</h5>
            </div>
            <div class="card-body">
                <canvas id="overviewChart" height="100"></canvas>
            </div>
        </div>

        <!-- Budget Health Section -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pt-4 pb-0">
                <h5 class="fw-bold mb-0">Budget Health</h5>
                <p class="text-body-secondary small mb-0 mt-1">Categories with transactions this month</p>
            </div>
            <div class="card-body pt-3">
                <?php if (empty($activeBudgets)): ?>
                    <p class="text-muted text-center py-3">No expenses recorded against active budgets this month.</p>
                <?php else: ?>
                    <div class="d-flex flex-column gap-3">
                        <?php foreach ($activeBudgets as $b): ?>
                            <?php
                            $isDanger = $b['percent'] >= 80;
                            $barColor = $isDanger ? 'bg-danger' : 'bg-success';
                            $textColor = $isDanger ? 'text-danger' : 'text-success';
                            ?>
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-semibold"><?= htmlspecialchars($b['category_name']) ?></span>
                                    <span class="small <?= $textColor ?> fw-bold"><?= $b['percent'] ?>% (<?= htmlspecialchars($_SESSION['currency'] ?? 'UGX') ?> <?= number_format($b['spent_amount'], 2) ?>)</span>
                                </div>
                                <div class="progress bg-secondary bg-opacity-10" style="height: 8px;">
                                    <div class="progress-bar <?= $barColor ?>" role="progressbar" style="width: <?= $b['percent'] ?>%" aria-valuenow="<?= $b['percent'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <!-- Right Column (Span 4) - Activity Feed -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">Activity Feed</h5>
                <a href="index.php?page=transactions" class="btn btn-sm btn-link text-decoration-none">View All</a>
            </div>
            <div class="card-body p-0 mt-3">
                <?php if (empty($recent_transactions)): ?>
                    <p class="text-center text-muted p-4">No recent activity.</p>
                <?php else: ?>
                    <div class="list-group list-group-flush border-0">
                        <?php foreach ($recent_transactions as $t): ?>
                            <?php
                            $isIncome = $t['type'] === 'income';
                            $borderClass = $isIncome ? 'border-success' : 'border-danger';
                            $textClass = $isIncome ? 'text-success' : 'text-danger';
                            $iconClass = $isIncome ? 'bi-arrow-down-circle-fill' : 'bi-arrow-up-circle-fill';
                            $amountPrefix = $isIncome ? '+' : '-';
                            ?>
                            <!-- Transaction Row -->
                            <div class="list-group-item list-group-item-action d-flex align-items-center p-3 border-0 border-start border-4 <?= $borderClass ?> bg-body mb-1 rounded ms-2 me-2 shadow-sm">

                                <!-- Icon Circle -->
                                <div class="<?= $isIncome ? 'bg-success' : 'bg-danger' ?> bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; flex-shrink: 0;">
                                    <i class="bi <?= $iconClass ?> <?= $textClass ?> fs-4"></i>
                                </div>

                                <!-- Center Text -->
                                <div class="flex-grow-1 overflow-hidden" style="min-width: 0;">
                                    <h6 class="mb-0 text-truncate fw-semibold"><?= htmlspecialchars($t['description']) ?></h6>
                                    <small class="text-body-secondary d-block text-truncate">
                                        <?= htmlspecialchars($t['category_name'] ?? 'Uncategorized') ?> •
                                        <?= date('M j', strtotime($t['transaction_date'])) ?>
                                    </small>
                                </div>

                                <!-- Amount -->
                                <div class="ms-2 text-end">
                                    <span class="fw-bold <?= $textClass ?>">
                                        <?= $amountPrefix ?><?= htmlspecialchars($_SESSION['currency'] ?? 'UGX') ?> <?= number_format($t['amount']) ?>
                                    </span>
                                </div>

                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Animations and Charts -->
<script>
    document.addEventListener("DOMContentLoaded", function() {

        // 1. Count-Up Animation
        const animatedElements = document.querySelectorAll('.count-up');

        animatedElements.forEach(el => {
            const targetValue = parseFloat(el.getAttribute('data-value'));
            const duration = 1000; // 1 second
            const frames = 60; // 60 updates per second
            const stepTime = duration / frames;

            let currentStep = 0;

            const countInterval = setInterval(() => {
                currentStep++;
                // Calculate progress (ease-out curve)
                const progress = currentStep / frames;
                const easeOutProgress = 1 - Math.pow(1 - progress, 3);

                const currentValue = targetValue * easeOutProgress;

                // Format number with commas
                el.textContent = currentValue.toLocaleString('en-US', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: (targetValue % 1 !== 0) ? 2 : 0
                });

                if (currentStep >= frames) {
                    clearInterval(countInterval);
                    // Ensure exact final number is reached
                    el.textContent = targetValue.toLocaleString('en-US', {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: (targetValue % 1 !== 0) ? 2 : 0
                    });
                }
            }, stepTime);
        });

        // 2. Chart.js 6-Month Overview Initialization
        // Fetch data generated by PHP safely encoded
        const labels = <?= json_encode($chartLabels) ?>;
        const incomeData = <?= json_encode($chartIncome) ?>;
        const expenseData = <?= json_encode($chartExpense) ?>;

        // Get current theme from HTML tag to adjust text colors
        const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
        const textColor = isDark ? '#f8f9fa' : '#212529';
        const gridColor = isDark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';

        const ctx = document.getElementById('overviewChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                        label: 'Income',
                        data: incomeData,
                        backgroundColor: '#22c55e',
                        borderRadius: 4,
                        barPercentage: 0.8,
                        categoryPercentage: 0.8
                    },
                    {
                        label: 'Expenses',
                        data: expenseData,
                        backgroundColor: '#ef4444',
                        borderRadius: 4,
                        barPercentage: 0.8,
                        categoryPercentage: 0.8
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            color: textColor,
                            font: {
                                family: "'Segoe UI', Roboto, sans-serif"
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let val = Number(context.raw).toLocaleString();
                                return ' <?= htmlspecialchars($_SESSION['currency'] ?? 'UGX') ?> ' + val;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: textColor
                        }
                    },
                    y: {
                        grid: {
                            color: gridColor
                        },
                        ticks: {
                            color: textColor
                        }
                    }
                }
            }
        });
    });
</script>

<?php require_once 'views/layout/footer.php'; ?>