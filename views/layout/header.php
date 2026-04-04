<?php
// views/layout/header.php
// This file contains the top part of our HTML, included on every page.
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark" id="html-root">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UFTS - Finance Tracker</title>
    <!-- Bootstrap 5 CSS via CDN for quick styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CDN (Upgrade 1 Requirement) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS (Upgrade 1 Requirement) -->
    <link href="public/css/style.css" rel="stylesheet">

    <!-- Script to set dark theme instantly on load to avoid white flash -->
    <script>
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
        } else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches) {
            // If no preference is saved but OS wants light mode 
            document.documentElement.setAttribute('data-bs-theme', 'light');
        }
    </script>
</head>

<body class="app-body">
    <?php if (isset($_SESSION['user_id'])): ?>
        <!-- LOGGED IN USER VIEW - Sidebar Layout -->
        <div class="app-wrapper d-flex" style="min-height: 100vh;">

            <!-- Left Sidebar (Upgrade 1 Requirement) -->
            <aside class="sidebar d-flex flex-column p-3 shadow" id="sidebar">
                <a href="index.php" class="d-flex align-items-center justify-content-center mb-3 mb-md-0 mx-auto text-decoration-none border-bottom w-100 pb-3" style="color:white;">
                    <i class="bi bi-wallet2 fs-2 me-2 text-success"></i>
                    <span class="fs-3 fw-bold tracking-wide text-white">UFTS</span>
                </a>

                <ul class="nav nav-pills flex-column mb-auto mt-4 gap-2">
                    <li class="nav-item">
                        <a href="index.php?page=dashboard" class="nav-link <?php echo (!isset($_GET['page']) || $_GET['page'] == 'dashboard') ? 'active shadow-sm' : 'sidebar-link hover-lift'; ?>">
                            <i class="bi bi-grid-1x2-fill me-3"></i> Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="index.php?page=transactions" class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'transactions') ? 'active shadow-sm' : 'sidebar-link hover-lift'; ?>">
                            <i class="bi bi-card-list me-3"></i> Transactions
                        </a>
                    </li>
                    <li>
                        <a href="index.php?page=budget_settings" class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'budget_settings') ? 'active shadow-sm' : 'sidebar-link hover-lift'; ?>">
                            <i class="bi bi-piggy-bank me-3"></i> Budgets
                        </a>
                    </li>
                    <li>
                        <a href="index.php?page=reports" class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'reports') ? 'active shadow-sm' : 'sidebar-link hover-lift'; ?>">
                            <i class="bi bi-pie-chart-fill me-3"></i> Reports
                        </a>
                    </li>

                    <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'finance_officer'): ?>
                        <hr class="border-secondary opacity-25 my-2">
                        <li>
                            <a href="index.php?page=finance_reports" class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'finance_reports') ? 'active bg-success text-white shadow-sm' : 'sidebar-link hover-lift text-success'; ?>">
                                <i class="bi bi-bar-chart-steps me-3"></i> System Data
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <li>
                            <a href="index.php?page=admin_panel" class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'admin_panel') ? 'active bg-warning text-dark shadow-sm' : 'sidebar-link hover-lift text-warning'; ?>">
                                <i class="bi bi-shield-lock-fill me-3"></i> Admin Panel
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>

                <!-- User Profile & Logout Bottom Section -->
                <div class="user-profile-box mt-auto pt-3 border-top border-secondary border-opacity-25 w-100">
                    <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle px-2 py-2 rounded profile-dropdown hover-lift w-100" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle fs-3 me-3 text-secondary"></i>
                        <div class="overflow-hidden">
                            <strong class="d-block lh-1 text-truncate" style="color: white;"><?= htmlspecialchars($_SESSION['name']) ?></strong>
                            <small class="text-secondary d-block mt-1 text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;"><?= htmlspecialchars($_SESSION['role']) ?></small>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark text-small shadow-lg w-100 mt-2 rounded-3 border-0" aria-labelledby="dropdownUser">
                        <li><a class="dropdown-item text-danger py-2 fw-semibold" href="index.php?page=logout"><i class="bi bi-box-arrow-right me-3"></i>Logout</a></li>
                    </ul>
                </div>
            </aside>

            <!-- Main Content Area to the right -->
            <main class="main-content flex-grow-1 overflow-auto d-flex flex-column" style="height: 100vh;">

                <!-- Topbar (Upgrade 1 Requirement) -->
                <header class="topbar sticky-top d-flex justify-content-between align-items-center p-3 shadow-sm bg-body">
                    <div class="d-flex align-items-center">
                        <!-- Mobile sidebar toggle -->
                        <button class="btn btn-outline-secondary border-0 d-md-none me-3 shadow-sm hover-lift" id="sidebarToggle">
                            <i class="bi bi-list fs-4"></i>
                        </button>
                        <h4 class="m-0 d-inline-block fw-bold text-body-emphasis tracking-tight">
                            <?php
                            // Quick logic to get current page name for top bar
                            $p = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
                            $titles = [
                                'dashboard' => 'Dashboard Overview',
                                'transactions' => 'Your Transactions',
                                'budget_settings' => 'Budget Manager',
                                'reports' => 'Financial Reports',
                                'transaction_add' => 'Add Transaction',
                                'finance_reports' => 'System Export Data',
                                'admin_panel' => 'Site Administration'
                            ];
                            echo isset($titles[$p]) ? $titles[$p] : ucfirst(str_replace('_', ' ', $p));
                            ?>
                        </h4>
                    </div>
                    <div class="d-flex align-items-center gap-2 gap-md-3">

                        <!-- UPGRADE 7: Real-Time Notification Bell -->
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary rounded-circle shadow-sm border-0 bg-body-tertiary hover-lift position-relative d-flex align-items-center justify-content-center"
                                type="button" id="notifDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="width:42px; height:42px;">
                                <i class="bi bi-bell-fill fs-5"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light d-none" id="notifBadge" style="font-size: 0.65rem; padding: 0.25em 0.5em;">
                                    0
                                </span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-0 mt-2 slide-in-top" aria-labelledby="notifDropdown" style="width: 320px; max-height: 400px; overflow-y: auto;" id="notifList">
                                <li class="p-3 border-bottom text-center text-muted small fw-bold text-uppercase tracking-wider rounded-top-4 bg-body-tertiary">
                                    Notifications
                                </li>
                                <div id="notifItemsContainer">
                                    <li class="p-4 text-center text-muted small" id="emptyNotifMsg">No new notifications.</li>
                                </div>
                            </ul>
                        </div>

                        <!-- Dark Mode Toggle Button (Upgrade 1 Requirement) -->
                        <button id="themeToggleBtn" class="btn btn-outline-secondary rounded-circle shadow-sm border-0 bg-body-tertiary hover-lift d-flex align-items-center justify-content-center" style="width:42px; height:42px;">
                            <i class="bi bi-moon-stars-fill fs-5" id="themeIcon"></i>
                        </button>

                        <!-- Quick Add Button inside top bar -->
                        <a class="btn btn-success rounded-pill px-3 px-md-4 shadow hover-lift fw-semibold" href="index.php?page=transaction_add" style="transition:all 0.2s ease;">
                            <i class="bi bi-plus-lg fw-bold me-1"></i> <span class="d-none d-sm-inline">Add Transaction</span>
                        </a>
                    </div>
                </header>

                <!-- Main Content Container with Fade-In Animation -->
                <div class="app-content-container container-fluid animate-fade-in p-4 flex-grow-1">

                <?php else: ?>

                    <!-- GUEST NAVBAR -->
                    <nav class="navbar navbar-expand-lg shadow-sm bg-body py-3 sticky-top">
                        <div class="container">
                            <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="index.php">
                                <i class="bi bi-wallet2 fs-3 text-success"></i>
                                <span class="tracking-wide">UFTS</span>
                            </a>

                            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#guestNav">
                                <span class="navbar-toggler-icon"></span>
                            </button>

                            <div class="collapse navbar-collapse" id="guestNav">
                                <ul class="navbar-nav ms-auto align-items-center gap-2">
                                    <li class="nav-item me-3 d-none d-lg-block">
                                        <!-- Guest Dark Mode Toggle -->
                                        <button id="themeToggleBtnGuest" class="btn btn-outline-secondary rounded-circle shadow-sm border-0 bg-body-tertiary hover-lift" style="width:40px; height:40px;">
                                            <i class="bi bi-moon-stars-fill" id="themeIconGuest"></i>
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link fw-semibold hover-lift" href="index.php?page=login">Login</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="btn btn-primary rounded-pill px-4 shadow-sm hover-lift fw-bold" href="index.php?page=register">Create Account</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </nav>
                    <div class="container animate-fade-in mt-5">
                    <?php endif; ?>