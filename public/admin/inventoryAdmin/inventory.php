<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin' || $_SESSION['role'] !== 'inventory_admin') {
    header("Location: ../../auth.php");
    exit;
}

require_once '../../../config/db.php';

// Fetch inventory items safely
$inventory_items = [];
try {
    $inv_stmt = $pdo->prepare("SELECT id, name, quantity, status, last_verification_image, updated_by, updated_at FROM inventory ORDER BY name ASC");
    $inv_stmt->execute();
    $inventory_items = $inv_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Ignore if table is missing
}

require_once '../../../includes/inventoryAdminHeader.php';
?>

<div class="dashboard">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Manage Inventory</h2>
        <button class="btn btn-primary-purple d-flex align-items-center gap-2">
            <i class="bi bi-plus-lg"></i> Add New Item
        </button>
    </div>
    
    <div class="info-box mb-4">
        <i class="bi bi-info-circle"></i>
        <span>Here you can view and manage all inventory stock levels. This data is updated in real-time.</span>
    </div>
    
    <div class="card p-4 mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="text-white mb-0">Global Inventory Catalog</h4>
            <div class="input-group" style="width: 250px;">
                <span class="input-group-text bg-transparent border-end-0 border-outline-variant text-secondary"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control border-start-0 border-outline-variant bg-transparent text-white shadow-none ps-0" placeholder="Search items...">
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0">
                <thead>
                    <tr>
                        <th class="text-secondary" style="width: 10%;">ID</th>
                        <th class="text-secondary" style="width: 20%;">Item Name</th>
                        <th class="text-secondary" style="width: 10%;">Quantity</th>
                        <th class="text-secondary" style="width: 15%;">Status</th>
                        <th class="text-secondary" style="width: 15%;">Verification</th>
                        <th class="text-secondary" style="width: 15%;">Updated By</th>
                        <th class="text-secondary" style="width: 15%;">Last Updated</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($inventory_items) > 0): ?>
                        <?php foreach ($inventory_items as $item): ?>
                            <tr>
                                <td class="align-middle text-secondary">#<?php echo str_pad($item['id'], 4, '0', STR_PAD_LEFT); ?></td>
                                <td class="align-middle fw-semibold text-white"><?php echo htmlspecialchars($item['name']); ?></td>
                                <td class="align-middle">
                                    <span class="text-white"><?php echo (int)$item['quantity']; ?></span>
                                </td>
                                <td class="align-middle">
                                    <?php if ($item['status'] === 'Out of Stock'): ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1">Out of Stock</span>
                                    <?php elseif ($item['status'] === 'Low'): ?>
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1">Low</span>
                                    <?php else: ?>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">Good</span>
                                    <?php endif; ?>
                                </td>
                                <td class="align-middle">
                                    <?php if ($item['last_verification_image']): ?>
                                        <a href="../../uploads/inventory/<?php echo htmlspecialchars($item['last_verification_image']); ?>" target="_blank">
                                            <img src="../../uploads/inventory/<?php echo htmlspecialchars($item['last_verification_image']); ?>" alt="Verification" class="img-thumbnail border-secondary bg-transparent" style="max-height: 40px; max-width: 60px; object-fit: cover;">
                                        </a>
                                    <?php else: ?>
                                        <span class="text-secondary small">N/A</span>
                                    <?php endif; ?>
                                </td>
                                <td class="align-middle text-white small">
                                    <?php echo $item['updated_by'] ? htmlspecialchars($item['updated_by']) : '<span class="text-secondary">N/A</span>'; ?>
                                </td>
                                <td class="align-middle text-secondary small">
                                    <?php echo date('M d, Y h:i A', strtotime($item['updated_at'])); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-secondary py-4">No items found in inventory.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
require_once '../../../includes/inventoryAdminFooter.php';
?>
