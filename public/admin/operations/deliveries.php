<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin' || $_SESSION['role'] !== 'operations_admin') {
    header("Location: ../../auth.php");
    exit;
}

require_once '../../../config/db.php';

// Fetch all current deliveries
$tasks_stmt = $pdo->prepare("
    SELECT t.id as task_id, t.assigned_to, t.status as task_status, t.assigned_at, t.completed_at,
           r.item_requested, r.request_image, r.requested_by
    FROM tasks t
    JOIN requests r ON t.request_id = r.id
    ORDER BY 
        CASE t.status 
            WHEN 'In Progress' THEN 1 
            WHEN 'Assigned' THEN 2 
            WHEN 'Delivered' THEN 3 
        END,
        t.assigned_at DESC
");
$tasks_stmt->execute();
$all_deliveries = $tasks_stmt->fetchAll(PDO::FETCH_ASSOC);

require_once '../../../includes/operationManagerHeader.php';
?>

<div class="dashboard">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Current Deliveries</h2>
    </div>

    <div class="info-box mb-4">
        <i class="bi bi-truck"></i>
        <span>Track the live status of all physical deliveries assigned to Stock Holders.</span>
    </div>

    <div class="card p-4 mb-5">
        <h4 class="text-white mb-3">Delivery Tracker</h4>
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0">
                <thead>
                    <tr>
                        <th class="text-secondary" style="width: 5%;">Task ID</th>
                        <th class="text-secondary" style="width: 15%;">Verification Picture</th>
                        <th class="text-secondary" style="width: 20%;">Item Requested</th>
                        <th class="text-secondary" style="width: 20%;">Assigned To</th>
                        <th class="text-secondary" style="width: 15%;">Status</th>
                        <th class="text-secondary" style="width: 25%;">Timeline</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($all_deliveries) > 0): ?>
                        <?php foreach ($all_deliveries as $task): ?>
                            <tr>
                                <td class="align-middle text-secondary">#<?php echo str_pad($task['task_id'], 4, '0', STR_PAD_LEFT); ?></td>
                                <td class="align-middle">
                                    <a href="../../uploads/inventory/requests/<?php echo htmlspecialchars($task['request_image']); ?>" target="_blank">
                                        <img src="../../uploads/inventory/requests/<?php echo htmlspecialchars($task['request_image']); ?>" alt="Proof" class="img-thumbnail border-secondary bg-transparent" style="max-height: 50px; max-width: 80px; object-fit: cover;">
                                    </a>
                                </td>
                                <td class="align-middle text-white fw-bold"><?php echo htmlspecialchars($task['item_requested']); ?></td>
                                <td class="align-middle text-white">
                                    <i class="bi bi-person me-1 text-secondary"></i><?php echo htmlspecialchars($task['assigned_to']); ?>
                                </td>
                                <td class="align-middle">
                                    <?php if ($task['task_status'] === 'Assigned'): ?>
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1">Pending Start</span>
                                    <?php elseif ($task['task_status'] === 'In Progress'): ?>
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1">In Progress</span>
                                    <?php else: ?>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">Delivered</span>
                                    <?php endif; ?>
                                </td>
                                <td class="align-middle text-secondary small">
                                    <div class="mb-1"><strong>Assigned:</strong> <?php echo date('M d, Y h:i A', strtotime($task['assigned_at'])); ?></div>
                                    <?php if ($task['completed_at']): ?>
                                        <div class="text-success"><i class="bi bi-check-circle me-1"></i><strong>Completed:</strong> <?php echo date('M d, Y h:i A', strtotime($task['completed_at'])); ?></div>
                                    <?php else: ?>
                                        <div class="text-warning"><i class="bi bi-clock me-1"></i>Awaiting Completion</div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-secondary py-4">No deliveries have been assigned yet.</td>
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
