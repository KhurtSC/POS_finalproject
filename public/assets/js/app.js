document.addEventListener('DOMContentLoaded', () => {
    const themeToggle = document.querySelector('[data-theme-toggle]');
    if (themeToggle) {
        const syncThemeLabel = () => {
            themeToggle.textContent = document.documentElement.classList.contains('dark') ? 'Light' : 'Dark';
        };

        themeToggle.addEventListener('click', () => {
            document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light');
            syncThemeLabel();
        });

        syncThemeLabel();
    }

    document.querySelectorAll('[data-alert]').forEach((alert) => {
        setTimeout(() => {
            alert.style.transition = 'opacity 250ms ease, transform 250ms ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-4px)';
            setTimeout(() => alert.remove(), 300);
        }, 4000);
    });

    document.querySelectorAll('[data-confirm]').forEach((button) => {
        button.addEventListener('click', (event) => {
            const message = button.getAttribute('data-confirm') || 'Are you sure?';
            if (!window.confirm(message)) {
                event.preventDefault();
            }
        });
    });

    document.querySelectorAll('[data-close-modal]').forEach((button) => {
        button.addEventListener('click', () => button.closest('[data-modal]')?.classList.add('hidden'));
    });

    const notificationRoot = document.querySelector('[data-notifications]');
    if (notificationRoot) {
        const toggle = notificationRoot.querySelector('[data-notification-toggle]');
        const panel = notificationRoot.querySelector('[data-notification-panel]');
        const list = notificationRoot.querySelector('[data-notification-list]');
        const countBadge = notificationRoot.querySelector('[data-notification-count]');
        let latestId = 0;
        let unseen = 0;

        const renderNotifications = (notifications, append = false) => {
            if (!append) list.innerHTML = '';

            if (!notifications.length && !append) {
                list.innerHTML = '<p class="p-3 text-sm font-semibold text-slate-500">No recent notifications yet.</p>';
                return;
            }

            notifications.forEach((item) => {
                const row = document.createElement('div');
                row.className = 'rounded-md px-3 py-2 hover:bg-slate-50 dark:hover:bg-slate-800';
                row.innerHTML = `
                    <p class="font-bold text-slate-900 dark:text-white">${item.description || item.event}</p>
                    <p class="mt-0.5 text-xs font-semibold text-slate-500">${item.user || 'System'} ${item.created_at || ''}</p>
                `;
                list.prepend(row);
            });
        };

        const syncBadge = () => {
            countBadge.textContent = unseen;
            countBadge.classList.toggle('hidden', unseen <= 0);
        };

        const loadNotifications = async (increment = false) => {
            try {
                const response = await fetch(`/api/notifications${latestId ? `?after_id=${latestId}` : ''}`, {
                    headers: { Accept: 'application/json' },
                });
                if (!response.ok) return;

                const data = await response.json();
                const notifications = data.notifications || [];

                if (notifications.length) {
                    renderNotifications(notifications, latestId > 0);
                    latestId = Math.max(latestId, data.latest_id || 0);
                    if (increment && panel.classList.contains('hidden')) {
                        unseen += notifications.length;
                        syncBadge();
                    }
                } else if (!latestId) {
                    renderNotifications([]);
                }
            } catch {
                if (!latestId) {
                    list.innerHTML = '<p class="p-3 text-sm font-semibold text-red-500">Notifications are unavailable.</p>';
                }
            }
        };

        toggle.addEventListener('click', () => {
            panel.classList.toggle('hidden');
            unseen = 0;
            syncBadge();
        });

        document.addEventListener('click', (event) => {
            if (!notificationRoot.contains(event.target)) {
                panel.classList.add('hidden');
            }
        });

        loadNotifications(false);
        setInterval(() => loadNotifications(true), 15000);
    }

    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/service-worker.js').catch(() => {});
    }
});
