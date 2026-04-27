<?php include 'views/layout/header.php'; ?>

<div class="row mt-4">
    <div class="col-md-4">
        <div class="card shadow-sm border-0 mb-4 slide-in-bottom">
            <div class="card-body text-center p-4">
                <div class="mb-3 position-relative d-inline-block">
                    <?php if (!empty($user['avatar'])): ?>
                        <img src="<?= htmlspecialchars($user['avatar']) ?>" alt="Avatar" class="rounded-circle shadow" style="width: 120px; height: 120px; object-fit: cover;">
                    <?php else: ?>
                        <div class="rounded-circle shadow bg-primary text-white d-flex align-items-center justify-content-center mx-auto" style="width: 120px; height: 120px; font-size: 3rem;">
                            <?= strtoupper(substr($user['name'], 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                </div>
                <h4 class="mb-0 fw-bold"><?= htmlspecialchars($user['name']) ?></h4>
                <p class="text-muted mb-0"><?= ucfirst(htmlspecialchars($user['role'])) ?></p>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow-sm border-0 slide-in-right">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="mb-0 fw-bold"><i class="bi bi-person-gear text-primary me-2"></i>Profile Settings</h5>
            </div>
            <div class="card-body p-4">
                <?php if (isset($success)): ?>
                    <div class="alert alert-success border-0 shadow-sm"><i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger border-0 shadow-sm"><i class="bi bi-exclamation-octagon-fill me-2"></i><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <ul class="nav text-primary nav-tabs mb-4" id="profileTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">General</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button" role="tab">Security</button>
                    </li>
                </ul>

                <div class="tab-content" id="profileTabsContent">
                    <!-- General Settings -->
                    <div class="tab-pane fade show active" id="general" role="tabpanel">
                        <form action="index.php?page=profile" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="update_profile" value="1">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Display Name</label>
                                <input type="text" name="name" class="form-control bg-light" value="<?= htmlspecialchars($user['name']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Email Address <small class="text-danger">(Cannot be changed)</small></label>
                                <input type="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" readonly disabled>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Preferred Currency</label>
                                <select name="currency" class="form-select bg-light">
                                    <option value="UGX" <?= (isset($user['currency']) && $user['currency'] === 'UGX') ? 'selected' : '' ?>>UGX - Uganda Shilling</option>
                                    <option value="USD" <?= (isset($user['currency']) && $user['currency'] === 'USD') ? 'selected' : '' ?>>USD - US Dollar</option>
                                    <option value="EUR" <?= (isset($user['currency']) && $user['currency'] === 'EUR') ? 'selected' : '' ?>>EUR - Euro</option>
                                    <option value="GBP" <?= (isset($user['currency']) && $user['currency'] === 'GBP') ? 'selected' : '' ?>>GBP - British Pound</option>
                                    <option value="KES" <?= (isset($user['currency']) && $user['currency'] === 'KES') ? 'selected' : '' ?>>KES - Kenyan Shilling</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Profile Picture</label>
                                <input type="file" name="avatar" class="form-control bg-light" accept="image/*">
                            </div>
                            <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-2"></i>Save Changes</button>
                        </form>
                    </div>

                    <!-- Security Settings -->
                    <div class="tab-pane fade" id="security" role="tabpanel">
                        <form action="index.php?page=profile" method="POST">
                            <input type="hidden" name="update_password" value="1">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Current Password</label>
                                <input type="password" name="current_password" class="form-control bg-light" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">New Password</label>
                                <input type="password" name="new_password" class="form-control bg-light" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Confirm New Password</label>
                                <input type="password" name="confirm_password" class="form-control bg-light" required>
                            </div>
                            <button type="submit" class="btn btn-warning px-4"><i class="bi bi-shield-lock me-2"></i>Update Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Find all alerts with the 'alert-dismissible' class
        const alerts = document.querySelectorAll('.alert-dismissible');
        
        if (alerts.length > 0) {
            // Wait 5 seconds (5000ms), then close them
            setTimeout(function() {
                alerts.forEach(function(alertNode) {
                    // Use Bootstrap's Alert instance to properly close and animate the dismissal
                    const alertInstance = new bootstrap.Alert(alertNode);
                    alertInstance.close();
                });
            }, 5000);
        }
    });
</script>
<?php include 'views/layout/footer.php'; ?>
