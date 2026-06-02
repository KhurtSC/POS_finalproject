document.addEventListener('DOMContentLoaded', () => {
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
});
