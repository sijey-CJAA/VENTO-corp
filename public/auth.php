<?php
session_start();
require_once '../config/db.php';

$error = '';
$success = '';
$active_tab = 'signin';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'login';
    
    if ($action === 'login') {
        $active_tab = 'signin';
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
    } elseif ($action === 'signup') {
        $active_tab = 'signup';
        $first_name = $_POST['first_name'] ?? '';
        $last_name = $_POST['last_name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? '';
        
        // Define which table the role belongs to
        $admin_roles = ['operations_admin', 'it_admin', 'compensation_manager', 'inventory_admin'];
        $employee_roles = ['stock_holder', 'inventory_clerk', 'it_encoder', 'it_security'];
        
        $table = '';
        if (in_array($role, $admin_roles)) {
            $table = 'admins';
        } elseif (in_array($role, $employee_roles)) {
            $table = 'employees';
        } else {
            $error = 'Invalid role selected.';
        }
        
        if (!$error) {
            // Check if email already exists across all tables
            $email_exists = false;
            foreach (['hr', 'admins', 'employees'] as $t) {
                $stmt = $pdo->prepare("SELECT id FROM $t WHERE email = ?");
                $stmt->execute([$email]);
                if ($stmt->fetch()) {
                    $email_exists = true;
                    break;
                }
            }
            
            if ($email_exists) {
                $error = 'An account with this email already exists.';
            } else {
                // Handle File Upload
                $file_path = null;
                if (isset($_FILES['application_file']) && $_FILES['application_file']['error'] === UPLOAD_ERR_OK) {
                    $file_tmp_path = $_FILES['application_file']['tmp_name'];
                    $file_name = $_FILES['application_file']['name'];
                    $file_size = $_FILES['application_file']['size'];
                    $file_type = $_FILES['application_file']['type'];
                    $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                    
                    // Allowed extensions
                    $allowed_extensions = ['pdf', 'doc', 'docx'];
                    
                    if (!in_array($file_extension, $allowed_extensions)) {
                        $error = 'Upload failed. Allowed file types: PDF, DOC, DOCX.';
                    } else {
                        $upload_file_dir = '../uploads/applications/';
                        $new_file_name = md5(time() . $file_name) . '.' . $file_extension;
                        $dest_path = $upload_file_dir . $new_file_name;
                        
                        if (move_uploaded_file($file_tmp_path, $dest_path)) {
                            $file_path = 'uploads/applications/' . $new_file_name;
                        } else {
                            $error = 'There was an error moving the uploaded file.';
                        }
                    }
                } else {
                    $error = 'Please upload an application form.';
                }
                
                // Insert into Database
                if (!$error) {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("INSERT INTO $table (first_name, last_name, email, password, role, status, application_file) VALUES (?, ?, ?, ?, ?, 'pending', ?)");
                    
                    if ($stmt->execute([$first_name, $last_name, $email, $hashed_password, $role, $file_path])) {
                        $success = 'Application submitted successfully! Please wait for HR approval.';
                        $active_tab = 'signin'; // switch back to signin on success
                    } else {
                        $error = 'Failed to submit application. Please try again.';
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enterprise Secure Shell - VENTO CORP</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #09090b;
            color: #d4d4d8;
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .text-purple { color: #a855f7; }
        .bg-panel { background-color: #18181b; }
        .border-panel { border: 1px solid #27272a; }
        
        /* Tabs */
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
            color: white;
            border-bottom: 2px solid #a855f7;
        }
        .nav-tabs .nav-link:hover:not(.active) {
            color: #d4d4d8;
        }
        
        /* Form Elements */
        .form-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #a1a1aa;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
        }
        .form-label a {
            color: #a855f7;
            text-decoration: none;
        }
        .input-group-text, .form-control, .form-select {
            background-color: #09090b;
            border: 1px solid #27272a;
            color: #d4d4d8;
            padding: 0.75rem 1rem;
        }
        .input-group-text {
            color: #71717a;
            border-right: none;
        }
        .form-control {
            border-left: none;
        }
        .form-control:focus, .form-select:focus {
            background-color: #09090b;
            border-color: #a855f7;
            color: white;
            box-shadow: none;
        }
        /* Fix for regular inputs without group */
        input.form-control:not(.input-group > .form-control), select.form-select {
            border-left: 1px solid #27272a;
        }
        input.form-control:not(.input-group > .form-control):focus, select.form-select:focus {
            border-color: #a855f7;
            box-shadow: 0 0 0 1px #a855f7;
        }
        .input-group:focus-within .input-group-text, .input-group:focus-within .form-control {
            border-color: #a855f7;
        }
        
        .btn-primary-purple {
            background: linear-gradient(to right, #9333ea, #7c3aed);
            color: white;
            border: none;
            font-weight: 600;
            padding: 0.85rem;
            border-radius: 4px;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 1px;
            transition: opacity 0.2s;
        }
        .btn-primary-purple:hover {
            opacity: 0.9;
            color: white;
        }

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 1.5rem 0;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #27272a;
        }
        .divider-text {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #71717a;
            padding: 0 1rem;
        }

        .btn-outline-dark {
            background-color: #18181b;
            border: 1px solid #27272a;
            color: #a1a1aa;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.6rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .btn-outline-dark:hover {
            background-color: #27272a;
            color: white;
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
        }
        .info-box i {
            color: #a855f7;
            font-size: 1.1rem;
        }

        /* Left side branding */
        .brand-sub {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 3px;
            color: #a1a1aa;
            font-weight: 600;
        }
        .brand-title {
            font-size: 3.5rem;
            font-weight: 800;
            letter-spacing: -1px;
            line-height: 1.1;
            margin-bottom: 1.5rem;
        }
        .brand-desc {
            color: #a1a1aa;
            font-size: 1rem;
            line-height: 1.6;
            max-width: 450px;
        }
        .feature-card {
            background-color: #18181b;
            border: 1px solid #27272a;
            border-radius: 8px;
            padding: 1.5rem;
            height: 100%;
        }
        .feature-card h5 {
            color: white;
            font-weight: 600;
            margin: 1rem 0 0.5rem 0;
        }
        .feature-card p {
            color: #a1a1aa;
            font-size: 0.85rem;
            margin: 0;
        }
        .server-graphic {
            border-radius: 8px;
            overflow: hidden;
            position: relative;
            height: 280px;
            margin-top: 1.5rem;
            background: linear-gradient(180deg, #18181b 0%, #09090b 100%);
            border: 1px solid #27272a;
            background-image: 
                repeating-linear-gradient(90deg, transparent, transparent 40px, rgba(168, 85, 247, 0.05) 40px, rgba(168, 85, 247, 0.05) 42px),
                linear-gradient(180deg, #18181b 0%, #09090b 100%);
            box-shadow: inset 0 0 50px rgba(168, 85, 247, 0.1);
        }
        .server-column {
            position: absolute;
            top: 0; bottom: 0;
            width: 40px;
            border-left: 1px solid rgba(168,85,247,0.1);
            border-right: 1px solid rgba(168,85,247,0.1);
            background: linear-gradient(180deg, rgba(168,85,247,0.05) 0%, transparent 100%);
        }
        .system-status {
            position: absolute;
            bottom: 1.25rem;
            left: 1.25rem;
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
            color: #d4d4d8;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            z-index: 10;
        }
        .status-dot {
            width: 6px;
            height: 6px;
            background-color: #a855f7;
            border-radius: 50%;
            box-shadow: 0 0 8px #a855f7;
        }
        .bottom-footer {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #71717a;
            padding: 2rem 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .bottom-footer a {
            color: #71717a;
            text-decoration: none;
            margin-right: 1.5rem;
        }
        .bottom-footer a:hover {
            color: white;
        }
        
        .main-container {
            flex: 1;
            display: flex;
            align-items: center;
            padding: 2rem;
        }
        .auth-panel {
            background-color: #18181b;
            border: 1px solid #27272a;
            border-radius: 8px;
            padding: 2.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        /* Checkbox styling */
        .form-check-input {
            background-color: transparent;
            border-color: #27272a;
        }
        .form-check-input:checked {
            background-color: #a855f7;
            border-color: #a855f7;
        }
        
        /* Hidden panels */
        .d-none { display: none !important; }
    </style>
</head>
<body>

    <div class="main-container">
        <div class="container-fluid max-w-7xl mx-auto" style="max-width: 1200px;">
            <div class="row g-5 align-items-center">
                
                <!-- Left Column -->
                <div class="col-lg-7 pe-lg-5">
                    <div class="brand-sub mb-3">Enterprise Secure Shell</div>
                    <div class="brand-title text-white">VENTO <span class="text-purple">CORP</span></div>
                    
                    <p class="brand-desc mb-5">
                        Access the next generation of enterprise resource management. 
                        Our high-security portal ensures encrypted synchronization across 
                        HR, IT, and Operations infrastructures.
                    </p>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="feature-card">
                                <i class="bi bi-shield-check fs-4 text-purple"></i>
                                <h5>Encrypted</h5>
                                <p>Military-grade AES-256 bit protocols active.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="feature-card">
                                <i class="bi bi-diagram-3 fs-4 text-purple"></i>
                                <h5>Unified</h5>
                                <p>Centralized nodes for all department silos.</p>
                            </div>
                        </div>
                    </div>

                    <div class="server-graphic">
                        <!-- Abstract Server Visuals -->
                        <div class="server-column" style="left: 10%;"></div>
                        <div class="server-column" style="left: 30%;"></div>
                        <div class="server-column" style="left: 50%;"></div>
                        <div class="server-column" style="left: 70%;"></div>
                        <div class="server-column" style="left: 90%;"></div>
                        <div class="system-status">
                            <div class="status-dot"></div>
                            SYSTEM STATUS: NOMINAL
                        </div>
                    </div>
                </div>

                <!-- Right Column (Auth Panel) -->
                <div class="col-lg-5">
                    <div class="auth-panel">
                        <div class="nav-tabs">
                            <button class="nav-link <?php echo $active_tab === 'signin' ? 'active' : ''; ?>" id="tab-signin" onclick="switchTab('signin')">Sign In</button>
                            <button class="nav-link <?php echo $active_tab === 'signup' ? 'active' : ''; ?>" id="tab-signup" onclick="switchTab('signup')">Request Access</button>
                        </div>

                        <?php if ($error): ?>
                            <div class="alert alert-danger py-2 text-center small border-0 mb-4" style="background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2);">
                                <i class="bi bi-exclamation-triangle me-2"></i> <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success py-2 text-center small border-0 mb-4" style="background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2);">
                                <i class="bi bi-check-circle me-2"></i> <?php echo htmlspecialchars($success); ?>
                            </div>
                        <?php endif; ?>

                        <!-- Sign In Form -->
                        <form id="form-signin" action="auth.php" method="POST" class="<?php echo $active_tab !== 'signin' ? 'd-none' : ''; ?>">
                            <input type="hidden" name="action" value="login">
                            
                            <div class="mb-4">
                                <label class="form-label" for="signin_email">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                                    <input type="email" id="signin_email" name="email" class="form-control" placeholder="admin@vento-corp.com" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label" for="signin_password">
                                    Password
                                    <a href="#">Recover</a>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" id="signin_password" name="password" class="form-control" placeholder="••••••••" style="border-right: none;" required>
                                    <span class="input-group-text toggle-password" data-target="signin_password" style="border-right: 1px solid #27272a; border-left: none; cursor: pointer;">
                                        <i class="bi bi-eye-slash"></i>
                                    </span>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary-purple w-100 mb-4">
                                Initialize Session <i class="bi bi-arrow-right ms-2"></i>
                            </button>

                            <div class="divider">
                                <span class="divider-text">Secure Verification</span>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-6">
                                    <button type="button" class="btn btn-outline-dark w-100"><i class="bi bi-fingerprint me-2"></i> SSO</button>
                                </div>
                                <div class="col-6">
                                    <button type="button" class="btn btn-outline-dark w-100"><i class="bi bi-person-bounding-box me-2"></i> BIOMETRIC</button>
                                </div>
                            </div>

                            <div class="info-box">
                                <i class="bi bi-info-circle"></i>
                                <span>New employees must file an <strong>Access Request Protocol (ARP)</strong> before attempting to initialize first-time credentials.</span>
                            </div>
                        </form>

                        <!-- Sign Up Form -->
                        <form id="form-signup" action="auth.php" method="POST" enctype="multipart/form-data" class="needs-validation <?php echo $active_tab !== 'signup' ? 'd-none' : ''; ?>" novalidate>
                            <input type="hidden" name="action" value="signup">

                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <label class="form-label" for="first_name">First Name</label>
                                    <input type="text" id="first_name" name="first_name" class="form-control" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label" for="last_name">Last Name</label>
                                    <input type="text" id="last_name" name="last_name" class="form-control" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="signup_email">Email Address</label>
                                <input type="email" id="signup_email" name="email" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="signup_password">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" id="signup_password" name="password" class="form-control" style="border-right: none;" required>
                                    <span class="input-group-text toggle-password" data-target="signup_password" style="border-right: 1px solid #27272a; border-left: none; cursor: pointer;">
                                        <i class="bi bi-eye-slash"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="role">Department Role</label>
                                <select id="role" name="role" class="form-select" required>
                                    <option value="" disabled selected>Select a role...</option>
                                    <optgroup label="Admin Roles">
                                        <option value="operations_admin">Operations Admin</option>
                                        <option value="it_admin">IT Admin</option>
                                        <option value="compensation_manager">Compensation Manager</option>
                                        <option value="inventory_admin">Inventory Admin</option>
                                    </optgroup>
                                    <optgroup label="Employee Roles">
                                        <option value="stock_holder">Stock Holder</option>
                                        <option value="inventory_clerk">Inventory Clerk</option>
                                        <option value="it_encoder">IT Encoder</option>
                                        <option value="it_security">IT Security</option>
                                    </optgroup>
                                </select>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label" for="application_file">Application Form (PDF, DOC)</label>
                                <input class="form-control" type="file" id="application_file" name="application_file" accept=".pdf,.doc,.docx" required>
                            </div>

                            <div class="mb-4">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="auth_data" name="auth_data" required>
                                    <label class="form-check-label text-secondary small" for="auth_data">
                                        I authorize data processing for employment purposes.
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="auth_accuracy" name="auth_accuracy" required>
                                    <label class="form-check-label text-secondary small" for="auth_accuracy">
                                        I certify the accuracy of all provided information.
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary-purple w-100">
                                Submit Protocol <i class="bi bi-cloud-upload ms-2"></i>
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="container-fluid max-w-7xl mx-auto px-4" style="max-width: 1200px;">
        <div class="bottom-footer border-top" style="border-color: #27272a !important;">
            <div>
                <a href="#">System Status</a>
                <a href="#">Privacy Policy</a>
            </div>
            <div>
                <i class="bi bi-box me-1"></i> BUILD_v4.2.1-PROD
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function switchTab(tab) {
            // Update Tab Styling
            document.getElementById('tab-signin').classList.remove('active');
            document.getElementById('tab-signup').classList.remove('active');
            document.getElementById('tab-' + tab).classList.add('active');

            // Toggle Forms
            if (tab === 'signin') {
                document.getElementById('form-signin').classList.remove('d-none');
                document.getElementById('form-signup').classList.add('d-none');
            } else {
                document.getElementById('form-signup').classList.remove('d-none');
                document.getElementById('form-signin').classList.add('d-none');
            }
        }

        // Form validation
        const forms = document.querySelectorAll('.needs-validation');
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });

        // Password visibility toggle
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const passwordInput = document.getElementById(targetId);
                const icon = this.querySelector('i');
                
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                }
            });
        });
    </script>
</body>
</html>
