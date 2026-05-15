<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VENTO-corp | Inventory Admin</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Geist:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --surface-dim: #131313;
            --surface-container-highest: #353535;
            --surface-container-low: #1b1b1b;
            --surface-container: #1f1f1f;
            --primary: #d2bbff;
            --primary-container: #7c3aed;
            --secondary: #c4c1fb;
            --secondary-container: #444173;
            --outline-variant: #4a4455;
            --on-surface: #e2e2e2;
            --on-surface-variant: #ccc3d8;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #000000;
            color: var(--on-surface);
            overflow: hidden;
        }

        .font-display-lg { font-family: 'Geist', sans-serif; }
        .font-headline-sm { font-family: 'Geist', sans-serif; font-size: 20px; font-weight: 600; }

        /* ── Sidebar ── */
        .app-sidebar {
            position: fixed;
            left: 0; top: 0;
            height: 100vh;
            z-index: 1050;
            background-color: var(--surface-dim);
            border-right: 1px solid var(--outline-variant);
            width: 72px;
            transition: width 0.3s ease;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .app-sidebar:hover { width: 280px; }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 18px 16px;
            overflow: hidden;
            white-space: nowrap;
            min-height: 64px;
        }
        .sidebar-brand-icon {
            min-width: 40px; height: 40px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 10px;
            background: linear-gradient(135deg, #7c3aed, #9333ea);
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 11px 16px;
            color: var(--on-surface-variant);
            text-decoration: none;
            border-radius: 4px;
            transition: all 0.2s;
            white-space: nowrap;
        }
        .sidebar-link:hover {
            background-color: var(--surface-container-highest);
            color: var(--on-surface);
        }
        .sidebar-link.active {
            border-left: 3px solid var(--primary);
            background-color: rgba(68, 65, 115, 0.25);
            color: var(--primary);
        }

        .sidebar-text {
            opacity: 0;
            transition: opacity 0.25s;
            font-size: 14px;
            font-weight: 500;
        }
        .app-sidebar:hover .sidebar-text { opacity: 1; }

        .sidebar-section-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--on-surface-variant);
            padding: 0 16px;
            margin: 8px 0 4px;
            white-space: nowrap;
            opacity: 0;
            transition: opacity 0.25s;
        }
        .app-sidebar:hover .sidebar-section-label { opacity: 1; }

        /* ── Topbar ── */
        .app-topbar {
            position: fixed;
            top: 0; right: 0;
            left: 72px;
            height: 64px;
            z-index: 1040;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 24px;
            background-color: rgba(19, 19, 19, 0.85);
            backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--outline-variant);
            transition: left 0.3s ease;
        }

        .icon-btn {
            width: 38px; height: 38px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 50%;
            color: var(--on-surface-variant);
            background: transparent;
            border: none;
            transition: background 0.2s;
            cursor: pointer;
        }
        .icon-btn:hover {
            background-color: var(--surface-container-low);
            color: var(--on-surface);
        }

        /* Topbar action buttons */
        .btn-topbar-outline {
            background-color: var(--surface-container-highest);
            border: 1px solid var(--outline-variant);
            color: var(--on-surface);
            font-size: 13px;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
            white-space: nowrap;
        }
        .btn-topbar-outline:hover {
            background-color: #454545;
            color: #fff;
        }
        .btn-topbar-primary {
            background: linear-gradient(135deg, #7c3aed, #9333ea);
            border: none;
            color: #fff;
            font-size: 13px;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 6px;
            cursor: pointer;
            transition: opacity 0.2s;
            white-space: nowrap;
        }
        .btn-topbar-primary:hover { opacity: 0.88; }

        /* User avatar */
        .user-avatar {
            width: 34px; height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, #7c3aed, #9333ea);
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 700;
            color: #fff;
            border: 2px solid var(--outline-variant);
            cursor: pointer;
            flex-shrink: 0;
        }

        /* ── Main canvas ── */
        .app-main {
            margin-left: 72px;
            margin-top: 64px;
            padding: 28px;
            height: calc(100vh - 64px);
            overflow-y: auto;
            overflow-x: hidden;
            transition: margin-left 0.3s ease;
        }

        /* ── Cards / tables shared styles ── */
        .card, .stat-card {
            background-color: #18181b !important;
            border: 1px solid #27272a !important;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.4);
        }
        .table { color: var(--on-surface); }
        .table-dark {
            --bs-table-bg: #18181b;
            --bs-table-border-color: #27272a;
        }
        .table-hover tbody tr:hover {
            color: #fff;
            background-color: rgba(168,85,247,0.05);
        }
        .info-box {
            background-color: #0f0f11;
            border: 1px solid #27272a;
            border-radius: 6px;
            padding: 1rem;
            display: flex;
            gap: 0.75rem;
            font-size: 0.8rem;
            color: #a1a1aa;
            align-items: flex-start;
            margin-bottom: 1.5rem;
        }
        .info-box i { color: #a855f7; font-size: 1.1rem; }
        .feature-card {
            background-color: #18181b;
            border: 1px solid #27272a;
            border-radius: 10px;
            padding: 2rem;
        }
        .text-purple { color: #a855f7 !important; }

        /* ── Background auras ── */
        .bg-aura {
            position: fixed; top: 0; left: 0;
            width: 100vw; height: 100vh;
            pointer-events: none; z-index: 0;
        }
        .aura-1 {
            position: absolute; top: 15%; right: 8%;
            width: 450px; height: 450px;
            background-color: rgba(124,58,237,0.06);
            border-radius: 50%; filter: blur(110px);
        }
        .aura-2 {
            position: absolute; bottom: -5%; left: 5%;
            width: 380px; height: 380px;
            background-color: rgba(147,51,234,0.05);
            border-radius: 50%; filter: blur(100px);
        }

        /* ── Mobile ── */
        @media (max-width: 768px) {
            .app-sidebar {
                transform: translateX(-100%);
                width: 280px;
            }
            .app-sidebar.show {
                transform: translateX(0);
            }
            .app-sidebar.show .sidebar-text,
            .app-sidebar.show .sidebar-section-label { opacity: 1; }
            .app-topbar { left: 0; padding: 0 16px; }
            .app-main { margin-left: 0; padding: 16px; }
            .topbar-actions { display: none !important; }
        }
    </style>
</head>
<body>
<?php if (isset($_SESSION['user_id'])): ?>

    <!-- Background Aura -->
    <div class="bg-aura">
        <div class="aura-1"></div>
        <div class="aura-2"></div>
    </div>

    <!-- ── Sidebar ── -->
    <aside class="app-sidebar" id="appSidebar">
        <!-- Brand -->
        <div class="sidebar-brand">
            <div class="sidebar-brand-icon">
                <i class="bi bi-box-seam text-white fs-5"></i>
            </div>
            <div class="d-flex flex-column sidebar-text lh-1">
                <span class="font-display-lg fw-bold" style="font-size:17px;color:var(--primary)">VENTO CORP</span>
                <span style="font-size:11px;color:var(--on-surface-variant)">Inventory Management</span>
            </div>
        </div>

        <!-- Nav links -->
        <nav class="flex-grow-1 d-flex flex-column px-3 py-2" style="gap:2px; overflow-y:auto;">
            <div class="sidebar-section-label">Main</div>

            <a href="inventoryAdmin.php" class="sidebar-link <?php echo $current_page == 'inventoryAdmin.php' ? 'active' : ''; ?>">
                <i class="bi bi-speedometer2 fs-5"></i>
                <span class="sidebar-text">Dashboard</span>
            </a>
            <a href="#" class="sidebar-link <?php echo $current_page == 'products.php' ? 'active' : ''; ?>">
                <i class="bi bi-box-seam fs-5"></i>
                <span class="sidebar-text">Manage Products</span>
            </a>
            <a href="#" class="sidebar-link <?php echo $current_page == 'reports.php' ? 'active' : ''; ?>">
                <i class="bi bi-bar-chart-line fs-5"></i>
                <span class="sidebar-text">Inventory Reports</span>
            </a>
            <a href="#" class="sidebar-link <?php echo $current_page == 'suppliers.php' ? 'active' : ''; ?>">
                <i class="bi bi-truck fs-5"></i>
                <span class="sidebar-text">Suppliers</span>
            </a>

            <div class="sidebar-section-label mt-2">Team</div>
            <a href="#" class="sidebar-link <?php echo $current_page == 'clerks.php' ? 'active' : ''; ?>">
                <i class="bi bi-people fs-5"></i>
                <span class="sidebar-text">Inventory Clerks</span>
            </a>
        </nav>

        <!-- Bottom -->
        <div class="d-flex flex-column px-3 py-3 gap-1" style="border-top:1px solid var(--outline-variant)">
            <a href="#" class="sidebar-link">
                <i class="bi bi-question-circle fs-5"></i>
                <span class="sidebar-text">Support</span>
            </a>
            <a href="/VENTO-corp/public/logout.php" class="sidebar-link" style="color:#f87171">
                <i class="bi bi-box-arrow-right fs-5"></i>
                <span class="sidebar-text">Logout</span>
            </a>
        </div>
    </aside>

    <!-- ── Top Navigation Bar ── -->
    <header class="app-topbar">
        <!-- Left: hamburger (mobile) + page title -->
        <div class="d-flex align-items-center gap-3">
            <button class="icon-btn d-md-none" onclick="document.getElementById('appSidebar').classList.toggle('show')">
                <i class="bi bi-list fs-4"></i>
            </button>
            <h1 class="font-headline-sm text-white mb-0 d-none d-lg-block">Inventory Admin</h1>
        </div>

        <!-- Right: action buttons + user -->
        <div class="d-flex align-items-center gap-2">
            <!-- Quick-action buttons in header -->
            <div class="d-flex align-items-center gap-2 topbar-actions">
                <button class="btn-topbar-outline">
                    <i class="bi bi-bar-chart-line me-1"></i>Inventory Reports
                </button>
                <button class="btn-topbar-primary">
                    <i class="bi bi-box-seam me-1"></i>Manage Products
                </button>
            </div>

            <!-- Divider -->
            <div class="d-none d-md-block" style="width:1px;height:28px;background:var(--outline-variant);margin:0 6px"></div>

            <!-- Notification icon -->
            <button class="icon-btn d-none d-md-flex">
                <i class="bi bi-bell fs-5"></i>
            </button>

            <!-- User avatar with initials -->
            <div class="user-avatar" title="<?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . ($_SESSION['last_name'] ?? '')); ?>">
                <?php
                    $initials = strtoupper(substr($_SESSION['first_name'] ?? 'U', 0, 1));
                    if (!empty($_SESSION['last_name'])) $initials .= strtoupper(substr($_SESSION['last_name'], 0, 1));
                    echo $initials;
                ?>
            </div>
        </div>
    </header>

    <!-- ── Main Content Canvas ── -->
    <main class="app-main position-relative" style="z-index:10;">
        <div style="max-width:1400px;margin:0 auto;">

<?php else: ?>
    <!-- Not logged in: basic layout -->
    <main class="container-fluid flex-grow-1 p-4">
<?php endif; ?>
