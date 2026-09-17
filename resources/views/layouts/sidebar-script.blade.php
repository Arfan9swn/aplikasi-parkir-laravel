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

    function showTooltip(element) {
        hideTooltip();
        if (document.body.dataset.sidebarCollapsed !== 'true') return;
        target = element;
        tooltip.textContent = element.dataset.tooltip;
        tooltip.hidden = false;
        element.setAttribute('aria-describedby', tooltip.id);
        const rect = element.getBoundingClientRect();
        tooltip.style.left = `${Math.min(sidebar.getBoundingClientRect().right + 8, window.innerWidth - tooltip.offsetWidth - 8)}px`;
        tooltip.style.top = `${Math.max(8, Math.min(rect.top + (rect.height - tooltip.offsetHeight) / 2, window.innerHeight - tooltip.offsetHeight - 8))}px`;
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
    sidebar.querySelectorAll('[data-tooltip]').forEach(element => {
        element.addEventListener('mouseenter', () => showTooltip(element));
        element.addEventListener('mouseleave', scheduleHide);
        element.addEventListener('focus', () => showTooltip(element));
        element.addEventListener('blur', hideTooltip);
        element.addEventListener('click', hideTooltip);
    });
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
})();
</script>
