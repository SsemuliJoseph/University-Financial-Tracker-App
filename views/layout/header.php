<?php
// views/layout/header.php
// This file contains the top part of our HTML, included on every page.
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UFTS - Finance Tracker</title>
    <!-- Bootstrap 5 CSS via CDN for quick styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="index.php">UFTS</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <!-- Shown only to logged-in users -->
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?page=dashboard">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?page=transactions">Transactions</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?page=budget_settings">Budgets</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?page=reports">Reports</a>
                        </li>
                        <li class="nav-item me-2">
                            <a class="nav-link btn btn-success text-white px-3 fw-bold" href="index.php?page=transaction_add">+ Add Transaction</a>
                        </li>
                        <ul class="navbar-nav ms-auto border-start ms-2 ps-3">
                            <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'finance_officer'): ?>
                                <li class="nav-item border border-info rounded me-2">
                                    <a class="nav-link text-info fw-bold px-3" href="index.php?page=finance_reports">System Data</a>
                                </li>
                            <?php endif; ?>
                            
                            <!-- Only Admins can see the Admin Panel link -->
                            <?php if ($_SESSION['role'] === 'admin'): ?>
                                <li class="nav-item border border-warning rounded">
                                    <a class="nav-link text-warning fw-bold px-3" href="index.php?page=admin_panel">Admin Panel</a>
                                </li>
                            <?php endif; ?>
                            <li class="nav-item ms-2 pe-3">
                                <a class="nav-link" href="index.php?page=logout">Logout (<?= htmlspecialchars($_SESSION['name']) ?>)</a>
                            </li>
                        </ul>
                    <?php else: ?>
                        <!-- Shown only to guests -->
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?page=login">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?page=register">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <!-- Main Content Container -->
    <div class="container">