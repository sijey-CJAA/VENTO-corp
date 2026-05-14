<?php
session_start();

// Ensure the user is logged in and is HR
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'hr') {
    header("Location: ../../auth.php");
    exit;
}

require_once '../../../config/db.php';

// Get Total Active Employees (HR + Approved Admins + Approved Employees)
$hr_stmt = $pdo->query("SELECT COUNT(*) FROM hr");
$hr_count = $hr_stmt->fetchColumn();

$admin_stmt = $pdo->query("SELECT COUNT(*) FROM admins WHERE status = 'approved'");
$admin_count = $admin_stmt->fetchColumn();

$emp_stmt = $pdo->query("SELECT COUNT(*) FROM employees WHERE status = 'approved'");
$emp_count = $emp_stmt->fetchColumn();

$total_active = $hr_count + $admin_count + $emp_count;

// Get Pending Applications (Pending Admins + Pending Employees)
$admin_pending_stmt = $pdo->query("SELECT COUNT(*) FROM admins WHERE status = 'pending'");
$admin_pending_count = $admin_pending_stmt->fetchColumn();

$emp_pending_stmt = $pdo->query("SELECT COUNT(*) FROM employees WHERE status = 'pending'");
$emp_pending_count = $emp_pending_stmt->fetchColumn();

$total_pending = $admin_pending_count + $emp_pending_count;

require_once '../../../includes/adminHeader.php';
?>

<div class="dashboard">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>HR Dashboard</h2>
        <a href="../../logout.php" class="btn btn-outline-danger">Logout</a>
    </div>
    
    <div class="alert alert-info">
        Welcome back, <strong><?php echo htmlspecialchars($_SESSION['first_name']); ?></strong>! You are logged in as the Main HR Admin.
    </div>
    
    <div class="stats-grid mt-4" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
        <div class="stat-card" style="background: var(--card-bg); padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
            <h3>Total Employees</h3>
            <p class="stat-value" style="font-size: 2rem; font-weight: bold; color: var(--primary-color);"><?php echo $total_active; ?></p>
            <p class="small text-muted">Active across all departments</p>
        </div>
        
        <div class="stat-card" style="background: var(--card-bg); padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
            <h3>Pending Applications</h3>
            <p class="stat-value" style="font-size: 2rem; font-weight: bold; color: #f59e0b;"><?php echo $total_pending; ?></p>
            <p class="small text-muted">Awaiting review</p>
        </div>
        
        <div class="stat-card" style="background: var(--card-bg); padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
            <h3>Promotions Review</h3>
            <p class="stat-value" style="font-size: 2rem; font-weight: bold; color: #10b981;">0</p>
            <p class="small text-muted">Pending approval</p>
        </div>
    </div>

    <div style="margin-top: 3rem;">
        <h3>Quick HR Actions</h3>
        <div style="display: flex; gap: 1rem; margin-top: 1rem; flex-wrap: wrap;">
            <a href="applications.php" class="btn btn-primary px-4 py-2 fw-medium border-0" style="background: #4F46E5; border-radius: 6px;">Review Applications</a>
            <a href="directory.php" class="btn btn-primary px-4 py-2 fw-medium border-0" style="background: #6366f1; border-radius: 6px;">Employee Directory</a>
            <button class="btn btn-primary px-4 py-2 fw-medium border-0" style="background: #10b981; border-radius: 6px;">Manage Promotions</button>
        </div>
    </div>
</div>

<?php
require_once '../../../includes/adminFooter.php';
?>
