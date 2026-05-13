<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin' || $_SESSION['role'] !== 'it_admin') {
    header("Location: ../../login.php");
    exit;
}

require_once '../../../config/db.php';
require_once '../../../includes/header.php';
?>

<div class="dashboard">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>IT Admin Dashboard</h2>
    </div>
    
    <div class="alert alert-info border-0 shadow-sm" style="background: rgba(59, 130, 246, 0.1); color: #60a5fa;">
        Welcome, <strong><?php echo htmlspecialchars($_SESSION['first_name']); ?></strong>! You are managing the IT infrastructure and IT Encoder employees.
    </div>
    
    <div class="card p-5 text-center mt-4" style="background: rgba(255, 255, 255, 0.03); border: 1px dashed rgba(255,255,255,0.1); border-radius: 12px;">
        <h4 class="text-white mb-2">Systems Administration</h4>
        <p class="text-secondary small mb-4">Features for monitoring database health and IT Encoders will be placed here.</p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <button class="btn btn-primary px-4 py-2 fw-medium border-0 shadow-sm" style="background: #0ea5e9;">Manage IT Encoders</button>
            <button class="btn btn-primary px-4 py-2 fw-medium border-0 shadow-sm" style="background: #6366f1;">System Logs</button>
        </div>
    </div>
</div>

<?php
require_once '../../../includes/footer.php';
?>
