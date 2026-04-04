<?php
// views/transactions/edit.php
include 'views/layout/header.php';
?>

<div class="row justify-content-center mt-4">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Edit Transaction</h5>
                <a href="index.php?page=transactions" class="btn btn-sm btn-light">Cancel</a>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <!-- The action targets the current specific ID  -->
                <form action="index.php?page=transaction_edit&id=<?= htmlspecialchars($transaction['transaction_id']) ?>" method="POST">

                    <div class="mb-3">
                        <label>Amount (UGX)</label>
                        <!-- We pre-fill the values with the existing transaction data -->
                        <input type="number" name="amount" class="form-control" placeholder="e.g., 50000" min="1" required
                            value="<?= htmlspecialchars($transaction['amount']) ?>">
                    </div>

                    <div class="mb-3">
                        <label>Category (Type)</label>
                        <select name="category_data" class="form-select" required>
                            <option value="">-- Select a Category --</option>
                            <?php foreach ($categories as $cat): ?>
                                <?php
                                // See if this category block matches the one saved for this transaction 
                                $value = $cat['category_id'] . '-' . $cat['type'];
                                $isSelected = ($transaction['category_id'] == $cat['category_id'] && $transaction['type'] == $cat['type']) ? 'selected' : '';
                                ?>
                                <option value="<?= htmlspecialchars($value) ?>" <?= $isSelected ?>>
                                    <?= htmlspecialchars($cat['name']) ?> (<?= ucfirst($cat['type']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Date</label>
                        <input type="date" name="transaction_date" class="form-control" required
                            value="<?= htmlspecialchars($transaction['transaction_date']) ?>">
                    </div>

                    <div class="mb-3">
                        <label>Description</label>
                        <input type="text" name="description" class="form-control" required
                            value="<?= htmlspecialchars($transaction['description']) ?>">
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Update Transaction</button>

                </form>
            </div>
        </div>
    </div>
</div>

<?php
include 'views/layout/footer.php';
?>