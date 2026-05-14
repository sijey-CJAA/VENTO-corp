<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'hr') {
    header("Location: ../../auth.php");
    exit;
}

require_once '../../../config/db.php';

// Fetch Active / Approved
$stmt = $pdo->query("
    SELECT id, first_name, last_name, email, role, created_at, updated_at, 'admins' as source 
    FROM admins WHERE status = 'approved'
    UNION ALL
    SELECT id, first_name, last_name, email, role, created_at, updated_at, 'employees' as source 
    FROM employees WHERE status = 'approved'
    ORDER BY updated_at DESC
");
$active = $stmt->fetchAll();

// Fetch Rejected
$stmt = $pdo->query("
    SELECT id, first_name, last_name, email, role, applied_at as created_at, rejected_at as updated_at, 'rejection_history' as source 
    FROM rejection_history
    ORDER BY rejected_at DESC
");
$rejected = $stmt->fetchAll();

require_once '../../../includes/hrHeader.php';
?>

<div class="dashboard">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Employee Directory</h2>
        <a href="../../logout.php" class="btn btn-outline-danger">Logout</a>
    </div>

    <div class="nav-tabs mb-4" id="directoryTabs" role="tablist">
        <button class="nav-link active" id="active-tab" data-bs-toggle="tab" data-bs-target="#active-pane" type="button" role="tab">Active Employees (<?php echo count($active); ?>)</button>
        <button class="nav-link" id="rejected-tab" data-bs-toggle="tab" data-bs-target="#rejected-pane" type="button" role="tab">Rejected Applicants (<?php echo count($rejected); ?>)</button>
    </div>

    <div class="tab-content" id="myTabContent">
        <!-- Active Tab -->
        <div class="tab-pane fade show active" id="active-pane" role="tabpanel" tabindex="0">
            <div class="card p-0 overflow-hidden shadow-sm">
                <div class="table-responsive">
                    <table class="table table-dark table-hover mb-0 align-middle">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 border-secondary font-monospace">ID</th>
                                <th class="py-3 border-secondary">Name</th>
                                <th class="py-3 border-secondary">Role</th>
                                <th class="py-3 border-secondary">Date Applied</th>
                                <th class="px-4 py-3 border-secondary">Date Accepted</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($active) > 0): ?>
                                <?php foreach ($active as $user): ?>
                                    <tr>
                                        <td class="px-4 border-secondary text-secondary font-monospace small">#<?php echo $user['id']; ?></td>
                                        <td class="border-secondary fw-medium">
                                            <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?><br>
                                            <span class="text-secondary small fw-normal"><?php echo htmlspecialchars($user['email']); ?></span>
                                        </td>
                                        <td class="border-secondary">
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">
                                                <?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $user['role']))); ?>
                                            </span>
                                        </td>
                                        <td class="border-secondary text-secondary small">
                                            <?php echo date('M d, Y', strtotime($user['created_at'])); ?>
                                        </td>
                                        <td class="px-4 border-secondary text-success small fw-medium">
                                            <?php echo date('M d, Y', strtotime($user['updated_at'])); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-secondary">No active employees found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Rejected Tab -->
        <div class="tab-pane fade" id="rejected-pane" role="tabpanel" tabindex="0">
            <div class="card p-0 overflow-hidden shadow-sm">
                <div class="table-responsive">
                    <table class="table table-dark table-hover mb-0 align-middle">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 border-secondary font-monospace">ID</th>
                                <th class="py-3 border-secondary">Name</th>
                                <th class="py-3 border-secondary">Role Applied For</th>
                                <th class="py-3 border-secondary">Date Applied</th>
                                <th class="px-4 py-3 border-secondary">Date Rejected</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($rejected) > 0): ?>
                                <?php foreach ($rejected as $user): ?>
                                    <tr>
                                        <td class="px-4 border-secondary text-secondary font-monospace small">#<?php echo $user['id']; ?></td>
                                        <td class="border-secondary fw-medium text-muted">
                                            <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                                        </td>
                                        <td class="border-secondary">
                                            <span class="badge bg-secondary bg-opacity-25 text-secondary border border-secondary border-opacity-25 px-2 py-1">
                                                <?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $user['role']))); ?>
                                            </span>
                                        </td>
                                        <td class="border-secondary text-secondary small">
                                            <?php echo date('M d, Y', strtotime($user['created_at'])); ?>
                                        </td>
                                        <td class="px-4 border-secondary text-danger small fw-medium">
                                            <?php echo date('M d, Y', strtotime($user['updated_at'])); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-secondary">No rejected applicants found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once '../../../includes/adminFooter.php';
?>
