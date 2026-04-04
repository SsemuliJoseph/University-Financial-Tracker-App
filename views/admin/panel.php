<?php
// views/admin/panel.php
include 'views/layout/header.php';
?>

<div class="row mb-4">
    <div class="col-md-12">
        <h2>Admin Panel</h2>
        <p class="text-muted">Manage system users, change roles, and deactivate accounts.</p>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">All System Users</h5>
            </div>
            <div class="card-body p-0">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger m-3"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <?php if (isset($success)): ?>
                    <div class="alert alert-success m-3"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>

                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Registration Date</th>
                            <th>Current Role</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= htmlspecialchars($user['user_id']) ?></td>
                                <td><?= htmlspecialchars($user['name']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><?= htmlspecialchars($user['created_at']) ?></td>

                                <!-- Role Change Form -->
                                <td>
                                    <?php if ($user['user_id'] != $_SESSION['user_id']): ?>
                                        <!-- Only allow role changes if it's NOT the current admin admin looking at themselves -->
                                        <form action="index.php?page=admin_panel" method="POST" class="d-flex align-items-center">
                                            <input type="hidden" name="action" value="update_role">
                                            <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">

                                            <select name="role" class="form-select form-select-sm me-2" style="width: auto;">
                                                <option value="student" <?= $user['role'] === 'student' ? 'selected' : '' ?>>Student</option>
                                                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                                <option value="finance_officer" <?= $user['role'] === 'finance_officer' ? 'selected' : '' ?>>Finance Officer</option>
                                            </select>

                                            <button type="submit" class="btn btn-sm btn-outline-primary">Update</button>
                                        </form>
                                    <?php else: ?>
                                        <!-- For perfectly safety, just show the badge if it's their own row -->
                                        <span class="badge bg-dark mt-1 px-3 py-2"><?= ucfirst($user['role']) ?></span>
                                    <?php endif; ?>
                                </td>

                                <!-- Deactivate/Delete Action Button -->
                                <td class="text-center">
                                    <?php if ($user['user_id'] != $_SESSION['user_id']): ?>
                                        <!-- Deactivates/Deletes the user requiring a JS confirmation pop-up first -->
                                        <a href="index.php?page=admin_delete_user&id=<?= $user['user_id'] ?>"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('Are you sure you want to deactivate and delete this user entirely? This action cannot be undone.');">
                                            Delete User
                                        </a>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-secondary" disabled>Current Session</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
include 'views/layout/footer.php';
?>