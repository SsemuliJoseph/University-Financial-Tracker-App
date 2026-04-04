<?php require 'views/layout/header.php'; ?>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- UPGRADE 5: Toolbar Row -->
<div class="d-flex justify-content-between align-items-center mb-4 slide-in-top">
    <h2 class="mb-0">Financial Reports</h2>
    <div class="d-flex gap-2">
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm shadow-sm hover-lift d-none d-md-inline-block">
            <i class="bi bi-printer"></i> Print Report
        </button>
        <a id="exportBtn" href="index.php?page=reports_export&month=<?= isset($_GET['month']) ? $_GET['month'] : date('Y-m') ?>" class="btn btn-success btn-sm shadow-sm hover-lift">
            <i class="bi bi-file-earmark-excel"></i> Export CSV
        </a>
    </div>
</div>

<!-- UPGRADE 5: Month/Year Selector -->
<div class="card border-0 shadow-sm rounded-4 mb-4 slide-in-top">
    <div class="card-body p-3">
        <form class="row g-3 align-items-center m-0">
            <div class="col-auto">
                <label for="monthSelector" class="col-form-label fw-bold text-muted mb-0">Select Period:</label>
            </div>
            <div class="col-auto">
                <input type="month" id="monthSelector" class="form-control form-control-sm bg-light"
                    value="<?= isset($_GET['month']) ? $_GET['month'] : date('Y-m') ?>">
            </div>
            <!-- Loading Indicator for AJAX -->
            <div class="col-auto d-none" id="loadingIndicator">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <span class="ms-2 small text-muted">Refreshing data...</span>
            </div>
        </form>
    </div>
</div>

<!-- Main Content Grid -->
<div class="row g-4" id="reportContentArea">

    <!-- Health Score (LEFT) -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 hover-lift">
            <div class="card-body text-center py-5 d-flex flex-column justify-content-center">
                <h6 class="text-muted text-uppercase tracking-wider fw-bold mb-4">Financial Health Score</h6>

                <!-- Circular Progress Score -->
                <div class="position-relative d-inline-block mx-auto mb-3" style="width: 150px; height: 150px;">
                    <svg class="w-100 h-100" viewBox="0 0 36 36">
                        <!-- Background Circle -->
                        <path class="text-light" stroke-width="3" stroke="currentColor" fill="none"
                            d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        <!-- Colored Progress Circle (Updated by JS) -->
                        <path id="healthCircle" class="text-success" stroke-dasharray="<?= $healthScore ?>, 100" stroke-width="3" stroke-linecap="round" stroke="currentColor" fill="none"
                            d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                    </svg>
                    <div class="position-absolute top-50 start-50 translate-middle text-center w-100">
                        <h2 class="mb-0 fw-bold" id="healthScoreText"><?= $healthScore ?>%</h2>
                    </div>
                </div>

                <p class="small text-muted px-3" id="healthMessage">
                    <?php if ($healthScore >= 50): ?>
                        Excellent! You are saving a healthy amount of your income.
                    <?php elseif ($healthScore > 0): ?>
                        Good. You have a positive cash flow.
                    <?php else: ?>
                        Warning: You are spending more than you earn.
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Doughnut Category Chart (MIDDLE) -->
    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-4 h-100 hover-lift">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h6 class="text-muted fw-bold mb-0">Expense Distribution</h6>
            </div>
            <div class="card-body position-relative d-flex justify-content-center align-items-center" style="min-height: 300px;">
                <!-- The centered text via CSS and Chart.js plugin -->
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>

    <!-- 6 Month Trend Bar Chart (FULL WIDTH) -->
    <div class="col-12 mt-4">
        <div class="card border-0 shadow-sm rounded-4 hover-lift">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h6 class="text-muted fw-bold mb-0">6-Month Trend: Income vs. Expenses</h6>
            </div>
            <div class="card-body">
                <canvas id="trendChart" style="max-height: 300px;"></canvas>
            </div>
        </div>
    </div>

    <!-- Summary Table (FULL WIDTH) -->
    <div class="col-12 mt-4">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden hover-lift mb-5">
            <div class="card-header bg-white border-bottom pt-4 pb-3">
                <h6 class="text-muted fw-bold mb-0">Category Breakdown</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="summaryTable">
                    <thead class="table-light text-muted small">
                        <tr>
                            <th>Category</th>
                            <th class="text-end">Total Spent (UGX)</th>
                            <th class="text-center">% of Total</th>
                            <th class="text-center">vs Last Month</th>
                        </tr>
                    </thead>
                    <tbody id="summaryTableBody">
                        <!-- Populated by PHP initially, updated by JS JS -->
                        <?php if (empty($richCategoryData)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">No expenses recorded this month.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($richCategoryData as $cat): ?>
                                <tr>
                                    <td class="fw-medium">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px;">
                                                <i class="bi bi-tag-fill small"></i>
                                            </div>
                                            <?= htmlspecialchars($cat['name']) ?>
                                        </div>
                                    </td>
                                    <td class="text-end fw-bold text-danger">
                                        <?= number_format($cat['total']) ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <span class="me-2 text-muted small" style="width: 30px;"><?= $cat['pct'] ?>%</span>
                                            <div class="progress flex-grow-1" style="height: 6px; max-width: 100px;">
                                                <div class="progress-bar bg-danger" style="width: <?= $cat['pct'] ?>%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($cat['diff'] > 0): ?>
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill">
                                                <i class="bi bi-arrow-up-right"></i> +<?= number_format($cat['diff']) ?>
                                            </span>
                                        <?php elseif ($cat['diff'] < 0): ?>
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill">
                                                <i class="bi bi-arrow-down-right"></i> <?= number_format($cat['diff']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill">
                                                <i class="bi bi-dash"></i> Unchanged
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    /* Smooth fade UI transition */
    .slide-in-top {
        animation: slideInTop 0.4s ease forwards;
    }

    @keyframes slideInTop {
        from {
            transform: translateY(-10px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    /* For Print Report */
    @media print {
        body * {
            visibility: hidden;
        }

        #reportContentArea,
        #reportContentArea * {
            visibility: visible;
        }

        #reportContentArea {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }

        .card {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        // Initial PHP Data
        let initialCategoryData = <?= $jsonCategoryData ?>;
        let initialTrendData = <?= $jsonTrendData ?>;

        let catChartInstance = null;
        let trendChartInstance = null;

        // Color Palette (Toshl-style advanced)
        const colors = [
            '#ef4444', '#f97316', '#eab308', '#84cc16',
            '#22c55e', '#10b981', '#14b8a6', '#06b6d4',
            '#0ea5e9', '#3b82f6', '#6366f1', '#8b5cf6'
        ];

        // ----- 1. Doughnut Chart Plugin (Text in Center) -----
        const centerTextPlugin = {
            id: 'centerText',
            beforeDraw: function(chart) {
                if (chart.config.type !== 'doughnut') return;
                const ctx = chart.ctx;
                const chartArea = chart.chartArea;
                if (!chartArea) return;

                const centerX = chartArea.left + (chartArea.right - chartArea.left) / 2;
                const centerY = chartArea.top + (chartArea.bottom - chartArea.top) / 2;

                // Calculate total inside plugin
                let total = 0;
                if (chart.data.datasets.length > 0) {
                    total = chart.data.datasets[0].data.reduce((a, b) => a + parseFloat(b), 0);
                }

                ctx.restore();
                // Scale font size a bit if it gets too large, but 1.2rem should fit most 
                ctx.font = "bold 1.2rem 'Inter', sans-serif";
                ctx.textBaseline = "middle";
                ctx.fillStyle = document.documentElement.getAttribute('data-bs-theme') === 'dark' ? '#f8f9fa' : '#333';

                const text = total > 0 ? "UGX " + new Intl.NumberFormat('en-US').format(total) : "UGX 0";
                const textX = Math.round(centerX - ctx.measureText(text).width / 2);
                const textY = centerY;

                ctx.fillText(text, textX, textY);

                ctx.font = "normal 0.8rem 'Inter', sans-serif";
                ctx.fillStyle = "#6c757d";
                const subText = "Total Spent";
                const subTextX = Math.round(centerX - ctx.measureText(subText).width / 2);
                ctx.fillText(subText, subTextX, textY + 20);
                ctx.save();
            }
        };
        Chart.register(centerTextPlugin);

        // ----- Helper to render Category Chart -----
        function renderCategoryChart(data) {
            if (catChartInstance) catChartInstance.destroy();

            const labels = data.map(d => d.name);
            const values = data.map(d => d.total);

            // Assign random colors or fixed mapping
            const bgColors = labels.map((_, i) => colors[i % colors.length]);

            const ctx = document.getElementById('categoryChart').getContext('2d');
            catChartInstance = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: bgColors,
                        borderWidth: 2,
                        borderColor: 'transparent', // Looks better in dark mode
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%', // Makes the hole bigger
                    animation: {
                        animateScale: true,
                        animateRotate: true,
                        duration: 1000
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                usePointStyle: true,
                                padding: 20
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(ctx) {
                                    let val = ctx.raw;
                                    return ' UGX ' + new Intl.NumberFormat('en-US').format(val);
                                }
                            }
                        }
                    }
                }
            });
        }

        // ----- Helper to render Trend Bar Chart -----
        function renderTrendChart(data) {
            if (trendChartInstance) trendChartInstance.destroy();

            const labels = data.map(d => {
                // Convert YYYY-MM to Month abbreviation
                const str = d.month_str + "-01";
                const date = new Date(str);
                return date.toLocaleString('default', {
                    month: 'short',
                    year: '2-digit'
                });
            });
            const income = data.map(d => parseFloat(d.total_income));
            const expenses = data.map(d => parseFloat(d.total_expense));

            const ctx = document.getElementById('trendChart').getContext('2d');
            trendChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                            label: 'Income',
                            data: income,
                            backgroundColor: '#22c55e', // Success Green
                            borderRadius: 4
                        },
                        {
                            label: 'Expenses',
                            data: expenses,
                            backgroundColor: '#ef4444', // Danger Red
                            borderRadius: 4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 1200,
                        easing: 'easeOutQuart'
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                borderDash: [2, 4],
                                color: '#e5e7eb'
                            },
                            ticks: {
                                callback: function(value) {
                                    if (value >= 1000) return value / 1000 + 'k';
                                    return value;
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            align: 'end',
                            labels: {
                                usePointStyle: true
                            }
                        }
                    }
                }
            });
        }

        // ----- Helper to update Summary Table DOM -----
        function updateSummaryTable(data) {
            const tbody = document.getElementById('summaryTableBody');
            tbody.innerHTML = '';

            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center py-4 text-muted">No expenses recorded this month.</td></tr>';
                return;
            }

            data.forEach(cat => {

                // Determine badge for diff
                let badgeHtml = '';
                if (cat.diff > 0) {
                    badgeHtml = `<span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill">
                                <i class="bi bi-arrow-up-right"></i> +${new Intl.NumberFormat('en-US').format(cat.diff)}
                             </span>`;
                } else if (cat.diff < 0) {
                    badgeHtml = `<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill">
                                <i class="bi bi-arrow-down-right"></i> ${new Intl.NumberFormat('en-US').format(cat.diff)}
                             </span>`;
                } else {
                    badgeHtml = `<span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill">
                                <i class="bi bi-dash"></i> Unchanged
                             </span>`;
                }

                const tr = document.createElement('tr');
                tr.innerHTML = `
                <td class="fw-medium">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px;">
                            <i class="bi bi-tag-fill small"></i>
                        </div>
                        ${cat.name}
                    </div>
                </td>
                <td class="text-end fw-bold text-danger">
                    ${new Intl.NumberFormat('en-US').format(cat.total)}
                </td>
                <td class="text-center">
                    <div class="d-flex align-items-center justify-content-center">
                        <span class="me-2 text-muted small" style="width: 30px;">${cat.pct}%</span>
                        <div class="progress flex-grow-1" style="height: 6px; max-width: 100px;">
                            <div class="progress-bar bg-danger" style="width: ${cat.pct}%"></div>
                        </div>
                    </div>
                </td>
                <td class="text-center">${badgeHtml}</td>
            `;
                tbody.appendChild(tr);
            });
        }

        // ----- Helper to update Health Score DOM -----
        function updateHealthScore(score) {
            document.getElementById('healthScoreText').innerText = score + "%";

            // Update SVG Circle (dasharray maps to percentage visually)
            const circle = document.getElementById('healthCircle');
            circle.setAttribute('stroke-dasharray', `${score}, 100`);

            // Color mapping
            circle.classList.remove('text-success', 'text-warning', 'text-danger');
            let msg = document.getElementById('healthMessage');

            if (score >= 50) {
                circle.classList.add('text-success');
                msg.innerText = "Excellent! You are saving a healthy amount of your income.";
            } else if (score > 0) {
                circle.classList.add('text-warning');
                msg.innerText = "Good. You have a positive cash flow.";
            } else {
                circle.classList.add('text-danger');
                msg.innerText = "Warning: You are spending more than you earn.";
                circle.setAttribute('stroke-dasharray', `100, 100`); // Full Red Ring
            }
        }

        // ----- AJAX Fetch handler -----
        const monthSelector = document.getElementById('monthSelector');
        const loadingUI = document.getElementById('loadingIndicator');
        const exportBtn = document.getElementById('exportBtn');

        monthSelector.addEventListener('change', function(e) {
            const selectedMonth = e.target.value;
            loadingUI.classList.remove('d-none'); // Show loading spinner

            // Update Export link dynamically without reload
            exportBtn.href = "index.php?page=reports_export&month=" + selectedMonth;

            // Fetch new data
            fetch(`index.php?page=reports&ajax=1&month=${selectedMonth}`)
                .then(res => res.json())
                .then(data => {
                    // Update UI Components
                    updateHealthScore(data.healthScore);
                    renderCategoryChart(data.categories);
                    renderTrendChart(data.trend);
                    updateSummaryTable(data.categories);

                    loadingUI.classList.add('d-none'); // Hide loading
                })
                .catch(err => {
                    console.error("Failed to load report data", err);
                    loadingUI.innerHTML = "<span class='text-danger small'>Error loading data</span>";
                });
        });

        // Draw initial charts
        renderCategoryChart(initialCategoryData);
        renderTrendChart(initialTrendData);
    });
</script>

<?php require 'views/layout/footer.php'; ?>