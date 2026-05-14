<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin' || $_SESSION['role'] !== 'operations_admin') {
    header("Location: ../../login.php");
    exit;
}

require_once '../../../config/db.php';

// Fetch employees under this admin
$emp_stmt = $pdo->prepare("SELECT first_name, last_name, email, status, created_at FROM employees WHERE role = 'stock_holder'");
$emp_stmt->execute();
$employees = $emp_stmt->fetchAll(PDO::FETCH_ASSOC);

require_once '../../../includes/adminHeader.php';
?>

<div class="dashboard">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Operations Admin Dashboard</h2>
    </div>
    
    <div class="alert alert-info border-0 shadow-sm" style="background: rgba(59, 130, 246, 0.1); color: #60a5fa;">
        Welcome, <strong><?php echo htmlspecialchars($_SESSION['first_name']); ?></strong>! You are overseeing the operations and managing the Stock Holder employees.
    </div>
    
    <div class="card p-5 text-center mt-4" style="background: rgba(255, 255, 255, 0.03); border: 1px dashed rgba(255,255,255,0.1); border-radius: 12px;">
        <h4 class="text-white mb-2">Operations Control</h4>
        <p class="text-secondary small mb-4">Features for overseeing warehouse operations and dispatching tasks will go here.</p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <button class="btn btn-primary px-4 py-2 fw-medium border-0 shadow-sm" style="background: #f59e0b;">Manage Stock Holders</button>
            <button class="btn btn-primary px-4 py-2 fw-medium border-0 shadow-sm" style="background: #10b981;">View Active Operations</button>
        </div>
    </div>
    <div class="card p-4 mt-4 border-0 shadow-sm" style="background: rgba(255, 255, 255, 0.03); border: 1px dashed rgba(255,255,255,0.1); border-radius: 12px;">
        <h4 class="mb-3 text-white">My Employees (Stock Holders)</h4>
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0" style="background: transparent;">
                <thead>
                    <tr>
                        <th class="text-secondary" style="border-bottom-color: rgba(255,255,255,0.1);">Name</th>
                        <th class="text-secondary" style="border-bottom-color: rgba(255,255,255,0.1);">Email</th>
                        <th class="text-secondary" style="border-bottom-color: rgba(255,255,255,0.1);">Status</th>
                        <th class="text-secondary" style="border-bottom-color: rgba(255,255,255,0.1);">Date Joined</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($employees) > 0): ?>
                        <?php foreach ($employees as $emp): ?>
                            <tr>
                                <td style="border-bottom-color: rgba(255,255,255,0.05);"><?php echo htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']); ?></td>
                                <td style="border-bottom-color: rgba(255,255,255,0.05);"><?php echo htmlspecialchars($emp['email']); ?></td>
                                <td style="border-bottom-color: rgba(255,255,255,0.05);">
                                    <?php if ($emp['status'] === 'approved'): ?>
                                        <span class="badge bg-success" style="background-color: #10b981 !important;">Approved</span>
                                    <?php elseif ($emp['status'] === 'pending'): ?>
                                        <span class="badge text-dark" style="background-color: #f59e0b;">Pending</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Rejected</span>
                                    <?php endif; ?>
                                </td>
                                <td style="border-bottom-color: rgba(255,255,255,0.05);"><?php echo date('M d, Y', strtotime($emp['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center text-secondary py-4" style="border-bottom-color: rgba(255,255,255,0.05);">No employees found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
require_once '../../../includes/adminFooter.php';
?>
