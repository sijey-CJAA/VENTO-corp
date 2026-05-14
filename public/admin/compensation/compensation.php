<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin' || $_SESSION['role'] !== 'compensation_manager') {
    header("Location: ../../auth.php");
    exit;
}

require_once '../../../config/db.php';
require_once '../../../includes/compensationManagerHeader.php';
?>

<div class="dashboard">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Compensation Manager Dashboard</h2>
    </div>
    
    <div class="info-box mb-4">
        <i class="bi bi-info-circle"></i>
        <span>Welcome, <strong><?php echo htmlspecialchars($_SESSION['first_name']); ?></strong>! You are responsible for handling employee salaries and payroll operations.</span>
    </div>
    
    <div class="feature-card text-center mt-4">
        <h4 class="mb-2">Payroll & Compensation</h4>
        <p class="text-secondary small mb-4">Select an option from the sidebar navigation to manage payrolls and salary adjustments.</p>
    </div>
</div>

<?php
require_once '../../../includes/adminFooter.php';
?>