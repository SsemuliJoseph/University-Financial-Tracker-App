<?php 
// views/transactions/add.php
include 'views/layout/header.php'; 

// Icon Mapping for standard categories
$iconMap = [
    'Tuition Fees' => 'bi-mortarboard',
    'Accommodation' => 'bi-house',
    'Food' => 'bi-cup-hot',
    'Transport' => 'bi-bus-front',
    'Books & Stationery' => 'bi-book',
    'Airtime & Internet' => 'bi-phone',
    'Entertainment' => 'bi-controller',
    'Other Expense' => 'bi-grid',
    'Bursary / Scholarship' => 'bi-award',
    'Part-time Work' => 'bi-briefcase',
    'Family Support' => 'bi-people',
    'Other Income' => 'bi-cash-coin'
];
?>

<style>
/* UPGRADE 4: Sliding Panel Animation */
.slide-in-right {
    animation: slideInRight 0.4s cubic-bezier(0.25, 0.8, 0.25, 1) forwards;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

/* UPGRADE 4: Calculator-style Input */
.calc-input {
    font-size: 3.5rem;
    font-weight: 700;
    border: none !important;
    background: transparent !important;
    text-align: center;
    box-shadow: none !important;
    color: var(--bs-danger); /* Default Expense Red */
    padding: 0;
    transition: color 0.3s ease;
}
.calc-input.income-mode {
    color: var(--bs-success); /* Income Green */
}
.calc-input::placeholder {
    color: #adb5bd;
    opacity: 0.5;
}

/* Category grid buttons */
.category-card {
    border: 2px solid transparent;
    transition: all 0.2s ease;
    cursor: pointer;
    background: var(--bs-light);
}
.category-card:hover, .category-card.selected {
    transform: translateY(-2px);
}
.category-card.selected {
    background-color: var(--bs-danger);
    color: #fff;
    border-color: var(--bs-danger);
}
.income-mode .category-card.selected {
    background-color: var(--bs-success);
    border-color: var(--bs-success);
}
.category-card-icon {
    font-size: 1.5rem;
    margin-bottom: 0.25rem;
}

/* Field validation styles */
.form-control.is-valid, .form-control.is-invalid {
    background-image: none !important; /* Hide bootstrap default icons to let ours show cleanly */
}
.validation-icon {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 1.25rem;
}

/* Base Theme Switcher wrapper */
.mobile-form-wrapper {
    background-color: #fff;
    border-radius: 1.5rem;
    overflow: hidden;
}
[data-bs-theme="dark"] .mobile-form-wrapper {
    background-color: #1e293b;
}
</style>

<div class="row justify-content-center slide-in-right">
    <div class="col-md-8 col-lg-6">
        
        <div class="mobile-form-wrapper shadow-lg mb-5">
            <!-- Header App Bar -->
            <div class="d-flex justify-content-between align-items-center p-4 border-bottom">
                <a href="index.php?page=dashboard" class="btn btn-sm btn-light rounded-circle shadow-sm" style="width:36px; height:36px; display:inline-flex; align-items:center; justify-content:center;">
                    <i class="bi bi-arrow-left fs-5"></i>
                </a>
                <h5 class="mb-0 fw-bold">New Transaction</h5>
                <div style="width:36px;"></div> <!-- Spacer for center alignment -->
            </div>

            <div class="p-4">
                <!-- Validation Toast Container -->
                <div class="position-fixed bottom-0 start-50 translate-middle-x p-3" style="z-index: 1080">
                    <div id="successToast" class="toast align-items-center text-white bg-success border-0 shadow-lg rounded-4" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body fs-6" id="toastMessage">
                                <i class="bi bi-check-circle-fill me-2"></i> Transaction added!
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                    </div>
                </div>

                <form id="transactionForm" action="index.php?page=transaction_add" method="POST">
                    
                    <!-- UPGRADE 4: Toggle Switch (Income / Expense) -->
                    <div class="d-flex justify-content-center mb-4">
                        <div class="btn-group shadow-sm rounded-pill p-1 bg-light border" role="group">
                            <input type="radio" class="btn-check" name="temp_type" id="typeExpense" value="expense" autocomplete="off" checked>
                            <label class="btn btn-outline-danger border-0 rounded-pill px-4 fw-bold" for="typeExpense">Expense</label>

                            <input type="radio" class="btn-check" name="temp_type" id="typeIncome" value="income" autocomplete="off">
                            <label class="btn btn-outline-success border-0 rounded-pill px-4 fw-bold" for="typeIncome">Income</label>
                        </div>
                    </div>

                    <!-- UPGRADE 4: Calculator Style Amount -->
                    <div class="mb-4 text-center position-relative">
                        <div class="text-muted small fw-bold mb-1 text-uppercase tracking-wider">Amount (UGX)</div>
                        <div class="d-flex justify-content-center align-items-center">
                            <span class="fs-3 fw-bold text-muted me-1">UGX</span>
                            <input type="number" id="amountInput" name="amount" class="form-control calc-input" placeholder="0" min="1" required style="width: 250px;">
                        </div>
                        <div id="amountFeedback" class="small text-danger mt-1 d-none"><i class="bi bi-x-circle"></i> Enter a valid amount</div>
                    </div>

                    <!-- Hidden input to store proper dropdown value based on taps -->
                    <input type="hidden" id="categoryDataInput" name="category_data" required>

                    <!-- UPGRADE 4: Visual Icon Cards for Categories -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-end mb-2">
                            <label class="form-label text-muted small fw-bold mb-0">Category</label>
                            <div id="categoryFeedback" class="small text-danger d-none"><i class="bi bi-x-circle"></i> Please select a category</div>
                        </div>

                        <!-- Grid container for Expense Categories -->
                        <div id="expenseCategoriesGrid" class="row g-2">
                            <?php foreach ($categories as $cat): if($cat['type'] == 'expense'): ?>
                                <?php $icon = $iconMap[$cat['name']] ?? 'bi-tags'; ?>
                                <div class="col-4 col-sm-3">
                                    <div class="category-card rounded-4 p-2 text-center shadow-sm" data-val="<?= $cat['category_id'] ?>-expense">
                                        <i class="bi <?= $icon ?> category-card-icon d-block"></i>
                                        <small class="d-block text-truncate" style="font-size: 0.75rem;"><?= htmlspecialchars($cat['name']) ?></small>
                                    </div>
                                </div>
                            <?php endif; endforeach; ?>
                        </div>

                        <!-- Grid container for Income Categories (Hidden by default) -->
                        <div id="incomeCategoriesGrid" class="row g-2 d-none">
                            <?php foreach ($categories as $cat): if($cat['type'] == 'income'): ?>
                                <?php $icon = $iconMap[$cat['name']] ?? 'bi-tags'; ?>
                                <div class="col-4 col-sm-3">
                                    <div class="category-card rounded-4 p-2 text-center shadow-sm" data-val="<?= $cat['category_id'] ?>-income">
                                        <i class="bi <?= $icon ?> category-card-icon d-block"></i>
                                        <small class="d-block text-truncate" style="font-size: 0.75rem;"><?= htmlspecialchars($cat['name']) ?></small>
                                    </div>
                                </div>
                            <?php endif; endforeach; ?>
                        </div>
                    </div>

                    <!-- UPGRADE 4: Description with Character Counter -->
                    <div class="mb-4 position-relative">
                        <label class="form-label text-muted small fw-bold">Note / Description</label>
                        <div class="position-relative">
                            <input type="text" id="descInput" name="description" class="form-control form-control-lg bg-light border-0" placeholder="What was this for?" maxlength="100" required style="padding-right: 40px;">
                            <span id="descIcon" class="validation-icon text-success d-none"><i class="bi bi-check-circle-fill"></i></span>
                        </div>
                        <div class="d-flex justify-content-between mt-1">
                            <div id="descFeedback" class="small text-danger d-none"><i class="bi bi-x-circle"></i> Description is required</div>
                            <small class="text-muted ms-auto"><span id="charCount">0</span>/100</small>
                        </div>
                    </div>

                    <!-- UPGRADE 4: Date Defaulting -->
                    <div class="mb-5 position-relative">
                        <label class="form-label text-muted small fw-bold">Date</label>
                        <div class="position-relative">
                            <input type="date" id="dateInput" name="transaction_date" class="form-control form-control-lg bg-light border-0" required value="<?= date('Y-m-d') ?>">
                            <span id="dateIcon" class="validation-icon text-success"><i class="bi bi-check-circle-fill"></i></span>
                        </div>
                        <div id="dateFeedback" class="small text-danger mt-1 d-none"><i class="bi bi-x-circle"></i> Invalid date</div>
                    </div>

                    <button type="submit" id="saveBtn" class="btn btn-danger btn-lg w-100 rounded-pill hover-lift shadow fw-bold p-3" disabled>
                        <i class="bi bi-save2 me-2"></i> Save Expense
                    </button>
                    
                </form>
            </div>
        </div>
    </div>
</div>

<!-- UPGRADE 4: Interactivity Script -->
<script>
document.addEventListener('DOMContentLoaded', () => {

    const form = document.getElementById('transactionForm');
    const typeExpense = document.getElementById('typeExpense');
    const typeIncome = document.getElementById('typeIncome');
    const amountInput = document.getElementById('amountInput');
    const saveBtn = document.getElementById('saveBtn');
    
    // Grids
    const expGrid = document.getElementById('expenseCategoriesGrid');
    const incGrid = document.getElementById('incomeCategoriesGrid');
    const catCards = document.querySelectorAll('.category-card');
    const catInput = document.getElementById('categoryDataInput');

    // Validation items
    const descInput = document.getElementById('descInput');
    const charCount = document.getElementById('charCount');
    const descIcon = document.getElementById('descIcon');
    const descFeedback = document.getElementById('descFeedback');

    const dateInput = document.getElementById('dateInput');
    const dateIcon = document.getElementById('dateIcon');
    const dateFeedback = document.getElementById('dateFeedback');

    const amountFeedback = document.getElementById('amountFeedback');
    const categoryFeedback = document.getElementById('categoryFeedback');

    // State
    let isIncome = false;

    // --- 1. Type Switcher ---
    function updateTheme() {
        isIncome = typeIncome.checked;
        
        if(isIncome) {
            amountInput.classList.add('income-mode');
            
            // Switch grids
            expGrid.classList.add('d-none');
            incGrid.classList.remove('d-none');
            
            // Re-style selected cards inside grid class
            document.body.classList.add('income-mode');
            
            // Update button
            saveBtn.classList.remove('btn-danger');
            saveBtn.classList.add('btn-success');
            saveBtn.innerHTML = '<i class="bi bi-save2 me-2"></i> Save Income';
        } else {
            amountInput.classList.remove('income-mode');
            
            expGrid.classList.remove('d-none');
            incGrid.classList.add('d-none');
            
            document.body.classList.remove('income-mode');
            
            saveBtn.classList.remove('btn-success');
            saveBtn.classList.add('btn-danger');
            saveBtn.innerHTML = '<i class="bi bi-save2 me-2"></i> Save Expense';
        }
        
        // Reset category on switch
        catInput.value = '';
        catCards.forEach(c => c.classList.remove('selected'));
        checkFormValidity();
    }

    typeExpense.addEventListener('change', updateTheme);
    typeIncome.addEventListener('change', updateTheme);

    // --- 2. Category Card Selection ---
    catCards.forEach(card => {
        card.addEventListener('click', function() {
            // Deselect all in same grid
            const siblings = this.parentElement.parentElement.querySelectorAll('.category-card');
            siblings.forEach(s => s.classList.remove('selected'));
            
            // Select this
            this.classList.add('selected');
            
            // Set value
            catInput.value = this.getAttribute('data-val');
            
            // Remove error immediately
            categoryFeedback.classList.add('d-none');
            
            checkFormValidity();
        });
    });

    // --- 3. Real-Time Validation ---
    function validateAmount() {
        const val = parseFloat(amountInput.value);
        if (isNaN(val) || val <= 0) {
            amountFeedback.classList.remove('d-none');
            return false;
        } else {
            amountFeedback.classList.add('d-none');
            return true;
        }
    }

    function validateDesc() {
        const val = descInput.value.trim();
        charCount.textContent = val.length;
        if (val.length === 0) {
            descIcon.classList.add('d-none');
            // Only show feedback if touched
            return false;
        } else {
            descIcon.classList.remove('d-none');
            descIcon.className = 'validation-icon text-success';
            descFeedback.classList.add('d-none');
            return true;
        }
    }

    function validateDate() {
        if (!dateInput.value) {
            dateIcon.classList.add('d-none');
            dateFeedback.classList.remove('d-none');
            return false;
        } else {
            dateIcon.classList.remove('d-none');
            dateFeedback.classList.add('d-none');
            return true;
        }
    }

    function checkFormValidity() {
        let valid = true;
        if (!validateAmount()) valid = false;
        if (catInput.value === '') valid = false;
        if (!validateDesc()) valid = false;
        if (!validateDate()) valid = false;

        saveBtn.disabled = !valid;
    }

    amountInput.addEventListener('input', checkFormValidity);
    descInput.addEventListener('input', () => { validateDesc(); checkFormValidity(); });
    descInput.addEventListener('blur', () => { if(descInput.value.trim()==='') descFeedback.classList.remove('d-none'); });
    dateInput.addEventListener('change', () => { validateDate(); checkFormValidity(); });

    // --- 4. AJAX Submission & Toast Notification ---
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        if(saveBtn.disabled) {
            if(catInput.value === '') categoryFeedback.classList.remove('d-none');
            return;
        }

        // Show loading state
        const oldBtnHtml = saveBtn.innerHTML;
        saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Saving...';
        saveBtn.disabled = true;

        const formData = new FormData(form);
        formData.append('ajax', '1'); // Tell backend we want JSON

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                // Show amazing Toast!
                const toastEl = document.getElementById('successToast');
                const msgEl = document.getElementById('toastMessage');
                
                msgEl.innerHTML = `<i class="bi bi-check-circle-fill me-2 fs-5"></i> <strong>Amazing!</strong><br>${data.message}`;
                
                // Color the toast
                toastEl.classList.remove('bg-success', 'bg-danger');
                if (data.type === 'expense') {
                    toastEl.classList.add('bg-danger');
                } else {
                    toastEl.classList.add('bg-success');
                }

                const bsToast = new bootstrap.Toast(toastEl, { delay: 2000 });
                bsToast.show();

                // Redirect after toast fade out
                setTimeout(() => {
                    window.location.href = 'index.php?page=dashboard';
                }, 2000);

            } else {
                alert('Error: ' + data.error);
                saveBtn.disabled = false;
                saveBtn.innerHTML = oldBtnHtml;
            }
        })
        .catch(err => {
            console.error('Save failed:', err);
            alert('A network error occurred. Check your connection.');
            saveBtn.disabled = false;
            saveBtn.innerHTML = oldBtnHtml;
        });
    });

    // Initial check
    updateTheme();
});
</script>

<?php include 'views/layout/footer.php'; ?>
