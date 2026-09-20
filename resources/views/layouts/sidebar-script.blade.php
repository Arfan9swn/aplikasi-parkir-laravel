<script>
(() => {
    const sidebar = document.getElementById('app-sidebar');
    const toggle = document.getElementById('sidebar-toggle');
    const tooltip = document.getElementById('sidebar-tooltip');
    const mobile = window.matchMedia('(max-width: 767px)');
    let preference = false;
    let target = null;
    let hideTimer;
    const scheduleHide = () => { hideTimer = setTimeout(hideTooltip, 150); };
    try { preference = localStorage.getItem('park.sidebar.collapsed') === 'true'; } catch (_) {}

    function hideTooltip() {
        clearTimeout(hideTimer);
        if (target) target.removeAttribute('aria-describedby');
        target = null;
        tooltip.hidden = true;
    }

    function setCollapsed(collapsed) {
        hideTooltip();
        document.body.dataset.sidebarCollapsed = String(collapsed);
        toggle.setAttribute('aria-expanded', String(!collapsed));
        const label = collapsed ? 'Perluas sidebar' : 'Ciutkan sidebar';
        toggle.setAttribute('aria-label', label);
        toggle.dataset.tooltip = label;
        toggle.querySelector('.sidebar-label').textContent = label;
    }

    function showTooltip(element, position) {
        hideTooltip();
        target = element;
        tooltip.textContent = element.dataset.tooltip;
        tooltip.hidden = false;
        element.setAttribute('aria-describedby', tooltip.id);
        tooltip.style.left = `${position.left}px`;
        tooltip.style.top = `${position.top}px`;
    }

    function showSidebarTooltip(element) {
        if (document.body.dataset.sidebarCollapsed !== 'true') return;
        const rect = element.getBoundingClientRect();
        showTooltip(element, {
            left: Math.min(sidebar.getBoundingClientRect().right + 8, window.innerWidth - tooltip.offsetWidth - 8),
            top: Math.max(8, Math.min(rect.top + (rect.height - tooltip.offsetHeight) / 2, window.innerHeight - tooltip.offsetHeight - 8)),
        });
    }

    function showPageTooltip(element) {
        const rect = element.getBoundingClientRect();
        showTooltip(element, {
            left: Math.max(8, Math.min(rect.left + (rect.width - tooltip.offsetWidth) / 2, window.innerWidth - tooltip.offsetWidth - 8)),
            top: Math.max(8, rect.top - tooltip.offsetHeight - 8),
        });
    }

    function bind(elements, show) {
        elements.forEach(element => {
            element.addEventListener('mouseenter', () => show(element));
            element.addEventListener('mouseleave', scheduleHide);
            element.addEventListener('focus', () => show(element));
            element.addEventListener('blur', hideTooltip);
            element.addEventListener('click', hideTooltip);
        });
    }

    setCollapsed(mobile.matches || preference);
    toggle.hidden = false;
    toggle.addEventListener('click', () => {
        const collapsed = document.body.dataset.sidebarCollapsed !== 'true';
        setCollapsed(collapsed);
        if (!mobile.matches) {
            preference = collapsed;
            try { localStorage.setItem('park.sidebar.collapsed', String(collapsed)); } catch (_) {}
        }
    });
    bind(Array.from(sidebar.querySelectorAll('[data-tooltip]')), showSidebarTooltip);
    bind(Array.from(document.querySelectorAll('[data-tooltip]')).filter(el => !sidebar.contains(el) && el !== toggle), showPageTooltip);
    tooltip.addEventListener('mouseenter', () => clearTimeout(hideTimer));
    tooltip.addEventListener('mouseleave', hideTooltip);
    sidebar.addEventListener('scroll', hideTooltip);
    window.addEventListener('resize', hideTooltip);
    mobile.addEventListener('change', () => setCollapsed(mobile.matches || preference));
    document.addEventListener('keydown', event => {
        if (event.key !== 'Escape') return;
        hideTooltip();
        if (mobile.matches && document.body.dataset.sidebarCollapsed === 'false') {
            setCollapsed(true);
            toggle.focus();
        }
    });
    document.addEventListener('click', event => {
        if (mobile.matches && !sidebar.contains(event.target)) setCollapsed(true);
    });

    // ---- Dark mode toggle (persists; falls back to system preference) ----
    const themeToggle = document.getElementById('theme-toggle');
    const themeRoot = document.documentElement;
    const storedTheme = (() => { try { return localStorage.getItem('park.theme'); } catch (_) { return null; } })();

    function syncThemeToggle() {
        const dark = themeRoot.classList.contains('dark');
        themeToggle.setAttribute('aria-pressed', String(dark));
        themeToggle.dataset.tooltip = dark ? 'Mode Terang' : 'Mode Gelap';
        themeToggle.setAttribute('aria-label', dark ? 'Aktifkan mode terang' : 'Aktifkan mode gelap');
        const label = themeToggle.querySelector('.sidebar-label');
        if (label) label.textContent = dark ? 'Mode Terang' : 'Mode Gelap';
    }

    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            const dark = themeRoot.classList.toggle('dark');
            try { localStorage.setItem('park.theme', dark ? 'dark' : 'light'); } catch (_) {}
            syncThemeToggle();
        });
        syncThemeToggle();

        // Follow OS changes only while the user has not picked a theme.
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', event => {
            if (storedTheme) return;
            themeRoot.classList.toggle('dark', event.matches);
            syncThemeToggle();
        });
    }
})();
</script>
