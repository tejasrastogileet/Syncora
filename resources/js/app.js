import './bootstrap';

function initializeSyncoraUi() {
    const shell = document.getElementById('app-shell');
    const sidebarToggles = document.querySelectorAll('[data-sidebar-toggle]');
    const closeSidebarButtons = document.querySelectorAll('[data-sidebar-close]');

    const setSidebarState = (open) => {
        if (! shell) return;

        const mobile = window.matchMedia('(max-width: 760px)').matches;
        if (mobile) {
            shell.classList.toggle('mobile-sidebar-open', open);
        } else {
            shell.classList.toggle('sidebar-collapsed', ! open);
        }

        sidebarToggles.forEach((button) => {
            button.setAttribute('aria-expanded', String(open));
            button.setAttribute('aria-label', open ? 'Collapse navigation' : 'Expand navigation');
        });
    };

    sidebarToggles.forEach((button) => {
        button.addEventListener('click', () => {
            const mobile = window.matchMedia('(max-width: 760px)').matches;
            const isOpen = mobile
                ? shell?.classList.contains('mobile-sidebar-open')
                : ! shell?.classList.contains('sidebar-collapsed');

            setSidebarState(! isOpen);
        });
    });

    closeSidebarButtons.forEach((button) => {
        button.addEventListener('click', () => setSidebarState(false));
    });

    document.addEventListener('keydown', (event) => {
        if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 'k') {
            const search = document.getElementById('global-meeting-search');
            if (search) {
                event.preventDefault();
                search.focus();
            }
        }

        if (event.key === 'Escape') {
            shell?.classList.remove('mobile-sidebar-open');
            document.querySelectorAll('details[open]').forEach((details) => details.removeAttribute('open'));
        }
    });

    document.querySelectorAll('[data-password-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const input = document.getElementById(button.getAttribute('aria-controls'));
            if (! input) return;

            const visible = input.type === 'password';
            input.type = visible ? 'text' : 'password';
            button.textContent = visible ? 'Hide' : 'Show';
            button.setAttribute('aria-label', visible ? 'Hide password' : 'Show password');
        });
    });

    document.querySelectorAll('[data-loading-form]').forEach((form) => {
        form.addEventListener('submit', () => {
            const button = form.querySelector('[data-loading-label]');
            if (! button) return;

            button.disabled = true;
            button.textContent = button.dataset.loadingLabel;
        });
    });

    document.querySelectorAll('[data-copy-target]').forEach((button) => {
        button.addEventListener('click', async () => {
            const source = document.getElementById(button.dataset.copyTarget);
            if (! source) return;

            const text = source.innerText.trim();
            if (! text) return;

            try {
                await navigator.clipboard.writeText(text);
                button.dataset.originalLabel ??= button.textContent;
                button.textContent = 'Copied';
                window.setTimeout(() => {
                    button.textContent = button.dataset.originalLabel;
                }, 1600);
            } catch {
                button.textContent = 'Copy unavailable';
                window.setTimeout(() => {
                    button.textContent = button.dataset.originalLabel || 'Copy';
                }, 2200);
            }
        });
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeSyncoraUi, { once: true });
} else {
    initializeSyncoraUi();
}

window.meetingRealtime = {
    subscribe(meetingId, callback) {
        if (! window.Echo || ! meetingId) {
            return null;
        }

        const channel = window.Echo.private(`meeting.${meetingId}`);
        channel.listen('.meeting.processing.updated', callback);

        return channel;
    },
};
