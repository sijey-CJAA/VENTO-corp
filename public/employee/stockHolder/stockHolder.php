<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'employee' || $_SESSION['role'] !== 'stock_holder') {
    header("Location: ../../auth.php");
    exit;
}

require_once '../../../config/db.php';
require_once '../../../includes/employeeHeader.php';
?>

<div class="dashboard">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Stock Holder Dashboard</h2>
    </div>
    
    <div class="info-box mb-4">
        <i class="bi bi-info-circle"></i>
        <span>Welcome, <strong><?php echo htmlspecialchars($_SESSION['first_name']); ?></strong>! Your primary duty is to physically stock supplies and items in the warehouse.</span>
    </div>
    
    <div class="feature-card text-center mt-4">
        <h4 class="mb-2">Inventory Loading Area</h4>
        <p class="text-secondary small mb-4">Features for receiving shipments and shelving stock will be added here.</p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <button class="btn btn-primary-purple px-4 py-2">Receive Shipment</button>
            <button class="btn btn-outline-dark px-4 py-2">Log Shelved Items</button>
        </div>
    </div>
</div>

<?php
require_once '../../../includes/employeeFooter.php';
?>
