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
    // Strict validation to prevent SQL injection on table name
    $table = $_POST['table_name'] === 'admins' ? 'admins' : 'employees';
    
    if ($action === 'approve') {
        $stmt = $pdo->prepare("UPDATE $table SET status = 'approved' WHERE id = ?");
        if ($stmt->execute([$id])) {
            $msg = "Applicant approved successfully. They can now log in.";
        }
    } elseif ($action === 'reject') {
        // Fetch applicant data first
        $stmt = $pdo->prepare("SELECT first_name, last_name, email, role, application_file, created_at FROM $table WHERE id = ?");
        $stmt->execute([$id]);
        $app = $stmt->fetch();
        
        if ($app) {
            // Insert into rejection_history archive
            $insertStmt = $pdo->prepare("INSERT INTO rejection_history (first_name, last_name, email, role, application_file, applied_at) VALUES (?, ?, ?, ?, ?, ?)");
            if ($insertStmt->execute([$app['first_name'], $app['last_name'], $app['email'], $app['role'], $app['application_file'], $app['created_at']])) {
                // Delete from original table so they can re-apply and their email is freed up
                $delStmt = $pdo->prepare("DELETE FROM $table WHERE id = ?");
                $delStmt->execute([$id]);
                $msg = "Applicant rejected and archived. Their email is now freed for re-application.";
            }
        }
    }
}

// Fetch pending applicants from both tables
$stmt = $pdo->query("
    SELECT id, first_name, last_name, email, role, application_file, created_at, 'admins' as table_name 
    FROM admins WHERE status = 'pending'
    UNION ALL
    SELECT id, first_name, last_name, email, role, application_file, created_at, 'employees' as table_name 
    FROM employees WHERE status = 'pending'
    ORDER BY created_at DESC
");
$applicants = $stmt->fetchAll();

require_once '../../../includes/hrHeader.php';
?>

<div class="dashboard">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Review Applications</h2>
        <a href="../../logout.php" class="btn btn-outline-danger">Logout</a>
    </div>
    
    <?php if ($msg): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($msg); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card p-0 overflow-hidden shadow-sm">
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th class="px-4 py-3 border-secondary">Name</th>
                        <th class="py-3 border-secondary">Email</th>
                        <th class="py-3 border-secondary">Role Applied For</th>
                        <th class="py-3 border-secondary">Date Applied</th>
                        <th class="py-3 border-secondary">Application Form</th>
                        <th class="px-4 py-3 border-secondary text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($applicants) > 0): ?>
                        <?php foreach ($applicants as $app): ?>
                            <tr>
                                <td class="px-4 border-secondary fw-medium">
                                    <?php echo htmlspecialchars($app['first_name'] . ' ' . $app['last_name']); ?>
                                </td>
                                <td class="border-secondary text-secondary">
                                    <?php echo htmlspecialchars($app['email']); ?>
                                </td>
                                <td class="border-secondary">
                                    <span class="badge bg-primary bg-opacity-25 text-primary border border-primary border-opacity-25 px-2 py-1">
                                        <?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $app['role']))); ?>
                                    </span>
                                </td>
                                <td class="border-secondary text-secondary small">
                                    <?php echo date('M d, Y h:i A', strtotime($app['created_at'])); ?>
                                </td>
                                <td class="border-secondary">
                                    <?php if ($app['application_file']): ?>
                                        <a href="/VENTO-corp/<?php echo htmlspecialchars($app['application_file']); ?>" target="_blank" class="btn btn-sm btn-outline-info">
                                            View Form
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small">No file</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 border-secondary text-end">
                                    <form method="POST" style="display:inline-block;">
                                        <input type="hidden" name="id" value="<?php echo $app['id']; ?>">
                                        <input type="hidden" name="table_name" value="<?php echo htmlspecialchars($app['table_name']); ?>">
                                        
                                        <button type="submit" name="action" value="approve" class="btn btn-sm btn-success me-1">Approve</button>
                                        <button type="submit" name="action" value="reject" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to reject this applicant?');">Reject</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-secondary">No pending applications at the moment.</td>
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
