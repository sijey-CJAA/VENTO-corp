<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VENTO-corp Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0f172a;
            color: #f8fafc;
        }
        .sidebar {
            background-color: #1e1b4b !important;
            border-right: 1px solid rgba(255, 255, 255, 0.05);
        }
        .mobile-navbar {
            background-color: #1e1b4b !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .text-gradient {
            background: linear-gradient(to right, #818cf8, #c084fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .card, .stat-card {
            background: rgba(255, 255, 255, 0.03) !important;
            border: 1px solid rgba(255, 255, 255, 0.05) !important;
            backdrop-filter: blur(10px);
        }
        .nav-link { color: #94a3b8; font-weight: 500; padding-left: 1rem; }
        .nav-link:hover { color: #fff; background: rgba(255,255,255,0.05); border-radius: 6px; }
        .nav-link.active { background-color: #4F46E5 !important; color: #fff !important; border-radius: 6px; }
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
                            <a href="#" class="nav-link active">
                                Dashboard
                            </a>
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
