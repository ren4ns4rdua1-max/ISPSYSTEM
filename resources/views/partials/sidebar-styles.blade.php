<style>
    *, body { font-family: 'DM Sans', sans-serif; }
    .font-display { font-family: 'Syne', sans-serif; }

    /* ── Base layout ── */
    body { overflow: hidden; height: 100vh; }

    #sidebar {
        transition: width 0.35s cubic-bezier(0.2,0.9,0.4,1.1);
        background: linear-gradient(180deg,#0a0c18 0%,#0f111e 100%);
        backdrop-filter: blur(2px);
        position: fixed; top:0; left:0; bottom:0; z-index:50;
        width: 260px;
    }
    #main-content {
        transition: margin-left 0.35s cubic-bezier(0.2,0.9,0.4,1.1);
        margin-left: 260px;
        width: calc(100% - 260px);
        height: 100vh; overflow: hidden;
        display: flex; flex-direction: column;
    }

    /* ── Tablet & Mobile sidebar slide ── */
    @media (max-width: 1023px) {
        #sidebar {
            transform: translateX(-100%);
            transition: transform 0.3s ease-in-out;
            width: 260px !important;
        }
        #sidebar.mobile-open { transform: translateX(0); }
        #main-content { margin-left: 0 !important; width: 100% !important; }
        .mobile-menu-btn { display: block !important; }
    }

    /* ── Small mobile ── */
    @media (max-width: 639px) {
        body { overflow: auto; height: auto; min-height: 100vh; }
        #main-content { height: auto; min-height: 100vh; overflow: visible; }
        .main-scroll { overflow: visible !important; height: auto !important; }
        .topbar { position: sticky; top: 0; z-index: 40; }
        .topbar header { flex-wrap: wrap; gap: 0.5rem; padding: 0.75rem 1rem !important; }
        main.p-6, main.p-7 { padding: 0.75rem !important; }
        .px-7 { padding-left: 1rem !important; padding-right: 1rem !important; }
        .px-6 { padding-left: 0.75rem !important; padding-right: 0.75rem !important; }
        .gap-5, .gap-6 { gap: 0.75rem !important; }
        .space-y-5 > * + *, .space-y-6 > * + * { margin-top: 0.75rem !important; }
        .w-64 { width: 100% !important; }
        .text-\[20px\] { font-size: 1rem !important; }
        /* Stack grid columns */
        .lg\:grid-cols-3 { grid-template-columns: 1fr !important; }
        .lg\:col-span-2 { grid-column: span 1 !important; }
        .md\:grid-cols-2 { grid-template-columns: 1fr !important; }
        .md\:grid-cols-3 { grid-template-columns: 1fr !important; }
        .sm\:grid-cols-2 { grid-template-columns: 1fr 1fr !important; }
        /* Tables always scroll */
        table { min-width: 600px; }
        .overflow-x-auto { overflow-x: auto !important; -webkit-overflow-scrolling: touch; }
        /* Action buttons wrap */
        .flex.items-center.justify-end.gap-2 { flex-wrap: wrap; }
        /* Hide desktop search in topbar */
        .hidden.md\:block { display: none !important; }
        /* Stat cards 2 col */
        .xl\:grid-cols-4 { grid-template-columns: 1fr 1fr !important; }
        .lg\:grid-cols-4 { grid-template-columns: 1fr 1fr !important; }
        /* Form cards full width */
        .max-w-3xl, .max-w-2xl { max-width: 100% !important; }
    }

    /* ── Sidebar collapsed ── */
    .collapsible { transition: opacity .25s ease, max-width .3s ease; overflow:hidden; white-space:nowrap; }
    .sidebar-collapsed .collapsible { opacity:0; max-width:0 !important; pointer-events:none; }
    .sidebar-collapsed .nav-item-inner { justify-content:center; padding-left:.75rem; padding-right:.75rem; }
    .sidebar-collapsed .sec-lbl { opacity:0; height:0; margin:0; padding:0; overflow:hidden; }

    /* ── Nav items ── */
    .nav-active-bar {
        position:absolute; left:0; top:50%; transform:translateY(-50%);
        width:3px; height:60%; border-radius:0 6px 6px 0;
        background:linear-gradient(180deg,#ef4444,#f97316);
        box-shadow:0 0 6px rgba(239,68,68,.6);
    }
    .nav-item-inner { transition:all .2s cubic-bezier(0.2,0.9,0.4,1.1); position:relative; }
    .nav-item-inner:hover { background:rgba(255,255,255,.08); transform:translateX(4px); }

    .sec-lbl {
        font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.13em;
        color:rgba(255,255,255,.22); font-family:'Syne',sans-serif;
        padding:6px 10px 4px; margin-top:8px; transition:all .2s;
    }
    .nav-tooltip {
        position:absolute; left:calc(100% + 12px); top:50%; transform:translateY(-50%);
        background:#1e293b; color:#f1f5f9; font-size:12px; font-weight:600;
        padding:5px 12px; border-radius:10px; white-space:nowrap; pointer-events:none;
        opacity:0; transition:opacity .2s ease; z-index:999;
        box-shadow:0 8px 20px rgba(0,0,0,.2); letter-spacing:.3px;
    }
    .sidebar-collapsed .nav-wrapper:hover .nav-tooltip { opacity:1; }
    .nav-tooltip::before {
        content:''; position:absolute; right:100%; top:50%; transform:translateY(-50%);
        border:6px solid transparent; border-right-color:#1e293b;
    }

    /* ── Topbar ── */
    .topbar {
        background:rgba(255,255,255,.92); backdrop-filter:blur(20px);
        border-bottom:1px solid rgba(0,0,0,.05); box-shadow:0 2px 12px rgba(0,0,0,.02);
        flex-shrink:0;
    }

    /* ── Avatar ── */
    .avatar-grad {
        background:linear-gradient(125deg,#dc2626,#f97316,#ec4899);
        background-size:200% 200%; animation:shimmerAvatar 4s ease infinite;
    }
    @keyframes shimmerAvatar {
        0%{background-position:0% 50%} 50%{background-position:100% 50%} 100%{background-position:0% 50%}
    }

    /* ── Scrollbar ── */
    .main-scroll { overflow-y:auto; scrollbar-width:thin; }
    .main-scroll::-webkit-scrollbar { width:6px; }
    .main-scroll::-webkit-scrollbar-track { background:#f1f5f9; border-radius:10px; }
    .main-scroll::-webkit-scrollbar-thumb { background:#cbd5e1; border-radius:10px; }
    .main-scroll::-webkit-scrollbar-thumb:hover { background:#94a3b8; }

    /* ── Mobile menu button ── */
    .mobile-menu-btn { position:fixed; top:1rem; left:1rem; z-index:60; display:none; }

    /* ── Animations ── */
    @keyframes fadeUp { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:translateY(0)} }
    @keyframes fadeIn { from{opacity:0;transform:translateY(-8px)} to{opacity:1;transform:translateY(0)} }
    .animate-fadeIn { animation:fadeIn .3s ease forwards; }
    .trow { transition:background .15s ease; animation:fadeUp .35s ease both; }

    /* ── Table responsive ── */
    .overflow-x-auto { -webkit-overflow-scrolling: touch; }

    /* ── Status badges ── */
    .status-active { background:#ecfdf5; color:#059669; border:1px solid #d1fae5; }
    .status-inactive { background:#f3f4f6; color:#6b7280; border:1px solid #e5e7eb; }
    .status-suspended { background:#fef3c7; color:#d97706; border:1px solid #fde68a; }
    .status-cancelled { background:#fef2f2; color:#dc2626; border:1px solid #fecaca; }
    .status-pending_approval { background:#f3e8ff; color:#7c3aed; border:1px solid #e9d5ff; }

    /* ── Action buttons ── */
    .action-btn { transition:all .2s ease; }
    .action-btn:hover { transform:translateY(-2px); }

    /* ── Pagination ── */
    nav[aria-label="Pagination"] span, nav[aria-label="Pagination"] a {
        border-radius:10px !important; font-size:13px !important;
        font-weight:600 !important; transition:all .2s ease;
    }
    nav[aria-label="Pagination"] a:hover {
        background:#fee2e2 !important; color:#dc2626 !important;
    }
</style>
