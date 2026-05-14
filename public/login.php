<?php
session_start();

require_once '../config/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($email && $password) {
        $user = null;

        // Check HR table
        $stmt = $pdo->prepare("SELECT id, first_name, last_name, email, password, 'hr' AS user_type, 'hr' AS role, 'approved' AS status FROM hr WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // If not found in HR, check Admins
        if (!$user) {
            $stmt = $pdo->prepare("SELECT id, first_name, last_name, email, password, 'admin' AS user_type, role, status FROM admins WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
        }

        // If not found in Admins, check Employees
        if (!$user) {
            $stmt = $pdo->prepare("SELECT id, first_name, last_name, email, password, 'employee' AS user_type, role, status FROM employees WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
        }

        // Verify user and password
        if ($user && password_verify($password, $user['password'])) {
            if (isset($user['status']) && $user['status'] === 'pending') {
                $error = 'Your application is still pending review by HR.';
            } elseif (isset($user['status']) && $user['status'] === 'rejected') {
                $error = 'Your application has been rejected.';
            } else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_type'] = $user['user_type'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['first_name'] = $user['first_name'];
            
            // Redirect based on type and role
            if ($user['user_type'] === 'hr') {
                header("Location: admin/hr/hr.php");
                exit;
            } elseif ($user['user_type'] === 'admin') {
                switch ($user['role']) {
                    case 'operations_admin': header("Location: admin/operations/operations.php"); break;
                    case 'it_admin': header("Location: admin/it/it.php"); break;
                    case 'compensation_manager': header("Location: admin/compensation/compensation.php"); break;
                    case 'inventory_admin': header("Location: admin/inventoryAdmin/inventoryAdmin.php"); break;
                    default: header("Location: admin/admin.php"); break;
                }
                exit;
            } elseif ($user['user_type'] === 'employee') {
                switch ($user['role']) {
                    case 'stock_holder': header("Location: employee/stockHolder/stockHolder.php"); break;
                    case 'inventory_clerk': header("Location: employee/inventoryClerk/inventoryClerk.php"); break;
                    case 'it_encoder': header("Location: employee/itEncoder/itEncoder.php"); break;
                    case 'it_security': header("Location: employee/itSecurity/itSecurity.php"); break;
                    default: header("Location: employee/employee.php"); break;
                }
                exit;
            }
        }
        } else {
            $error = 'Invalid email or password.';
        }
    } else {
        $error = 'Please enter both email and password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - VENTO-corp</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a, #1e1b4b);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .text-gradient {
            background: linear-gradient(to right, #818cf8, #c084fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body class="d-flex vh-100 align-items-center justify-content-center">

    <div class="container d-flex justify-content-center p-3">
        <div class="card glass-card shadow-lg p-4 p-md-5 w-100" style="max-width: 420px; border-radius: 1rem;">
            
            <div class="text-center mb-4 d-flex flex-column lh-1">
                <h1 class="h1 fw-bolder text-gradient mb-2">VENTO</h1>
                <span class="text-secondary fw-bold text-uppercase mb-2" style="font-size: 0.8rem; letter-spacing: 4px;">Corporation</span>
                <p class="text-secondary small mb-0 mt-2">Sign in to your account</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger py-2 text-center small border-0" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="mb-3">
                    <label class="form-label text-secondary small fw-medium" for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control bg-dark border-secondary text-light" placeholder="admin@vento-corp.com" required>
                </div>

                <div class="mb-4">
                    <label class="form-label text-secondary small fw-medium" for="password">Password</label>
                    <div class="input-group">
                        <input type="password" id="password" name="password" class="form-control bg-dark border-secondary text-light" placeholder="••••••••" required>
                        <button class="btn btn-outline-secondary border-secondary bg-dark text-secondary" type="button" id="togglePassword" style="border-left: none;">
                            <i class="bi bi-eye-slash" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 fw-semibold py-2" style="background-color: #4F46E5; border-color: #4F46E5;">
                    Sign In
                </button>
            </form>

            <div class="d-flex justify-content-between align-items-center mt-4 small">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember" style="background-color: transparent; border-color: #6c757d;">
                    <label class="form-check-label text-secondary" for="remember">
                        Remember me
                    </label>
                </div>
                <a href="#" class="text-decoration-none" style="color: #818cf8;">Forgot password?</a>
            </div>
            
            <div class="text-center mt-4 small pt-3" style="border-top: 1px solid rgba(255,255,255,0.05);">
                <span class="text-secondary">Don't have an account?</span> 
                <a href="signup.php" class="text-decoration-none fw-bold" style="color: #818cf8;">Sign Up</a>
            </div>

        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');
        const toggleIcon = document.querySelector('#toggleIcon');

        togglePassword.addEventListener('click', function () {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            toggleIcon.classList.toggle('bi-eye');
            toggleIcon.classList.toggle('bi-eye-slash');
        });
    </script>
</body>
</html>
