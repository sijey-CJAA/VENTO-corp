<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin' || $_SESSION['role'] !== 'operations_admin') {
    header("Location: ../../auth.php");
    exit;
}

require_once '../../../config/db.php';



require_once '../../../includes/operationManagerHeader.php';
?>

<div class="dashboard pb-5 mt-2">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3 mb-4">
        <div>
            <span class="text-purple small fw-bold text-uppercase" style="letter-spacing: 1px;">Operations Admin</span>
            <h2 class="text-white fw-semibold mb-2 mt-1">Operations Management Center</h2>
            <p class="text-secondary mb-0">Monitor warehouse activity, stock handling work, employee productivity, and report submissions.</p>
        </div>
        <button class="btn btn-primary-purple px-4 py-2" type="button">
            <i class="bi bi-cloud-upload me-1"></i>Upload Employee Report
        </button>
    </div>

    <div class="info-box mb-4">
        <i class="bi bi-info-circle"></i>
        <span>Welcome, <strong><?php echo htmlspecialchars($_SESSION['first_name']); ?></strong>! This dashboard presents the frontend overview for the Operations Manager workspace.</span>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-lg-4">
            <div class="card h-100 p-4">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <span class="text-secondary small fw-bold text-uppercase" style="letter-spacing: 1px;">Employees Managed</span>
                        <h4 class="text-white fw-semibold mb-0 mt-2">Stock Handlers</h4>
                    </div>
                    <div class="rounded d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background-color: rgba(124,58,237,0.18); color: #d2bbff;">
                        <i class="bi bi-people fs-5"></i>
                    </div>
                </div>
                <p class="text-secondary small mb-4">Primary warehouse personnel responsible for stock movement, handling, and shelf coordination.</p>
                <div class="d-flex align-items-center gap-2 mt-auto">
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Active Group</span>
                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25">Operations Team</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-8">
            <div class="feature-card h-100">
                <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
                    <div>
                        <span class="text-purple small fw-bold text-uppercase" style="letter-spacing: 1px;">Responsibilities</span>
                        <h4 class="text-white fw-semibold mb-1 mt-2">Operations Admin</h4>
                        <p class="text-secondary small mb-0">A simple frontend view of the core responsibilities assigned to the Operations Manager.</p>
                    </div>
                    <button class="btn btn-outline-dark align-self-start" type="button">
                        <i class="bi bi-file-earmark-text me-1"></i>Report Panel
                    </button>
                </div>

                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <div class="card h-100 p-3">
                            <div class="d-flex align-items-start gap-3">
                                <div class="rounded d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px; background-color: rgba(210,187,255,0.12); color: #d2bbff;">
                                    <i class="bi bi-building-gear"></i>
                                </div>
                                <div>
                                    <h6 class="text-white fw-semibold mb-1">Oversees warehouse activities</h6>
                                    <p class="text-secondary small mb-0">Keeps warehouse operations organized and visible throughout the workday.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="card h-100 p-3">
                            <div class="d-flex align-items-start gap-3">
                                <div class="rounded d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px; background-color: rgba(196,193,251,0.12); color: #c4c1fb;">
                                    <i class="bi bi-box-seam"></i>
                                </div>
                                <div>
                                    <h6 class="text-white fw-semibold mb-1">Monitors stock handling tasks</h6>
                                    <p class="text-secondary small mb-0">Reviews stock movement, handling assignments, and task readiness.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="card h-100 p-3">
                            <div class="d-flex align-items-start gap-3">
                                <div class="rounded d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px; background-color: rgba(52,211,153,0.12); color: #34d399;">
                                    <i class="bi bi-graph-up-arrow"></i>
                                </div>
                                <div>
                                    <h6 class="text-white fw-semibold mb-1">Tracks employee productivity</h6>
                                    <p class="text-secondary small mb-0">Provides a visual space for productivity snapshots and team performance signals.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="card h-100 p-3">
                            <div class="d-flex align-items-start gap-3">
                                <div class="rounded d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px; background-color: rgba(250,204,21,0.12); color: #facc15;">
                                    <i class="bi bi-cloud-upload"></i>
                                </div>
                                <div>
                                    <h6 class="text-white fw-semibold mb-1">Uploads employee reports</h6>
                                    <p class="text-secondary small mb-0">Shows where employee report upload activity will be represented in the interface.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <div>
                <h5 class="text-white fw-semibold mb-1">Operations Snapshot</h5>
                <p class="text-secondary small mb-0">Frontend-only placeholders for warehouse visibility and stock handling coordination.</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <span class="badge bg-primary bg-opacity-10 text-purple border border-primary border-opacity-25 px-3 py-2">Warehouse Activities</span>
                <span class="badge bg-primary bg-opacity-10 text-purple border border-primary border-opacity-25 px-3 py-2">Stock Handling</span>
                <span class="badge bg-primary bg-opacity-10 text-purple border border-primary border-opacity-25 px-3 py-2">Employee Reports</span>
            </div>
        </div>
    </div>
</div>

<?php
require_once '../../../includes/adminFooter.php';
?>
