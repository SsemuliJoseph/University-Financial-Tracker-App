<?php
// views/layout/footer.php
// This file closes the HTML tags opened in header.php
?>
</div> <!-- Close Main Container -->
<?php if (isset($_SESSION['user_id'])): ?>

    <!-- Mobile Bottom Tab Bar (Upgrade 9) -->
    <nav class="bottom-tab-bar d-md-none">
        <a href="index.php?page=dashboard" class="nav-link <?php echo (!isset($_GET['page']) || $_GET['page'] == 'dashboard') ? 'active' : ''; ?>">
            <i class="bi bi-grid-1x2-fill"></i>
            <span>Home</span>
        </a>
        <a href="index.php?page=transactions" class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'transactions') ? 'active' : ''; ?>">
            <i class="bi bi-card-list"></i>
            <span>Transact</span>
        </a>
        <a href="index.php?page=transaction_add" class="nav-link fab-button">
            <i class="bi bi-plus-lg"></i>
        </a>
        <a href="index.php?page=budget_settings" class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'budget_settings') ? 'active' : ''; ?>">
            <i class="bi bi-piggy-bank"></i>
            <span>Budget</span>
        </a>
        <a href="index.php?page=reports" class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'reports') ? 'active' : ''; ?>">
            <i class="bi bi-pie-chart-fill"></i>
            <span>Reports</span>
        </a>
    </nav>

    </main> <!-- Close .main-content -->
    </div> <!-- Close .app-wrapper -->
<?php endif; ?>

<!-- Bootstrap 5 JS via CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Dark Mode Toggle Script (Upgrade 1 Requirement) -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Utility to handle dark mode toggling
        const htmlRoot = document.getElementById('html-root');
        const toggleBtns = [document.getElementById('themeToggleBtn'), document.getElementById('themeToggleBtnGuest')];
        const themeIcons = [document.getElementById('themeIcon'), document.getElementById('themeIconGuest')];

        function applyTheme(theme) {
            htmlRoot.setAttribute('data-bs-theme', theme);
            localStorage.setItem('theme', theme);

            themeIcons.forEach(icon => {
                if (icon) {
                    if (theme === 'dark') {
                        // Switch to sun icon when in dark mode
                        icon.classList.remove('bi-moon-stars-fill');
                        icon.classList.add('bi-sun-fill');
                        icon.classList.add('text-warning');
                    } else {
                        // Switch to moon icon when in light mode
                        icon.classList.remove('bi-sun-fill');
                        icon.classList.remove('text-warning');
                        icon.classList.add('bi-moon-stars-fill');
                    }
                }
            });
        }

        // Set initial icon correct based on saved theme
        const currentTheme = htmlRoot.getAttribute('data-bs-theme');
        applyTheme(currentTheme);

        toggleBtns.forEach(btn => {
            if (btn) {
                btn.addEventListener('click', () => {
                    const newTheme = htmlRoot.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
                    applyTheme(newTheme);
                });
            }
        });

        // Sidebar Toggle Script (Mobile & Desktop)
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');

        function toggleSidebar() {
            if (sidebar) {
                if (window.innerWidth <= 768) {
                    // Mobile slide behavior
                    sidebar.classList.toggle('show-mobile');
                    if (sidebarBackdrop) {
                        if (sidebar.classList.contains('show-mobile')) {
                            sidebarBackdrop.classList.add('show');
                        } else {
                            sidebarBackdrop.classList.remove('show');
                        }
                    }
                } else {
                    // Desktop collapse behavior
                    sidebar.classList.toggle('collapsed');
                }
            }
        }

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', toggleSidebar);
        }

        if (sidebarBackdrop) {
            sidebarBackdrop.addEventListener('click', toggleSidebar);
        }

        // UPGRADE 7: Real-Time Notification Polling
        const notifBadge = document.getElementById('notifBadge');
        const notifItemsContainer = document.getElementById('notifItemsContainer');
        const emptyNotifMsg = document.getElementById('emptyNotifMsg');

        if (notifBadge && notifItemsContainer) {
            function fetchNotifications() {
                fetch('index.php?page=notifications&action=fetch')
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            // Update badge
                            if (data.count > 0) {
                                notifBadge.innerText = data.count;
                                notifBadge.classList.remove('d-none');
                            } else {
                                notifBadge.classList.add('d-none');
                            }

                            // Update dropdown list
                            if (data.notifications && data.notifications.length > 0) {
                                notifItemsContainer.innerHTML = ''; // clear loading/empty
                                const typeColors = {
                                    'warning': 'text-warning bg-warning bg-opacity-10',
                                    'success': 'text-success bg-success bg-opacity-10',
                                    'info': 'text-primary bg-primary bg-opacity-10'
                                };
                                const typeIcons = {
                                    'warning': 'bi-exclamation-triangle-fill',
                                    'success': 'bi-check-circle-fill',
                                    'info': 'bi-info-circle-fill'
                                };

                                data.notifications.forEach(n => {
                                    const colorClass = typeColors[n.type] || typeColors['info'];
                                    const iconClass = typeIcons[n.type] || typeIcons['info'];

                                    // Format timestamp nicely
                                    const dateOpts = {
                                        month: 'short',
                                        day: 'numeric',
                                        hour: '2-digit',
                                        minute: '2-digit'
                                    };
                                    const niceDate = new Date(n.created_at).toLocaleDateString('en-US', dateOpts);

                                    const html = `
                                    <li class="dropdown-item px-3 py-3 border-bottom d-flex align-items-start gap-3 hover-lift shadow-sm-hover position-relative" style="white-space: normal; transition: all 0.2s;" id="notif-row-${n.notification_id}">
                                        <div class="rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0 ${colorClass}" style="width: 36px; height: 36px;">
                                            <i class="bi ${iconClass} m-0 fs-6"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <p class="mb-1 text-wrap small fw-medium" style="line-height: 1.4;">${n.message}</p>
                                            <small class="text-muted d-block" style="font-size: 0.7rem;">${niceDate}</small>
                                        </div>
                                        <button class="btn btn-link text-muted p-0 ms-2 text-decoration-none hover-danger flex-shrink-0" title="Mark as read" onclick="markNotificationRead(event, ${n.notification_id})">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </li>
                                `;
                                    notifItemsContainer.insertAdjacentHTML('beforeend', html);
                                });
                            } else {
                                notifItemsContainer.innerHTML = '<li class="p-4 text-center text-muted small">No new notifications.</li>';
                            }
                        }
                    })
                    .catch(err => console.error("Notification poll failed", err));
            }

            // Global function to mark read
            window.markNotificationRead = function(e, id) {
                e.preventDefault();
                e.stopPropagation();

                // Optimistic UI hiding
                const row = document.getElementById('notif-row-' + id);
                if (row) {
                    row.style.opacity = '0';
                    setTimeout(() => row.remove(), 200);
                }

                fetch('index.php?page=notifications&action=read', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            notification_id: id
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            if (data.count > 0) {
                                notifBadge.innerText = data.count;
                            } else {
                                notifBadge.classList.add('d-none');
                                notifItemsContainer.innerHTML = '<li class="p-4 text-center text-muted small">No new notifications.</li>';
                            }
                        }
                    });
            };

            // Fetch immediately, then every 60 seconds
            fetchNotifications();
            setInterval(fetchNotifications, 60000);
        }
    });
</script>


<!-- UPGRADE 10: Progressive Web App Scripts -->
<script>
    // Register Service Worker
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/finance-tracker/sw.js')
                .then(registration => {
                    console.log('SW registered: ', registration);
                }).catch(registrationError => {
                    console.log('SW registration failed: ', registrationError);
                });
        });
    }

    // PWA Install Button Logic
    let deferredPrompt;
    const installBtn = document.getElementById('installPwaBtn');

    if (installBtn) {
        window.addEventListener('beforeinstallprompt', (e) => {
            // Prevent Chrome 67 and earlier from automatically showing the prompt
            e.preventDefault();
            // Stash the event so it can be triggered later.
            deferredPrompt = e;
            // Update UI to notify the user they can add to home screen
            installBtn.classList.remove('d-none');
        });

        installBtn.addEventListener('click', (e) => {
            // Hide our user interface that shows our A2HS button
            installBtn.classList.add('d-none');
            // Show the prompt
            if (deferredPrompt) {
                deferredPrompt.prompt();
                // Wait for the user to respond to the prompt
                deferredPrompt.userChoice.then((choiceResult) => {
                    if (choiceResult.outcome === 'accepted') {
                        console.log('User accepted the A2HS prompt');
                    } else {
                        console.log('User dismissed the A2HS prompt');
                    }
                    deferredPrompt = null;
                });
            }
        });

        // Hide button once installed
        window.addEventListener('appinstalled', (evt) => {
            installBtn.classList.add('d-none');
            console.log('UFTS was installed');
        });
    }
</script>


<!-- UPGRADE 11: Performance & UX Polish JS -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // 1. Loading Spinner Overlay
        const overlay = document.getElementById('pageLoadingOverlay');
        if (overlay) {
            // Small delay to ensure smooth transition
            setTimeout(() => {
                overlay.classList.add('hidden');
                overlay.style.display = 'none';
            }, 150);
        }

        // 2. Dashboard Skeletons
        const skeletons = document.querySelectorAll('.skeleton');
        if (skeletons.length > 0) {
            setTimeout(() => {
                skeletons.forEach(el => el.classList.remove('skeleton'));
            }, 600); // Reveal after 600ms to simulate data load feeling
        }

        // 3. Form Submit Buttons Loading State
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    // To submit natively after disabling, wait a tick
                    setTimeout(() => {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>Saving...';
                    }, 10);
                }
            });
        });

        // 4. Keyboard Shortcuts
        document.addEventListener('keydown', (e) => {
            // Ignore if user is typing in an input or textarea
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.tagName === 'SELECT' || e.target.isContentEditable) {
                return;
            }

            // Only trigger if no modifier keys are pressed
            if (e.ctrlKey || e.altKey || e.metaKey || e.shiftKey) return;

            const key = e.key.toLowerCase();
            if (key === 'n') {
                e.preventDefault();
                window.location.href = 'index.php?page=transaction_add';
            } else if (key === 'd') {
                e.preventDefault();
                window.location.href = 'index.php?page=dashboard';
            } else if (key === 'r') {
                e.preventDefault();
                window.location.href = 'index.php?page=reports';
            }
        });
    });
</script>

<!-- UPGRADE 11: Performance & UX Polish JS -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // 1. Loading Spinner Overlay
        const overlay = document.getElementById('pageLoadingOverlay');
        if (overlay) {
            // Small delay to ensure smooth transition
            setTimeout(() => {
                overlay.classList.add('hidden');
                overlay.style.display = 'none';
            }, 150);
        }

        // 2. Dashboard Skeletons
        const skeletons = document.querySelectorAll('.skeleton');
        if (skeletons.length > 0) {
            setTimeout(() => {
                skeletons.forEach(el => el.classList.remove('skeleton'));
            }, 600); // Reveal after 600ms to simulate data load feeling
        }

        // 3. Form Submit Buttons Loading State
        const formSubmitters = document.querySelectorAll('button[type="submit"]');
        formSubmitters.forEach(btn => {
            btn.closest('form').addEventListener('submit', function() {
                setTimeout(() => {
                    btn.disabled = true;
                    const orig = btn.innerHTML;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>Saving...';
                    // Reset state after a short while so it doesnt stay stuck on validation failure.
                    setTimeout(() => {
                        btn.disabled = false;
                        btn.innerHTML = orig;
                    }, 4000);
                }, 10);
            });
        });

        // 4. Keyboard Shortcuts
        document.addEventListener('keydown', (e) => {
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.tagName === 'SELECT') {
                return;
            }
            if (e.ctrlKey || e.altKey || e.metaKey || e.shiftKey) return;

            const key = e.key.toLowerCase();
            if (key === 'n') {
                e.preventDefault();
                window.location.href = 'index.php?page=transaction_add';
            } else if (key === 'd') {
                e.preventDefault();
                window.location.href = 'index.php?page=dashboard';
            } else if (key === 'r') {
                e.preventDefault();
                window.location.href = 'index.php?page=reports';
            }
        });
    });
</script>

<!-- UPGRADE 11: Performance & UX Polish JS -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // 1. Loading Spinner Overlay
        const overlay = document.getElementById('pageLoadingOverlay');
        if (overlay) {
            setTimeout(() => {
                overlay.classList.add('hidden');
                overlay.style.display = 'none';
            }, 150);
        }

        // 2. Dashboard Skeletons
        const skeletons = document.querySelectorAll('.skeleton');
        if (skeletons.length > 0) {
            setTimeout(() => {
                skeletons.forEach(el => el.classList.remove('skeleton'));
            }, 600); // Reveal after 600ms
        }

        // 3. Form Submit Buttons Loading State
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', function() {
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    setTimeout(() => {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>Saving...';
                    }, 10);
                }
            });
        });

        // 4. Keyboard Shortcuts
        document.addEventListener('keydown', (e) => {
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.tagName === 'SELECT') {
                return;
            }
            if (e.ctrlKey || e.altKey || e.metaKey || e.shiftKey) return;

            const key = e.key.toLowerCase();
            if (key === 'n') {
                e.preventDefault();
                window.location.href = 'index.php?page=transaction_add';
            } else if (key === 'd') {
                e.preventDefault();
                window.location.href = 'index.php?page=dashboard';
            } else if (key === 'r') {
                e.preventDefault();
                window.location.href = 'index.php?page=reports';
            }
        });
    });


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

</body>

</html>

</html>