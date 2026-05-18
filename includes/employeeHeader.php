<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VENTO-corp Employee Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #09090b;
            color: #d4d4d8;
        }

        .sidebar {
            background-color: #18181b !important;
            border-right: 1px solid #27272a;
        }

        .mobile-navbar {
            background-color: #18181b !important;
            border-bottom: 1px solid #27272a;
        }

        .text-gradient {
            background: linear-gradient(to right, #9333ea, #7c3aed);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .card,
        .stat-card {
            background-color: #18181b !important;
            border: 1px solid #27272a !important;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.5);
        }

        .nav-link {
            color: #a1a1aa;
            font-weight: 500;
            padding-left: 1rem;
        }

        .nav-link:hover {
            color: #fff;
            background: #27272a;
            border-radius: 6px;
        }

        .nav-link.active {
            background: linear-gradient(to right, #9333ea, #7c3aed) !important;
            color: #fff !important;
            border-radius: 6px;
        }

        /* Unified Custom Styles */
        .text-purple {
            color: #a855f7 !important;
        }

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

        .form-control,
        .form-select {
            background-color: #09090b;
            border: 1px solid #27272a;
            color: #d4d4d8;
        }

        .form-control:focus,
        .form-select:focus {
            background-color: #09090b;
            border-color: #a855f7;
            color: white;
            box-shadow: 0 0 0 1px #a855f7;
        }

        .table {
            color: #d4d4d8;
        }

        .table-dark {
            --bs-table-bg: #18181b;
            --bs-table-border-color: #27272a;
        }

        .table-hover tbody tr:hover {
            color: white;
            background-color: rgba(168, 85, 247, 0.05);
        }

        .nav-tabs {
            border-bottom: 1px solid #27272a;
            display: flex;
            margin-bottom: 2rem;
        }

        .nav-tabs .nav-link {
            flex: 1;
            color: #a1a1aa;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 1px;
            padding: 1rem;
            border: none;
            background: transparent;
            font-weight: 600;
            cursor: pointer;
            text-align: center;
        }

        .nav-tabs .nav-link.active {
            color: white !important;
            border-bottom: 2px solid #a855f7 !important;
            background: transparent !important;
        }

        .nav-tabs .nav-link:hover:not(.active) {
            color: #d4d4d8;
            background: transparent;
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
    </style>
</head>

<body>
    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="d-flex min-vh-100 flex-column flex-lg-row">

            <!-- Mobile Navbar (Hidden on PC) -->
            <nav class="navbar navbar-dark mobile-navbar d-lg-none px-3 py-3">
                <div class="container-fluid">
                    <a class="navbar-brand m-0 d-flex flex-column lh-1 text-decoration-none" href="#">
                        <span class="fs-3 fw-bolder text-gradient mb-1">VENTO</span>
                        <span class="text-secondary fw-bold text-uppercase" style="font-size: 0.6rem; letter-spacing: 2px;">Corporation</span>
                    </a>
                    <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>
            </nav>

            <!-- Sidebar / Offcanvas (Sidebar on PC, Offcanvas on Mobile) -->
            <div class="offcanvas-lg offcanvas-start sidebar flex-column flex-shrink-0 p-3" tabindex="-1" id="sidebarMenu" style="width: 280px;">
                <div class="offcanvas-header d-lg-none border-bottom border-secondary mb-3 pb-3">
                    <div class="offcanvas-title d-flex flex-column lh-1">
                        <span class="fs-3 fw-bolder text-gradient mb-1">VENTO</span>
                        <span class="text-secondary fw-bold text-uppercase" style="font-size: 0.6rem; letter-spacing: 2px;">Corporation</span>
                    </div>
                    <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="offcanvas" data-bs-target="#sidebarMenu" aria-label="Close"></button>
                </div>

                <div class="offcanvas-body flex-column h-100 px-0 px-lg-2">
                    <a href="#" class="d-none d-lg-flex flex-column align-items-start lh-1 mb-4 text-decoration-none pt-2">
                        <span class="fs-2 fw-bolder text-gradient mb-1">VENTO</span>
                        <span class="text-secondary fw-bold text-uppercase" style="font-size: 0.7rem; letter-spacing: 3px;">Corporation</span>
                    </a>

                    <div class="mb-auto">
                        <span class="d-block text-secondary small text-uppercase fw-bold mb-3 px-3">Navigation</span>
                        <ul class="nav flex-column mb-auto gap-1">
                            <li class="nav-item">
                                <a href="#" class="nav-link active">Dashboard</a>
                            </li>
                        </ul>
                    </div>

                    <hr class="border-secondary mt-auto mb-3">
                    <div class="small text-secondary mb-3 px-3">
                        Role: <strong class="text-white"><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $_SESSION['role'] ?? $_SESSION['user_type']))); ?></strong>
                    </div>
                    <div class="px-2">
                        <a class="btn btn-outline-danger w-100" href="/VENTO-corp/public/logout.php">Logout</a>
                    </div>
                </div>
            </div>

            <!-- Main Content Area Wrapper -->
            <div class="flex-grow-1 d-flex flex-column min-vh-100 w-100" style="overflow-x: hidden;">
                <main class="container-fluid flex-grow-1 p-4 p-md-5">
                <?php else: ?>
                    <!-- If not logged in, basic layout -->
                    <main class="container-fluid flex-grow-1 p-4">
                    <?php endif; ?>