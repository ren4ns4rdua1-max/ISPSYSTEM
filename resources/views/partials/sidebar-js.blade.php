<script>
    let collapsed = false;

    function toggleSidebar() {
        collapsed = !collapsed;
        const sidebar = document.getElementById('sidebar');
        const main    = document.getElementById('main-content');
        const icon    = document.getElementById('toggle-icon');
        if (collapsed) {
            sidebar.style.width = '72px';
            main.style.marginLeft = '72px';
            main.style.width = 'calc(100% - 72px)';
            sidebar.classList.add('sidebar-collapsed');
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>';
        } else {
            sidebar.style.width = '260px';
            main.style.marginLeft = '260px';
            main.style.width = 'calc(100% - 260px)';
            sidebar.classList.remove('sidebar-collapsed');
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>';
        }
    }

    function toggleMobileSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('mobile-overlay');
        if (!sidebar || !overlay) return;
        sidebar.classList.toggle('mobile-open');
        overlay.classList.toggle('hidden', !sidebar.classList.contains('mobile-open'));
        document.body.style.overflow = sidebar.classList.contains('mobile-open') ? 'hidden' : '';
    }

    function closeMobileSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('mobile-overlay');
        if (!sidebar || !overlay) return;
        sidebar.classList.remove('mobile-open');
        overlay.classList.add('hidden');
        document.body.style.overflow = '';
    }

    window.addEventListener('resize', function() {
        if (window.innerWidth >= 1024) {
            closeMobileSidebar();
            const main = document.getElementById('main-content');
            if (main) {
                main.style.marginLeft = collapsed ? '72px' : '260px';
                main.style.width = collapsed ? 'calc(100% - 72px)' : 'calc(100% - 260px)';
            }
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        // Close sidebar on nav link click on mobile
        if (window.innerWidth < 1024) {
            document.querySelectorAll('.nav-item-inner').forEach(function(link) {
                link.addEventListener('click', closeMobileSidebar);
            });
        }

        // Auto-wrap tables in overflow container on small screens
        if (window.innerWidth < 768) {
            document.querySelectorAll('table').forEach(function(table) {
                if (!table.closest('.overflow-x-auto')) {
                    var wrapper = document.createElement('div');
                    wrapper.style.cssText = 'overflow-x:auto;-webkit-overflow-scrolling:touch;';
                    table.parentNode.insertBefore(wrapper, table);
                    wrapper.appendChild(table);
                }
            });
        }

        // Make topbar search hidden on very small screens
        if (window.innerWidth < 480) {
            document.querySelectorAll('.topbar .hidden.md\\:block').forEach(function(el) {
                el.style.display = 'none';
            });
        }
    });
</script>
