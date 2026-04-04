<?php
// controllers/AdminController.php
// Strictly for admin-level operations (managing users)

require_once 'models/User.php';

class AdminController
{

    // Validates the current session belongs to an Admin
    private function checkAdmin()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            // Kick them out to the dashboard immediately if they aren't an admin
            header("Location: index.php?page=dashboard");
            exit;
        }
    }

    // Displays the main Admin Panel and user list
    public function panel()
    {
        $this->checkAdmin(); // Enforce security

        $db = getConnection();
        $userModel = new User($db);

        // Process any role change requests
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_role') {
            $target_user_id = (int) $_POST['user_id'];
            $new_role = $_POST['role'];

            // Prevent the admin from accidentally changing their own role and locking themselves out!
            if ($target_user_id !== $_SESSION['user_id']) {
                if ($userModel->updateRole($target_user_id, $new_role)) {
                    $success = "User role successfully updated!";
                } else {
                    $error = "Failed to update user role.";
                }
            } else {
                $error = "You cannot change your own role!";
            }
        }

        // Fetch all users to display in the table
        $users = $userModel->getAllUsers();

        require 'views/admin/panel.php';
    }

    // Handles deleting/deactivating a user account
    public function deleteUser()
    {
        $this->checkAdmin(); // Enforce security

        if (isset($_GET['id'])) {
            $target_user_id = (int) $_GET['id'];

            // Prevent the admin from perfectly deleting themselves
            if ($target_user_id !== $_SESSION['user_id']) {
                $db = getConnection();
                $userModel = new User($db);
                $userModel->delete($target_user_id);
            }
        }

        // Redirect back to the admin panel
        header("Location: index.php?page=admin_panel");
        exit;
    }
}
