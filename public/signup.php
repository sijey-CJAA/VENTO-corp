<?php
session_start();
require_once '../config/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';
    
    // Define which table the role belongs to
    $admin_roles = ['operations_admin', 'it_admin', 'compensation_manager', 'inventory_admin'];
    $employee_roles = ['stock_holder', 'inventory_clerk', 'it_encoder'];
    
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
                } else {
                    $error = 'Failed to submit application. Please try again.';
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
    <title>Sign Up - VENTO-corp</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a, #1e1b4b);
            min-height: 100vh;
            padding: 2rem 0;
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
<body class="d-flex align-items-center justify-content-center">

    <div class="container d-flex justify-content-center px-3">
        <div class="card glass-card shadow-lg p-4 p-md-5 w-100" style="max-width: 600px; border-radius: 1rem;">
            
            <div class="text-center mb-4 d-flex flex-column lh-1">
                <h1 class="h1 fw-bolder text-gradient mb-2">VENTO</h1>
                <span class="text-secondary fw-bold text-uppercase mb-2" style="font-size: 0.8rem; letter-spacing: 4px;">Corporation</span>
                <p class="text-secondary small mb-0 mt-2">Apply to join our team</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger py-2 text-center small border-0" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success py-2 text-center small border-0" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                    <?php echo htmlspecialchars($success); ?>
                </div>
                <div class="text-center mt-3">
                    <a href="login.php" class="btn btn-outline-light w-100">Go to Login</a>
                </div>
            <?php else: ?>

            <form action="" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-secondary small fw-medium" for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" class="form-control bg-dark border-secondary text-light" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-secondary small fw-medium" for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" class="form-control bg-dark border-secondary text-light" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-secondary small fw-medium" for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control bg-dark border-secondary text-light" required>
                </div>

                <div class="mb-3">
                    <label class="form-label text-secondary small fw-medium" for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control bg-dark border-secondary text-light" required>
                </div>

                <div class="mb-3">
                    <label class="form-label text-secondary small fw-medium" for="role">Role Applying For</label>
                    <select id="role" name="role" class="form-select bg-dark border-secondary text-light" required>
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
                        </optgroup>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="form-label text-secondary small fw-medium" for="application_file">Application Form (PDF, DOC, DOCX)</label>
                    <input class="form-control bg-dark border-secondary text-light" type="file" id="application_file" name="application_file" accept=".pdf,.doc,.docx" required>
                </div>

                <button type="submit" class="btn btn-primary w-100 fw-semibold py-2" style="background-color: #4F46E5; border-color: #4F46E5;">
                    Submit Application
                </button>
            </form>
            
            <div class="text-center mt-4 small">
                <span class="text-secondary">Already have an account?</span> 
                <a href="login.php" class="text-decoration-none" style="color: #818cf8;">Sign In</a>
            </div>

            <?php endif; ?>

        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
