<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'employee' || $_SESSION['role'] !== 'stock_holder') {
    header("Location: ../../auth.php");
    exit;
}

require_once '../../../config/db.php';

// Fetch exact full name from database
$emp_stmt = $pdo->prepare("SELECT first_name, last_name FROM employees WHERE id = ?");
$emp_stmt->execute([$_SESSION['user_id']]);
$emp_data = $emp_stmt->fetch(PDO::FETCH_ASSOC);
$employee_name = $emp_data['first_name'] . ' ' . $emp_data['last_name'];

// Fetch completed tasks for this stock holder
$stmt = $pdo->prepare("
    SELECT t.id as task_id, t.assigned_at, t.completed_at,
           r.item_requested, r.request_image, r.requested_by
    FROM tasks t
    JOIN requests r ON t.request_id = r.id
    WHERE t.assigned_to = ? AND t.status = 'Delivered'
    ORDER BY t.completed_at DESC
");
$stmt->execute([$employee_name]);
$completed_tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once '../../../includes/stockHolderHeader.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Delivery History</h2>
</div>

<div class="info-box mb-4">
    <i class="bi bi-clock-history"></i>
    <span>Review all your completed delivery tasks. The items listed here have successfully reached the warehouse.</span>
</div>

<div class="card p-4 mb-5">
    <h4 class="text-white mb-3">Completed Deliveries</h4>
    <div class="table-responsive">
        <table class="table table-dark table-hover mb-0">
            <thead>
                <tr>
                    <th class="text-secondary" style="width: 10%;">Task ID</th>
                    <th class="text-secondary" style="width: 15%;">Verification Picture</th>
                    <th class="text-secondary" style="width: 25%;">Item Delivered</th>
                    <th class="text-secondary" style="width: 20%;">Requested By</th>
                    <th class="text-secondary" style="width: 30%;">Delivery Timeline</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($completed_tasks) > 0): ?>
                    <?php foreach ($completed_tasks as $task): ?>
                        <tr>
                            <td class="align-middle text-secondary">#<?php echo str_pad($task['task_id'], 4, '0', STR_PAD_LEFT); ?></td>
                            <td class="align-middle">
                                <?php if ($task['request_image']): ?>
                                    <a href="../../uploads/inventory/requests/<?php echo htmlspecialchars($task['request_image']); ?>" target="_blank">
                                        <img src="../../uploads/inventory/requests/<?php echo htmlspecialchars($task['request_image']); ?>" alt="Proof" class="img-thumbnail border-secondary bg-transparent" style="max-height: 50px; max-width: 80px; object-fit: cover;">
                                    </a>
                                <?php else: ?>
                                    <span class="text-secondary small">N/A</span>
                                <?php endif; ?>
                            </td>
                            <td class="align-middle text-white fw-bold"><?php echo htmlspecialchars($task['item_requested']); ?></td>
                            <td class="align-middle text-white">
                                <i class="bi bi-person me-1 text-secondary"></i><?php echo htmlspecialchars($task['requested_by']); ?>
                            </td>
                            <td class="align-middle text-secondary small">
                                <div class="mb-1"><strong>Assigned:</strong> <?php echo date('M d, Y h:i A', strtotime($task['assigned_at'])); ?></div>
                                <div class="text-success"><i class="bi bi-check-circle me-1"></i><strong>Delivered:</strong> <?php echo date('M d, Y h:i A', strtotime($task['completed_at'])); ?></div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-secondary py-4">You have not completed any deliveries yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
require_once '../../../includes/employeeFooter.php';
?>
