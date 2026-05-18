<?php
session_start();

// Ensure the user is logged in and is HR
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'hr') {
    header("Location: ../../auth.php");
    exit;
}

require_once '../../../config/db.php';

$msg = '';

// Handle Approve / Reject actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['id'], $_POST['table_name'])) {
    $action = $_POST['action'];
    $id = (int)$_POST['id'];
    $table_name = $_POST['table_name'];
    
    if (in_array($table_name, ['admins', 'employees'])) {
        $status = ($action === 'approve') ? 'approved' : 'rejected';
        $stmt = $pdo->prepare("UPDATE $table_name SET status = ? WHERE id = ?");
        if ($stmt->execute([$status, $id])) {
            $msg = "<div class='alert alert-success'>Application " . htmlspecialchars($action) . "d successfully.</div>";
        } else {
            $msg = "<div class='alert alert-danger'>Error updating application status.</div>";
        }
    }
}

// Fetch Pending Admins
$stmt_admins = $pdo->query("SELECT * FROM admins WHERE status = 'pending'");
$pending_admins = $stmt_admins->fetchAll();

// Fetch Pending Employees
$stmt_employees = $pdo->query("SELECT * FROM employees WHERE status = 'pending'");
$pending_employees = $stmt_employees->fetchAll();

require_once '../../../includes/hrHeader.php';
?>

<div class="dashboard">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Pending Applications</h2>
        <a href="../../logout.php" class="btn btn-outline-danger">Logout</a>
    </div>

    <?php echo $msg; ?>

    <h4 class="mt-4 mb-3 text-purple">Pending Admin Applications</h4>
    <div class="card p-4 mb-4">
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($pending_admins) > 0): ?>
                        <?php foreach ($pending_admins as $admin): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($admin['id']); ?></td>
                            <td><?php echo htmlspecialchars($admin['first_name'] . ' ' . $admin['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($admin['email']); ?></td>
                            <td><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $admin['role'] ?? ''))); ?></td>
                            <td>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="id" value="<?php echo $admin['id']; ?>">
                                    <input type="hidden" name="table_name" value="admins">
                                    <button type="submit" name="action" value="approve" class="btn btn-sm btn-success">Approve</button>
                                    <button type="submit" name="action" value="reject" class="btn btn-sm btn-danger">Reject</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center text-muted">No pending admin applications.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <h4 class="mt-5 mb-3 text-purple">Pending Employee Applications</h4>
    <div class="card p-4">
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($pending_employees) > 0): ?>
                        <?php foreach ($pending_employees as $emp): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($emp['id']); ?></td>
                            <td><?php echo htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($emp['email']); ?></td>
                            <td>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="id" value="<?php echo $emp['id']; ?>">
                                    <input type="hidden" name="table_name" value="employees">
                                    <button type="submit" name="action" value="approve" class="btn btn-sm btn-success">Approve</button>
                                    <button type="submit" name="action" value="reject" class="btn btn-sm btn-danger">Reject</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center text-muted">No pending employee applications.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php require_once '../../../includes/adminFooter.php'; ?>
