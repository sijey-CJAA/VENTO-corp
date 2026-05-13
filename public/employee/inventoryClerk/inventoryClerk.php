<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'employee' || $_SESSION['role'] !== 'inventory_clerk') {
    header("Location: ../../login.php");
    exit;
}

require_once '../../../config/db.php';
require_once '../../../includes/header.php';
?>

<div class="dashboard">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Inventory Clerk Dashboard</h2>
    </div>
    
    <div class="alert alert-info border-0 shadow-sm" style="background: rgba(59, 130, 246, 0.1); color: #60a5fa;">
        Welcome, <strong><?php echo htmlspecialchars($_SESSION['first_name']); ?></strong>! Your role is to update and monitor the exact quantity of supplies and items.
    </div>
    
    <div class="card p-5 text-center mt-4" style="background: rgba(255, 255, 255, 0.03); border: 1px dashed rgba(255,255,255,0.1); border-radius: 12px;">
        <h4 class="text-white mb-2">Stock Quantity Management</h4>
        <p class="text-secondary small mb-4">Features for recounting items and adjusting stock levels will be added here.</p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <button class="btn btn-primary px-4 py-2 fw-medium border-0 shadow-sm" style="background: #6366f1;">Update Item Quantities</button>
            <button class="btn btn-primary px-4 py-2 fw-medium border-0 shadow-sm" style="background: #8b5cf6;">Perform Stock Audit</button>
        </div>
    </div>
</div>

<?php
require_once '../../../includes/footer.php';
?>
