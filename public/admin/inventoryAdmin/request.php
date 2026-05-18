<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin' || $_SESSION['role'] !== 'inventory_admin') {
    header("Location: ../../auth.php");
    exit;
}

require_once '../../../config/db.php';

$success_msg = '';
$error_msg = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_request'])) {
    $item_requested = trim(filter_input(INPUT_POST, 'item_requested', FILTER_SANITIZE_STRING));
    
    if (!empty($item_requested) && isset($_FILES['request_image']) && $_FILES['request_image']['error'] === UPLOAD_ERR_OK) {
        $requested_by = $_SESSION['first_name'] . ' ' . ($_SESSION['last_name'] ?? '');
        
        $upload_dir = '../../uploads/inventory/requests/';
        $file_extension = pathinfo($_FILES['request_image']['name'], PATHINFO_EXTENSION);
        $filename = 'request_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
        $destination = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['request_image']['tmp_name'], $destination)) {
            $stmt = $pdo->prepare("INSERT INTO requests (request_image, item_requested, requested_by) VALUES (?, ?, ?)");
            if ($stmt->execute([$filename, $item_requested, $requested_by])) {
                $_SESSION['success_msg'] = "Restock request submitted successfully!";
                header("Location: request.php");
                exit;
            } else {
                $error_msg = "Failed to submit request to database.";
            }
        } else {
            $error_msg = "Failed to upload the verification image.";
        }
    } else {
        $error_msg = "Please provide an item name and upload a verification picture.";
    }
}

if (isset($_SESSION['success_msg'])) {
    $success_msg = $_SESSION['success_msg'];
    unset($_SESSION['success_msg']);
}

// Fetch all existing items for cards safely
$inventory_items = [];
try {
    $inv_stmt = $pdo->prepare("SELECT id, name, quantity, status FROM inventory ORDER BY name ASC");
    $inv_stmt->execute();
    $inventory_items = $inv_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Table missing
}

// Fetch my request history safely
$my_requests = [];
try {
    $req_stmt = $pdo->prepare("SELECT id, request_image, item_requested, requested_at, status, handled_by FROM requests ORDER BY requested_at DESC");
    $req_stmt->execute();
    $my_requests = $req_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Table missing
}

require_once '../../../includes/inventoryAdminHeader.php';
?>

<div class="dashboard">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Restock Requests</h2>
        <button class="btn btn-primary-purple" data-bs-toggle="modal" data-bs-target="#customRequestModal">
            <i class="bi bi-plus-lg me-2"></i> Request Custom Item
        </button>
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
        <span>Submit restock requests to the Operations Admin. You can quickly request existing items or create a custom request for new supplies.</span>
    </div>

    <!-- Request History Table -->
    <div class="card p-4 mb-5">
        <h4 class="text-white mb-3">Request History</h4>
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0">
                <thead>
                    <tr>
                        <th class="text-secondary" style="width: 5%;">ID</th>
                        <th class="text-secondary" style="width: 25%;">Verification Picture</th>
                        <th class="text-secondary" style="width: 25%;">Item Requested</th>
                        <th class="text-secondary" style="width: 15%;">Status</th>
                        <th class="text-secondary" style="width: 15%;">Handled By</th>
                        <th class="text-secondary" style="width: 15%;">Requested At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($my_requests) > 0): ?>
                        <?php foreach ($my_requests as $req): ?>
                            <tr>
                                <td class="align-middle text-secondary">#<?php echo str_pad($req['id'], 4, '0', STR_PAD_LEFT); ?></td>
                                <td class="align-middle">
                                    <a href="../../uploads/inventory/requests/<?php echo htmlspecialchars($req['request_image']); ?>" target="_blank">
                                        <img src="../../uploads/inventory/requests/<?php echo htmlspecialchars($req['request_image']); ?>" alt="Proof" class="img-thumbnail border-secondary bg-transparent" style="max-height: 50px; object-fit: cover;">
                                    </a>
                                </td>
                                <td class="align-middle text-white fw-bold"><?php echo htmlspecialchars($req['item_requested']); ?></td>
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
                                <td class="align-middle text-white small">
                                    <?php echo $req['handled_by'] ? htmlspecialchars($req['handled_by']) : '<span class="text-secondary">Awaiting Admin</span>'; ?>
                                </td>
                                <td class="align-middle text-secondary small">
                                    <?php echo date('M d, Y h:i A', strtotime($req['requested_at'])); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-secondary py-4">You have not submitted any requests yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Restock Cards -->
    <h4 class="text-white mb-3">Quick Restock Catalog</h4>
    <div class="row g-4">
        <?php foreach ($inventory_items as $item): ?>
            <div class="col-md-4 col-lg-3">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column text-center">
                        <div class="mb-3">
                            <h5 class="card-title text-white fw-bold mb-1"><?php echo htmlspecialchars($item['name']); ?></h5>
                            <?php if ($item['status'] === 'Out of Stock'): ?>
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">Out of Stock</span>
                            <?php elseif ($item['status'] === 'Low'): ?>
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25">Low Stock (<?php echo $item['quantity']; ?>)</span>
                            <?php else: ?>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Stock: <?php echo $item['quantity']; ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mt-auto pt-3 border-top border-secondary">
                            <button class="btn btn-outline-light w-100 btn-sm" data-bs-toggle="modal" data-bs-target="#restockModal" data-item="<?php echo htmlspecialchars($item['name']); ?>">
                                Request Restock
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Restock Existing Item Modal -->
<div class="modal fade" id="restockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark border border-secondary text-white">
            <form method="POST" action="request.php" enctype="multipart/form-data">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title">Request Restock</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-secondary small">Item to Restock</label>
                        <input type="text" name="item_requested" id="modalItemInput" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-secondary small">Verification Picture / Proof</label>
                        <input type="file" name="request_image" class="form-control" accept="image/*" required>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="create_request" class="btn btn-primary-purple">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Custom Item Request Modal -->
<div class="modal fade" id="customRequestModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark border border-secondary text-white">
            <form method="POST" action="request.php" enctype="multipart/form-data">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title">Request New/Custom Item</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-secondary small">Item Name / Description</label>
                        <input type="text" name="item_requested" class="form-control" placeholder="e.g., Ergonomic Office Chairs" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-secondary small">Verification Picture / Proof</label>
                        <input type="file" name="request_image" class="form-control" accept="image/*" required>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="create_request" class="btn btn-primary-purple">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap JS (Needed for Modals) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Move modals to body to avoid z-index issues with the main container
    document.body.appendChild(document.getElementById('restockModal'));
    document.body.appendChild(document.getElementById('customRequestModal'));

    // Populate the Restock Modal with the correct item name when a button is clicked
    const restockModal = document.getElementById('restockModal');
    if (restockModal) {
        restockModal.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget;
            const itemName = button.getAttribute('data-item');
            const input = restockModal.querySelector('#modalItemInput');
            input.value = itemName;
        });
    }
</script>

<?php
require_once '../../../includes/inventoryAdminFooter.php';
?>
