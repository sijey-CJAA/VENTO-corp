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

require_once '../../../includes/hrHeader.php';
?>

<div class="dashboard">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>HR Dashboard</h2>
        <a href="../../logout.php" class="btn btn-outline-danger">Logout</a>
    </div>
    
    <div class="info-box mb-4">
        <i class="bi bi-info-circle"></i>
        <span>Welcome back, <strong><?php echo htmlspecialchars($_SESSION['first_name']); ?></strong>! You are logged in as the Main HR Admin.</span>
    </div>
    
    <div class="stats-grid mt-4" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
        <div class="stat-card p-4">
            <h3 class="h5 text-white mb-2">Total Employees</h3>
            <p class="stat-value" style="font-size: 2rem; font-weight: bold; color: #a855f7;"><?php echo $total_active; ?></p>
            <p class="small text-muted mb-0">Active across all departments</p>
        </div>
        
        <div class="stat-card p-4">
            <h3 class="h5 text-white mb-2">Pending Applications</h3>
            <p class="stat-value" style="font-size: 2rem; font-weight: bold; color: #f59e0b;"><?php echo $total_pending; ?></p>
            <p class="small text-muted mb-0">Awaiting review</p>
        </div>
        
        <div class="stat-card p-4">
            <h3 class="h5 text-white mb-2">Promotions Review</h3>
            <p class="stat-value" style="font-size: 2rem; font-weight: bold; color: #10b981;">0</p>
            <p class="small text-muted mb-0">Pending approval</p>
        </div>
    </div>


</div>

<?php
require_once '../../../includes/adminFooter.php';
?>
