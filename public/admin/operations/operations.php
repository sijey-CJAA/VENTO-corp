<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin' || $_SESSION['role'] !== 'operations_admin') {
    header("Location: ../../auth.php");
    exit;
}

require_once '../../../config/db.php';

function operationsDashboardEscape($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function operationsDashboardFormatDateTime($value)
{
    if (empty($value)) {
        return 'Not yet recorded';
    }

    return date('M d, Y h:i A', strtotime($value));
}

function operationsDashboardFormatTaskAge($value)
{
    if (empty($value)) {
        return 'None';
    }

    $assigned_at = new DateTime($value);
    $now = new DateTime();
    $diff = $assigned_at->diff($now);

    if ($diff->days > 0) {
        return $diff->days . ' day' . ($diff->days === 1 ? '' : 's');
    }

    if ($diff->h > 0) {
        return $diff->h . ' hour' . ($diff->h === 1 ? '' : 's');
    }

    return max(1, $diff->i) . ' minute' . ($diff->i === 1 ? '' : 's');
}

function operationsDashboardTaskBadgeClass($status)
{
    if ($status === 'In Progress') {
        return 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25';
    }

    if ($status === 'Delivered') {
        return 'bg-success bg-opacity-10 text-success border border-success border-opacity-25';
    }

    return 'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25';
}

function operationsDashboardStockHolderKey($name)
{
    return strtolower(trim(preg_replace('/\s+/', ' ', (string) $name)));
}

// High-level counters for work that needs Operations Admin attention.
$pending_request_stmt = $pdo->prepare("SELECT COUNT(*) FROM requests WHERE status = 'Pending'");
$pending_request_stmt->execute();
$pending_request_count = (int) $pending_request_stmt->fetchColumn();

$unassigned_request_stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM requests r
    LEFT JOIN tasks t ON t.request_id = r.id
    WHERE r.status = 'Approved' AND t.id IS NULL
");
$unassigned_request_stmt->execute();
$unassigned_request_count = (int) $unassigned_request_stmt->fetchColumn();

$active_task_stmt = $pdo->prepare("SELECT COUNT(*) FROM tasks WHERE status IN ('Assigned', 'In Progress')");
$active_task_stmt->execute();
$active_task_count = (int) $active_task_stmt->fetchColumn();

$oldest_task_stmt = $pdo->prepare("SELECT MIN(assigned_at) FROM tasks WHERE status IN ('Assigned', 'In Progress')");
$oldest_task_stmt->execute();
$oldest_unfinished_at = $oldest_task_stmt->fetchColumn();

$delivered_stmt = $pdo->prepare("SELECT COUNT(*) FROM tasks WHERE status = 'Delivered'");
$delivered_stmt->execute();
$delivered_task_count = (int) $delivered_stmt->fetchColumn();

// Recent active tasks give the manager a quick view of current assignments.
$recent_tasks_stmt = $pdo->prepare("
    SELECT t.id, t.assigned_to, t.status, t.assigned_at, r.item_requested
    FROM tasks t
    JOIN requests r ON r.id = t.request_id
    WHERE t.status IN ('Assigned', 'In Progress')
    ORDER BY
        CASE t.status
            WHEN 'In Progress' THEN 1
            WHEN 'Assigned' THEN 2
            ELSE 3
        END,
        t.assigned_at ASC
    LIMIT 5
");
$recent_tasks_stmt->execute();
$recent_active_tasks = $recent_tasks_stmt->fetchAll(PDO::FETCH_ASSOC);

// Stock handler availability is derived from approved stock holders and active tasks.
$stock_holder_stmt = $pdo->prepare("
    SELECT first_name, last_name, email
    FROM employees
    WHERE role = 'stock_holder' AND status = 'approved'
    ORDER BY first_name ASC, last_name ASC
");
$stock_holder_stmt->execute();
$stock_holders = $stock_holder_stmt->fetchAll(PDO::FETCH_ASSOC);

$active_by_handler_stmt = $pdo->prepare("
    SELECT assigned_to, COUNT(*) AS active_count
    FROM tasks
    WHERE status IN ('Assigned', 'In Progress')
    GROUP BY assigned_to
");
$active_by_handler_stmt->execute();
$active_assignments = $active_by_handler_stmt->fetchAll(PDO::FETCH_ASSOC);

$active_task_counts_by_name = [];
foreach ($active_assignments as $assignment) {
    $active_task_counts_by_name[operationsDashboardStockHolderKey($assignment['assigned_to'])] = (int) $assignment['active_count'];
}

$handler_statuses = [];
$busy_handler_count = 0;

foreach ($stock_holders as $holder) {
    $full_name = trim($holder['first_name'] . ' ' . $holder['last_name']);
    $active_count = $active_task_counts_by_name[operationsDashboardStockHolderKey($full_name)] ?? 0;

    if ($active_count > 0) {
        $busy_handler_count++;
    }

    $handler_statuses[] = [
        'name' => $full_name,
        'email' => $holder['email'],
        'active_count' => $active_count,
        'status_label' => $active_count > 0 ? 'Busy' : 'Available',
        'status_class' => $active_count > 0
            ? 'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25'
            : 'bg-success bg-opacity-10 text-success border border-success border-opacity-25',
    ];
}

$approved_stock_holder_count = count($stock_holders);
$available_handler_count = max(0, $approved_stock_holder_count - $busy_handler_count);
$unfinished_attention_count = $pending_request_count + $unassigned_request_count + $active_task_count;

require_once '../../../includes/operationManagerHeader.php';
?>

<div class="dashboard">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Operations Manager Dashboard</h2>
    </div>

    <div class="info-box mb-4">
        <i class="bi bi-info-circle"></i>
        <span>Welcome, <strong><?php echo operationsDashboardEscape($_SESSION['first_name']); ?></strong>! Use this dashboard to assign work, monitor stock handlers, and catch unfinished operations tasks early.</span>
    </div>

    <div class="row g-4 mt-4">
        <div class="col-12 col-xl-7">
            <div class="card p-4 h-100">
                <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
                    <div>
                        <h5 class="text-white fw-bold mb-1">Task Assignment Queue</h5>
                        <p class="text-secondary small mb-0">Requests and delivery tasks that need Operations Admin supervision.</p>
                    </div>
                    <a href="request.php" class="btn btn-primary-purple align-self-md-start">
                        <i class="bi bi-clipboard-check me-1"></i> Manage Requests
                    </a>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-6 col-lg-3">
                        <div class="border border-secondary border-opacity-25 rounded p-3 h-100">
                            <div class="text-secondary small mb-2">Pending Requests</div>
                            <div class="stat-value text-warning"><?php echo $pending_request_count; ?></div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="border border-secondary border-opacity-25 rounded p-3 h-100">
                            <div class="text-secondary small mb-2">Awaiting Assignment</div>
                            <div class="stat-value text-purple"><?php echo $unassigned_request_count; ?></div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="border border-secondary border-opacity-25 rounded p-3 h-100">
                            <div class="text-secondary small mb-2">Active Tasks</div>
                            <div class="stat-value text-primary"><?php echo $active_task_count; ?></div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="border border-secondary border-opacity-25 rounded p-3 h-100">
                            <div class="text-secondary small mb-2">Oldest Open</div>
                            <div class="fs-3 fw-bold text-white"><?php echo operationsDashboardEscape(operationsDashboardFormatTaskAge($oldest_unfinished_at)); ?></div>
                        </div>
                    </div>
                </div>

                <h6 class="text-white fw-semibold mb-3">Recent Unfinished Tasks</h6>
                <?php if (count($recent_active_tasks) > 0): ?>
                    <div class="d-flex flex-column gap-3">
                        <?php foreach ($recent_active_tasks as $task): ?>
                            <div class="border border-secondary border-opacity-25 rounded p-3">
                                <div class="d-flex flex-column flex-md-row justify-content-between gap-2">
                                    <div>
                                        <div class="text-white fw-semibold"><?php echo operationsDashboardEscape($task['item_requested']); ?></div>
                                        <div class="text-secondary small">
                                            Task #<?php echo str_pad((string) $task['id'], 4, '0', STR_PAD_LEFT); ?>
                                            &bull; Assigned to <?php echo operationsDashboardEscape($task['assigned_to']); ?>
                                        </div>
                                    </div>
                                    <span class="badge <?php echo operationsDashboardEscape(operationsDashboardTaskBadgeClass($task['status'])); ?> align-self-start px-3 py-2">
                                        <?php echo operationsDashboardEscape($task['status']); ?>
                                    </span>
                                </div>
                                <div class="text-secondary small mt-2">
                                    Assigned: <?php echo operationsDashboardEscape(operationsDashboardFormatDateTime($task['assigned_at'])); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="border border-secondary border-opacity-25 rounded p-4 text-center text-secondary">
                        No unfinished delivery tasks right now.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-12 col-xl-5">
            <div class="card p-4 h-100">
                <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
                    <div>
                        <h5 class="text-white fw-bold mb-1">Stock Handler Status</h5>
                        <p class="text-secondary small mb-0">Availability based on active assigned or in-progress tasks.</p>
                    </div>
                    <a href="employees.php" class="btn btn-outline-dark align-self-md-start">
                        <i class="bi bi-people me-1"></i> View Employees
                    </a>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <div class="border border-secondary border-opacity-25 rounded p-3 h-100">
                            <div class="text-secondary small mb-2">Approved Handlers</div>
                            <div class="stat-value text-white"><?php echo $approved_stock_holder_count; ?></div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border border-secondary border-opacity-25 rounded p-3 h-100">
                            <div class="text-secondary small mb-2">Available</div>
                            <div class="stat-value text-success"><?php echo $available_handler_count; ?></div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border border-secondary border-opacity-25 rounded p-3 h-100">
                            <div class="text-secondary small mb-2">Busy</div>
                            <div class="stat-value text-warning"><?php echo $busy_handler_count; ?></div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border border-secondary border-opacity-25 rounded p-3 h-100">
                            <div class="text-secondary small mb-2">Delivered</div>
                            <div class="stat-value text-purple"><?php echo $delivered_task_count; ?></div>
                        </div>
                    </div>
                </div>

                <h6 class="text-white fw-semibold mb-3">Handler Availability</h6>
                <?php if (count($handler_statuses) > 0): ?>
                    <div class="d-flex flex-column gap-3">
                        <?php foreach ($handler_statuses as $handler): ?>
                            <div class="border border-secondary border-opacity-25 rounded p-3">
                                <div class="d-flex flex-column flex-md-row justify-content-between gap-2">
                                    <div>
                                        <div class="text-white fw-semibold"><?php echo operationsDashboardEscape($handler['name']); ?></div>
                                        <div class="text-secondary small"><?php echo operationsDashboardEscape($handler['email']); ?></div>
                                    </div>
                                    <span class="badge <?php echo operationsDashboardEscape($handler['status_class']); ?> align-self-start px-3 py-2">
                                        <?php echo operationsDashboardEscape($handler['status_label']); ?>
                                    </span>
                                </div>
                                <div class="text-secondary small mt-2">
                                    Active tasks: <?php echo $handler['active_count']; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="border border-secondary border-opacity-25 rounded p-4 text-center text-secondary">
                        No approved stock handlers found.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-12">
            <div class="card p-4">
                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                    <div>
                        <h5 class="text-white fw-bold mb-1">Operations Alerts</h5>
                        <p class="text-secondary small mb-0">Real-time reminders from existing request and task records.</p>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-value text-warning"><?php echo $unfinished_attention_count; ?></div>
                        <span class="text-secondary small">items need attention</span>
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-12 col-md-4">
                        <div class="info-box mb-0 h-100">
                            <i class="bi bi-bell"></i>
                            <span><?php echo $pending_request_count; ?> restock request<?php echo $pending_request_count === 1 ? '' : 's'; ?> waiting for approval or rejection.</span>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="info-box mb-0 h-100">
                            <i class="bi bi-person-check"></i>
                            <span><?php echo $unassigned_request_count; ?> approved request<?php echo $unassigned_request_count === 1 ? '' : 's'; ?> still need a stock handler assignment.</span>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="info-box mb-0 h-100">
                            <i class="bi bi-clock-history"></i>
                            <span><?php echo $active_task_count; ?> unfinished task<?php echo $active_task_count === 1 ? '' : 's'; ?> currently assigned or in progress.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once '../../../includes/adminFooter.php';
?>
