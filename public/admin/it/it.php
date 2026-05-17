<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin' || $_SESSION['role'] !== 'it_admin') {
    header("Location: ../../auth.php");
    exit;
}

require_once '../../../config/db.php';

// Fetch employees under this admin
$emp_stmt = $pdo->prepare("SELECT first_name, last_name, email, role, status, created_at FROM employees WHERE role = 'it_security'");
$emp_stmt->execute();
$employees = $emp_stmt->fetchAll(PDO::FETCH_ASSOC);

require_once '../../../includes/itAdminHeader.php';
?>

<style>
    .inner-glow { box-shadow: inset 0 0 12px rgba(124, 58, 237, 0.05); }
    .bg-surface-container-low { background-color: #1b1b1b !important; }
    .bg-surface-container { background-color: #1f1f1f !important; }
    .bg-surface-container-highest { background-color: #353535 !important; }
    .text-emerald-400 { color: #34d399 !important; }
    .bg-emerald-500 { background-color: #10b981 !important; }
    .text-error { color: #ffb4ab !important; }
    .bg-error { background-color: #ffb4ab !important; }
    .text-on-error { color: #690005 !important; }
    .border-outline-variant { border-color: #4a4455 !important; }
    .text-primary { color: #d2bbff !important; }
    .bg-primary { background-color: #d2bbff !important; }
    .font-data-mono { font-family: 'Courier New', Courier, monospace; }
</style>

<div class="dashboard pb-5 mt-2">
    
    <!-- Hero Stats Row -->
    <div class="row g-4 mb-4">
        <!-- System Integrity Status -->
        <div class="col-12 col-lg-4">
            <div class="card h-100 bg-surface-container-low border-outline-variant inner-glow p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-primary small fw-bold tracking-widest text-uppercase" style="letter-spacing: 1px;">System Health</span>
                    <i class="bi bi-hdd-network text-primary"></i>
                </div>
                <h4 class="text-white mb-2 fw-semibold">Database Integrity</h4>
                <div class="d-flex align-items-center gap-2 mb-4">
                    <div class="rounded-circle bg-emerald-500" style="width: 10px; height: 10px; box-shadow: 0 0 8px rgba(16,185,129,0.5);"></div>
                    <span class="text-emerald-400 small fw-bold text-uppercase" style="font-size: 0.8rem;">Optimal Operational Status</span>
                </div>
                
                <div class="mt-auto">
                    <div class="d-flex justify-content-between text-secondary small mb-1">
                        <span>Cluster Sync</span>
                        <span class="font-data-mono text-white">99.99%</span>
                    </div>
                    <div class="progress" style="height: 6px; background-color: #353535;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 99.99%; box-shadow: 0 0 10px rgba(124,58,237,0.4);" aria-valuenow="99.99" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alerts Banner -->
        <div class="col-12 col-lg-8">
            <div class="card h-100 bg-surface-container-low border-outline-variant inner-glow p-4 overflow-hidden position-relative">
                <div class="d-flex justify-content-between align-items-center mb-3 position-relative" style="z-index: 2;">
                    <span class="text-error small fw-bold tracking-widest text-uppercase" style="letter-spacing: 1px;">Suspicious Activity</span>
                    <i class="bi bi-exclamation-triangle text-error"></i>
                </div>
                <div class="row g-4 position-relative" style="z-index: 2;">
                    <div class="col-md-9 d-flex flex-column gap-3">
                        <div class="d-flex align-items-start gap-3 p-3 rounded" style="background-color: rgba(147,0,10,0.1); border: 1px solid rgba(255,180,171,0.2);">
                            <i class="bi bi-unlock text-error mt-1"></i>
                            <div>
                                <p class="text-white fw-semibold mb-0" style="font-size: 0.95rem;">Brute force attempt detected</p>
                                <p class="text-secondary small mb-0">IP: 192.168.1.104 • 42 attempts in 30s</p>
                            </div>
                            <span class="ms-auto font-data-mono text-error px-2 py-1 rounded" style="font-size: 10px; background-color: rgba(255,180,171,0.2);">URGENT</span>
                        </div>
                        <div class="d-flex align-items-start gap-3 p-3 rounded" style="background-color: rgba(53,53,53,0.4); border: 1px solid #4a4455;">
                            <i class="bi bi-geo-alt text-warning mt-1"></i>
                            <div>
                                <p class="text-white fw-semibold mb-0" style="font-size: 0.95rem;">Anomalous login location</p>
                                <p class="text-secondary small mb-0">User: k_miller • Singapore (Expected: NY)</p>
                            </div>
                            <span class="ms-auto font-data-mono text-warning px-2 py-1 rounded" style="font-size: 10px; background-color: rgba(255,193,7,0.2);">PENDING</span>
                        </div>
                    </div>
                    <div class="col-md-3 d-flex flex-column justify-content-center align-items-center border-start border-outline-variant">
                        <span class="text-error fw-bold mb-0" style="font-size: 3rem; line-height: 1;">03</span>
                        <span class="text-secondary small mb-3">Unresolved</span>
                        <button class="btn btn-sm px-3 font-weight-bold w-100" style="background-color: #ffb4ab; color: #690005; border: none; font-size: 0.8rem;">Investigate All</button>
                    </div>
                </div>
                <!-- Decorative element -->
                <div class="position-absolute rounded-circle" style="right: -40px; bottom: -40px; width: 160px; height: 160px; background-color: rgba(255,180,171,0.05); filter: blur(40px); z-index: 1;"></div>
            </div>
        </div>
    </div>

    <!-- Main Data Section Row -->
    <div class="row g-4 mb-4">
        <!-- User Roles & Permissions -->
        <div class="col-12 col-xl-7">
            <div class="card h-100 bg-surface-container border-outline-variant">
                <div class="p-4 border-bottom border-outline-variant d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="text-white mb-1 fw-semibold">Access Management</h5>
                        <p class="text-secondary small mb-0">Manage IT Encoders & Security</p>
                    </div>
                    <button class="btn btn-sm btn-outline-secondary text-white d-flex align-items-center gap-1 border-outline-variant" style="font-size: 0.85rem;">
                        <i class="bi bi-plus fs-5"></i> Add Role
                    </button>
                </div>
                <div class="table-responsive">
                    <?php
                    $it_auditor_count = 0;
                    foreach ($employees as $emp) {
                        if ($emp['role'] === 'it_security') $it_auditor_count++;
                    }
                    
                    $it_auditor_display = str_pad($it_auditor_count, 2, '0', STR_PAD_LEFT);
                    ?>
                    <table class="table table-dark table-hover mb-0" style="--bs-table-bg: transparent; --bs-table-border-color: rgba(255,255,255,0.05);">
                        <thead>
                            <tr style="background-color: #2a2a2a;">
                                <th class="text-secondary small font-weight-bold px-4 py-3 border-0">ROLE ENTITY</th>
                                <th class="text-secondary small font-weight-bold px-4 py-3 border-0">SECURITY CLEARANCE</th>
                                <th class="text-secondary small font-weight-bold px-4 py-3 border-0">MEMBERS</th>
                                <th class="text-secondary small font-weight-bold px-4 py-3 border-0">STATUS</th>
                                <th class="text-secondary small font-weight-bold px-4 py-3 border-0 text-end">ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Super Admin -->
                            <tr>
                                <td class="px-4 py-4 align-middle border-bottom border-outline-variant">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background-color: rgba(210,187,255,0.1); color: #d2bbff;">
                                            <i class="bi bi-shield-lock fs-6"></i>
                                        </div>
                                        <span class="text-white fw-semibold">Super Admin</span>
                                    </div>
                                </td>
                                <td class="px-4 py-4 align-middle border-bottom border-outline-variant font-data-mono text-primary" style="font-size: 0.8rem;">
                                    LEVEL 05 - ROOT
                                </td>
                                <td class="px-4 py-4 align-middle border-bottom border-outline-variant text-secondary small">
                                    04 Users
                                </td>
                                <td class="px-4 py-4 align-middle border-bottom border-outline-variant">
                                    <span class="d-flex align-items-center gap-2 text-emerald-400 fw-bold text-uppercase" style="font-size: 0.7rem;">
                                        <div class="rounded-circle bg-emerald-500" style="width: 6px; height: 6px;"></div> ACTIVE
                                    </span>
                                </td>
                                <td class="px-4 py-4 align-middle border-bottom border-outline-variant text-end">
                                    <button class="btn btn-sm border-0 text-secondary hover-primary" style="padding: 0;"><i class="bi bi-pencil fs-6"></i></button>
                                </td>
                            </tr>
                            
                            <!-- IT Auditor -->
                            <tr style="background-color: rgba(42,42,42,0.5);">
                                <td class="px-4 py-4 align-middle border-bottom border-outline-variant">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background-color: rgba(196,193,251,0.1); color: #c4c1fb;">
                                            <i class="bi bi-shield-check fs-6"></i>
                                        </div>
                                        <span class="text-white fw-semibold">IT Auditor</span>
                                    </div>
                                </td>
                                <td class="px-4 py-4 align-middle border-bottom border-outline-variant font-data-mono" style="font-size: 0.8rem; color: #c4c1fb;">
                                    LEVEL 03 - READ
                                </td>
                                <td class="px-4 py-4 align-middle border-bottom border-outline-variant text-secondary small">
                                    <?php echo $it_auditor_display; ?> Users
                                </td>
                                <td class="px-4 py-4 align-middle border-bottom border-outline-variant">
                                    <span class="d-flex align-items-center gap-2 text-emerald-400 fw-bold text-uppercase" style="font-size: 0.7rem;">
                                        <div class="rounded-circle bg-emerald-500" style="width: 6px; height: 6px;"></div> ACTIVE
                                    </span>
                                </td>
                                <td class="px-4 py-4 align-middle border-bottom border-outline-variant text-end">
                                    <button class="btn btn-sm border-0 text-secondary hover-primary" style="padding: 0;"><i class="bi bi-pencil fs-6"></i></button>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Compliance & Privacy Tracking -->
        <div class="col-12 col-xl-5 d-flex flex-column gap-4">
            <div class="card bg-surface-container border-outline-variant p-4 flex-grow-1">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="text-white mb-0 fw-semibold">Compliance & Privacy</h5>
                    <span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1 fw-bold" style="font-size: 0.7rem;">GDPR / SOC2</span>
                </div>
                
                <div class="d-flex flex-column gap-4 mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded bg-surface-container-highest d-flex justify-content-center align-items-center border border-outline-variant" style="width: 48px; height: 48px;">
                            <i class="bi bi-hammer text-primary fs-5"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-end mb-1">
                                <span class="text-white fw-semibold" style="font-size: 0.9rem;">SOC2 Audit Preparedness</span>
                                <span class="font-data-mono text-primary small">88%</span>
                            </div>
                            <div class="progress" style="height: 6px; background-color: #353535;">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: 88%;"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded bg-surface-container-highest d-flex justify-content-center align-items-center border border-outline-variant" style="width: 48px; height: 48px;">
                            <i class="bi bi-shield-check text-info fs-5"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-end mb-1">
                                <span class="text-white fw-semibold" style="font-size: 0.9rem;">Data Privacy Index</span>
                                <span class="font-data-mono text-info small">94%</span>
                            </div>
                            <div class="progress" style="height: 6px; background-color: #353535;">
                                <div class="progress-bar bg-info" role="progressbar" style="width: 94%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <button class="btn btn-outline-secondary w-100 mt-auto border-outline-variant text-white" style="font-size: 0.85rem; padding-top: 0.6rem; padding-bottom: 0.6rem;">Generate Compliance Report</button>
            </div>
            
            <div class="card bg-surface-container-highest border-outline-variant p-4 d-flex flex-row align-items-center gap-4">
                <i class="bi bi-clock-history text-warning fs-1 ms-2"></i>
                <div>
                    <p class="text-secondary small mb-1">Next Compliance Deadline</p>
                    <p class="text-white h5 mb-0 fw-bold">October 24, 2024</p>
                </div>
                <i class="bi bi-chevron-right text-secondary ms-auto fs-5 me-2"></i>
            </div>
        </div>
    </div>

    <!-- Access Logs -->
    <div class="card bg-surface-container border-outline-variant p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="text-white mb-0 fw-semibold">System Access Logs</h5>
            <div class="d-flex gap-2">
                <select class="form-select form-select-sm bg-surface-container-low border-outline-variant text-secondary shadow-none" style="width: auto;">
                    <option>All Systems</option>
                    <option>Database</option>
                    <option>Network</option>
                    <option>Auth Server</option>
                </select>
                <button class="btn btn-sm fw-bold" style="background-color: rgba(124,58,237,0.2); color: #ede0ff; border: 1px solid rgba(124,58,237,0.3); font-size: 0.75rem; letter-spacing: 0.5px;">Export CSV</button>
            </div>
        </div>
        
        <div class="d-flex flex-column gap-3">
            <!-- Log Item 1 -->
            <div class="row g-3 align-items-center bg-surface-container-low rounded border border-outline-variant px-3 py-2 mx-0">
                <div class="col-12 col-md-2 font-data-mono text-secondary" style="font-size: 0.75rem;">2023-11-04 14:22:01</div>
                <div class="col-12 col-md-3 d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-surface-container-highest overflow-hidden d-flex justify-content-center align-items-center" style="width: 24px; height: 24px;">
                        <i class="bi bi-person-fill text-secondary" style="font-size: 14px;"></i>
                    </div>
                    <span class="text-white small fw-semibold">james.dev@ventocorp.io</span>
                </div>
                <div class="col-12 col-md-3">
                    <span class="badge fw-bold text-uppercase" style="background-color: rgba(196,193,251,0.2); color: #c4c1fb; font-size: 0.65rem; padding: 0.35rem 0.5rem; letter-spacing: 0.5px;">SQL_QUERY_SUCCESS</span>
                </div>
                <div class="col-12 col-md-3 text-secondary small">Production Read-Only Cluster</div>
                <div class="col-12 col-md-1 text-end d-none d-md-block">
                    <i class="bi bi-three-dots-vertical text-secondary" style="cursor: pointer;"></i>
                </div>
            </div>

            <!-- Log Item 2 -->
            <div class="row g-3 align-items-center bg-surface-container-low rounded border border-outline-variant px-3 py-2 mx-0">
                <div class="col-12 col-md-2 font-data-mono text-secondary" style="font-size: 0.75rem;">2023-11-04 14:19:55</div>
                <div class="col-12 col-md-3 d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-surface-container-highest overflow-hidden d-flex justify-content-center align-items-center" style="width: 24px; height: 24px;">
                        <i class="bi bi-person-fill text-secondary" style="font-size: 14px;"></i>
                    </div>
                    <span class="text-white small fw-semibold">a.mercer@ventocorp.io</span>
                </div>
                <div class="col-12 col-md-3">
                    <span class="badge fw-bold text-uppercase" style="background-color: rgba(210,187,255,0.2); color: #d2bbff; font-size: 0.65rem; padding: 0.35rem 0.5rem; letter-spacing: 0.5px;">LOGIN_AUTH_OAUTH2</span>
                </div>
                <div class="col-12 col-md-3 text-secondary small">External Portal Integration</div>
                <div class="col-12 col-md-1 text-end d-none d-md-block">
                    <i class="bi bi-three-dots-vertical text-secondary" style="cursor: pointer;"></i>
                </div>
            </div>

            <!-- Log Item 3 (Error) -->
            <div class="row g-3 align-items-center bg-surface-container-low rounded px-3 py-2 mx-0" style="border: 1px solid #4a4455; border-left: 4px solid #ffb4ab !important;">
                <div class="col-12 col-md-2 font-data-mono text-error" style="font-size: 0.75rem;">2023-11-04 14:15:12</div>
                <div class="col-12 col-md-3 d-flex align-items-center gap-2">
                    <div class="rounded-circle d-flex justify-content-center align-items-center" style="width: 24px; height: 24px; background-color: rgba(255,180,171,0.2);">
                        <i class="bi bi-person-x-fill text-error" style="font-size: 14px;"></i>
                    </div>
                    <span class="text-error small fw-semibold">unknown_daemon_x</span>
                </div>
                <div class="col-12 col-md-3">
                    <span class="badge fw-bold text-error text-uppercase" style="background-color: rgba(255,180,171,0.2); font-size: 0.65rem; padding: 0.35rem 0.5rem; letter-spacing: 0.5px;">UNAUTHORIZED_ACCESS_BLOCK</span>
                </div>
                <div class="col-12 col-md-3 text-secondary small">Core Security Vault</div>
                <div class="col-12 col-md-1 text-end d-none d-md-block">
                    <i class="bi bi-shield-fill-exclamation text-error" style="cursor: pointer;"></i>
                </div>
            </div>
        </div>

        <div class="mt-4 text-center">
            <a href="#" class="text-primary small fw-semibold text-decoration-none d-inline-flex align-items-center gap-2" style="font-size: 0.85rem;">
                View Complete Audit History <i class="bi bi-arrow-down"></i>
            </a>
        </div>
    </div>

</div>

<?php
require_once '../../../includes/adminFooter.php';
?>
