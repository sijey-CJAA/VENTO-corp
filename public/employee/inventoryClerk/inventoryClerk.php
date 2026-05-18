<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'employee' || $_SESSION['role'] !== 'inventory_clerk') {
    header("Location: ../../auth.php");
    exit;
}

require_once '../../../config/db.php';

// Inventory summary counts for dashboard cards
$stmt = $pdo->prepare("SELECT status, COUNT(*) AS count FROM inventory GROUP BY status");
$stmt->execute();
$status_counts = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

$total_items = array_sum($status_counts);
$out_of_stock_items = $status_counts['Out of Stock'] ?? 0;
$low_stock_items = $status_counts['Low'] ?? 0;
$good_items = $status_counts['Good'] ?? 0;

// Inventory list for exact items display
$item_stmt = $pdo->prepare("SELECT id, name, quantity, status, updated_at FROM inventory ORDER BY name ASC");
$item_stmt->execute();
$inventory_items = $item_stmt->fetchAll(PDO::FETCH_ASSOC);

require_once '../../../includes/inventoryClerkHeader.php';
?>

<div class="dashboard">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Inventory Clerk Dashboard</h2>
    </div>

    <div class="info-box mb-4">
        <i class="bi bi-info-circle"></i>
        <span>Welcome, <strong><?php echo htmlspecialchars($_SESSION['first_name']); ?></strong>! Manage item counts and report shortages.</span>
    </div>

    <div class="row g-4 mt-2">
        <div class="col-md-6">
            <div class="card p-4 h-100">
                <h5 class="text-white mb-3" style="font-size: 1.1rem; font-weight: 500;">Total Items</h5>
                <h2 class="text-purple fw-bold mb-3" style="font-size: 2.5rem;"><?php echo (int)$total_items; ?></h2>
                <span class="text-secondary small">All tracked inventory items</span>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card p-4 h-100">
                <h5 class="text-white mb-3" style="font-size: 1.1rem; font-weight: 500;">Out of Stock</h5>
                <h2 class="text-warning fw-bold mb-3" style="font-size: 2.5rem;"><?php echo (int)$out_of_stock_items; ?></h2>
                <span class="text-secondary small">Items that need restocking</span>
            </div>
        </div>
    </div>

    
<?php
require_once '../../../includes/employeeFooter.php';
?>
