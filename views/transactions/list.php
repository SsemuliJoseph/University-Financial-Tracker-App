<?php require 'views/layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Transactions</h2>
    <div>
        <a href="index.php?page=transaction_add" class="btn btn-primary shadow-sm hover-lift">
            <i class="bi bi-plus-lg"></i> Add New
        </a>
    </div>
</div>

<!-- UPGRADE 3: FILTER BAR -->
<div class="card shadow-sm border-0 mb-4 rounded-4">
    <div class="card-body">
        <form method="GET" action="index.php" id="filterForm" class="row g-3">
            <input type="hidden" name="page" value="transactions">
            
            <!-- JS Live Search (Visible row filtering) -->
            <div class="col-md-3">
                <label class="form-label text-muted small fw-bold">Search Description</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" id="liveSearch" class="form-control border-start-0 bg-light" placeholder="Type to filter...">
                </div>
            </div>

            <!-- Categories -->
            <div class="col-md-2">
                <label class="form-label text-muted small fw-bold">Category</label>
                <select name="category" class="form-select bg-light">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['category_id'] ?>" <?= ($filters['category_id'] == $cat['category_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Type -->
            <div class="col-md-2">
                <label class="form-label text-muted small fw-bold">Type</label>
                <select name="type" class="form-select bg-light">
                    <option value="">All Types</option>
                    <option value="income" <?= ($filters['type'] == 'income') ? 'selected' : '' ?>>Income</option>
                    <option value="expense" <?= ($filters['type'] == 'expense') ? 'selected' : '' ?>>Expense</option>
                </select>
            </div>

            <!-- Date Range -->
            <div class="col-md-3">
                <label class="form-label text-muted small fw-bold">Date Range</label>
                <div class="input-group">
                    <input type="date" name="date_from" class="form-control bg-light" value="<?= htmlspecialchars($filters['date_from']) ?>">
                    <span class="input-group-text bg-light border-0">to</span>
                    <input type="date" name="date_to" class="form-control bg-light" value="<?= htmlspecialchars($filters['date_to']) ?>">
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="col-md-2 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-navy text-white w-100 hover-lift"><i class="bi bi-funnel"></i> Filter</button>
                <a href="index.php?page=transactions" class="btn btn-light border w-100 hover-lift"><i class="bi bi-x-circle"></i> Clear</a>
            </div>
        </form>
    </div>
</div>

<!-- DATA TABLE AND BULK CONTROLS -->
<form method="POST" action="index.php?page=transactions_bulk_delete" id="bulkDeleteForm">
    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <button type="button" class="btn btn-danger btn-sm shadow-sm hover-lift d-none" id="bulkDeleteBtn" data-bs-toggle="modal" data-bs-target="#bulkDeleteModal">
                <i class="bi bi-trash"></i> Delete Selected (<span id="selectedCount">0</span>)
            </button>
        </div>
        
        <?php 
            // Rebuild current query string for export
            $exportUrl = "index.php?page=transactions_export&" . http_build_query(array_merge($_GET, ['page' => null]));
        ?>
        <a href="<?= $exportUrl ?>" class="btn btn-success btn-sm shadow-sm hover-lift">
            <i class="bi bi-file-earmark-excel"></i> Export to CSV
        </a>
    </div>

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="transactionsTable">
                <thead class="table-light">
                    <?php
                        function sortLink($colName, $label, $currentSort, $currentDir) {
                            $dir = ($currentSort === $colName && $currentDir === 'asc') ? 'desc' : 'asc';
                            $icon = '';
                            if ($currentSort === $colName) {
                                $icon = $currentDir === 'asc' ? '<i class="bi bi-sort-up"></i>' : '<i class="bi bi-sort-down"></i>';
                            } else {
                                $icon = '<i class="bi bi-arrow-down-up text-muted" style="opacity: 0.3;"></i>';
                            }
                            
                            $params = $_GET;
                            $params['sort'] = $colName;
                            $params['dir'] = $dir;
                            
                            $url = 'index.php?' . http_build_query($params);
                            echo "<a href='$url' class='text-dark text-decoration-none d-flex justify-content-between align-items-center'>$label $icon</a>";
                        }
                    ?>
                    <tr>
                        <th width="40" class="text-center">
                            <input class="form-check-input" type="checkbox" id="selectAll" style="transform: scale(1.2);">
                        </th>
                        <th><?php sortLink('transaction_date', 'Date', $sortBy, $sortDir); ?></th>
                        <th><?php sortLink('description', 'Description', $sortBy, $sortDir); ?></th>
                        <th><?php sortLink('category_name', 'Category', $sortBy, $sortDir); ?></th>
                        <th><?php sortLink('type', 'Type', $sortBy, $sortDir); ?></th>
                        <th class="text-end"><?php sortLink('amount', 'Amount (UGX)', $sortBy, $sortDir); ?></th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($transactions)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No transactions found. Try adjusting your filters or add a new one.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($transactions as $t): ?>
                            <tr class="transaction-row">
                                <td class="text-center">
                                    <input class="form-check-input row-checkbox" type="checkbox" name="selected_ids[]" value="<?= $t['transaction_id'] ?>" style="transform: scale(1.2);">
                                </td>
                                <td><?= htmlspecialchars($t['transaction_date']) ?></td>
                                <td class="desc-cell fw-medium">
                                    <?= htmlspecialchars($t['description']) ?>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        <?= htmlspecialchars($t['category_name']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($t['type'] === 'income'): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25"><i class="bi bi-arrow-down-left"></i> Income</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25"><i class="bi bi-arrow-up-right"></i> Expense</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end fw-bold <?= $t['type'] === 'income' ? 'text-success' : 'text-danger' ?>">
                                    <?= $t['type'] === 'income' ? '+' : '-' ?>UGX <?= number_format($t['amount']) ?>
                                </td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn btn-light btn-sm border-0 bg-transparent text-muted" type="button" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                            <li>
                                                <a class="dropdown-item text-primary" href="index.php?page=transaction_edit&id=<?= $t['transaction_id'] ?>">
                                                    <i class="bi bi-pencil-square me-2"></i> Edit
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <button type="button" class="dropdown-item text-danger" onclick="confirmDelete(<?= $t['transaction_id'] ?>)">
                                                    <i class="bi bi-trash me-2"></i> Delete
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</form>

<!-- UPGRADE 3: PAGINATION -->
<?php if ($totalPages > 1): ?>
<nav aria-label="Transaction pagination">
    <ul class="pagination justify-content-center">
        <?php 
            $pgParams = $_GET; 
            
            // Previous button
            $pgParams['p'] = $page - 1;
            $prevUrl = 'index.php?' . http_build_query($pgParams);
            $prevDisabled = ($page <= 1) ? 'disabled' : '';

            // Next button
            $pgParams['p'] = $page + 1;
            $nextUrl = 'index.php?' . http_build_query($pgParams);
            $nextDisabled = ($page >= $totalPages) ? 'disabled' : '';
        ?>

        <li class="page-item <?= $prevDisabled ?>">
            <a class="page-link" href="<?= $prevDisabled ? '#' : $prevUrl ?>" tabindex="-1">Previous</a>
        </li>
        
        <?php for($i = 1; $i <= $totalPages; $i++): ?>
            <?php 
                $pgParams['p'] = $i;
                $url = 'index.php?' . http_build_query($pgParams);
            ?>
            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                <a class="page-link" href="<?= $url ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>

        <li class="page-item <?= $nextDisabled ?>">
            <a class="page-link" href="<?= $nextDisabled ? '#' : $nextUrl ?>">Next</a>
        </li>
    </ul>
</nav>
<?php endif; ?>

<!-- Single Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Confirm Deletion</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete this transaction? This action cannot be undone.
      </div>
      <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
        <a href="#" id="confirmDeleteBtn" class="btn btn-danger hover-lift">Delete Permanently</a>
      </div>
    </div>
  </div>
</div>

<!-- Bulk Delete Modal -->
<div class="modal fade" id="bulkDeleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Delete Multiple</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete the selected transactions? This action cannot be undone.
      </div>
      <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
        <button type="button" onclick="document.getElementById('bulkDeleteForm').submit();" class="btn btn-danger hover-lift">Delete All Selected</button>
      </div>
    </div>
  </div>
</div>

<!-- UPGRADE 3: JS FOR LIVE SEARCH AND BULK SELECT -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    
    // 1. Live Search (Filters visible rows without reload)
    const liveSearch = document.getElementById('liveSearch');
    const tableRows = document.querySelectorAll('.transaction-row');

    if (liveSearch) {
        liveSearch.addEventListener('keyup', function(e) {
            const term = e.target.value.toLowerCase();
            
            tableRows.forEach(row => {
                // Match primarily on description, but we check textContent of the description cell
                const desc = row.querySelector('.desc-cell').textContent.toLowerCase();
                if (desc.includes(term)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    // 2. Bulk Select Logic
    const selectAllCheckbox = document.getElementById('selectAll');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    const selectedCount = document.getElementById('selectedCount');

    function updateBulkButton() {
        // Find how many are checked
        const count = document.querySelectorAll('.row-checkbox:checked').length;
        selectedCount.textContent = count;
        
        if (count > 0) {
            bulkDeleteBtn.classList.remove('d-none');
        } else {
            bulkDeleteBtn.classList.add('d-none');
            // If nothing checked, uncheck 'select all'
            if(selectAllCheckbox) selectAllCheckbox.checked = false;
        }
    }

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            rowCheckboxes.forEach(checkbox => {
                checkbox.checked = isChecked;
            });
            updateBulkButton();
        });
    }

    rowCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkButton);
    });
});

// Single Deletion Modal Trigger
let deleteModal;
function confirmDelete(id) {
    if(!deleteModal) {
        deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    }
    const btn = document.getElementById('confirmDeleteBtn');
    // Set the href to the actual delete endpoint
    btn.href = `index.php?page=transaction_delete&id=${id}`;
    deleteModal.show();
}
</script>

<?php require 'views/layout/footer.php'; ?>
