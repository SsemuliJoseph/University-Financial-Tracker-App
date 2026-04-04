<?php
// views/transactions/list.php
include 'views/layout/header.php';
?>

<div class="row mb-4">
    <div class="col-md-12 d-flex justify-content-between align-items-center">
        <h2>My Transactions</h2>
        <a href="index.php?page=transaction_add" class="btn btn-success">+ New</a>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-body p-0">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Category</th>
                            <th>Amount (UGX)</th>
                            <th>Type</th>
                            <!-- The 'Actions' column intentionally set to line up buttons -->
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($transactions)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">You have no transactions to display.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($transactions as $t): ?>
                                <tr>
                                    <td class="align-middle"><?= htmlspecialchars($t['transaction_date']) ?></td>
                                    <td class="align-middle"><?= htmlspecialchars($t['description']) ?></td>
                                    <td class="align-middle"><?= htmlspecialchars($t['category_name'] ?? 'None') ?></td>
                                    <td class="align-middle fw-bold <?= $t['type'] === 'income' ? 'text-success' : 'text-danger' ?>">
                                        <?= number_format($t['amount']) ?>
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge <?= $t['type'] === 'income' ? 'bg-success' : 'bg-danger' ?>">
                                            <?= ucfirst($t['type']) ?>
                                        </span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <!-- Edit button goes to the edit page with the transaction ID (Feature 7!) -->
                                        <a href="index.php?page=transaction_edit&id=<?= $t['transaction_id'] ?>" class="btn btn-sm btn-primary mb-1">Edit</a>

                                        <!-- Delete button goes to delete function and requires confirmation via javascript -->
                                        <a href="index.php?page=transaction_delete&id=<?= $t['transaction_id'] ?>"
                                            class="btn btn-sm btn-danger mb-1"
                                            onclick="return confirm('Are you sure you want to delete this transaction?');">
                                            Delete
                                        </a>
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

<?php
include 'views/layout/footer.php';
?>