<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin' || $_SESSION['role'] !== 'operations_admin') {
    header("Location: ../../auth.php");
    exit;
}

require_once '../../../config/db.php';



require_once '../../../includes/operationManagerHeader.php';
?>

<div class="dashboard">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Operations Admin Dashboard</h2>
    </div>
    
    <div class="info-box mb-4">
        <i class="bi bi-info-circle"></i>
        <span>Welcome, <strong><?php echo htmlspecialchars($_SESSION['first_name']); ?></strong>! You are overseeing the operations and managing the Stock Holder employees.</span>
    </div>
    
    <div class="feature-card text-center mt-4">
        <h4 class="mb-2">Operations Control</h4>
        <p class="text-secondary small mb-4">Features for overseeing warehouse operations and dispatching tasks will go here.</p>
    </div>
</div>

<?php
require_once '../../../includes/adminFooter.php';
?>
