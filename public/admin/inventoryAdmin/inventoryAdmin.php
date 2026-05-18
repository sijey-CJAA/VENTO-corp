<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin' || $_SESSION['role'] !== 'inventory_admin') {
    header("Location: ../../auth.php");
    exit;
}

require_once '../../../config/db.php';

// Fetch employees under this admin
$emp_stmt = $pdo->prepare("SELECT first_name, last_name, email, status, created_at FROM employees WHERE role = 'inventory_clerk'");
$emp_stmt->execute();
$employees = $emp_stmt->fetchAll(PDO::FETCH_ASSOC);

require_once '../../../includes/inventoryAdminHeader.php';
?>

<?php
$total_employees = count($employees);
$pending_count = 0;
foreach ($employees as $emp) {
    if ($emp['status'] === 'pending') {
        $pending_count++;
    }
}
?>

<div class="dashboard pt-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-medium text-white m-0">Inventory Admin Dashboard</h2>
    </div>
    
    <div class="info-box mb-4" style="background-color: #121212; border: 1px solid #27272a; padding: 20px; border-radius: 8px;">
        <span style="color: #a1a1aa; font-size: 14px;">Welcome back, <strong class="text-white"><?php echo htmlspecialchars($_SESSION['first_name']); ?></strong>! You are logged in as the Main Inventory Admin.</span>
    </div>
    
    <div class="row g-4 mt-2">
        <!-- Total Employees Card -->
        <div class="col-md-6">
            <div class="card p-4 h-100" style="background-color: #18181b; border: 1px solid #27272a; border-radius: 10px; box-shadow: none;">
                <h6 class="text-white mb-3 fw-medium" style="font-size: 16px;">Total Employees</h6>
                <h2 style="color: #a855f7; font-weight: 700; margin-bottom: 1.5rem; font-size: 2rem;"><?php echo $total_employees; ?></h2>
                <p class="mb-0" style="color: #a1a1aa; font-size: 13px;">Active across all departments</p>
            </div>
        </div>
        
        <!-- Pending Applications Card -->
        <div class="col-md-6">
            <div class="card p-4 h-100" style="background-color: #18181b; border: 1px solid #27272a; border-radius: 10px; box-shadow: none;">
                <h6 class="text-white mb-3 fw-medium" style="font-size: 16px;">Pending Applications</h6>
                <h2 style="color: #f97316; font-weight: 700; margin-bottom: 1.5rem; font-size: 2rem;"><?php echo $pending_count; ?></h2>
                <p class="mb-0" style="color: #a1a1aa; font-size: 13px;">Awaiting review</p>
            </div>
        </div>
    </div>
</div>

<?php
require_once '../../../includes/inventoryAdminFooter.php';
?>
