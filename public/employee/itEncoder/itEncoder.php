<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'employee' || $_SESSION['role'] !== 'it_encoder') {
    header("Location: ../../auth.php");
    exit;
}

require_once '../../../config/db.php';
require_once '../../../includes/employeeHeader.php';
?>

<div class="dashboard">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>IT Encoder Dashboard</h2>
    </div>
    
    <div class="info-box mb-4">
        <i class="bi bi-info-circle"></i>
        <span>Welcome, <strong><?php echo htmlspecialchars($_SESSION['first_name']); ?></strong>! Your role is to sync and update the quantity metrics on the main corporate stock server.</span>
    </div>
    
    <div class="feature-card text-center mt-4">
        <h4 class="mb-2">Main Server Synchronization</h4>
        <p class="text-secondary small mb-4">Features for syncing local warehouse data to the central database will be added here.</p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <button class="btn btn-primary-purple px-4 py-2">Sync Main Database</button>
            <button class="btn btn-outline-dark px-4 py-2">View Data Discrepancies</button>
        </div>
    </div>
</div>

<?php
require_once '../../../includes/employeeFooter.php';
?>
