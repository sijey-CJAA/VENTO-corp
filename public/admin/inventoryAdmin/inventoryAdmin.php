<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin' || $_SESSION['role'] !== 'inventory_admin') {
    header("Location: ../../auth.php");
    exit;
}

require_once '../../../config/db.php';

// Fetch employees under this admin
$emp_stmt = $pdo->prepare("SELECT first_name, last_name, email, status, created_at FROM employees WHERE role = 'inventory_clerk'");
$emp_stmt->execute();
$employees = $emp_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch inventory items safely
$inventory_items = [];
try {
    $inv_stmt = $pdo->prepare("SELECT id, name, quantity, status FROM inventory ORDER BY name ASC");
    $inv_stmt->execute();
    $inventory_items = $inv_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Ignore if table is missing
}

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

<?php
$total_employees = count($employees);
$pending_count = 0;
foreach ($employees as $emp) {
    if ($emp['status'] === 'pending') {
        $pending_count++;
    }
}

$total_inventory = count($inventory_items);
$total_requests = count($my_requests);
$pending_requests = 0;
foreach ($my_requests as $req) {
    if ($req['status'] === 'Pending') {
        $pending_requests++;
    }
}
?>

<div class="dashboard pt-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-medium text-white m-0">Inventory Admin Dashboard</h2>
    </div>
    
    <div class="info-box mb-4" style="background-color: #121212; border: 1px solid #27272a; padding: 20px; border-radius: 8px;">
        <span style="color: #a1a1aa; font-size: 14px;">Welcome back, <strong class="text-white"><?php echo htmlspecialchars($_SESSION['first_name']); ?></strong>! You are logged in as the Main Inventory Admin.</span>
    </div>
    
    <!-- Navigation Tabs -->
    <ul class="nav nav-pills mb-4" id="dashboardTabs" role="tablist" style="gap: 10px;">
        <li class="nav-item" role="presentation">
            <button class="nav-link active px-4 py-2" id="overview-tab" data-bs-toggle="pill" data-bs-target="#overview" type="button" role="tab" aria-controls="overview" aria-selected="true" style="border-radius: 8px; font-weight: 500; transition: all 0.2s;">
                <i class="bi bi-grid me-2"></i>Overview
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link px-4 py-2" id="request-history-tab" data-bs-toggle="pill" data-bs-target="#request-history" type="button" role="tab" aria-controls="request-history" aria-selected="false" style="border-radius: 8px; font-weight: 500; transition: all 0.2s;">
                <i class="bi bi-clock-history me-2"></i>Request History
            </button>
        </li>
    </ul>
    
    <div class="tab-content" id="dashboardTabsContent">
        <!-- Overview Tab -->
        <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
            <div class="row g-4 mt-2">
                <!-- Total Employees Card -->
                <div class="col-md-3">
                    <div class="card p-4 h-100" style="background-color: #18181b; border: 1px solid #27272a; border-radius: 10px; box-shadow: none;">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h6 class="text-white fw-medium m-0" style="font-size: 16px;">Employees</h6>
                            <i class="bi bi-people text-secondary fs-5"></i>
                        </div>
                        <h2 style="color: #a855f7; font-weight: 700; margin-bottom: 1.5rem; font-size: 2rem;"><?php echo $total_employees; ?></h2>
                        <p class="mb-0" style="color: #a1a1aa; font-size: 13px;">Active across all departments</p>
                    </div>
                </div>
                
                <!-- Pending Applications Card -->
                <div class="col-md-3">
                    <div class="card p-4 h-100" style="background-color: #18181b; border: 1px solid #27272a; border-radius: 10px; box-shadow: none;">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h6 class="text-white fw-medium m-0" style="font-size: 16px;">Pending Apps</h6>
                            <i class="bi bi-person-plus text-secondary fs-5"></i>
                        </div>
                        <h2 style="color: #f97316; font-weight: 700; margin-bottom: 1.5rem; font-size: 2rem;"><?php echo $pending_count; ?></h2>
                        <p class="mb-0" style="color: #a1a1aa; font-size: 13px;">Awaiting review</p>
                    </div>
                </div>

                <!-- Total Inventory Card -->
                <div class="col-md-3">
                    <div class="card p-4 h-100" style="background-color: #18181b; border: 1px solid #27272a; border-radius: 10px; box-shadow: none;">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h6 class="text-white fw-medium m-0" style="font-size: 16px;">Inventory Items</h6>
                            <i class="bi bi-box-seam text-secondary fs-5"></i>
                        </div>
                        <h2 style="color: #3b82f6; font-weight: 700; margin-bottom: 1.5rem; font-size: 2rem;"><?php echo $total_inventory; ?></h2>
                        <p class="mb-0" style="color: #a1a1aa; font-size: 13px;">Total unique items</p>
                    </div>
                </div>

                <!-- Pending Requests Card -->
                <div class="col-md-3">
                    <div class="card p-4 h-100" style="background-color: #18181b; border: 1px solid #27272a; border-radius: 10px; box-shadow: none;">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h6 class="text-white fw-medium m-0" style="font-size: 16px;">Pending Requests</h6>
                            <i class="bi bi-clipboard-pulse text-secondary fs-5"></i>
                        </div>
                        <h2 style="color: #ef4444; font-weight: 700; margin-bottom: 1.5rem; font-size: 2rem;"><?php echo $pending_requests; ?></h2>
                        <p class="mb-0" style="color: #a1a1aa; font-size: 13px;">Awaiting fulfillment</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Request History Tab -->
        <div class="tab-pane fade" id="request-history" role="tabpanel" aria-labelledby="request-history-tab">
            <div class="card p-4 h-100 mt-2" style="background-color: #18181b; border: 1px solid #27272a; border-radius: 10px; box-shadow: none;">
                <h4 class="text-white mb-3" style="font-size: 18px; font-weight: 600;">Request History</h4>
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
    </div>
</div>

<style>
/* Custom styling for nav pills */
.nav-pills .nav-link {
    color: #a1a1aa;
    background-color: transparent;
    border: 1px solid transparent;
}
.nav-pills .nav-link:hover {
    color: #fff;
    background-color: #18181b;
    border-color: #27272a;
}
.nav-pills .nav-link.active {
    color: #fff;
    background: linear-gradient(135deg, #7c3aed, #9333ea);
    border-color: transparent;
    box-shadow: 0 4px 12px rgba(124, 58, 237, 0.3);
}
/* Ensure table rows hover style */
.table-hover tbody tr:hover td {
    background-color: rgba(168,85,247,0.05) !important;
}
</style>

<?php
require_once '../../../includes/inventoryAdminFooter.php';
?>
