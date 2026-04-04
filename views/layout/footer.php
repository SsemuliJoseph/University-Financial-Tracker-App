<?php
// views/layout/footer.php
// This file closes the HTML tags opened in header.php
?>
</div> <!-- Close Main Container -->
<?php if (isset($_SESSION['user_id'])): ?>
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

        // Mobile Sidebar Toggle Script
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        if (sidebarToggle && sidebar) {
            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('show-mobile');
            });
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

</body>

</html>