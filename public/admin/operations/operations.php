<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin' || $_SESSION['role'] !== 'operations_admin') {
    header("Location: ../../auth.php");
    exit;
}

require_once '../../../config/db.php';

// Dashboard totals stay intentionally simple to match the shared team design.
$employee_stmt = $pdo->prepare("SELECT COUNT(*) FROM employees WHERE role = 'stock_holder'");
$employee_stmt->execute();
$employee_count = (int) $employee_stmt->fetchColumn();

$request_stmt = $pdo->prepare("SELECT COUNT(*) FROM requests");
$request_stmt->execute();
$request_count = (int) $request_stmt->fetchColumn();

$delivery_stmt = $pdo->prepare("SELECT COUNT(*) FROM tasks WHERE status = 'Delivered'");
$delivery_stmt->execute();
$delivery_count = (int) $delivery_stmt->fetchColumn();

require_once '../../../includes/operationManagerHeader.php';
?>

<div class="dashboard">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Operations Manager Dashboard</h2>
    </div>

    <div class="info-box mb-4">
        <i class="bi bi-info-circle"></i>
        <span>Welcome, <strong><?php echo htmlspecialchars($_SESSION['first_name']); ?></strong>! Use this dashboard to monitor stock holders, restock requests, and completed deliveries.</span>
    </div>

    <div class="row g-4 mt-4">
        <div class="col-12 col-lg-4">
            <div class="card p-4 h-100">
                <h5 class="text-white fw-bold mb-3">Employees</h5>
                <div class="stat-value text-purple mb-3"><?php echo $employee_count; ?></div>
                <p class="text-secondary mb-0">Stock holders in operations</p>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card p-4 h-100">
                <h5 class="text-white fw-bold mb-3">Requests</h5>
                <div class="stat-value text-warning mb-3"><?php echo $request_count; ?></div>
                <p class="text-secondary mb-0">Restock requests received</p>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card p-4 h-100">
                <h5 class="text-white fw-bold mb-3">Deliveries</h5>
                <div class="stat-value text-success mb-3"><?php echo $delivery_count; ?></div>
                <p class="text-secondary mb-0">Successfully delivered</p>
            </div>
        </div>
    </div>
</div>

<?php
require_once '../../../includes/adminFooter.php';
?>
