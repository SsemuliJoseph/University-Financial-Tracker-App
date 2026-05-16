<?php
// views/auth/login.php 
include 'views/layout/header.php';
?>

<div class="row justify-content-center mt-3 mt-md-5">
    <div class="col-12 col-sm-10 col-md-8 col-lg-6">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4>System Login</h4>
            </div>
            <div class="card-body">
                <?php if (isset($_GET['msg']) && $_GET['msg'] === 'logged_out'): ?>
                    <div class="alert alert-info">You have securely logged out.</div>
                <?php endif; ?>

                <!-- If the Controller found an error, output it dynamically -->
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form action="index.php?page=login" method="POST" role="form" aria-labelledby="login-heading">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" name="email" id="email" class="form-control" required
                            value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" aria-describedby="email-help">
                        <div id="email-help" class="form-text">Enter your registered email address.</div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control" required aria-describedby="password-help">
                        <div id="password-help" class="form-text">Enter your password.</div>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" name="remember_me" id="rememberMe" aria-describedby="remember-help">
                        <label class="form-check-label" for="rememberMe">Remember me</label>
                        <div id="remember-help" class="form-text">Keep me logged in for 30 days.</div>
                    </div>


                    <button type="submit" class="btn btn-primary w-100" aria-describedby="login-button-help">Login</button>
                    <div id="login-button-help" class="form-text text-center">Click to sign in to your account.</div>
                </form>
            </div>
            <div class="card-footer text-center">
                <!-- A small helper link back to the registration page -->
                <small>Don't have an account? <a href="index.php?page=register">Register here</a></small>
            </div>
        </div>
    </div>
</div>

<?php
include 'views/layout/footer.php';
?>