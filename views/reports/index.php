<?php
// views/reports/index.php
include 'views/layout/header.php';
?>

<div class="row mb-4">
    <div class="col-md-12">
        <h2>My Financial Reports (<?= date('F Y') ?>)</h2>
        <p class="text-muted">A visual breakdown of your monthly expenses.</p>
    </div>
</div>

<div class="row">
    <!-- Pie Chart (Half Screen) -->
    <div class="col-md-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0">Spending by Category</h5>
            </div>
            <div class="card-body d-flex justify-content-center align-items-center">
                <div style="width: 80%;">
                    <!-- Chart.js looks for <canvas> tags -->
                    <canvas id="categoryPieChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Line Graph (Half Screen) -->
    <div class="col-md-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0">Spending Over Time</h5>
            </div>
            <div class="card-body">
                <div>
                    <canvas id="dailyLineChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Load Chart.js via CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- The JS script to build our charts using the data from the controller -->
<script>
    // 1. Pass the generated JSON string from PHP directly into a JavaScript variable!
    const catData = <?= $jsonCategoryData ?>;
    const dailyData = <?= $jsonDailyData ?>;

    // 2. Build Pie Chart
    // Chart.js needs arrays of labels and corresponding data points. Map extracts them!
    const pieContext = document.getElementById('categoryPieChart').getContext('2d');
    new Chart(pieContext, {
        type: 'pie',
        data: {
            // Extract the 'category_name' from each object in catData
            labels: catData.map(item => item.category_name),
            datasets: [{
                data: catData.map(item => item.total),
                // Random default colors for Chart.js
                backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#0dcaf0', '#6f42c1', '#fd7e14'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
        }
    });

    // 3. Build Line Graph
    const lineContext = document.getElementById('dailyLineChart').getContext('2d');
    new Chart(lineContext, {
        type: 'line',
        data: {
            // Extract the 'date' safely
            labels: dailyData.map(item => item.date),
            datasets: [{
                label: 'Daily Expense (UGX)',
                data: dailyData.map(item => item.total),
                borderColor: '#dc3545',
                backgroundColor: 'rgba(220, 53, 69, 0.2)',
                borderWidth: 2,
                fill: true, // Shades directly under the line
                tension: 0.3 // Adds slight curves to the lines
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

<?php
include 'views/layout/footer.php';
?>