<?php
session_start();

// Ensure the user is logged in and is HR
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'hr') {
    header("Location: ../../auth.php");
    exit;
}

require_once '../../../config/db.php';

// Fetch Approved Admins
$stmt_admins = $pdo->query("SELECT * FROM admins WHERE status = 'approved'");
$active_admins = $stmt_admins->fetchAll();

// Fetch Approved Employees
$stmt_employees = $pdo->query("SELECT * FROM employees WHERE status = 'approved'");
$active_employees = $stmt_employees->fetchAll();

// Fetch HR (Main HR Admin)
$stmt_hr = $pdo->query("SELECT * FROM hr");
$hr_users = $stmt_hr->fetchAll();

require_once '../../../includes/hrHeader.php';
?>

<div class="dashboard">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Active Employees</h2>
        <a href="../../logout.php" class="btn btn-outline-danger">Logout</a>
    </div>

    <h4 class="mt-4 mb-3 text-purple">HR Personnel</h4>
    <div class="card p-4 mb-4">
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
                    <?php if (count($hr_users) > 0): ?>
                        <?php foreach ($hr_users as $hr): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($hr['id']); ?></td>
                            <td><?php echo htmlspecialchars($hr['first_name'] . ' ' . $hr['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($hr['email']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="3" class="text-center text-muted">No HR personnel found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <h4 class="mt-4 mb-3 text-purple">Active Admin Accounts</h4>
    <div class="card p-4 mb-4">
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
                    <?php if (count($active_admins) > 0): ?>
                        <?php foreach ($active_admins as $admin): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($admin['id']); ?></td>
                            <td><?php echo htmlspecialchars($admin['first_name'] . ' ' . $admin['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($admin['email']); ?></td>
                            <td><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $admin['role'] ?? ''))); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center text-muted">No active admins found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <h4 class="mt-5 mb-3 text-purple">Active Employees</h4>
    <div class="card p-4">
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
                    <?php if (count($active_employees) > 0): ?>
                        <?php foreach ($active_employees as $emp): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($emp['id']); ?></td>
                            <td><?php echo htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($emp['email']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="text-center text-muted">No active employees found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php require_once '../../../includes/adminFooter.php'; ?>
