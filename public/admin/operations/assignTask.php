<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin' || $_SESSION['role'] !== 'operations_admin') {
    header("Location: ../../auth.php");
    exit;
}

require_once '../../../config/db.php';

$success_msg = '';
$error_msg = '';

$request_id = filter_input(INPUT_GET, 'request_id', FILTER_VALIDATE_INT) ?: filter_input(INPUT_POST, 'request_id', FILTER_VALIDATE_INT);

if (!$request_id) {
    header("Location: request.php");
    exit;
}

// Fetch request details
$req_stmt = $pdo->prepare("SELECT * FROM requests WHERE id = ?");
$req_stmt->execute([$request_id]);
$request_data = $req_stmt->fetch(PDO::FETCH_ASSOC);

if (!$request_data || $request_data['status'] !== 'Approved') {
    header("Location: request.php");
    exit;
}

// Handle Task Assignment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_task'])) {
    $assigned_to = trim(filter_input(INPUT_POST, 'assigned_to', FILTER_SANITIZE_STRING));
    
    if (!empty($assigned_to)) {
        $stmt = $pdo->prepare("INSERT INTO tasks (request_id, assigned_to) VALUES (?, ?)");
        if ($stmt->execute([$request_id, $assigned_to])) {
            $_SESSION['success_msg'] = "Task successfully assigned to " . htmlspecialchars($assigned_to) . ".";
            header("Location: request.php");
            exit;
        } else {
            $error_msg = "Failed to assign the task in the database.";
        }
    } else {
        $error_msg = "Please select an employee to assign this task.";
    }
}

// Fetch Stock Holders for the dropdown
$emp_stmt = $pdo->prepare("SELECT first_name, last_name, email FROM employees WHERE role = 'stock_holder' AND status = 'approved' ORDER BY first_name ASC");
$emp_stmt->execute();
$stock_holders = $emp_stmt->fetchAll(PDO::FETCH_ASSOC);

require_once '../../../includes/operationManagerHeader.php';
?>

<div class="dashboard">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Assign Delivery Task</h2>
        <a href="request.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i> Back to Requests
        </a>
    </div>

    <?php if ($error_msg): ?>
        <div class="alert alert-danger bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25" role="alert">
            <?php echo htmlspecialchars($error_msg); ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card p-4 h-100">
                <h4 class="text-white mb-4 border-bottom border-secondary pb-2">Request Information</h4>
                
                <div class="mb-3">
                    <span class="text-secondary small d-block">Request ID</span>
                    <span class="text-white fw-bold fs-5">#<?php echo str_pad($request_data['id'], 4, '0', STR_PAD_LEFT); ?></span>
                </div>
                
                <div class="mb-3">
                    <span class="text-secondary small d-block">Item Requested</span>
                    <span class="text-white fw-bold fs-5 text-purple"><?php echo htmlspecialchars($request_data['item_requested']); ?></span>
                </div>
                
                <div class="mb-3">
                    <span class="text-secondary small d-block">Requested By (Inventory Admin)</span>
                    <span class="text-white"><?php echo htmlspecialchars($request_data['requested_by']); ?></span>
                </div>
                
                <div class="mb-3">
                    <span class="text-secondary small d-block">Verification Picture</span>
                    <?php if ($request_data['request_image']): ?>
                        <a href="../../uploads/inventory/requests/<?php echo htmlspecialchars($request_data['request_image']); ?>" target="_blank">
                            <img src="../../uploads/inventory/requests/<?php echo htmlspecialchars($request_data['request_image']); ?>" alt="Proof" class="img-thumbnail border-secondary bg-transparent mt-2" style="max-height: 150px;">
                        </a>
                    <?php else: ?>
                        <span class="text-secondary">N/A</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card p-4 h-100">
                <h4 class="text-white mb-4 border-bottom border-secondary pb-2">Task Assignment</h4>
                
                <div class="info-box mb-4">
                    <i class="bi bi-person-check"></i>
                    <span>Select an available Stock Holder to physically fulfill this request and deliver the items to the warehouse.</span>
                </div>

                <form method="POST" action="assignTask.php">
                    <input type="hidden" name="request_id" value="<?php echo $request_data['id']; ?>">
                    
                    <div class="mb-4">
                        <label class="form-label text-secondary">Assign To (Stock Holder)</label>
                        <select name="assigned_to" class="form-select text-white" required>
                            <option value="" selected disabled>-- Select Employee --</option>
                            <?php foreach ($stock_holders as $emp): ?>
                                <?php $emp_name = htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']); ?>
                                <option value="<?php echo $emp_name; ?>"><?php echo $emp_name; ?> (<?php echo htmlspecialchars($emp['email']); ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <button type="submit" name="assign_task" class="btn btn-primary-purple w-100 py-2 fs-5">
                        <i class="bi bi-send-check me-2"></i> Confirm Assignment
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
require_once '../../../includes/adminFooter.php';
?>
