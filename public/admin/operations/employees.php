<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin' || $_SESSION['role'] !== 'operations_admin') {
    header("Location: ../../auth.php");
    exit;
}

require_once '../../../config/db.php';

// Fetch employees under this admin
$emp_stmt = $pdo->prepare("SELECT first_name, last_name, email, status, created_at FROM employees WHERE role = 'stock_holder'");
$emp_stmt->execute();
$employees = $emp_stmt->fetchAll(PDO::FETCH_ASSOC);

require_once '../../../includes/operationManagerHeader.php';
?>

<div class="dashboard">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Manage Employees</h2>
    </div>

    <div class="info-box mb-4">
        <i class="bi bi-info-circle"></i>
        <span>Here you can view all Stock Holders assigned under your supervision.</span>
    </div>

    <div class="card p-4 mt-4">
        <h4 class="mb-3 text-white">My Employees (Stock Holders)</h4>
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0">
                <thead>
                    <tr>
                        <th class="text-secondary">Name</th>
                        <th class="text-secondary">Email</th>
                        <th class="text-secondary">Status</th>
                        <th class="text-secondary">Date Joined</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($employees) > 0): ?>
                        <?php foreach ($employees as $emp): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($emp['email']); ?></td>
                                <td>
                                    <?php if ($emp['status'] === 'approved'): ?>
                                        <span class="badge text-success bg-success bg-opacity-10 border border-success border-opacity-25">Approved</span>
                                    <?php elseif ($emp['status'] === 'pending'): ?>
                                        <span class="badge text-warning bg-warning bg-opacity-10 border border-warning border-opacity-25">Pending</span>
                                    <?php else: ?>
                                        <span class="badge text-danger bg-danger bg-opacity-10 border border-danger border-opacity-25">Rejected</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($emp['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center text-secondary py-4">No employees found.</td>
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
