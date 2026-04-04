<?php
// controllers/AuthController.php
// The controller handles form logic, talks to models, and loads the correct views

require_once 'models/User.php';

class AuthController
{

    // This method handles the logic for the Registration page
    public function register()
    {
        // If the request method is POST, someone filled out the form and clicked "Create Account"
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Connect to database and load our Model
            $db = getConnection();
            $userModel = new User($db);

            // Sanitize variables to remove trailing whitespace created by mistake
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $password_confirm = $_POST['password_confirm'];

            // Validation 1: Do passwords match?
            if ($password !== $password_confirm) {
                $error = "Passwords do not match!";
            }
            // Validation 2: Does email already exist?
            elseif ($userModel->emailExists($email)) {
                $error = "An account with this email already exists.";
            } else {
                // Try to create the user via the Model
                if ($userModel->create($name, $email, $password, 'student')) {
                    $success = "Registration successful! You can now login.";
                    // We successfully registered, so we'll intentionally empty POST so the form clears
                    $_POST = [];
                } else {
                    $error = "Something went wrong creating your account.";
                }
            }
        }

        // Require (load) the HTML view and pass $error or $success variables down to it
        require 'views/auth/register.php';
    }

    // This method handles the logic for the Login page
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = getConnection();
            $userModel = new User($db);
            
            $email = trim($_POST['email']);
            $password = $_POST['password'];

            $userCheck = $userModel->getByEmail($email);
            if ($userCheck) {
                if ($userCheck['locked_until'] !== null && strtotime($userCheck['locked_until']) > time()) {
                    $minutesLeft = ceil((strtotime($userCheck['locked_until']) - time()) / 60);
                    $error = "Account locked. Try again in $minutesLeft minute(s).";
                    require 'views/auth/login.php'; return;
                }
                $user = $userModel->login($email, $password);
                if ($user !== false) {
                    $userModel->resetFailedAttempts($email);
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['name'] = $user['name'];
                    $_SESSION['role'] = $user['role'];
                    if (isset($user['currency'])) $_SESSION['currency'] = $user['currency'];
                    $_SESSION['avatar'] = $user['avatar'] ?? null;

                    if (isset($_POST['remember_me'])) {
                        $token = bin2hex(random_bytes(32));
                        $userModel->updateRememberToken($user['user_id'], $token);
                        setcookie('remember_me', $token, time() + (86400 * 30), "/");
                    }
                    header("Location: index.php?page=dashboard"); exit;
                } else {
                    $userModel->incrementFailedAttempts($email);
                    $attempts = $userCheck['failed_attempts'] + 1;
                    if ($attempts >= 5) {
                        $userModel->lockAccount($email);
                        $error = "Account locked for 10 minutes.";
                    } else {
                        $left = 5 - $attempts;
                        $error = "Invalid password. $left attempt(s) remaining.";
                    }
                }
            } else {
                $error = "Invalid email or password.";
            }
        }
        require 'views/auth/login.php';
    }

    public function profile()
    {
        if (!isset($_SESSION['user_id'])) { header("Location: index.php?page=login"); exit; }
        $db = getConnection(); $userModel = new User($db);
        $user = $userModel->getById($_SESSION['user_id']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['update_profile'])) {
                $name = trim($_POST['name']); $currency = trim($_POST['currency']); $avatar = $user['avatar'];
                if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                    if (!is_dir('public/uploads/avatars')) mkdir('public/uploads/avatars', 0777, true);
                    $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                        $dest = 'public/uploads/avatars/avatar_' . $_SESSION['user_id'] . '_' . time() . '.' . $ext;
                        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $dest)) $avatar = $dest;
                    }
                }
                if ($userModel->updateProfile($_SESSION['user_id'], $name, $currency, $avatar)) {
                    $_SESSION['name'] = $name; $_SESSION['currency'] = $currency; $_SESSION['avatar'] = $avatar;
                    $success = "Profile updated!"; $user = $userModel->getById($_SESSION['user_id']);
                } else $error = "Failed to update profile.";
            }
            if (isset($_POST['update_password'])) {
                if (!password_verify($_POST['current_password'], $user['password'])) $error = "Wrong password.";
                elseif ($_POST['new_password'] !== $_POST['confirm_password']) $error = "Passwords mismatch.";
                else {
                    if ($userModel->updatePassword($_SESSION['user_id'], $_POST['new_password'])) $success = "Password changed!";
                    else $error = "Failed to change password.";
                }
            }
        }
        require 'views/profile.php';
    }
}
