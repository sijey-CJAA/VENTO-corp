<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin' || $_SESSION['role'] !== 'it_admin') {
    header("Location: ../../auth.php");
    exit;
}

require_once '../../../config/db.php';

// Fetch employees under this admin
$emp_stmt = $pdo->prepare("SELECT first_name, last_name, email, role, status, created_at FROM employees WHERE role IN ('it_encoder', 'it_security')");
$emp_stmt->execute();
$employees = $emp_stmt->fetchAll(PDO::FETCH_ASSOC);

require_once '../../../includes/adminHeader.php';
?>

<div class="dashboard">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>IT Admin Dashboard</h2>
    </div>
    
    <div class="info-box mb-4">
        <i class="bi bi-info-circle"></i>
        <span>Welcome, <strong><?php echo htmlspecialchars($_SESSION['first_name']); ?></strong>! You are managing the IT infrastructure and IT Encoder employees.</span>
    </div>
    
    <div class="feature-card text-center mt-4">
        <h4 class="mb-2">Systems Administration</h4>
        <p class="text-secondary small mb-4">Features for monitoring database health, IT Encoders, and IT Security will be placed here.</p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <button class="btn btn-primary-purple px-4 py-2">Manage IT Encoders</button>
            <button class="btn btn-outline-dark px-4 py-2">System Logs</button>
        </div>
    </div>
    <div class="card p-4 mt-4">
        <h4 class="mb-3 text-white">My Employees (IT Encoders & Security)</h4>
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0">
                <thead>
                    <tr>
                        <th class="text-secondary">Name</th>
                        <th class="text-secondary">Email</th>
                        <th class="text-secondary">Role</th>
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
                                    <?php if ($emp['role'] === 'it_encoder'): ?>
                                        <span class="badge text-info bg-info bg-opacity-10 border border-info border-opacity-25">IT Encoder</span>
                                    <?php else: ?>
                                        <span class="badge text-danger bg-danger bg-opacity-10 border border-danger border-opacity-25">IT Security</span>
                                    <?php endif; ?>
                                </td>
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
                            <td colspan="5" class="text-center text-secondary py-4">No employees found.</td>
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
