<?php // views/finance/reports.php 
?>
<?php require_once 'views/layout/header.php'; ?>

<div class="container mt-4">
    <h2>System Financial Reports</h2>
    <p class="text-muted">As a Finance Officer, you can view and export all system transactions.</p>

    <div class="mb-4">
        <a href="index.php?page=finance_export" class="btn btn-success">
            <i class="bi bi-file-earmark-spreadsheet"></i> Export to CSV
        </a>
    </div>

    <div class="card">
        <div class="card-header bg-dark text-white">
            System-wide Transactions
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>User (Email)</th>
                            <th>Description</th>
                            <th>Category</th>
                            <th>Type</th>
                            <th>Amount (LKR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($transactions)): ?>
                            <tr>
                                <td colspan="6" class="text-center">No transactions found in the system.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($transactions as $t): ?>
                                <tr>
                                    <td><?= htmlspecialchars($t['transaction_date']) ?></td>
                                    <td>
                                        <?= htmlspecialchars($t['user_name']) ?><br />
                                        <small class="text-muted"><?= htmlspecialchars($t['user_email']) ?></small>
                                    </td>
                                    <td><?= htmlspecialchars($t['description']) ?></td>
                                    <td>
                                        <?php if ($t['category_name']): ?>
                                            <span class="badge bg-secondary"><?= htmlspecialchars($t['category_name']) ?></span>
                                        <?php else: ?>
                                            <em class="text-muted">None</em>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($t['type'] === 'income'): ?>
                                            <span class="badge bg-success">Income</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Expense</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="<?= $t['type'] === 'income' ? 'text-success' : 'text-danger' ?>">
                                            <?= $t['type'] === 'income' ? '+' : '-' ?> Rs. <?= number_format($t['amount'], 2) ?>
                                        </span>
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

<?php require_once 'views/layout/footer.php'; ?>