<?php
// views/auth/register.php 
// Include the shared header with our Bootstrap styles/navbar
include 'views/layout/header.php';
?>

<div class="row justify-content-center mt-3 mt-md-5">
    <div class="col-12 col-sm-10 col-md-8 col-lg-6">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4>User Registration</h4>
            </div>
            <div class="card-body">
                <!-- If the Controller found an error, it will pass it here to display -->
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <?php if (isset($success)): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>

                <!-- The form submits a POST request to ?page=register -->
                <form action="index.php?page=register" method="POST">
                    <div class="mb-3">
                        <label>Full Name</label>
                        <!-- htmlspecialchars prevents XSS (Cross Site Scripting) if we echo user input back -->
                        <input type="text" name="name" class="form-control" required
                            value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>">
                    </div>
                    <div class="mb-3">
                        <label>Email Address</label>
                        <input type="email" name="email" class="form-control" required
                            value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                    </div>
                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required minlength="6">
                    </div>
                    <div class="mb-3">
                        <label>Confirm Password</label>
                        <input type="password" name="password_confirm" class="form-control" required minlength="6">
                    </div>

                    <div class="mb-3">
                        <label>Role</label>
                        <select name="role" class="form-select" required>
                            <option value="student">Student</option>
                            <option value="finance_officer">Finance Officer</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Create Account</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
// Include the shared footer 
include 'views/layout/footer.php';
?>