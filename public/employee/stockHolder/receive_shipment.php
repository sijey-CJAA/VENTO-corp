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
        <h2>Receive Shipment</h2>
    </div>
    
    <div class="card p-4 mt-4">
        <p class="text-secondary mb-0">Feature to receive shipments will be implemented here.</p>
    </div>
</div>

<?php
require_once '../../../includes/employeeFooter.php';
?>
