<?php
// Operations Manager layout based on the IT Admin dashboard pattern.
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VENTO-corp Operations Manager</title>
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

        .app-sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            z-index: 1050;
            background-color: var(--surface-dim);
            border-right: 1px solid var(--outline-variant);
            width: 85px;
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

        .sidebar-section-label {
            color: var(--on-surface-variant);
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            opacity: 0;
            transition: opacity 0.3s;
            padding: 8px 16px 2px;
            white-space: nowrap;
        }

        .app-sidebar:hover .sidebar-text,
        .app-sidebar:hover .sidebar-section-label {
            opacity: 1;
        }

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

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 1px solid var(--outline-variant);
            background: linear-gradient(135deg, #7c3aed, #9333ea);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .app-main {
            margin-left: 72px;
            margin-top: 64px;
            padding: 24px;
            height: calc(100vh - 64px);
            overflow-y: auto;
            overflow-x: hidden;
            transition: margin-left 0.3s ease;
        }

        .card, .stat-card {
            background-color: #18181b !important;
            border: 1px solid #27272a !important;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.35);
        }

        .feature-card {
            background-color: #18181b;
            border: 1px solid #27272a;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.35);
        }

        .info-box {
            background-color: #0f0f11;
            border: 1px solid #27272a;
            border-radius: 4px;
            padding: 1rem;
            display: flex;
            gap: 0.75rem;
            font-size: 0.8rem;
            color: #a1a1aa;
            align-items: flex-start;
            margin-bottom: 1.5rem;
        }

        .info-box i {
            color: #a855f7;
            font-size: 1.1rem;
        }

        .table { color: var(--on-surface); }

        .table-dark {
            --bs-table-bg: #18181b;
            --bs-table-border-color: #27272a;
        }

        .table-hover tbody tr:hover {
            color: #fff;
            background-color: rgba(168, 85, 247, 0.05);
        }

        .text-purple { color: #a855f7 !important; }

        .btn-primary-purple {
            background: linear-gradient(to right, #9333ea, #7c3aed) !important;
            color: white !important;
            border: none !important;
            transition: opacity 0.2s;
        }

        .btn-primary-purple:hover {
            opacity: 0.9;
        }

        .btn-outline-dark {
            background-color: #18181b;
            border: 1px solid #27272a;
            color: #a1a1aa;
        }

        .btn-outline-dark:hover {
            background-color: #27272a;
            color: white;
        }

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

        @media (max-width: 768px) {
            .app-sidebar {
                transform: translateX(-100%);
                width: 280px;
            }

            .app-sidebar.show {
                transform: translateX(0);
            }

            .app-sidebar.show .sidebar-text,
            .app-sidebar.show .sidebar-section-label {
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

            .app-sidebar:hover ~ .flex-grow-1 .app-main {
                margin-left: 280px;
            }
        }
    </style>
</head>
<body>
    <?php if (isset($_SESSION['user_id'])): ?>
    <div class="d-flex min-vh-100">
        <div class="bg-aura">
            <div class="aura-1"></div>
            <div class="aura-2"></div>
        </div>

        <aside class="app-sidebar">
            <div class="d-flex align-items-center gap-3 p-4" style="overflow: hidden; white-space: nowrap;">
                <div class="d-flex align-items-center justify-content-center rounded" style="min-width: 40px; height: 40px; background-color: var(--primary-container);">
                    <i class="bi bi-diagram-3 text-white fs-5"></i>
                </div>
                <div class="d-flex flex-column sidebar-text">
                    <span class="font-display-lg fw-bold" style="font-size: 18px; color: var(--primary);">VENTO CORP</span>
                    <span style="font-size: 12px; color: var(--on-surface-variant);">Operations Manager</span>
                </div>
            </div>

            <nav class="flex-grow-1 d-flex flex-column gap-2 px-3 py-4">
                <div class="sidebar-section-label">Operations</div>
                <a href="operations.php" class="sidebar-link active">
                    <i class="bi bi-speedometer2 fs-5"></i>
                    <span class="sidebar-text">Dashboard</span>
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

        <header class="app-topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-link text-white d-md-none p-0 me-2" onclick="document.querySelector('.app-sidebar').classList.toggle('show')">
                    <i class="bi bi-list fs-3"></i>
                </button>
                <h1 class="font-headline-sm text-white mb-0 d-none d-lg-block">Operations Manager</h1>
                <div class="position-relative d-none d-sm-block">
                    <i class="bi bi-search position-absolute" style="left: 12px; top: 50%; transform: translateY(-50%); color: var(--on-surface-variant);"></i>
                    <input class="search-input" placeholder="Search operations..." type="text">
                </div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center border-end pe-3 me-1 d-none d-md-flex" style="border-color: var(--outline-variant) !important;">
                    <button class="icon-btn" type="button"><i class="bi bi-bell fs-5"></i></button>
                    <button class="icon-btn" type="button"><i class="bi bi-clock-history fs-5"></i></button>
                    <button class="icon-btn" type="button"><i class="bi bi-chat-left-text fs-5"></i></button>
                </div>
                <div class="small text-secondary d-none d-xl-block">
                    Role: <strong class="text-white"><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $_SESSION['role'] ?? $_SESSION['user_type']))); ?></strong>
                </div>
                <div class="user-avatar" title="<?php echo htmlspecialchars($_SESSION['first_name'] ?? 'User'); ?>">
                    <?php echo strtoupper(substr($_SESSION['first_name'] ?? 'U', 0, 1)); ?>
                </div>
            </div>
        </header>

        <div class="flex-grow-1 d-flex flex-column min-vh-100 w-100" style="overflow-x: hidden;">
            <main class="app-main position-relative" style="z-index: 10;">
    <?php else: ?>
    <!-- Basic layout if not logged in -->
    <main class="container-fluid flex-grow-1 p-4">
    <?php endif; ?>

