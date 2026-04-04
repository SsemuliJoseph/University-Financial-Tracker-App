<?php require 'views/layout/header.php'; ?>

<!-- UPGRADE 6: Budget Settings -->

<div class="d-flex justify-content-between align-items-center mb-4 slide-in-top">
    <h2 class="mb-0">Budget Settings</h2>
    <div class="text-muted small">
        <i class="bi bi-calendar3"></i> <?= date('F Y') ?>
    </div>
</div>

<!-- Total Monthly Budget Summary -->
<div class="card border-0 shadow-sm rounded-4 mb-4 slide-in-top hover-lift">
    <div class="card-body p-4">
        <h6 class="text-muted fw-bold text-uppercase tracking-wider mb-3">Overall Monthly Budget</h6>
        <div class="row align-items-center">
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">Total Budget:</span>
                    <span class="fw-bold" id="overallTotalBudget">UGX <?= number_format($overallTotalBudget) ?></span>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">Total Spent:</span>
                    <span class="fw-bold text-danger" id="overallTotalSpent">UGX <?= number_format($overallTotalSpent) ?></span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Remaining:</span>
                    <span class="fw-bold text-success" id="overallRemaining">UGX <?= number_format(max(0, $overallTotalBudget - $overallTotalSpent)) ?></span>
                </div>
            </div>
            <div class="col-md-8">
                <?php
                $overallBg = 'bg-success';
                if ($overallPercent >= 80) $overallBg = 'bg-danger';
                elseif ($overallPercent >= 60) $overallBg = 'bg-warning';
                ?>
                <div class="d-flex justify-content-between mb-2">
                    <span class="small fw-bold">Overall Progress</span>
                    <span class="small fw-bold" id="overallPercentText"><?= $overallPercent ?>% Used</span>
                </div>
                <div class="progress rounded-pill" style="height: 12px;">
                    <div id="overallProgressBar" class="progress-bar <?= $overallBg ?> rounded-pill transition-bar" 
                         role="progressbar" style="width: <?= min(100, $overallPercent) ?>%;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Budget Category Cards Grid -->
<div class="row g-4" id="budgetCardsContainer">
    <?php foreach ($budgets as $b): ?>
        <?php
        $catId = $b['category_id'];
        $name = htmlspecialchars($b['category_name']);
        $limit = (int)$b['budget_amount'];
        $spent = (int)$b['spent_amount'];
        
        $percent = $limit > 0 ? round(($spent / $limit) * 100) : 0;
        $remaining = max(0, $limit - $spent);
        $isExceeded = ($spent > $limit && $limit > 0);
        
        // Colors
        $bgClass = 'bg-success';
        $textClass = 'text-success';
        if ($percent >= 80 || $isExceeded) {
            $bgClass = 'bg-danger';
            $textClass = 'text-danger';
        } elseif ($percent >= 60) {
            $bgClass = 'bg-warning';
            $textClass = 'text-warning';
        }
        
        $cardClass = $isExceeded ? 'border-danger pulse-danger' : 'border-0';
        ?>
        <div class="col-md-6 col-lg-4 budget-card-col slide-in-top" data-category="<?= $catId ?>">
            <div class="card shadow-sm rounded-4 h-100 hover-lift <?= $cardClass ?>" id="budgetCard-<?= $catId ?>">
                
                <?php if ($isExceeded): ?>
                    <div class="bg-danger text-white text-center py-1 small fw-bold rounded-top-4" id="warning-<?= $catId ?>">
                        <i class="bi bi-exclamation-triangle-fill"></i> Budget Exceeded by UGX <?= number_format($spent - $limit) ?>
                    </div>
                <?php else: ?>
                    <div class="bg-danger text-white text-center py-1 small fw-bold rounded-top-4 d-none" id="warning-<?= $catId ?>"></div>
                <?php endif; ?>

                <div class="card-body p-4 position-relative">
                    
                    <div class="d-flex align-items-center mb-4">
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                            <i class="bi bi-wallet2 fs-5"></i>
                        </div>
                        <h5 class="mb-0 fw-bold"><?= $name ?></h5>
                    </div>

                    <!-- Inline Editing Area -->
                    <div class="mb-3 p-3 bg-light rounded-3 text-center position-relative editable-area" onclick="enableEdit(<?= $catId ?>)">
                        <div id="display-limit-<?= $catId ?>" class="display-mode">
                            <div class="text-muted small text-uppercase fw-bold mb-1">Set Budget</div>
                            <h3 class="mb-0 text-primary fw-bold hover-edit" title="Click to edit">
                                UGX <?= number_format($limit) ?>
                                <i class="bi bi-pencil-square ms-2 small text-muted opacity-50"></i>
                            </h3>
                        </div>
                        
                        <div id="edit-limit-<?= $catId ?>" class="edit-mode d-none">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-primary border-end-0 text-muted">UGX</span>
                                <input type="number" class="form-control border-primary border-start-0 border-end-0 shadow-none fw-bold" 
                                       id="input-<?= $catId ?>" value="<?= $limit ?>" min="0" 
                                       onkeydown="handleEnter(event, <?= $catId ?>)">
                                <button class="btn btn-primary shadow-none" type="button" onclick="saveBudget(event, <?= $catId ?>)">
                                    <i class="bi bi-check2"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mb-1 small">
                        <span class="text-muted">Spent: <span id="spent-<?= $catId ?>" class="fw-bold ms-1 text-dark">UGX <?= number_format($spent) ?></span></span>
                        <span class="text-muted">Left: <span id="remaining-<?= $catId ?>" class="fw-bold ms-1 <?= $textClass ?>">UGX <?= number_format($remaining) ?></span></span>
                    </div>

                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="fw-bold">Progress</span>
                        <span class="fw-bold <?= $textClass ?>" id="percentText-<?= $catId ?>"><?= $percent ?>%</span>
                    </div>

                    <div class="progress rounded-pill" style="height: 8px;">
                        <div id="progressBar-<?= $catId ?>" class="progress-bar <?= $bgClass ?> rounded-pill transition-bar" 
                             role="progressbar" style="width: <?= min(100, $percent) ?>%;"></div>
                    </div>

                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Inline CSS -->
<style>
/* Smooth fade UI transition */
.slide-in-top {
    animation: slideInTop 0.4s ease forwards;
}
@keyframes slideInTop {
    from { transform: translateY(-10px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

.transition-bar {
    transition: width 0.8s ease-in-out, background-color 0.4s ease;
}

.hover-edit {
    cursor: pointer;
    transition: all 0.2s;
}
.hover-edit:hover {
    color: var(--bs-primary) !important;
    transform: scale(1.05);
}
.hover-edit:hover i {
    opacity: 1 !important;
    color: var(--bs-primary) !important;
}

/* Pulsing animation for exceeded budgets */
@keyframes pulseDanger {
    0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.4); }
    70% { box-shadow: 0 0 0 10px rgba(220, 53, 69, 0); }
    100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
}
.pulse-danger {
    border: 2px solid var(--bs-danger) !important;
    animation: pulseDanger 2s infinite;
}
</style>

<!-- AJAX JavaScript -->
<script>
function enableEdit(catId) {
    document.getElementById('display-limit-' + catId).classList.add('d-none');
    let editDiv = document.getElementById('edit-limit-' + catId);
    editDiv.classList.remove('d-none');
    
    // Focus the input
    let input = document.getElementById('input-' + catId);
    input.focus();
    input.select();
}

function handleEnter(e, catId) {
    if (e.key === 'Enter') {
        e.preventDefault();
        saveBudget(e, catId);
    }
}

function saveBudget(e, catId) {
    e.stopPropagation(); // Prevents triggering the onclick of the container again
    
    let input = document.getElementById('input-' + catId);
    let amount = parseInt(input.value) || 0;
    
    // Switch back to loading/display state immediately
    document.getElementById('edit-limit-' + catId).classList.add('d-none');
    let displayDiv = document.getElementById('display-limit-' + catId);
    displayDiv.classList.remove('d-none');
    
    // Add temporary loading indicator to the number
    displayDiv.querySelector('h3').innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';

    // Send AJAX POST
    fetch('index.php?page=budget_settings', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            category_id: catId,
            amount: amount
        })
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            updateCardUI(catId, data.category);
            updateOverallUI(data.totalBudget, data.totalSpent);
            
            // Show toast
            showToast('Budget Updated', 'Success! Budget limit saved.', 'success');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error', 'Failed to save budget. Please check connection.', 'danger');
        // Restore old value UI by force reloading or just let user re-click
        displayDiv.querySelector('h3').innerHTML = 'Error <i class="bi bi-pencil-square ms-2 small text-muted opacity-50"></i>';
    });
}

function updateCardUI(catId, categoryData) {
    let limit = parseInt(categoryData.budget_amount);
    let spent = parseInt(categoryData.spent_amount);
    let percent = limit > 0 ? Math.round((spent / limit) * 100) : 0;
    let remaining = Math.max(0, limit - spent);
    let isExceeded = (spent > limit && limit > 0);
    
    // Update Display Limit
    let displayHtml = `UGX ${new Intl.NumberFormat('en-US').format(limit)} <i class="bi bi-pencil-square ms-2 small text-muted opacity-50"></i>`;
    document.getElementById('display-limit-' + catId).querySelector('h3').innerHTML = displayHtml;
    document.getElementById('input-' + catId).value = limit;

    // Remaining text & color
    let remEl = document.getElementById('remaining-' + catId);
    remEl.innerText = `UGX ${new Intl.NumberFormat('en-US').format(remaining)}`;
    remEl.className = 'fw-bold ms-1'; // reset
    
    // Progress text & color
    let pctEl = document.getElementById('percentText-' + catId);
    pctEl.innerText = percent + "%";
    pctEl.className = 'fw-bold'; // reset
    
    // Progress Bar
    let barEl = document.getElementById('progressBar-' + catId);
    barEl.style.width = Math.min(100, percent) + "%";
    barEl.className = 'progress-bar rounded-pill transition-bar'; // reset

    // Warning Banner & Card Pulse
    let cardEl = document.getElementById('budgetCard-' + catId);
    let warnEl = document.getElementById('warning-' + catId);
    
    if (isExceeded) {
        remEl.classList.add('text-danger');
        pctEl.classList.add('text-danger');
        barEl.classList.add('bg-danger');
        
        cardEl.classList.add('border-danger', 'pulse-danger');
        cardEl.classList.remove('border-0');
        
        warnEl.classList.remove('d-none');
        warnEl.innerHTML = `<i class="bi bi-exclamation-triangle-fill"></i> Budget Exceeded by UGX ${new Intl.NumberFormat('en-US').format(spent - limit)}`;
    } else if (percent >= 80) {
        remEl.classList.add('text-danger');
        pctEl.classList.add('text-danger');
        barEl.classList.add('bg-danger');
        
        cardEl.classList.remove('border-danger', 'pulse-danger');
        cardEl.classList.add('border-0');
        warnEl.classList.add('d-none');
    } else if (percent >= 60) {
        remEl.classList.add('text-warning');
        pctEl.classList.add('text-warning');
        barEl.classList.add('bg-warning');
        
        cardEl.classList.remove('border-danger', 'pulse-danger');
        cardEl.classList.add('border-0');
        warnEl.classList.add('d-none');
    } else {
        remEl.classList.add('text-success');
        pctEl.classList.add('text-success');
        barEl.classList.add('bg-success');
        
        cardEl.classList.remove('border-danger', 'pulse-danger');
        cardEl.classList.add('border-0');
        warnEl.classList.add('d-none');
    }
}

function updateOverallUI(totalBudget, totalSpent) {
    let overallPercent = totalBudget > 0 ? Math.round((totalSpent / totalBudget) * 100) : 0;
    
    document.getElementById('overallTotalBudget').innerText = `UGX ${new Intl.NumberFormat('en-US').format(totalBudget)}`;
    document.getElementById('overallTotalSpent').innerText = `UGX ${new Intl.NumberFormat('en-US').format(totalSpent)}`;
    document.getElementById('overallRemaining').innerText = `UGX ${new Intl.NumberFormat('en-US').format(Math.max(0, totalBudget - totalSpent))}`;
    
    document.getElementById('overallPercentText').innerText = overallPercent + "% Used";
    
    let barEl = document.getElementById('overallProgressBar');
    barEl.style.width = Math.min(100, overallPercent) + "%";
    
    barEl.className = 'progress-bar rounded-pill transition-bar';
    if (overallPercent >= 80) barEl.classList.add('bg-danger');
    else if (overallPercent >= 60) barEl.classList.add('bg-warning');
    else barEl.classList.add('bg-success');
}

// Simple Toast function simulating mobile app corner notifications
function showToast(title, message, type='success') {
    // If toast container doesn't exist, create it
    if (!document.getElementById('toastContainer')) {
        const container = document.createElement('div');
        container.id = 'toastContainer';
        container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        document.body.appendChild(container);
    }
    
    const toastId = 'toast-' + Date.now();
    const bgClass = type === 'success' ? 'bg-success' : 'bg-danger';
    
    const toastHtml = `
        <div id="${toastId}" class="toast align-items-center text-white ${bgClass} border-0 show slide-in-top" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body fw-bold">
                    <i class="bi ${type==='success' ? 'bi-check-circle-fill' : 'bi-exclamation-circle-fill'} me-2"></i> ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close" onclick="document.getElementById('${toastId}').remove()"></button>
            </div>
        </div>
    `;
    
    document.getElementById('toastContainer').insertAdjacentHTML('beforeend', toastHtml);
    setTimeout(() => {
        let el = document.getElementById(toastId);
        if(el) {
            el.classList.remove('show');
            setTimeout(() => el.remove(), 300);
        }
    }, 4000);
}
</script>

<?php require 'views/layout/footer.php'; ?>
