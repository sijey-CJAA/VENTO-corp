<?php
// Ensure this file is included properly
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VENTO-corp IT Admin</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Geist:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
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
        
        /* Sidebar Styling */
        .app-sidebar {
            position: fixed;
            left: 0;
            top: 0;
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
        
        .app-sidebar:hover {
            width: 280px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 12px 16px;
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
            border-left: 4px solid var(--primary);
            background-color: rgba(68, 65, 115, 0.2);
            color: var(--primary);
        }

        .sidebar-text {
            opacity: 0;
            transition: opacity 0.3s;
            font-size: 14px;
            font-weight: 500;
        }
        
        .app-sidebar:hover .sidebar-text {
            opacity: 1;
        }

        /* Top Navbar */
        .app-topbar {
            position: fixed;
            top: 0;
            right: 0;
            left: 72px;
            height: 64px;
            z-index: 1040;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 24px;
            background-color: rgba(19, 19, 19, 0.8);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--outline-variant);
            transition: left 0.3s ease;
        }

        .search-input {
            background-color: var(--surface-container-low);
            border: 1px solid var(--outline-variant);
            color: var(--on-surface);
            padding: 6px 16px 6px 40px;
            border-radius: 8px;
            width: 250px;
            transition: all 0.2s;
        }
        .search-input:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 1px var(--primary);
        }

        .icon-btn {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            color: var(--on-surface-variant);
            background: transparent;
            border: none;
            transition: background 0.2s;
        }
        .icon-btn:hover {
            background-color: var(--surface-container-low);
            color: var(--on-surface);
        }

        /* Main Canvas */
        .app-main {
            margin-left: 72px;
            margin-top: 64px;
            padding: 24px;
            height: calc(100vh - 64px);
            overflow-y: auto;
            overflow-x: hidden;
            transition: margin-left 0.3s ease;
        }
        
        /* Responsiveness */
        @media (max-width: 768px) {
            .app-sidebar {
                transform: translateX(-100%);
                width: 280px;
            }
            .app-sidebar.show {
                transform: translateX(0);
            }
            .app-sidebar.show .sidebar-text {
                opacity: 1;
            }
            .app-topbar {
                left: 0;
                padding: 0 16px;
            }
            .app-main {
                margin-left: 0;
                padding: 16px;
            }
            .search-input {
                width: 180px;
            }
        }
        
        @media (min-width: 769px) {
            .app-sidebar:hover ~ .app-topbar {
                left: 280px;
            }
            .app-sidebar:hover ~ .app-main {
                margin-left: 280px;
            }
        }
        
        /* Auras */
        .bg-aura {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            pointer-events: none;
            z-index: 0;
        }
        .aura-1 {
            position: absolute;
            top: 20%;
            right: 10%;
            width: 500px;
            height: 500px;
            background-color: rgba(210, 187, 255, 0.05);
            border-radius: 50%;
            filter: blur(120px);
        }
        .aura-2 {
            position: absolute;
            bottom: -10%;
            left: 5%;
            width: 400px;
            height: 400px;
            background-color: rgba(196, 193, 251, 0.05);
            border-radius: 50%;
            filter: blur(100px);
        }
    </style>
</head>
<body>
    <?php if (isset($_SESSION['user_id'])): ?>
    
    <!-- Visual Polish: Background Aura -->
    <div class="bg-aura">
        <div class="aura-1"></div>
        <div class="aura-2"></div>
    </div>

    <!-- Sidebar -->
    <aside class="app-sidebar">
        <div class="d-flex align-items-center gap-3 p-4" style="overflow: hidden; white-space: nowrap;">
            <div class="d-flex align-items-center justify-content-center rounded" style="min-width: 40px; height: 40px; background-color: var(--primary-container);">
                <i class="bi bi-shield-fill text-white fs-5"></i>
            </div>
            <div class="d-flex flex-column sidebar-text">
                <span class="font-display-lg fw-bold" style="font-size: 18px; color: var(--primary);">VENTO CORP</span>
                <span style="font-size: 12px; color: var(--on-surface-variant);">Enterprise Resource</span>
            </div>
        </div>
        
        <nav class="flex-grow-1 d-flex flex-column gap-2 px-3 py-4">
            <a href="#" class="sidebar-link">
                <i class="bi bi-people fs-5"></i>
                <span class="sidebar-text">HR Management</span>
            </a>
            <a href="#" class="sidebar-link">
                <i class="bi bi-diagram-3 fs-5"></i>
                <span class="sidebar-text">Operations</span>
            </a>
            <a href="#" class="sidebar-link">
                <i class="bi bi-box-seam fs-5"></i>
                <span class="sidebar-text">Inventory</span>
            </a>
            
            <!-- Active State: IT Administration -->
            <a href="#" class="sidebar-link active">
                <i class="bi bi-person-fill-lock fs-5"></i>
                <span class="sidebar-text">IT Administration</span>
            </a>

            <a href="#" class="sidebar-link">
                <i class="bi bi-wallet2 fs-5"></i>
                <span class="sidebar-text">Compensation</span>
            </a>
        </nav>
        
        <div class="d-flex flex-column gap-2 px-3 py-4 border-top" style="border-color: var(--outline-variant) !important;">
            <a href="#" class="sidebar-link">
                <i class="bi bi-question-circle fs-5"></i>
                <span class="sidebar-text">Support</span>
            </a>
            <a href="/VENTO-corp/public/logout.php" class="sidebar-link text-danger">
                <i class="bi bi-box-arrow-right fs-5"></i>
                <span class="sidebar-text">Logout</span>
            </a>
        </div>
    </aside>

    <!-- Top Navigation -->
    <header class="app-topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-link text-white d-md-none p-0 me-2" onclick="document.querySelector('.app-sidebar').classList.toggle('show')">
                <i class="bi bi-list fs-3"></i>
            </button>
            <h1 class="font-headline-sm text-white mb-0 d-none d-lg-block">VENTO Dashboard</h1>
            <div class="position-relative">
                <i class="bi bi-search position-absolute" style="left: 12px; top: 50%; transform: translateY(-50%); color: var(--on-surface-variant);"></i>
                <input class="search-input" placeholder="Search resources..." type="text" />
            </div>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="d-flex align-items-center border-end pe-3 me-1 d-none d-md-flex" style="border-color: var(--outline-variant) !important;">
                <button class="icon-btn"><i class="bi bi-bell fs-5"></i></button>
                <button class="icon-btn"><i class="bi bi-clock-history fs-5"></i></button>
                <button class="icon-btn"><i class="bi bi-chat-left-text fs-5"></i></button>
            </div>
            <button class="btn btn-sm text-white border-0 fw-semibold d-none d-xl-block">Help</button>
            <div class="d-flex gap-2">
                <button class="btn btn-sm d-none d-md-block" style="background-color: var(--surface-container-highest); border: 1px solid var(--outline-variant); color: var(--on-surface); font-size: 13px; font-weight: 600;">System Logs</button>
                <button class="btn btn-sm text-dark fw-semibold" style="background-color: var(--primary); font-size: 13px;">Manage IT Encoders</button>
            </div>
            <div class="rounded-circle overflow-hidden border ms-1" style="width: 32px; height: 32px; border-color: var(--outline-variant) !important;">
                <img alt="User profile" class="w-100 h-100 object-fit-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAORfowAaYs5rUZASLxswT2ExzOzGG7uZivyy7e4P_c0kHaOk_K0Fv6ruT3DZS4PxG-2AR-8mfYYrFOguoLFt08fo5mldcaBB7K29uWhpjEljQ23Rbr3JBdO3VOfkc_XAAsL8h196hq7xLX5v-YSOmbDe511mETCEbXPbhwo70HdIRtOEM6E8p4G6KRm7P1VTrAeW6cHJlQovmZckitsPIBq0zXVDk6HNGFacRZ1H2pm4-YAwU8rgagbdCAhLinH2INJ-zIt3fE3R4" />
            </div>
        </div>
    </header>

    <!-- Main Content Canvas -->
    <main class="app-main position-relative" style="z-index: 10;">
        <div style="max-width: 1400px; margin: 0 auto;">
    <?php else: ?>
    <!-- Basic layout if not logged in -->
    <main class="container-fluid flex-grow-1 p-4">
    <?php endif; ?>
