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
        <span>Welcome, <strong><?php echo htmlspecialchars($_SESSION['first_name']); ?></strong>! Your role is to update and monitor the exact quantity of supplies and items.</span>
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-4 mb-4">
        <div class="col">
            <div class="stat-card h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="text-secondary small text-uppercase">Total items</span>
                        <h3 class="text-white mb-0"><?php echo $total_items; ?></h3>
                    </div>
                    <i class="bi bi-box-seam fs-2 text-purple"></i>
                </div>
                <p class="text-secondary small mb-0">All inventory items tracked across stock status.</p>
            </div>
        </div>
        <div class="col">
            <div class="stat-card h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="text-secondary small text-uppercase">Good stock</span>
                        <h3 class="text-white mb-0"><?php echo $good_items; ?></h3>
                    </div>
                    <i class="bi bi-check-circle-fill fs-2 text-success"></i>
                </div>
                <p class="text-secondary small mb-0">Items with healthy quantity levels.</p>
            </div>
        </div>
        <div class="col">
            <div class="stat-card h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="text-secondary small text-uppercase">Low stock</span>
                        <h3 class="text-white mb-0"><?php echo $low_stock_items; ?></h3>
                    </div>
                    <i class="bi bi-exclamation-triangle-fill fs-2 text-warning"></i>
                </div>
                <p class="text-secondary small mb-0">Items that need restocking soon.</p>
            </div>
        </div>
        <div class="col">
            <div class="stat-card h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="text-secondary small text-uppercase">Out of stock</span>
                        <h3 class="text-white mb-0"><?php echo $out_of_stock_items; ?></h3>
                    </div>
                    <i class="bi bi-slash-circle-fill fs-2 text-danger"></i>
                </div>
                <p class="text-secondary small mb-0">Items currently unavailable in stock.</p>
            </div>
        </div>
    </div>

    <div class="card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="mb-0 text-white">Exact inventory items</h5>
                <p class="text-secondary small mb-0">Current item names, quantities, and stock status.</p>
            </div>
            <span class="badge bg-white text-dark py-2 px-3"><?php echo count($inventory_items); ?> items</span>
        </div>

        <div class="row g-3">
            <?php foreach ($inventory_items as $item): ?>
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="card h-100 p-3 bg-dark border-secondary">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="text-white mb-1"><?php echo htmlspecialchars($item['name']); ?></h6>
                            <?php if ($item['status'] === 'Out of Stock'): ?>
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">Out of Stock</span>
                            <?php elseif ($item['status'] === 'Low'): ?>
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25">Low</span>
                            <?php else: ?>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Good</span>
                            <?php endif; ?>
                        </div>
                        <p class="text-secondary mb-1 small"><strong>Quantity:</strong> <?php echo (int)$item['quantity']; ?></p>
                        <p class="text-secondary mb-0 small">Updated: <?php echo date('M d, Y', strtotime($item['updated_at'])); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
