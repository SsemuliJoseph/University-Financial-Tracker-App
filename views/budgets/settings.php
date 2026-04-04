<?php
// views/budgets/settings.php
include 'views/layout/header.php';
?>

<div class="row mb-4">
    <div class="col-md-12">
        <!-- Show nice human readable month and year -->
        <h2>Budget Settings (<?= date('F Y') ?>)</h2>
        <p class="text-muted">Set monthly limits for your expenses and track your usage.</p>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-body">
                <?php if (isset($success)): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>

                <!-- Submits back to itself -->
                <form action="index.php?page=budget_settings" method="POST">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Expense Category</th>
                                <th width="25%">Monthly Limit (UGX)</th>
                                <th>Spent So Far</th>
                                <th>Usage Progress</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($budgets as $b): ?>
                                <?php
                                // Math to calculate progress percentage!
                                $spent = $b['spent_amount'];
                                $limit = $b['budget_amount'];
                                $percent = $limit > 0 ? min(100, round(($spent / $limit) * 100)) : 0;

                                // Make progress bar change color automatically using Bootstrap coloring!
                                $bgClass = 'bg-success';
                                if ($percent >= 90) {
                                    $bgClass = 'bg-danger';
                                } elseif ($percent >= 75) {
                                    $bgClass = 'bg-warning';
                                }
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($b['category_name']) ?></td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text">UGX</span>
                                            <!-- The name uses array syntax: budgets[3] allows PHP to receive this as an array grouped by category ID! -->
                                            <input type="number" name="budgets[<?= $b['category_id'] ?>]"
                                                class="form-control form-control-sm"
                                                value="<?= $limit > 0 ? htmlspecialchars($limit) : '' ?>"
                                                placeholder="0" min="0">
                                        </div>
                                    </td>
                                    <!-- Makes text red if over budget -->
                                    <td class="<?= ($spent > $limit && $limit > 0) ? 'text-danger fw-bold' : '' ?>">
                                        UGX <?= number_format($spent) ?>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 me-2" style="height: 10px;">
                                                <div class="progress-bar <?= $bgClass ?>" role="progressbar" style="width: <?= $percent ?>%;"></div>
                                            </div>
                                            <small><?= $percent ?>%</small>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Save Budgets</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
include 'views/layout/footer.php';
?>