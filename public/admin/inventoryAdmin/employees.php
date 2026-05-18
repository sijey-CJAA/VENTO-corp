<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin' || $_SESSION['role'] !== 'inventory_admin') {
    header("Location: ../../auth.php");
    exit;
}

require_once '../../../config/db.php';

// Fetch employees under this admin
$emp_stmt = $pdo->prepare("SELECT first_name, last_name, email, status, created_at FROM employees WHERE role = 'inventory_clerk'");
$emp_stmt->execute();
$employees = $emp_stmt->fetchAll(PDO::FETCH_ASSOC);

require_once '../../../includes/inventoryAdminHeader.php';
?>

<div class="dashboard pt-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-medium text-white m-0">My Employees</h2>
    </div>
    
    <div class="card p-4 mt-4" style="background-color: #18181b; border: 1px solid #27272a; border-radius: 10px; box-shadow: none;">
        <h4 class="mb-3 text-white fw-medium" style="font-size: 18px;">Inventory Clerks</h4>
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0" style="--bs-table-bg: #18181b; --bs-table-border-color: #27272a;">
                <thead>
                    <tr>
                        <th class="text-secondary fw-normal">Name</th>
                        <th class="text-secondary fw-normal">Email</th>
                        <th class="text-secondary fw-normal">Status</th>
                        <th class="text-secondary fw-normal">Date Joined</th>
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
                                        <span class="badge text-success bg-success bg-opacity-10 border border-success border-opacity-25" style="border-radius: 4px;">Approved</span>
                                    <?php elseif ($emp['status'] === 'pending'): ?>
                                        <span class="badge text-warning bg-warning bg-opacity-10 border border-warning border-opacity-25" style="border-radius: 4px;">Pending</span>
                                    <?php else: ?>
                                        <span class="badge text-danger bg-danger bg-opacity-10 border border-danger border-opacity-25" style="border-radius: 4px;">Rejected</span>
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
require_once '../../../includes/inventoryAdminFooter.php';
?>
