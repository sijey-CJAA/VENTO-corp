<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'employee' || $_SESSION['role'] !== 'stock_holder') {
    header("Location: ../../auth.php");
    exit;
}

require_once '../../../config/db.php';

// Fetch exact full name from database
$emp_stmt = $pdo->prepare("SELECT first_name, last_name FROM employees WHERE id = ?");
$emp_stmt->execute([$_SESSION['user_id']]);
$emp_data = $emp_stmt->fetch(PDO::FETCH_ASSOC);
$employee_name = $emp_data['first_name'] . ' ' . $emp_data['last_name'];

// Fetch pending tasks count
$stmt = $pdo->prepare("SELECT COUNT(*) FROM tasks WHERE assigned_to = ? AND status != 'Delivered'");
$stmt->execute([$employee_name]);
$pending_tasks = $stmt->fetchColumn();

// Fetch completed history count
$stmt = $pdo->prepare("SELECT COUNT(*) FROM tasks WHERE assigned_to = ? AND status = 'Delivered'");
$stmt->execute([$employee_name]);
$completed_tasks = $stmt->fetchColumn();

require_once '../../../includes/stockHolderHeader.php';
?>

<div class="dashboard">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Stock Holder Dashboard</h2>
    </div>
    
    <div class="info-box mb-4">
        <i class="bi bi-info-circle"></i>
        <span>Welcome, <strong><?php echo htmlspecialchars($_SESSION['first_name']); ?></strong>! Your primary duty is to physically stock supplies and items in the warehouse.</span>
    </div>
    
    <div class="row g-4 mt-2">
        <div class="col-md-6">
            <div class="card p-4 h-100">
                <h5 class="text-white mb-3" style="font-size: 1.1rem; font-weight: 500;">Pending Tasks</h5>
                <h2 class="text-purple fw-bold mb-3" style="font-size: 2.5rem;"><?php echo $pending_tasks; ?></h2>
                <span class="text-secondary small">Active across your assignments</span>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card p-4 h-100">
                <h5 class="text-white mb-3" style="font-size: 1.1rem; font-weight: 500;">Delivery History</h5>
                <h2 class="text-warning fw-bold mb-3" style="font-size: 2.5rem;"><?php echo $completed_tasks; ?></h2>
                <span class="text-secondary small">Successfully delivered</span>
            </div>
        </div>
    </div>
</div>

<?php
require_once '../../../includes/employeeFooter.php';
?>
