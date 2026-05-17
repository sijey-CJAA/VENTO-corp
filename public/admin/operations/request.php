<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin' || $_SESSION['role'] !== 'operations_admin') {
    header("Location: ../../auth.php");
    exit;
}

require_once '../../../config/db.php';

$success_msg = '';
$error_msg = '';

// Handle form submission for approve/reject
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $request_id = filter_input(INPUT_POST, 'request_id', FILTER_VALIDATE_INT);
    $action = $_POST['action'];
    
    if ($request_id) {
        $handled_by = $_SESSION['first_name'] . ' ' . ($_SESSION['last_name'] ?? '');
        
        if ($action === 'approve') {
            $stmt = $pdo->prepare("UPDATE requests SET status = 'Approved', handled_by = ? WHERE id = ?");
            if ($stmt->execute([$handled_by, $request_id])) {
                // Redirect directly to assign task page
                header("Location: assignTask.php?request_id=" . $request_id);
                exit;
            } else {
                $error_msg = "Failed to approve request.";
            }
        } elseif ($action === 'reject') {
            $stmt = $pdo->prepare("UPDATE requests SET status = 'Rejected', handled_by = ? WHERE id = ?");
            if ($stmt->execute([$handled_by, $request_id])) {
                $_SESSION['success_msg'] = "Request has been rejected.";
                header("Location: request.php");
                exit;
            } else {
                $error_msg = "Failed to reject request.";
            }
        }
    } else {
        $error_msg = "Invalid request ID.";
    }
}

if (isset($_SESSION['success_msg'])) {
    $success_msg = $_SESSION['success_msg'];
    unset($_SESSION['success_msg']);
}

// Fetch all requests
$req_stmt = $pdo->prepare("SELECT id, request_image, item_requested, requested_by, requested_at, status, handled_by FROM requests ORDER BY CASE WHEN status = 'Pending' THEN 1 ELSE 2 END, requested_at DESC");
$req_stmt->execute();
$all_requests = $req_stmt->fetchAll(PDO::FETCH_ASSOC);

require_once '../../../includes/operationManagerHeader.php';
?>

<div class="dashboard">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Restock Requests</h2>
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
        <i class="bi bi-info-circle"></i>
        <span>Review and manage incoming restock requests from the Inventory Administration. Approving a request will direct you to assign a task to a Stock Holder.</span>
    </div>

    <!-- Request Management Table -->
    <div class="card p-4 mb-5">
        <h4 class="text-white mb-3">Incoming & Past Requests</h4>
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0">
                <thead>
                    <tr>
                        <th class="text-secondary" style="width: 5%;">ID</th>
                        <th class="text-secondary" style="width: 15%;">Verification Picture</th>
                        <th class="text-secondary" style="width: 20%;">Item Requested</th>
                        <th class="text-secondary" style="width: 15%;">Requested By</th>
                        <th class="text-secondary" style="width: 15%;">Requested At</th>
                        <th class="text-secondary" style="width: 10%;">Status</th>
                        <th class="text-secondary text-end" style="width: 20%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($all_requests) > 0): ?>
                        <?php foreach ($all_requests as $req): ?>
                            <tr>
                                <td class="align-middle text-secondary">#<?php echo str_pad($req['id'], 4, '0', STR_PAD_LEFT); ?></td>
                                <td class="align-middle">
                                    <a href="../../uploads/inventory/requests/<?php echo htmlspecialchars($req['request_image']); ?>" target="_blank">
                                        <img src="../../uploads/inventory/requests/<?php echo htmlspecialchars($req['request_image']); ?>" alt="Proof" class="img-thumbnail border-secondary bg-transparent" style="max-height: 50px; max-width: 80px; object-fit: cover;">
                                    </a>
                                </td>
                                <td class="align-middle text-white fw-bold"><?php echo htmlspecialchars($req['item_requested']); ?></td>
                                <td class="align-middle text-secondary"><?php echo htmlspecialchars($req['requested_by']); ?></td>
                                <td class="align-middle text-secondary small">
                                    <?php echo date('M d, Y h:i A', strtotime($req['requested_at'])); ?>
                                </td>
                                <td class="align-middle">
                                    <?php if ($req['status'] === 'Pending'): ?>
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1">Pending</span>
                                    <?php elseif ($req['status'] === 'Approved'): ?>
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1">Approved</span>
                                    <?php elseif ($req['status'] === 'Completed'): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">Completed</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1">Rejected</span>
                                    <?php endif; ?>
                                </td>
                                <td class="align-middle text-end">
                                    <?php if ($req['status'] === 'Pending'): ?>
                                        <form method="POST" action="request.php" class="d-inline">
                                            <input type="hidden" name="request_id" value="<?php echo $req['id']; ?>">
                                            <button type="submit" name="action" value="approve" class="btn btn-sm btn-outline-success me-1" title="Approve & Assign Task">
                                                <i class="bi bi-check-lg"></i> Approve
                                            </button>
                                            <button type="submit" name="action" value="reject" class="btn btn-sm btn-outline-danger" title="Reject Request" onclick="return confirm('Are you sure you want to reject this request?');">
                                                <i class="bi bi-x-lg"></i> Reject
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-secondary small">Handled by: <?php echo htmlspecialchars($req['handled_by']); ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-secondary py-4">No requests found.</td>
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
