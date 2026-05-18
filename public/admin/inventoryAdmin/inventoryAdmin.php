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

// Fetch inventory items safely
$inventory_items = [];
try {
    $inv_stmt = $pdo->prepare("SELECT id, name, quantity, status FROM inventory ORDER BY name ASC");
    $inv_stmt->execute();
    $inventory_items = $inv_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Ignore if table is missing
}

// Fetch my request history safely
$my_requests = [];
try {
    $req_stmt = $pdo->prepare("SELECT id, request_image, item_requested, requested_at, status, handled_by FROM requests ORDER BY requested_at DESC");
    $req_stmt->execute();
    $my_requests = $req_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Ignore if table is missing
}

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

$total_inventory = count($inventory_items);
$total_requests = count($my_requests);
$pending_requests = 0;
foreach ($my_requests as $req) {
    if ($req['status'] === 'Pending') {
        $pending_requests++;
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
    
    <div class="stats-grid mt-4" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
        
        <!-- Inventory Card -->
        <a href="inventory.php" class="text-decoration-none">
            <div class="stat-card p-4 h-100" style="cursor: pointer; transition: all 0.2s; background-color: #18181b; border: 1px solid #27272a; border-radius: 10px;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
                <h3 class="h5 text-white mb-2 fw-medium">Inventory Items</h3>
                <p class="stat-value" style="font-size: 2rem; font-weight: bold; color: #3b82f6; margin-bottom: 0.5rem;"><?php echo $total_inventory; ?></p>
                <p class="small text-muted mb-0">Total unique items</p>
            </div>
        </a>

        <!-- Employees Card -->
        <a href="employees.php" class="text-decoration-none">
            <div class="stat-card p-4 h-100" style="cursor: pointer; transition: all 0.2s; background-color: #18181b; border: 1px solid #27272a; border-radius: 10px;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
                <h3 class="h5 text-white mb-2 fw-medium">Employees</h3>
                <p class="stat-value" style="font-size: 2rem; font-weight: bold; color: #a855f7; margin-bottom: 0.5rem;"><?php echo $total_employees; ?></p>
                <p class="small text-muted mb-0">Active personnel</p>
            </div>
        </a>

        <!-- Pending Requests Card -->
        <a href="request.php" class="text-decoration-none">
            <div class="stat-card p-4 h-100" style="cursor: pointer; transition: all 0.2s; background-color: #18181b; border: 1px solid #27272a; border-radius: 10px;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
                <h3 class="h5 text-white mb-2 fw-medium">Pending Requests</h3>
                <p class="stat-value" style="font-size: 2rem; font-weight: bold; color: #f59e0b; margin-bottom: 0.5rem;"><?php echo $pending_requests; ?></p>
                <p class="small text-muted mb-0">Awaiting fulfillment</p>
            </div>
        </a>

        <!-- Request History Card -->
        <a href="requestHistory.php" class="text-decoration-none">
            <div class="stat-card p-4 h-100" style="cursor: pointer; transition: all 0.2s; background-color: #18181b; border: 1px solid #27272a; border-radius: 10px;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
                <h3 class="h5 text-white mb-2 fw-medium">Request History</h3>
                <p class="stat-value" style="font-size: 2rem; font-weight: bold; color: #10b981; margin-bottom: 0.5rem;"><?php echo $total_requests; ?></p>
                <p class="small text-muted mb-0">Total requests logged</p>
            </div>
        </a>

    </div>
</div>

<?php
require_once '../../../includes/inventoryAdminFooter.php';
?>
