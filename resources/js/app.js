import './bootstrap';
import 'trix/dist/trix.css';
import 'trix';

// --- Trix: text color support -----------------------------------------------
// Trix has no color button by default. We register a small, on-brand palette
// (reuses the app's own semantic tokens, so it stays consistent across every
// color scheme) as custom text attributes, then inject swatch buttons into
// every editor's toolbar. Colors are stored as CSS var references (not fixed
// hex), so previously-written stories re-render in whichever scheme is active.
const TRIX_TEXT_COLORS = {
    trixColorPrimary: { label: 'مهم', value: 'rgb(var(--color-primary))' },
    trixColorSuccess: { label: 'إيجابي', value: 'rgb(var(--color-success))' },
    trixColorWarning: { label: 'لاحظ', value: 'rgb(var(--color-warning))' },
    trixColorDanger: { label: 'تحذير', value: 'rgb(var(--color-danger))' },
    trixColorMuted: { label: 'هامشي', value: '#767676' },
};

document.addEventListener('trix-before-initialize', () => {
    Object.entries(TRIX_TEXT_COLORS).forEach(([name, { value }]) => {
        window.Trix.config.textAttributes[name] = {
            style: { color: value },
            parser: (element) => element.style.color === value,
            inheritable: true,
        };
    });
});

document.addEventListener('trix-initialize', (event) => {
    const toolbar = event.target.toolbarElement;
    if (!toolbar || toolbar.querySelector('[data-trix-color-tools]')) return;

    const group = document.createElement('span');
    group.className = 'trix-button-group trix-button-group--color-tools';
    group.setAttribute('data-trix-color-tools', '');

    Object.entries(TRIX_TEXT_COLORS).forEach(([name, { label, value }]) => {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'trix-button trix-color-swatch';
        button.style.setProperty('--trix-swatch-color', value);
        button.dataset.trixAttribute = name;
        button.title = label;
        group.appendChild(button);
    });

    const textTools = toolbar.querySelector('.trix-button-group--text-tools');
    (textTools ?? toolbar.firstElementChild)?.insertAdjacentElement('afterend', group);
});

// --- PWA: service worker registration + install prompt ---------------------
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch(() => {});
    });
}

// Capture the install prompt so a Settings button can trigger it.
window.deferredInstallPrompt = null;
window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    window.deferredInstallPrompt = e;
    window.dispatchEvent(new CustomEvent('pwa-installable'));
});
window.addEventListener('appinstalled', () => {
    window.deferredInstallPrompt = null;
    window.dispatchEvent(new CustomEvent('pwa-installed'));
});
