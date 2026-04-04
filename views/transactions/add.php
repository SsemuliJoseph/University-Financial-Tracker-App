<?php 
// views/transactions/add.php
include 'views/layout/header.php'; 
?>

<div class="row justify-content-center mt-4">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Add a New Transaction</h5>
                <a href="index.php?page=dashboard" class="btn btn-sm btn-light">Back to Dashboard</a>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form action="index.php?page=transaction_add" method="POST">
                    
                    <div class="mb-3">
                        <label>Amount (UGX)</label>
                        <!-- type="number" brings up a numeric keyboard on mobile devices -->
                        <input type="number" name="amount" class="form-control" placeholder="e.g., 50000" min="1" required>
                    </div>

                    <div class="mb-3">
                        <label>Category (Type)</label>
                        <select name="category_data" class="form-select" required>
                            <option value="">-- Select a Category --</option>
                            <?php foreach ($categories as $cat): ?>
                                <!-- We embed the category_id and type into the value separated by a hyphen -->
                                <option value="<?= $cat['category_id'] . '-' . $cat['type'] ?>">
                                    <?= htmlspecialchars($cat['name']) ?> (<?= ucfirst($cat['type']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Date</label>
                        <!-- type="date" displays a calendar date-picker in the browser -->
                        <input type="date" name="transaction_date" class="form-control" required
                               value="<?= date('Y-m-d') ?>"> <!-- Default to today's date -->
                    </div>

                    <div class="mb-3">
                        <label>Description</label>
                        <input type="text" name="description" class="form-control" placeholder="e.g., Lunch at cafeteria" required>
                    </div>

                    <button type="submit" class="btn btn-success w-100">Save Transaction</button>
                    
                </form>
            </div>
        </div>
    </div>
</div>

<?php 
include 'views/layout/footer.php'; 
?>