<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'employee' || $_SESSION['role'] !== 'inventory_clerk') {
    header("Location: ../../auth.php");
    exit;
}

require_once '../../../config/db.php';

$success_msg = '';
$error_msg = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_item'])) {
    $item_id = filter_input(INPUT_POST, 'item_id', FILTER_VALIDATE_INT);
    $new_quantity = filter_input(INPUT_POST, 'new_quantity', FILTER_VALIDATE_INT);
    
    if ($item_id && $new_quantity !== false && $new_quantity >= 0) {
        // Handle file upload
        if (isset($_FILES['verification_image']) && $_FILES['verification_image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../../uploads/inventory/';
            $file_extension = pathinfo($_FILES['verification_image']['name'], PATHINFO_EXTENSION);
            $filename = 'verification_' . $item_id . '_' . time() . '.' . $file_extension;
            $destination = $upload_dir . $filename;
            
            if (move_uploaded_file($_FILES['verification_image']['tmp_name'], $destination)) {
                // Update database
                $updater_name = $_SESSION['first_name'] . ' ' . ($_SESSION['last_name'] ?? '');
                $stmt = $pdo->prepare("UPDATE inventory SET quantity = ?, last_verification_image = ?, updated_by = ? WHERE id = ?");
                if ($stmt->execute([$new_quantity, $filename, $updater_name, $item_id])) {
                    $_SESSION['success_msg'] = "Item updated successfully!";
                    header("Location: status.php");
                    exit;
                } else {
                    $error_msg = "Database update failed.";
                }
            } else {
                $error_msg = "Failed to move uploaded file.";
            }
        } else {
            $error_msg = "A verification image is required.";
        }
    } else {
        $error_msg = "Invalid input provided.";
    }
}

if (isset($_SESSION['success_msg'])) {
    $success_msg = $_SESSION['success_msg'];
    unset($_SESSION['success_msg']);
}

// Fetch inventory items
$stmt = $pdo->prepare("SELECT id, name, quantity, status, last_verification_image, updated_at FROM inventory ORDER BY name ASC");
$stmt->execute();
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once '../../../includes/inventoryClerkHeader.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Inventory Status Updates</h2>
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

<div class="row g-4">
    <?php foreach ($items as $item): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h5 class="card-title text-white fw-bold mb-0"><?php echo htmlspecialchars($item['name']); ?></h5>
                        <?php if ($item['status'] === 'Out of Stock'): ?>
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">Out of Stock</span>
                        <?php elseif ($item['status'] === 'Low'): ?>
                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25">Low</span>
                        <?php else: ?>
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Good</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3 text-secondary">
                        <strong>Current Quantity:</strong> <span class="text-white"><?php echo (int)$item['quantity']; ?></span><br>
                        <small>Last Updated: <?php echo date('M d, Y h:i A', strtotime($item['updated_at'])); ?></small>
                    </div>

                    <?php if ($item['last_verification_image']): ?>
                        <div class="mb-3 text-center">
                            <img src="../../uploads/inventory/<?php echo htmlspecialchars($item['last_verification_image']); ?>" alt="Verification" class="img-fluid rounded border border-secondary" style="max-height: 150px; object-fit: cover;">
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="status.php" enctype="multipart/form-data" class="mt-auto pt-3 border-top border-secondary">
                        <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                        
                        <div class="mb-3">
                            <label class="form-label text-secondary small">New Quantity</label>
                            <input type="number" name="new_quantity" class="form-control" value="<?php echo (int)$item['quantity']; ?>" min="0" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-secondary small">Verification Picture</label>
                            <input type="file" name="verification_image" class="form-control" accept="image/*" required>
                        </div>
                        
                        <button type="submit" name="update_item" class="btn btn-primary-purple w-100">
                            <i class="bi bi-cloud-arrow-up me-2"></i>Update Item
                        </button>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    
    <?php if (empty($items)): ?>
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center text-secondary py-5">
                    No items found in inventory.
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    // Add file input visual feedback
    document.querySelectorAll('input[type="file"]').forEach(input => {
        input.addEventListener('change', function() {
            if(this.files && this.files[0]) {
                const label = this.previousElementSibling;
                label.innerHTML = `Verification Picture: <span class="text-success">${this.files[0].name}</span>`;
            }
        });
    });
</script>

<?php
require_once '../../../includes/employeeFooter.php';
?>
