<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'employee' || $_SESSION['role'] !== 'it_security') {
    header("Location: ../../login.php");
    exit;
}

require_once '../../../config/db.php';
require_once '../../../includes/employeeHeader.php';
?>

<div class="dashboard">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>IT Security Dashboard</h2>
    </div>
    
    <div class="alert alert-info border-0 shadow-sm" style="background: rgba(59, 130, 246, 0.1); color: #60a5fa;">
        Welcome, <strong><?php echo htmlspecialchars($_SESSION['first_name']); ?></strong>! Your role is to handle the security features of the system.
    </div>
    
    <div class="card p-5 text-center mt-4" style="background: rgba(255, 255, 255, 0.03); border: 1px dashed rgba(255,255,255,0.1); border-radius: 12px;">
        <h4 class="text-white mb-2">Security Features</h4>
        <p class="text-secondary small mb-4">Monitoring and managing security aspects of the system will be available here.</p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <button class="btn btn-primary px-4 py-2 fw-medium border-0 shadow-sm" style="background: #e11d48;">Security Logs</button>
            <button class="btn btn-primary px-4 py-2 fw-medium border-0 shadow-sm" style="background: #6366f1;">System Access Control</button>
        </div>
    </div>
</div>

<?php
require_once '../../../includes/employeeFooter.php';
?>
