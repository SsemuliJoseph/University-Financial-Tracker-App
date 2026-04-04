<?php 
// views/dashboard.php
include 'views/layout/header.php'; 
?>

<div class="row mb-4">
    <div class="col-md-12">
        <h2>Dashboard</h2>
        <p class="text-muted">Welcome back, <?= htmlspecialchars($_SESSION['name']) ?>!</p>
    </div>
</div>

<div class="row mb-4">
    <!-- Balance Card -->
    <div class="col-md-4">
        <div class="card text-white bg-primary shadow">
            <div class="card-body">
                <h5 class="card-title">Total Balance</h5>
                <!-- number_format() adds commas to thousands so it looks like currency -->
                <h2>UGX <?= number_format($balance) ?></h2>
            </div>
        </div>
    </div>
    <!-- (Budget summary cards will go here later) -->
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent Transactions</h5>
                <a href="index.php?page=transactions" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Category</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recent_transactions)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">No transactions recorded yet.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recent_transactions as $t): ?>
                                <tr>
                                    <td><?= htmlspecialchars($t['transaction_date']) ?></td>
                                    <td><?= htmlspecialchars($t['description']) ?></td>
                                    <td><?= htmlspecialchars($t['category_name'] ?? 'Uncategorized') ?></td>
                                    <!-- Change color based on if it's income (green) or expense (red) -->
                                    <td class="<?= $t['type'] === 'income' ? 'text-success' : 'text-danger' ?>">
                                        <?= $t['type'] === 'income' ? '+' : '-' ?> UGX <?= number_format($t['amount']) ?>
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

<?php include 'views/layout/footer.php'; ?>