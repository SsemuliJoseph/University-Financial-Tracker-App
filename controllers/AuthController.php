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
        // If the request method is POST, someone filled out the form and clicked "Login"
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = getConnection();
            $userModel = new User($db);
            
            $email = trim($_POST['email']);
            $password = $_POST['password'];

            // Ask the Model to see if the credentials are valid
            $user = $userModel->login($email, $password);

            if ($user !== false) {
                // SUCCESS! They logged in correctly. Let's create a session!
                // $_SESSION is a superglobal array that persists across multiple page loads for a single user
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['role'] = $user['role']; // 'student', 'admin', or 'finance_officer'

                // Let's redirect them away from the login page and towards their dashboard
                // header("Location: ...") tells the browser to automatically load a new URL
                header("Location: index.php?page=dashboard");
                exit; // Always put exit; immediately after a header redirect to stop executing the rest of the file
            } else {
                $error = "Invalid email address or password.";
            }
        }

        // Require (load) the HTML view for the login form
        require 'views/auth/login.php';
    }
}
