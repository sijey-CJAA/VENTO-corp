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

// Get Rejected Applications (Rejected Admins + Rejected Employees)
$admin_rejected_stmt = $pdo->query("SELECT COUNT(*) FROM admins WHERE status = 'rejected'");
$admin_rejected_count = $admin_rejected_stmt->fetchColumn();

$emp_rejected_stmt = $pdo->query("SELECT COUNT(*) FROM employees WHERE status = 'rejected'");
$emp_rejected_count = $emp_rejected_stmt->fetchColumn();

$total_rejected = $admin_rejected_count + $emp_rejected_count;

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
        <a href="applications.php" class="text-decoration-none">
            <div class="stat-card p-4 h-100" style="cursor: pointer; transition: all 0.2s;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
                <h3 class="h5 text-white mb-2">Pending Applications</h3>
                <p class="stat-value" style="font-size: 2rem; font-weight: bold; color: #f59e0b;"><?php echo $total_pending; ?></p>
                <p class="small text-muted mb-0">Awaiting review</p>
            </div>
        </a>
        
        <a href="employees.php" class="text-decoration-none">
            <div class="stat-card p-4 h-100" style="cursor: pointer; transition: all 0.2s;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
                <h3 class="h5 text-white mb-2">Total Employees</h3>
                <p class="stat-value" style="font-size: 2rem; font-weight: bold; color: #a855f7;"><?php echo $total_active; ?></p>
                <p class="small text-muted mb-0">Active personnel</p>
            </div>
        </a>
        
        <a href="rejections.php" class="text-decoration-none">
            <div class="stat-card p-4 h-100" style="cursor: pointer; transition: all 0.2s;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
                <h3 class="h5 text-white mb-2">Rejections</h3>
                <p class="stat-value" style="font-size: 2rem; font-weight: bold; color: #ef4444;"><?php echo $total_rejected; ?></p>
                <p class="small text-muted mb-0">Rejected applications</p>
            </div>
        </a>
    </div>


</div>

<?php
require_once '../../../includes/adminFooter.php';
?>
