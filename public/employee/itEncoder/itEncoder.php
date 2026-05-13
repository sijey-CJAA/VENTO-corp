<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'employee' || $_SESSION['role'] !== 'it_encoder') {
    header("Location: ../../login.php");
    exit;
}

require_once '../../../config/db.php';
require_once '../../../includes/header.php';
?>

<div class="dashboard">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>IT Encoder Dashboard</h2>
    </div>
    
    <div class="alert alert-info border-0 shadow-sm" style="background: rgba(59, 130, 246, 0.1); color: #60a5fa;">
        Welcome, <strong><?php echo htmlspecialchars($_SESSION['first_name']); ?></strong>! Your role is to sync and update the quantity metrics on the main corporate stock server.
    </div>
    
    <div class="card p-5 text-center mt-4" style="background: rgba(255, 255, 255, 0.03); border: 1px dashed rgba(255,255,255,0.1); border-radius: 12px;">
        <h4 class="text-white mb-2">Main Server Synchronization</h4>
        <p class="text-secondary small mb-4">Features for syncing local warehouse data to the central database will be added here.</p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <button class="btn btn-primary px-4 py-2 fw-medium border-0 shadow-sm" style="background: #0ea5e9;">Sync Main Database</button>
            <button class="btn btn-primary px-4 py-2 fw-medium border-0 shadow-sm" style="background: #14b8a6;">View Data Discrepancies</button>
        </div>
    </div>
</div>

<?php
require_once '../../../includes/footer.php';
?>
