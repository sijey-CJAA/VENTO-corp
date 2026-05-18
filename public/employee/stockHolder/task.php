<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'employee' || $_SESSION['role'] !== 'stock_holder') {
    header("Location: ../../auth.php");
    exit;
}

require_once '../../../config/db.php';

$success_msg = '';
$error_msg = '';

// Fetch exact full name from database
$emp_stmt = $pdo->prepare("SELECT first_name, last_name FROM employees WHERE id = ?");
$emp_stmt->execute([$_SESSION['user_id']]);
$emp_data = $emp_stmt->fetch(PDO::FETCH_ASSOC);
$employee_name = $emp_data['first_name'] . ' ' . $emp_data['last_name'];

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $task_id = filter_input(INPUT_POST, 'task_id', FILTER_VALIDATE_INT);
    $action = $_POST['action'];
    
    if ($task_id) {
        if ($action === 'start') {
            $stmt = $pdo->prepare("UPDATE tasks SET status = 'In Progress' WHERE id = ? AND assigned_to = ? AND status = 'Assigned'");
            if ($stmt->execute([$task_id, $employee_name])) {
                $_SESSION['success_msg'] = "Task started successfully! Please proceed to the warehouse.";
                header("Location: task.php");
                exit;
            } else {
                $error_msg = "Failed to update task status.";
            }
        } elseif ($action === 'deliver') {
            $stmt = $pdo->prepare("UPDATE tasks SET status = 'Delivered', completed_at = CURRENT_TIMESTAMP WHERE id = ? AND assigned_to = ? AND status = 'In Progress'");
            if ($stmt->execute([$task_id, $employee_name])) {
                $_SESSION['success_msg'] = "Items marked as delivered! Great job.";
                header("Location: task.php");
                exit;
            } else {
                $error_msg = "Failed to complete delivery task.";
            }
        }
    } else {
        $error_msg = "Invalid task specified.";
    }
}

if (isset($_SESSION['success_msg'])) {
    $success_msg = $_SESSION['success_msg'];
    unset($_SESSION['success_msg']);
}

// Fetch active tasks for this stock holder
$stmt = $pdo->prepare("
    SELECT t.id as task_id, t.status as task_status, t.assigned_at,
           r.item_requested, r.request_image, r.requested_by
    FROM tasks t
    JOIN requests r ON t.request_id = r.id
    WHERE t.assigned_to = ? AND t.status != 'Delivered'
    ORDER BY 
        CASE t.status 
            WHEN 'In Progress' THEN 1 
            WHEN 'Assigned' THEN 2 
        END,
        t.assigned_at DESC
");
$stmt->execute([$employee_name]);
$my_tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once '../../../includes/stockHolderHeader.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>My Delivery Tasks</h2>
</div>

<?php if ($success_msg): ?>
    <div class="alert alert-success bg-success bg-opacity-10 text-success border border-success border-opacity-25" role="alert">
        <?php echo htmlspecialchars($success_msg); ?>
    </div>
<?php endif; ?>

<?php if ($error_msg): ?>
    <div class="alert alert-danger bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25" role="alert">
        <?php echo htmlspecialchars($error_msg); ?>
    </div>
<?php endif; ?>

<div class="info-box mb-4">
    <i class="bi bi-box-seam"></i>
    <span>These tasks are assigned to you by the Operations Admin. Start a task when you begin moving items, and mark it as delivered once the items are physically at the warehouse.</span>
</div>

<div class="row g-4">
    <?php if (count($my_tasks) > 0): ?>
        <?php foreach ($my_tasks as $task): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-3 border-bottom border-secondary pb-3">
                            <div>
                                <span class="text-secondary small d-block mb-1">Task #<?php echo str_pad($task['task_id'], 4, '0', STR_PAD_LEFT); ?></span>
                                <h5 class="card-title text-white fw-bold mb-0 text-purple"><?php echo htmlspecialchars($task['item_requested']); ?></h5>
                            </div>
                            <?php if ($task['task_status'] === 'Assigned'): ?>
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1">Pending</span>
                            <?php elseif ($task['task_status'] === 'In Progress'): ?>
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1">In Progress</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-3 text-secondary small">
                            <i class="bi bi-person me-1"></i> Requested by: <span class="text-white"><?php echo htmlspecialchars($task['requested_by']); ?></span><br>
                            <i class="bi bi-clock me-1"></i> Assigned: <?php echo date('M d, Y h:i A', strtotime($task['assigned_at'])); ?>
                        </div>

                        <?php if ($task['request_image']): ?>
                            <div class="mb-4 text-center mt-auto">
                                <a href="../../uploads/inventory/requests/<?php echo htmlspecialchars($task['request_image']); ?>" target="_blank">
                                    <img src="../../uploads/inventory/requests/<?php echo htmlspecialchars($task['request_image']); ?>" alt="Proof" class="img-fluid rounded border border-secondary" style="max-height: 120px; object-fit: cover;">
                                </a>
                            </div>
                        <?php endif; ?>

                        <div class="mt-auto pt-3 border-top border-secondary">
                            <?php if ($task['task_status'] === 'Assigned'): ?>
                                <form method="POST" action="task.php">
                                    <input type="hidden" name="task_id" value="<?php echo $task['task_id']; ?>">
                                    <button type="submit" name="action" value="start" class="btn btn-outline-primary w-100">
                                        <i class="bi bi-play-circle me-2"></i>Start Delivery Task
                                    </button>
                                </form>
                            <?php elseif ($task['task_status'] === 'In Progress'): ?>
                                <form method="POST" action="task.php">
                                    <input type="hidden" name="task_id" value="<?php echo $task['task_id']; ?>">
                                    <button type="submit" name="action" value="deliver" class="btn btn-primary-purple w-100">
                                        <i class="bi bi-check2-circle me-2"></i>Mark as Delivered
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center text-secondary py-5">
                    <i class="bi bi-inbox fs-1 mb-3 d-block text-muted"></i>
                    You have no delivery tasks assigned currently.
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
require_once '../../../includes/employeeFooter.php';
?>
