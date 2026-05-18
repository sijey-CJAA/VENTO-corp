<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'employee' || $_SESSION['role'] !== 'inventory_clerk') {
    header("Location: ../../auth.php");
    exit;
}

require_once '../../../config/db.php';
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
    
    <div class="feature-card text-center mt-4">
        <h4 class="mb-2">Stock Quantity Management</h4>
        <p class="text-secondary small mb-0">Features for recounting items and adjusting stock levels will be added here.</p>
    </div>
</div>

<?php
require_once '../../../includes/employeeFooter.php';
?>
