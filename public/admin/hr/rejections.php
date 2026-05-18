<?php
session_start();

// Ensure the user is logged in and is HR
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'hr') {
    header("Location: ../../auth.php");
    exit;
}

require_once '../../../config/db.php';

// Fetch Rejected Admins
$stmt_admins = $pdo->query("SELECT * FROM admins WHERE status = 'rejected'");
$rejected_admins = $stmt_admins->fetchAll();

// Fetch Rejected Employees
$stmt_employees = $pdo->query("SELECT * FROM employees WHERE status = 'rejected'");
$rejected_employees = $stmt_employees->fetchAll();

require_once '../../../includes/hrHeader.php';
?>

<div class="dashboard">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Rejected Applications</h2>
        <a href="../../logout.php" class="btn btn-outline-danger">Logout</a>
    </div>

    <h4 class="mt-4 mb-3 text-danger">Rejected Admin Applications</h4>
    <div class="card p-4 mb-4" style="border-color: rgba(239, 68, 68, 0.3) !important;">
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($rejected_admins) > 0): ?>
                        <?php foreach ($rejected_admins as $admin): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($admin['id']); ?></td>
                            <td><?php echo htmlspecialchars($admin['first_name'] . ' ' . $admin['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($admin['email']); ?></td>
                            <td><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $admin['role'] ?? ''))); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center text-muted">No rejected admin applications.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <h4 class="mt-5 mb-3 text-danger">Rejected Employee Applications</h4>
    <div class="card p-4" style="border-color: rgba(239, 68, 68, 0.3) !important;">
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($rejected_employees) > 0): ?>
                        <?php foreach ($rejected_employees as $emp): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($emp['id']); ?></td>
                            <td><?php echo htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($emp['email']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="text-center text-muted">No rejected employee applications.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php require_once '../../../includes/adminFooter.php'; ?>
