<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin' || $_SESSION['role'] !== 'inventory_admin') {
    header("Location: ../../auth.php");
    exit;
}

require_once '../../../config/db.php';

// Fetch my request history safely
$my_requests = [];
try {
    $req_stmt = $pdo->prepare("SELECT id, request_image, item_requested, requested_at, status, handled_by FROM requests ORDER BY requested_at DESC");
    $req_stmt->execute();
    $my_requests = $req_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Ignore if table is missing
}

require_once '../../../includes/inventoryAdminHeader.php';
?>

<div class="dashboard pt-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-medium text-white m-0">Request History</h2>
    </div>

    <div class="card p-4 h-100 mt-2" style="background-color: #18181b; border: 1px solid #27272a; border-radius: 10px; box-shadow: none;">
        <h4 class="text-white mb-3" style="font-size: 18px; font-weight: 600;">Past Requests</h4>
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0">
                <thead>
                    <tr>
                        <th class="text-secondary" style="width: 5%; background-color: #18181b; border-bottom: 1px solid #27272a;">ID</th>
                        <th class="text-secondary" style="width: 25%; background-color: #18181b; border-bottom: 1px solid #27272a;">Verification Picture</th>
                        <th class="text-secondary" style="width: 25%; background-color: #18181b; border-bottom: 1px solid #27272a;">Item Requested</th>
                        <th class="text-secondary" style="width: 15%; background-color: #18181b; border-bottom: 1px solid #27272a;">Status</th>
                        <th class="text-secondary" style="width: 15%; background-color: #18181b; border-bottom: 1px solid #27272a;">Handled By</th>
                        <th class="text-secondary" style="width: 15%; background-color: #18181b; border-bottom: 1px solid #27272a;">Requested At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($my_requests) > 0): ?>
                        <?php foreach ($my_requests as $req): ?>
                            <tr>
                                <td class="align-middle text-secondary" style="background-color: transparent; border-bottom: 1px solid #27272a;">#<?php echo str_pad($req['id'], 4, '0', STR_PAD_LEFT); ?></td>
                                <td class="align-middle" style="background-color: transparent; border-bottom: 1px solid #27272a;">
                                    <a href="../../uploads/inventory/requests/<?php echo htmlspecialchars($req['request_image']); ?>" target="_blank">
                                        <img src="../../uploads/inventory/requests/<?php echo htmlspecialchars($req['request_image']); ?>" alt="Proof" class="img-thumbnail border-secondary bg-transparent" style="max-height: 50px; object-fit: cover;">
                                    </a>
                                </td>
                                <td class="align-middle text-white fw-bold" style="background-color: transparent; border-bottom: 1px solid #27272a;"><?php echo htmlspecialchars($req['item_requested']); ?></td>
                                <td class="align-middle" style="background-color: transparent; border-bottom: 1px solid #27272a;">
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
                                <td class="align-middle text-white small" style="background-color: transparent; border-bottom: 1px solid #27272a;">
                                    <?php echo $req['handled_by'] ? htmlspecialchars($req['handled_by']) : '<span class="text-secondary">Awaiting Admin</span>'; ?>
                                </td>
                                <td class="align-middle text-secondary small" style="background-color: transparent; border-bottom: 1px solid #27272a;">
                                    <?php echo date('M d, Y h:i A', strtotime($req['requested_at'])); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-secondary py-4" style="background-color: transparent; border-bottom: 1px solid #27272a;">You have not submitted any requests yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
/* Ensure table rows hover style */
.table-hover tbody tr:hover td {
    background-color: rgba(168,85,247,0.05) !important;
}
</style>

<?php
require_once '../../../includes/inventoryAdminFooter.php';
?>
