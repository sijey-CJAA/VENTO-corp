<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'employee' || $_SESSION['role'] !== 'stock_holder') {
    header("Location: ../../auth.php");
    exit;
}

require_once '../../../config/db.php';
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
    
    <div class="feature-card mt-4">
        <div class="card p-4">
            <h4 class="mb-3 text-white">Inventory Loading Area</h4>
            <p class="text-secondary mb-0">Use the navigation menu on the left to receive shipments or log newly shelved items into the system.</p>
        </div>
    </div>
</div>

<?php
require_once '../../../includes/employeeFooter.php';
?>
